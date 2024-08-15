<?php

namespace WordPressPlugin\REST;

use WordPressPlugin\Models\ModelInterface;
use WordPressPlugin\Repositories\OperatorEnum;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

abstract class AbstractRestController extends WP_REST_Controller {
	/**
	 * @var array
	 */
	protected array $settings = array();

	/**
	 * AbstractRestController constructor.
	 */
	public function __construct() {
		$this->settings = (array) get_option( 'wpp_settings_general' );
	}

	/**
	 * Prepares the model before being sent to the REST API consumer.
	 *
	 * @param ModelInterface $model Database model object.
	 *
	 * @return array
	 */
	protected function prepare( ModelInterface $model ): array {
		return $model->to_array();
	}

	/**
	 * Returns a structured response object for the API.
	 *
	 * @param bool   $success Indicates whether the request was successful.
	 * @param array  $data    Contains the response data.
	 * @param string $route   Contains the request route name.
	 * @param int    $code    Contains the response HTTP status code.
	 *
	 * @return WP_REST_Response
	 */
	protected function response( bool $success, array $data, string $route, int $code = 200 ): WP_REST_Response {
		// phpcs:ignore
		$request_method = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) );

		return new WP_REST_Response(
			array(
				'success' => $success,
				'data'    => apply_filters( 'wpp_rest_api_pre_response', $data, $request_method, $route ),
			),
			$code
		);
	}

	/**
	 * Adds a "BETWEEN X AND Y" to the query.
	 *
	 * @param string $value The two values separated by a comma.
	 * @param string $name  The key under which the BETWEEN clause will be
	 *                      added.
	 * @param array  $where The where clause.
	 *
	 * @return void
	 */
	protected function parse_between( string $value, string $name, array &$where ): void {
		$values = explode( ',', $value );

		if ( is_array( $values ) && count( $values ) === 2 ) {
			$where[ $name ] = array(
				OperatorEnum::BETWEEN => array( $values[0], $values[1] ),
			);
		}
	}

	/**
	 * Parsers the "order_by" parameter from the URL and turns into an
	 * associative array which the repositories can use for sorting.
	 *
	 * @param string|null $order_by The URL parameter "order_by".
	 *
	 * @return array
	 */
	protected function parse_order_by( ?string $order_by ): array {
		if ( ! $order_by ) {
			return array( 'id' => 'DESC' );
		}

		return array_reduce(
			explode( ',', $order_by ),
			function ( $result, $column ) {
				$value = wpp_clean_string( $column );

				if ( str_starts_with( $value, '-' ) ) {
					$key = substr( $value, 1 );

					$result[ $key ] = 'DESC';
				} else {
					$result[ $value ] = 'ASC';
				}

				return $result;
			},
			array(),
		);
	}

	/**
	 * Checks if the given string is a JSON object.
	 *
	 * @param string $value Possible JSON string.
	 *
	 * @return bool
	 */
	protected function is_json( string $value ): bool {
		json_decode( $value );

		return ( json_last_error() === JSON_ERROR_NONE );
	}

	/**
	 * Checks whether a specific API route is enabled.
	 *
	 * @param array  $settings Plugin settings array.
	 * @param string $route_id Unique plugin API endpoint ID.
	 *
	 * @return bool
	 */
	protected function is_route_enabled( array $settings, string $route_id ): bool {
		if ( ! array_key_exists( 'wpp_enabled_api_routes', $settings )
			|| ! array_key_exists( $route_id, $settings['wpp_enabled_api_routes'] )
			|| ! $settings['wpp_enabled_api_routes'][ $route_id ]
		) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the default error for disabled routes.
	 *
	 * @return WP_Error
	 */
	protected function route_disabled_error(): WP_Error {
		return new WP_Error(
			'wpp_rest_route_disabled_error',
			'This route is disabled via the plugin settings.',
			array( 'status' => 404 )
		);
	}

	/**
	 * Callback method for the "permission_callback" argument of the
	 * "register_rest_route" method.
	 *
	 * @param WP_REST_Request $request The WordPress REST Request object.
	 *
	 * @return bool|WP_Error
	 */
	public function permission_callback( WP_REST_Request $request ) {
		$error = apply_filters( 'wpp_rest_permission_callback', $request );

		if ( $error instanceof WP_Error ) {
			return $error;
		}

		return true;
	}

	/**
	 * Checks if the current user has permission to perform the request.
	 *
	 * @param string $cap Capability slug.
	 *
	 * @return bool
	 */
	protected function current_user_can( string $cap ): bool {
		$has_permission = current_user_can( $cap );

		return apply_filters( 'wpp_rest_capability_check', $has_permission, $cap );
	}

	/**
	 * Returns a contextual HTTP error code for authorization failure.
	 *
	 * @return int
	 */
	protected function authorization_required_code(): int {
		return is_user_logged_in() ? 403 : 401;
	}
}
