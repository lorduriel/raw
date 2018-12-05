<?php

namespace LoRDFM\Raw;
use Exception;

trait RawValidation{
	
	public static function storeValidations(){
		if (!isset(self::$storeValidations)){
            throw new Exception('No property $storeValidations in '.__CLASS__.'.'.PHP_EOL.__CLASS__.' should define a static $storeValidations array of rules to be used by Laravel "Validator" ');
    }
		return self::$storeValidations;
  }

  public static function updateValidations(){
		if (!isset(self::$updateValidations)){
            throw new Exception('No property $updateValidations in '.__CLASS__.'.'.PHP_EOL.__CLASS__.'  should define a static $updateValidations array of rules to be used by Laravel "Validator" ');
    }
		return self::$updateValidations;
  }
}