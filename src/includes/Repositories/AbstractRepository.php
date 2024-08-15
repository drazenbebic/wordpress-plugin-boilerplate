<?php
// phpcs:disable Generic.PHP.Syntax.PHPSyntax
// phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
// phpcs:disable Squiz.Commenting.FunctionComment.InvalidTypeHint
// phpcs:disable WordPress.DB

namespace WordPressPlugin\Repositories;

use WordPressPlugin\AbstractSingleton;
use WordPressPlugin\Models\ModelInterface;

defined( 'ABSPATH' ) || exit;

abstract class AbstractRepository extends AbstractSingleton implements RepositoryInterface {
	/**
	 * @var string
	 */
	protected string $table;

	/**
	 * @var string
	 */
	protected string $model;

	/**
	 * @var array
	 */
	protected array $mapping;

	/**
	 * Adds a single new entry to the table.
	 *
	 * @param array $data Key/value pairs of column names and their values.
	 *
	 * @return null|ModelInterface
	 */
	public function insert_one( array $data ): ?ModelInterface {
		global $wpdb;

		// Pass the data by reference and sanitize its contents.
		$this->sanitize( $data );

		$insert = $wpdb->insert( $this->table, array_merge( $data, $this->get_meta_created() ) );

		if ( ! $insert ) {
			return null;
		}

		return $this->find_one( $wpdb->insert_id );
	}

	/**
	 * Retrieves a single table row by its ID.
	 *
	 * @param null|int $id Table row ID.
	 *
	 * @return null|ModelInterface
	 */
	public function find_one( ?int $id ): ?ModelInterface {
		if ( ! class_exists( $this->model ) || ! $id ) {
			return null;
		}

		global $wpdb;

		$result = wp_cache_get( "{$this->table}_$id", $this->table );

		if ( $result === false ) {
			$result = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM $this->table WHERE id = %d;",
					$id
				)
			);

