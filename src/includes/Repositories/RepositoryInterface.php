<?php
// phpcs:disable Generic.PHP.Syntax.PHPSyntax

namespace WordPressPluginBoilerplate\Repositories;

use WordPressPluginBoilerplate\Models\ModelInterface;

defined( 'ABSPATH' ) || exit;

interface RepositoryInterface {
	/**
	 * Adds a single new entry to the table.
	 *
	 * @param array $data Key/value pairs of column names and their values.
	 *
	 * @return null|ModelInterface
	 */
	public function insert_one( array $data ): mixed;

	/**
	 * Retrieves a single table row by its ID.
	 *
	 * @param null|int $id Table row ID.
	 *
	 * @return null|ModelInterface
	 */
	public function find_one( ?int $id ): ?ModelInterface;

	/**
	 * Retrieves a single table row by the query parameter.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 *
	 * @return null|ModelInterface
	 */
	public function find_one_by( array $where ): ?ModelInterface;

	/**
	 * Retrieves all table rows as an array.
	 *
	 * @return ModelInterface[]
	 */
	public function find_all(): array;

	/**
	 * Retrieves multiple table rows as an array, filtered by the query.
	 *
	 * @param array $where    Key/value pairs of column names and their value
	 *                        to filter by.
	 * @param array $order_by Key/value pairs of column names and their value
	 *                        to sort by.
	 * @param int   $limit    How many rows should be fetched.
	 * @param int   $offset   Used for pagination, offsets the results by the
	 *                        given number.
	 *
	 * @return ModelInterface[]
	 */
	public function find_many( array $where = array(), array $order_by = array(), int $limit = 10, int $offset = 0 ): array;

	/**
	 * Updates a single table row by its ID.
	 *
	 * @param int   $id   Table row ID.
	 * @param array $data Key/value pairs of column names and their values.
	 *
	 * @return null|ModelInterface
	 */
	public function update_one( int $id, array $data ): ?ModelInterface;

	/**
	 * Updates one or multiple table rows by the query.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 * @param array $data  Key/value pairs of column names and their values.
	 *
	 * @return null|int
	 */
	public function update_many( array $where, array $data ): ?int;

	/**
	 * Removes multiple table rows by their IDs.
	 *
	 * @param array $ids Array of table row IDs.
	 *
	 * @return null|int
	 */
	public function delete( array $ids ): ?int;

	/**
	 * Deletes one or more table rows by the query parameter.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 *
	 * @return null|int
	 */
	public function delete_by( array $where ): ?int;

	/**
	 * Retrieves a count of table rows, optionally filtered by the $where
	 * parameter.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 *
	 * @return int
	 */
	public function count( array $where = array() ): int;

	/**
	 * Performs a general query on the table.
	 *
	 * @param string $sql    Raw SQL query.
	 * @param string $output Second parameter of $wpdb->get_results().
	 *
	 * @return array|object|null
	 */
	public function query( string $sql, string $output ): object|array|null;

	/**
	 * Truncates the table.
	 *
	 * @return null|int
	 */
	public function truncate(): ?int;

	/**
	 * Returns the table name.
	 *
	 * @return string
	 */
	public function get_table(): string;

	/**
	 * Returns the path to the model class.
	 *
	 * @return string
	 */
	public function get_model(): string;

	/**
	 * Returns the field mapping.
	 *
	 * @return array
	 */
	public function get_mapping(): array;
}
