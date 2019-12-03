<?php
namespace EPEX;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Export manager class
 */
class Export {

	private $data = array();
	private $options_to_export = array();

	/**
	 * Export data
	 * @param array $data [description]
	 */
	public function __construct( $data = array() ) {

		if ( ! empty( $data['options_to_export'] ) ) {
			$this->options_to_export = explode( ',', $data['options_to_export'] );
			unset( $data['options_to_export'] );
		}

		$this->data = $data;
	}

	/**
	 * Send file
	 *
	 * @return [type] [description]
	 */
	public function send_file() {

		$data = $this->get_data();
		$data = json_encode( $data );

		set_time_limit( 0 );

		@session_write_close();

		if( function_exists( 'apache_setenv' ) ) {
			@apache_setenv('no-gzip', 1);
		}

		@ini_set( 'zlib.output_compression', 'Off' );

		nocache_headers();

		header( "Robots: none" );
		header( "Content-Type: application/json" );
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename=\"export-data.json\";" );
		header( "Content-Transfer-Encoding: binary" );

		// Set the file size header
		header( "Content-Length: " . mb_strlen( $data, '8bit' ) );

		echo $data;

		die();

	}

	public function get_data() {

		$result = array();

		foreach ( $this->data as $group => $posts_string ) {

			$string_parts = explode( '|', $posts_string );
			$posts        = $string_parts[0];
			$posts        = $this->get_posts_from_string( $posts );

			$result[ $group ] = array();

			if ( ! empty( $string_parts[1] ) && 'undefined' !== $string_parts[1] ) {
				$meta = explode( ',', $string_parts[1] );
			} else {
				$meta = array();
			}

			foreach ( $posts as $post_id ) {
				$result[ $group ][] = $this->get_post_for_export( $post_id, $meta );
			}

		}

		if ( ! empty( $this->options_to_export ) ) {
			$result['options_to_export'] = array();
			foreach ( $this->options_to_export as $option ) {
				$result['options_to_export'][ $option ] = get_option( $option );
			}
		}

		return $result;

	}

	public function get_posts_from_string( $posts ) {

		$posts = explode( ',', $posts );
		$posts = array_filter( array_map( 'absint', $posts ) );

		if ( empty( $posts ) ) {
			$posts = array();
		}

		return $posts;

	}

	public function get_post_for_export( $post_id, $export_meta = array() ) {

		if ( empty( $export_meta ) ) {
			$export_meta = array(
				'_elementor_edit_mode',
				'_elementor_template_type',
				'_listing_data',
				'_elementor_page_settings',
				'_elementor_version',
				'_elementor_data',
				'_elementor_controls_usage',
			);
		}

		$export_props = array(
			'ID',
			'post_title',
			'post_name',
			'post_type',
		);

		$post = get_post( $post_id );

		if ( ! $post || is_wp_error( $post ) ) {
			return false;
		}

		$post_arr = $post->to_array();
		$result   = array();

		foreach ( $export_props as $prop ) {
			$result[ $prop ] = isset( $post_arr[ $prop ] ) ? $post_arr[ $prop ] : false;
		}

		$result = array_filter( $result );

		$result['meta_input'] = array();

		foreach ( $export_meta as $meta_key ) {
			$meta = get_post_meta( $post_id, $meta_key, true );

			if ( $meta ) {
				$result['meta_input'][ $meta_key ] = wp_unslash( $meta );
			}

		}

		return $result;

	}

}