			$result = wp_cache_set( "{$this->table}_$id", $result );
		}

		if ( ! $result ) {
			return null;
		}

		return new $this->model( $result );
	}

	/**
	 * Retrieves a single table row by the query parameter.
	 *
	 * @param array $where Key/value pairs of column names and their value to filter by.
	 *
	 * @return null|ModelInterface
	 */
	public function find_one_by( array $where ): ?ModelInterface {
		if ( ! class_exists( $this->model ) || ! $where || count( $where ) <= 0 ) {
			return null;
		}

		global $wpdb;

		$sql_query  = "SELECT * FROM $this->table WHERE 1=1 ";
		$sql_query .= $this->parse_where_clause( $where );
		$sql_query .= ';';

		$result = $wpdb->get_row( $sql_query );

		if ( ! $result ) {
			return null;
		}

		return new $this->model( $result );
	}

	/**
	 * Retrieves all table rows as an array.
	 *
	 * @return ModelInterface[]
	 */
	public function find_all(): array {
		global $wpdb;

		$value  = array();
		$result = $wpdb->get_results( "SELECT * FROM $this->table;" );

		if ( ! $result ) {
			return array();
		}

		foreach ( $result as $row ) {
			$value[] = new $this->model( $row );
		}

		return $value;
	}

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
	public function find_many(
		array $where = array(),
		array $order_by = array(),
		int $limit = 10,
		int $offset = 0
	): array {
		if ( ! class_exists( $this->model ) ) {
			return array();
		}

		global $wpdb;

		$result     = array();
		$sql_query  = "SELECT * FROM $this->table WHERE 1=1 ";
		$sql_query .= $this->parse_where_clause( $where );
		$sql_query .= $this->parse_order_by_clause( $order_by );

		if ( $limit !== -1 ) {
			$sql_query .= $wpdb->prepare( ' LIMIT %d', $limit );
			$sql_query .= $wpdb->prepare( ' OFFSET %d', $offset );
		}

		$sql_query .= ';';

		foreach ( $wpdb->get_results( $sql_query ) as $row ) {
			$result[] = new $this->model( $row );
		}

		return $result;
	}

	/**
	 * Updates a single table row by its ID.
	 *
	 * @param int   $id   Table row ID.
	 * @param array $data Key/value pairs of column names and their values.
	 *
	 * @return null|ModelInterface
	 */
	public function update_one( int $id, array $data ): ?ModelInterface {
		global $wpdb;

		// Pass the data by reference and sanitize its contents.
		$this->sanitize( $data );

		$updated = $wpdb->update(
			$this->table,
			array_merge( $data, $this->get_meta_updated() ),
			array( 'id' => $id )
		);

		if ( ! $updated ) {
			return null;
		}

		return $this->find_one( $id );
	}

	/**
	 * Updates one or multiple table rows by the query.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 * @param array $data  Key/value pairs of column names and their values.
	 *
	 * @return null|int
	 */
	public function update_many( array $where, array $data ): ?int {
		if ( empty( $where ) || empty( $data ) ) {
			return null;
		}

		global $wpdb;

		$sql_query  = "UPDATE $this->table SET ";
		$sql_query .= $wpdb->prepare( ' updated_at = %s,', gmdate( WPP_DB_DATE_FORMAT ) );
		$sql_query .= $wpdb->prepare( ' updated_by = %d,', get_current_user_id() );

		foreach ( $data as $column => $value ) {
			if ( is_numeric( $value ) ) {
				$sql_query .= " $column = $value,";
			} elseif ( is_string( $value ) ) {
				$sql_query .= " $column = '$value',";
			} elseif ( $value === null ) {
				$sql_query .= " $column = NULL,";
			}
		}

		$sql_query  = rtrim( $sql_query, ',' );
		$sql_query .= ' WHERE 1=1 ';
		$sql_query .= $this->parse_where_clause( $where );
		$sql_query .= ';';

		return $wpdb->query( $sql_query );
	}

	/**
	 * Removes multiple table rows by their IDs.
	 *
	 * @param array $ids Array of table row IDs.
	 *
	 * @return null|int
	 */
	public function delete( array $ids ): ?int {
		global $wpdb;

		$ids       = implode( ', ', array_map( 'intval', $ids ) );
		$sql_query = "DELETE FROM $this->table WHERE id IN ($ids);";

		return $wpdb->query( $sql_query );
	}

	/**
	 * Deletes one or more table rows by the query parameter.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 *
	 * @return null|int
	 */
	public function delete_by( array $where ): ?int {
		if ( empty( $where ) ) {
			return null;
		}

		global $wpdb;

		$sql_query  = "DELETE FROM $this->table WHERE 1=1 ";
		$sql_query .= $this->parse_where_clause( $where );
		$sql_query .= ';';

		return $wpdb->query( $sql_query );
	}

	/**
	 * Retrieves a count of table rows, optionally filtered by the $where
	 * parameter.
	 *
	 * @param array $where Key/value pairs of column names and their value to
	 *                     filter by.
	 *
	 * @return int
	 */
	public function count( array $where = array() ): int {
		global $wpdb;

		$sql_query  = "SELECT COUNT(*) FROM $this->table WHERE 1=1 ";
		$sql_query .= $this->parse_where_clause( $where );
		$sql_query .= ';';

		return (int) $wpdb->get_var( $sql_query );
	}

	/**
	 * Performs a general query on the table.
	 *
	 * @param string $sql    Raw SQL query.
	 * @param string $output Second parameter of $wpdb->get_results().
	 *
	 * @return array|object|null
	 */
	public function query( string $sql, string $output = OBJECT ): object|array|null {
		global $wpdb;

		return $wpdb->get_results( $sql, $output );
	}

	/**
	 * Truncates the table.
	 *
	 * @return null|int
	 */
	public function truncate(): ?int {
		global $wpdb;

		return $wpdb->query( "TRUNCATE TABLE $this->table;" );
	}

	/**
	 * Returns the table name.
	 *
	 * @return string
	 */
	public function get_table(): string {
		return $this->table;
	}

	/**
	 * Returns the path to the model class.
	 *
	 * @return string
	 */
	public function get_model(): string {
		return $this->model;
	}

	/**
	 * Returns the field mapping.
	 *
	 * @return array
	 */
	public function get_mapping(): array {
		return $this->mapping;
	}

	/**
	 * Sanitizes the input data when adding or updating entities.
	 *
	 * @param array $data Key/value pairs of column names and their values.
	 *
	 * @return void
	 */
	private function sanitize( array &$data ): void {
		foreach ( $data as $column => $value ) {
			switch ( $this->mapping[ $column ] ) {
				case ColumnTypeEnum::CHAR:
				case ColumnTypeEnum::VARCHAR:
				case ColumnTypeEnum::LONGTEXT:
				case ColumnTypeEnum::DATETIME:
					if ( $value !== null ) {
						$data[ $column ] = sanitize_text_field( $value );
					}
					break;
				case ColumnTypeEnum::INT:
				case ColumnTypeEnum::TINYINT:
				case ColumnTypeEnum::BIGINT:
					if ( $value !== null ) {
						$data[ $column ] = (int) $value;
					}
					break;
			}
		}
	}

	/**
	 * Returns the "created_at" and "created_by" fields as an associative
	 * array.
	 *
	 * @return array
	 */
	private function get_meta_created(): array {
		$meta = array(
			'created_at' => gmdate( WPP_DB_DATE_FORMAT ),
			'created_by' => get_current_user_id(),
		);

		return apply_filters( 'wpp_db_meta_created', $meta );
	}

	/**
	 * Returns the "updated_at" and "updated_by" fields as an associative
	 * array.
	 *
	 * @return array
	 */
	private function get_meta_updated(): array {
		$meta = array(
			'updated_at' => gmdate( WPP_DB_DATE_FORMAT ),
			'updated_by' => get_current_user_id(),
		);

		return apply_filters( 'wpp_db_meta_created', $meta );
	}

	/**
	 * @param array $where Key/value pairs of column names and their values.
	 *
	 * @return string
	 */
	private function parse_where_clause( array $where ): string {
		global $wpdb;

		$result = '';

		foreach ( $where as $column => $value ) {
			if ( is_string( $value ) ) {
				$result .= $wpdb->prepare( "AND $column = %s ", $value );
			} elseif ( is_numeric( $value ) ) {
				$result .= $wpdb->prepare( "AND $column = %d ", $value );
			} elseif ( $value === null ) {
				$result .= "AND $column IS NULL ";
			} elseif ( is_array( $value ) && $this->has_string_keys( $value ) ) {
				$filtered = array_filter( $value, array( $this, 'is_logical_operator' ), ARRAY_FILTER_USE_BOTH );

				foreach ( $filtered as $operator => $values ) {
					switch ( $operator ) {
						case OperatorEnum::NOT_IN:
						case OperatorEnum::IN:
							$in_nin  = implode( ', ', array_map( 'intval', $values ) );
							$result .= "AND $column $operator ( $in_nin ) ";
							break;
						case OperatorEnum::LIKE:
						case OperatorEnum::NOT_LIKE:
							if ( $values === null ) {
								$result .= "AND $column $operator NULL ";
							} else {
								$result .= $wpdb->prepare( "AND $column $operator %s ", $values );
							}
							break;
						case OperatorEnum::BETWEEN:
							if ( is_string( $values[0] ) && is_string( $values[1] ) ) {
								$value0  = wpp_clean_string( $values[0] );
								$value1  = wpp_clean_string( $values[1] );
								$result .= $wpdb->prepare( "AND $column BETWEEN %s AND %s ", $value0, $value1 );
							} elseif ( is_numeric( $values[0] ) && is_numeric( $values[1] ) ) {
								$value0  = (int) $values[0];
								$value1  = (int) $values[1];
								$result .= $wpdb->prepare( "AND $column BETWEEN %d AND %d ", $value0, $value1 );
							}
							break;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * @param array $order_by Key/value pairs of columns names and either ASC
	 *                        or DESC for the value.
	 *
	 * @return string
	 */
	private function parse_order_by_clause( array $order_by ): string {
		if ( empty( $order_by ) ) {
			return '';
		}

		$filtered_order_by = array_filter(
			$order_by,
			function ( string $value, string $key ) {
				$is_valid_value = in_array( strtoupper( $value ), array( 'ASC', 'DESC' ), true );
				$is_valid_key   = in_array( $key, array_keys( $this->mapping ), true );

				return $is_valid_value && $is_valid_key;
			},
			ARRAY_FILTER_USE_BOTH
		);

		if ( empty( $filtered_order_by ) ) {
			return '';
		}

		$i      = 0;
		$result = 'ORDER BY ';

		foreach ( $filtered_order_by as $column => $value ) {
			$value = strtoupper( $value );

			if ( $i > 0 ) {
				$result .= ', ';
			}

			$result .= "$column $value";
			++$i;
		}

		return $result;
	}

	/**
	 * Checks whether an array has string keys.
	 *
	 * @param array $value The array to check.
	 *
	 * @return bool
	 */
	private function has_string_keys( array $value ): bool {
		return count( array_filter( array_keys( $value ), 'is_string' ) ) > 0;
	}

	/**
	 * Determines if the given string is a valid MySQL logical operator.
	 * @see https://www.scommerce-mage.com/blog/magento2-condition-type-search-filter.html
	 *
	 * @param mixed  $value    Value of the query parameter.
	 * @param string $operator Key of the query parameter.
	 *
	 * @return bool
	 */
	private function is_logical_operator( mixed $value, string $operator ): bool {
		return in_array( strtoupper( $operator ), OperatorEnum::get_values(), true );
	}
}
