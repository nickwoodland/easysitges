<?php

/**
 * Keeps track of all registered gateways and their options
 */
class Colabs_Gateway_Registry{

	/**
	 * Options object containing the Gateway's options
	 * @var scbOptions
	 */
	public static $options;

	/**
	 * Currently registered gateways
	 * @var array
	 */
	public static $gateways;
	
	/**
	 * Registers a gateway by creating a new instance of it
	 * @param  string $class_name Class to create an instance of
	 * @return void
	 */
	public static function register_gateway( $class_name ){

		if( ! is_string( $class_name ) || ! class_exists( $class_name ) )
			trigger_error( 'Expecting existing class name in Colabs_Gateway_Registry::register_gateway', E_USER_WARNING );

		$instance = new $class_name;
		$identifier = $instance->identifier();

		self::$gateways[$identifier] = $instance;
		ksort( self::$gateways );

	}
	
	/**
	 * Returns an instance of a registered gateway
	 * @param  string $gateway_id Identifier of a registered gateway
	 * @return mixed              Instance of the gateway, or false on error
	 */
	public static function get_gateway( $gateway_id ){
		
		if( ! is_string( $gateway_id ) )
			trigger_error( 'Gateway ID must be a string', E_USER_WARNING );

		if ( !self::is_gateway_registered( $gateway_id ) )
			return false;

		return self::$gateways[$gateway_id];

	}
	
	/**
	 * Returns an array of registered gateways
	 * @return array Registered gatewasys
	 */
	public static function get_gateways(){

		return self::$gateways;

	}

	/**
	 * Checks if a given gateway is registered
	 * @param  string  $gateway_id Identifier for registered gateway
	 * @return boolean             True if the gateway is registered, false otherwise
	 */
	public static function is_gateway_registered( $gateway_id ){

		if( ! is_string( $gateway_id ) )
			trigger_error( 'Gateway ID must be a string', E_USER_WARNING );

		return isset( self::$gateways[ $gateway_id ] );

	}
	
	/**
	 * Returns the options for the given registered gateway
	 * @param  string $gateway_id Identifier for registered gateway
	 * @return array              Associative array of options. See Colabs_Gateway::form()
	 */
	public static function get_gateway_options( $gateway_id ){

		if( ! is_string( $gateway_id ) )
			trigger_error( 'Gateway ID must be a string', E_USER_WARNING );

		if( ! self::is_gateway_registered( $gateway_id ) )
			return false;

		$fields = self::get_gateway_fields( $gateway_id );

    foreach( $fields as $field ){
      $options[$field]= get_option($field);
    }
		
		return wp_parse_args( $options);

	}

	private static function get_gateway_fields( $gateway_id ){

		$fields = self::get_gateway( $gateway_id )->form();

		$defaults = array();
		foreach( $fields as $field ){

			$name = $field['id'];
      if($name){
        $defaults[] = $name;
      }
		}

		return $defaults;

	}

	/**
	 * Plucks off field arrays. Flattens array with sections
	 * @param  array $gateway_form Array representing form
	 * @return array               All fields in the array
	 */
	private static function get_fields( $gateway_form ){
		
		if( isset( $gateway_form['fields'] ) ){
			$gateway_form = array( $gateway_form );
		}

		$fields = array();
		foreach( $gateway_form as $section ){
			if( isset( $section['fields'] ) && is_array( $section['fields'] ) ){
				foreach( $section['fields'] as $field ){
					$fields[] = $field;
				}
			}
		}
		return $fields;

	}
  
  /**
	 * Returns an array of active gateways
	 * @return array Active gateways
	 */
	public static function get_active_gateways(){

		$gateways = array();
    
		foreach ( self::$gateways as $gateway ) {

			if ( self::is_gateway_enabled( $gateway->identifier() ) )
				$gateways[ $gateway->identifier() ] = $gateway;
		}

		return $gateways;

	}
  
  /**
	 * Checks if a given gateway is enabled
	 * @param  string  $gateway_id Identifier for registered gateway
	 * @return boolean             True if the gateway is enabled, false otherwise
	 */
	public static function is_gateway_enabled( $gateway_id ){

		if( ! is_string( $gateway_id ) )
			trigger_error( 'Gateway ID must be a string', E_USER_WARNING );
    
    if(get_option('colabs_enable_'.$gateway_id)=='true'){
      return TRUE;
    }else{
      return FALSE;
    }
		
	}

}
?>
