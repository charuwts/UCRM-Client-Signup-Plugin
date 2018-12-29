<?php
class IsNotAdminException extends \Exception {
  public function __construct($message = '', $code = 0, Exception $previous = null) {
      parent::__construct($message, $code, $previous);
  }
}
class ApiException extends \Exception {
}
class ConfigException extends \Exception {
}