<?php

namespace WordPressPlugin\Models;

use stdClass;

defined( 'ABSPATH' ) || exit;

abstract class AbstractModel implements ModelInterface {
	/**
	 * @var int
	 */
	protected int $id;

	/**
	 * @var string
	 */
	protected string $created_at;

	/**
	 * @var int
	 */
	protected int $created_by;

	/**
	 * @var null|string
	 */
	protected ?string $updated_at;

	/**
	 * @var null|int
	 */
	protected ?int $updated_by;

	/**
	 * AbstractModel constructor.
	 *
	 * @param null|stdClass $row Queried plugin table row.
	 */
	public function __construct( ?stdClass $row = null ) {
		if ( ! $row ) {
			return;
		}

		$this->id         = (int) $row->id;
		$this->created_at = (string) $row->created_at;
		$this->created_by = (int) $row->created_by;
		$this->updated_at = $row->updated_at === null ? null : (string) $row->updated_at;
		$this->updated_by = $row->updated_by === null ? null : (int) $row->updated_by;
	}

	/**
	 * Returns the class properties as an array.
	 *
	 * @return array
	 */
	public function to_array(): array {
		return get_object_vars( $this );
	}

	/**
	 * Properly serializes the instanced object when `json_serialize` is called.
	 *
	 * @return array
	 */
	public function jsonSerialize(): array {
		$data = $this->to_array();

		if ( isset( $data['created_by'] ) ) {
			$user = get_userdata( $data['created_by'] );

			if ( $user ) {
				$user->user_pass    = null;
				$data['created_by'] = $user->data;
			} else {
				$data['created_by'] = null;
			}
		}

		if ( isset( $data['updated_by'] ) ) {
			$user = get_userdata( $data['updated_by'] );

			if ( $user ) {
				$user->user_pass    = null;
				$data['updated_by'] = $user->data;
			} else {
				$data['updated_by'] = null;
			}
		}

		return $data;
	}

	/**
	 * @return int
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * @param int $id License ID.
	 */
	public function set_id( int $id ): void {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function get_created_at(): string {
		return $this->created_at;
	}

	/**
	 * @param string $created_at Timestamp.
	 */
	public function set_created_at( string $created_at ): void {
		$this->created_at = $created_at;
	}

	/**
	 * @return int
	 */
	public function get_created_by(): int {
		return $this->created_by;
	}

	/**
	 * @param int $created_by WordPress User ID.
	 */
	public function set_created_by( int $created_by ): void {
		$this->created_by = $created_by;
	}

	/**
	 * @return string|null
	 */
	public function get_updated_at(): ?string {
		return $this->updated_at;
	}

	/**
	 * @param string|null $updated_at Timestamp.
	 */
	public function set_updated_at( ?string $updated_at ): void {
		$this->updated_at = $updated_at;
	}

	/**
	 * @return int|null
	 */
	public function get_updated_by(): ?int {
		return $this->updated_by;
	}

	/**
	 * @param int|null $updated_by WordPress User ID.
	 */
	public function set_updated_by( ?int $updated_by ): void {
		$this->updated_by = $updated_by;
	}
}
