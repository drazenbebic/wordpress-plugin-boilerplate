<?php

if ( ! function_exists( 'wppb_clean_string' ) ) {
	/**
	 * @param mixed $input The input string.
	 *
	 * @return string
	 */
	function wppb_clean_string( mixed $input ): string {
		return sanitize_text_field( wp_unslash( $input ) );
	}
}
