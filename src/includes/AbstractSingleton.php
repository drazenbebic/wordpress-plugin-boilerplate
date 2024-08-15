<?php

namespace WordPressPlugin;

defined( 'ABSPATH' ) || exit;

abstract class AbstractSingleton {
	/**
	 * @var array
	 */
	protected static array $instance = array();

	/**
	 * @return $this
	 */
	public static function instance(): self {
		$class = get_called_class();

		if ( ! array_key_exists( $class, self::$instance ) ) {
			self::$instance[ $class ] = new $class();
		}

		return self::$instance[ $class ];
	}
}
