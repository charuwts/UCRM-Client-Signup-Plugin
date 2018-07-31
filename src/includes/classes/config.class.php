<?php
namespace UCSP;

class Config {
  public static $PAYMENT_GATEWAY = null;
  public static $PLUGIN_SUBSCRIPTION_ID = null;
  public static $PLUGIN_UNIQUE_KEY = null;
  public static $PLUGIN_DOMAIN = null;
  public static $PLUGIN_PUBLIC_URL = null;
  public static $CUSTOM_ATTRIBUTE_ID = null;
  public static $STRIPE_SECRET_KEY = null;
  public static $STRIPE_PUBLIC_KEY = null;
  public static $LOGO_URL = null;
  public static $FORM_TITLE = null;
  public static $FORM_DESCRIPTION = null;
  public static $COMPLETION_TEXT = null;
  public static $COUNTRY_SELECT = null;
  
  private static function parseLink($link_string) {
    $link_array = explode('|', $link_string, 2);
    return $link_array;
  }

  public static function PLUGIN_URL() {
    $root_url = str_replace('/_plugins/ucrm-client-signup-plugin/public.php', '', self::$PLUGIN_PUBLIC_URL);
    return $root_url;
  }

  public static function initializeStaticProperties($config_path) {
    
    // ## Setup user configuration settings, if they exist
    if (file_exists($config_path)) {
      // ## Get file and decode
      $config_string = file_get_contents($config_path);
      $config_json = json_decode($config_string);

      
      foreach ($config_json as $key => $value) {

        // ## Expect specific key naming convention
        $name = false;
        $count = false;
        if (strpos($key, 'REQUIRED_') !== false) { 
        
          $name = str_replace('REQUIRED_', '', $key);
          $new_value = $value;

        } elseif (strpos($key, 'HYPER_LINK_') !== false) { 

          $name = str_replace('HYPER_LINK_', '', $key); 
          $new_value = self::parseLink($value);
        
        } elseif (strpos($key, 'OPTIONAL_') !== false) { 
        
          $name = str_replace('OPTIONAL_', '', $key); 
          $new_value = $value;
        
        }

        // ## Do not define if no name is set
        if ($name !== false) {
          // ## Set to null if value is empty
          if (!empty($new_value)) {
            self::$$name = $new_value;
          } else {
            self::$$name = null;
          }
        }


      }

    }
  }

}

