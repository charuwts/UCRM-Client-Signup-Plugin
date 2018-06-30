<?php
chdir(__DIR__);
define("PROJECT_PATH", __DIR__);
require_once(PROJECT_PATH.'/../src/vendor/autoload.php');

// # Lightgig test
\Stripe\Stripe::setApiKey("sk_test_mrhveUUktyn3akYEKWlC1GtU");

function importPlans($product, $plans) {
  // If product, create plans if plan does not exist
  if ($product) {
    foreach ($plans as $plan) {
      try {
        \Stripe\Plan::retrieve($plan['id']);
        echo "Plan Exists: {$plan['id']}";
        echo "<br>";
      } catch (\Stripe\Error\InvalidRequest $e) {

        if( strpos( $e->getMessage(), "No such plan" ) !== false ) {
          $plan = \Stripe\Plan::create(array(
            "id" => $plan["id"],
            "amount" => $plan["amount"],
            "interval" => "month",
            "nickname" => $plan["nickname"],
            "product" => $product->id,
            "currency" => "usd",
          )); 
          echo "Plan Created: {$plan['id']}";
          echo "<br>";
        
        } else {
          echo $e->getMessage();
          echo "<br>";
        }
      }
    }

  }
}

function importProducts($products) {
  foreach ($products as $product) {
    try {
      $existing_product = \Stripe\Product::retrieve($product['id']);
      echo "Product Exists: {$product['id']}";
      echo "<br>";
      importPlans($existing_product, $product['plans']);

    } catch (\Stripe\Error\InvalidRequest $e) {

      if( strpos( $e->getMessage(), "No such product" ) !== false ) {
        $new_product = \Stripe\Product::create(array(
          "id" => $product['id'],
          "name" => $product['name'],
          "type" => $product['type'],
          "statement_descriptor" => $product['statement_descriptor']
        ));

        echo "Product Created: {$product['id']}";
        echo "<br>";
        importPlans($new_product, $product['plans']);
      
      } else {
        echo $e->getMessage();
        echo "<br>";
      }
    }
  }
}

function importCoupons($coupons) {
  foreach ($coupons as $coupon) {
    try {
      $existing_coupon = \Stripe\Coupon::retrieve($coupon['id']);
      echo "Coupon Exists: {$coupon['id']}";
      echo "<br>";
    } catch (\Stripe\Error\InvalidRequest $e) {

      if( strpos( $e->getMessage(), "No such coupon" ) !== false ) {
        
        \Stripe\Coupon::create(
          [
            "id" => $coupon['id'],
            "name" => $coupon['name'],
            "amount_off" => $coupon['amount_off'],
            "currency" => 'usd',
            "duration" => $coupon['duration'],
            "duration_in_months" => $coupon['duration_in_months']
          ]
        );
        echo "Coupon Created: {$coupon['id']}";
        echo "<br>";
      
      } else {
        echo $e->getMessage();
        echo "<br>";
      }
    }
  }
}

$products = [
  [
    "id" => "prod_InternetForSale",
    "name" => "Internet for Sale",
    "type" => "service",
    "statement_descriptor" => "INTERNETFORSALE",
    "plans" => [
      [
        "id" => "7mb-10mb",
        "nickname" => "7mb up to 10mb",
        "trial_period_days" => 30,
        "amount" => 3500,
      ],
      [
        "id" => "12mb-25mb-gigabit",
        "nickname" => "12mb up to 25mb",
        "trial_period_days" => 30,
        "amount" => 4500,
      ],
      [
        "id" => "27mb-50mb",
        "nickname" => "27mb up to 50mb",
        "trial_period_days" => 30,
        "amount" => 5500,
      ],
      [
        "id" => "50mb-75mb",
        "nickname" => "50mb up to 75mb",
        "trial_period_days" => 30,
        "amount" => 9000,
      ],
      [
        "id" => "75mb-100mb",
        "nickname" => "75mb up to 100mb",
        "trial_period_days" => 30,
        "amount" => 12900,
      ],
      [
        "id" => "100mb-1000mb",
        "nickname" => "100mb up to 1000mb",
        "trial_period_days" => 30,
        "amount" => 17900,
      ],
    ]
  ]
];


importProducts($products);
