<?php

namespace WordPressPluginBoilerplate;

defined( 'ABSPATH' ) || exit;

interface SingletonInterface {
	/**
	 * Get an instance of the class.
	 *
	 * @param mixed|null $data Optional data to pass to the class constructor.
	 *
	 * @return self Returns an instance of the class.
	 */
	public static function instance( mixed $data = null ): self;
}
