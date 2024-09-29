<?php

namespace WordPressPluginBoilerplate\Models;

use JsonSerializable;

defined( 'ABSPATH' ) || exit();

interface ModelInterface extends JsonSerializable {
	/**
	 * Returns the class properties as an array.
	 *
	 * @return array
	 */
	public function to_array(): array;

	/**
	 * @return mixed
	 */
	public function jsonSerialize(): mixed;
}
