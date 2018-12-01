<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */

class UsageHandler {

  /**
   * # Handle Guzzle Exception and exit
   *
   * @param array $e
   * @param boolean $log
   *
   * @return exit();
   */
  protected static function handleGuzzleException($e, $log = false, $endpoint='') {
    // # Get json response
    $body = $e->getResponse()->getBody();

    // # Get get code from response
    $json_decoded = json_decode($body);
    $code = $json_decoded->code;
    // # Send response and exit
    if ($log) {
      log_event('Exception', "{$body}: {$code} - Endpoint: {$endpoint}", 'error');
    }
    echo json_response($body, $code, true);
    exit();
  }

  /**
   * # Setup Guzzle for UCRM
   *
   * @param string $method // "GET", "POST", "PATCH"
   * @param string $endpoint
   * @param array  $content
   *
   * @return array
   */
  public static function guzzle(
    $method, 
    $endpoint,
    array $content = []
  ) {    
    // log_event('method', $method, 'test');
    // log_event('endpoint', $endpoint, 'test');
    // log_event('content', print_r($content, true), 'test');
    try {
      $client = new \GuzzleHttp\Client([
        'headers' => ['X-Auth-App-Key' => UcrmApi::$ucrm_key],
      ]);

      $url = UcrmApi::$ucrm_api_url.$endpoint;

      $res = $client->request($method, $url, ['json' => $content]);
      $code = $res->getStatusCode();
      $body = (string)$res->getBody();
      // log_event('body', print_r($body, true), 'test');

      return ["status" => $code, "message" => $body];
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      self::handleGuzzleException($e);
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      self::handleGuzzleException($e, true, $endpoint);
    }
  }
  # Taken from https://github.com/Ubiquiti-App/UCRM-plugins/blob/master/docs/security.md

  public static function retrieveCurrentUser(string $ucrmPublicUrl): array
  {
      $url = sprintf('%scurrent-user', $ucrmPublicUrl);

      $headers = [
          'Content-Type: application/json',
          'Cookie: PHPSESSID=' . preg_replace('~[^a-zA-Z0-9]~', '', $_COOKIE['PHPSESSID'] ?? ''),
      ];

      return self::curlQuery($url, $headers);
  }

  protected static function curlQuery(string $url, array $headers = [], array $parameters = []): array
  {
      if ($parameters) {
          $url .= '?' . http_build_query($parameters);
      }

      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, $url);
      curl_setopt($c, CURLOPT_HTTPHEADER, $headers);

      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
      curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);

      $result = curl_exec($c);

      $error = curl_error($c);
      $errno = curl_errno($c);

      if ($errno || $error) {
          throw new \Exception(sprintf('Error for request %s. Curl error %s: %s', $url, $errno, $error));
      }

      $httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);

      if ($httpCode < 200 || $httpCode >= 300) {
          throw new \Exception(
              sprintf('Error for request %s. HTTP error (%s): %s', $url, $httpCode, $result),
              $httpCode
          );
      }

      curl_close($c);

      if (! $result) {
          throw new \Exception(sprintf('Error for request %s. Empty result.', $url));
      }

      $decodedResult = json_decode($result, true);

      if ($decodedResult === null) {
          throw new \Exception(
              sprintf('Error for request %s. Failed JSON decoding. Response: %s', $url, $result)
          );
      }

      return $decodedResult;
  }
}


