<?php 
class StripeApi {
  private $response;
  private static $endpoint_secret = null;
  private static $is_valid = false;
  public static function setEndpointSecret($secret='') {
    self::$endpoint_secret = $secret;
  }
  /**
   * # Get UCRM response
   *
   * @param string $this->response
   *
   */
  public function getResponse() {
    return $this->response;
  }
    
  /**
   * # Set UCRM response
   *
   * @param string $message
   *
   */
  protected function setResponse($message) {
    $this->response = $message;
  }


}

echo '
<ippay>
  <TransactionType>CHECK</TransactionType>
  <TerminalID>TESTTERMINAL</TerminalID>
  <CardName>Mickey Mouse</CardName>
  <TotalAmount>1399</TotalAmount>
  <FeeAmount>100</FeeAmount>
  <ACH Type=\'SAVINGS\' SEC=\'PPD\'>
    <AccountNumber>11111999</AccountNumber>
    <ABA>071025661</ABA>
    <CheckNumber>15</CheckNumber>
  </ACH>
</ippay>
';