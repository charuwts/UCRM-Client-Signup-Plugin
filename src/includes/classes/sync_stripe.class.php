<?php 
class SyncStripe extends StripeApi {
  /**
   * ## Initial Syncronizations
   * # Convert Stripe Products and Pricing Plans to Service Plans and Periods
   *
   */
  public function convertProductsToServicePlans() {
    // ## Instantiate Ucrm 
    $ucrm_handler = new UcrmHandler;

    // ## Only import if UCRM plans are empty!
    $service_plans = json_decode($ucrm_handler->getServicePlans());
    if (count($service_plans) === 0) {

      // ## Get all Products from Stripe
      $products = \Stripe\Product::all(array("limit" => 10, "active" => true));

      // ## Loop all products
      foreach ($products->autoPagingIterator() as $product) {

        // ## Get Plans associated with current Product
        $plans = \Stripe\Plan::all(array("limit" => 10, "active" => true, "product" => $product->id));
        
        // ## Loop plans associated with current product
        foreach ($plans->autoPagingIterator() as $plan) {

          // ## Only handle plans with one month interval
          if (($plan->interval === "month") && ($plan->interval_count === 1)) {
            
            // ## Create service with product and plan
            $service_plan = $ucrm_handler->createServicePlan($product, $plan);

            // ## Store UCRM Service ID's in Stripe payment plan metadata
            $plan->metadata = [
              "ucrm_service_plan_id" => $service_plan->id,
              "ucrm_service_period_id" => $service_plan->periods[0]->id
            ];
            
            // ## Get result after StripeTry save
            $result = $this->StripeTry([$plan], function($plans) {
              return $plans[0]->save();
            });

            // ## Log Product import success
            log_event('Product Imported', $result->id);
            
          }

        } // # End second autoPagingIterator
        
      } // # End first autoPagingIterator

    } else {
      log_event('System', 'Service Plans already exist, skipping import products');
    }

  } // # End convertProductsToServicePlans

  public function syncSubscriptionsToServices() {
    

    // ## Instantiate Ucrm 
    $ucrm_handler = new UcrmHandler;

    // ## Get All Ucrm Clients 
    $all_clients = $ucrm_handler->getClients();

    // ## If clients does not return false 
    if ($all_clients !== false) {
      

        
        // ## Loop clients to sync 
        foreach ($all_clients as $client) {
          // ## Get UCRM User's Services
          $client_services = json_decode($ucrm_handler->getClientServices($client->id));

          // ## Client info for log
          $message = 'Client ID: ' . $client->id . ' Customer ID: ' . $client->userIdent;

          // ## If services do not already exist 
          if (count($client_services) === 0) {
            // ## Get Stripe Customer's Subscriptions
            $subscriptions = $this->StripeTry($client->userIdent, function($userIdent) {
              return \Stripe\Subscription::all(array('customer' => $userIdent, 'limit'=>10));
            });

            if (count($subscriptions->data) > 0) {
              foreach ($subscriptions->data as $subscription) {
                
                // ## Dates formatted
                $now = new DateTime();
                // ## Trial End, to compare to now
                if (empty($subscription->trial_end)) {
                  $trial_end_date = $now;
                } else {
                  $trial_end_date = new DateTime("@{$subscription->trial_end}");
                }
                $current_period_start = date('c', strtotime($subscription->current_period_start));
                // ## Day of month
                $invoicingPeriodStartDay = date('d', $subscription->current_period_start);

                // ## If subscription trial has ended
                if ($trial_end_date <= $now) {
                  // ## Setup service data
                  $service = [
                    "activeFrom" => $current_period_start,
                    "invoicingStart" => $current_period_start,
                    "invoicingPeriodStartDay" => (int)$invoicingPeriodStartDay,
                    "sendEmailsAutomatically" => false,
                    "useCreditAutomatically" => true,
                    "servicePlanId" => (int)$subscription->plan->metadata->ucrm_service_plan_id,
                    "servicePlanPeriodId" => (int)$subscription->plan->metadata->ucrm_service_period_id
                  ];

                  $service = $ucrm_handler->createService($service, $client->id);
                  $message .= ' Service ID: '.$service->id;
                  $message .= ' Subscription ID: '.$subscription->id;
                  log_event('Syncronizing', $message);
                } else {
                  $message .= ' Subscription ID: '.$subscription->id;
                  log_event('Delay Sync, Trial has not ended', $message);
                }
              } // # End foreach ($subscriptions
            } else {
              log_event('No Existing Subscriptions', $message);
            }

          } else {
            log_event('Services already exist', $message);
          } // # End if (count($client_services)


        } // # End foreach ($all_clients


    } // # if ($all_clients !== false) 
    
  } // # End convertProductsToServicePlans

}