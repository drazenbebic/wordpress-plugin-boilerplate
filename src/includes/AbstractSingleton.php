<?php

namespace WordPressPluginBoilerplate;

defined( 'ABSPATH' ) || exit;

abstract class AbstractSingleton implements SingletonInterface {
	/**
	 * Holds all instances of called class.
	 *
	 * @var self []
	 */
	protected static array $instance = array();

	/**
	 * Get an instance of the class.
	 *
	 * @param mixed|null $data Optional data to pass to the class constructor.
	 *
	 * @return self Returns an instance of the class.
	 */
	public static function instance( mixed $data = null ): self {
		$class = get_called_class();

		if ( ! array_key_exists( $class, self::$instance ) ) {
			self::$instance[ $class ] = new $class( $data );
		}

		return self::$instance[ $class ];
	}
}
