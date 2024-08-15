<?php

namespace WordPressPlugin\Repositories;

use ReflectionClass;

defined( 'ABSPATH' ) || exit;

abstract class OperatorEnum {
	const AND      = 'AND';
	const OR       = 'OR';
	const IN       = 'IN';
	const NOT_IN   = 'NOT IN';
	const NOT_LIKE = 'NOT LIKE';
	const LIKE     = 'LIKE';
	const BETWEEN  = 'BETWEEN';

	/**
	 * Returns class constants.
	 *
	 * @return array
	 */
	public static function get_keys(): array {
		$reflection_class = new ReflectionClass( __CLASS__ );

		return array_keys( $reflection_class->getConstants() );
	}

	/**
	 * Returns class constants.
	 *
	 * @return array
	 */
	public static function get_values(): array {
		$reflection_class = new ReflectionClass( __CLASS__ );

		return array_values( $reflection_class->getConstants() );
	}
}
