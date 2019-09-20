<?php
namespace EPEX;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Posts_Search manager class
 */
class Posts_Search {

	/**
	 * Perform search query
	 *
	 * @param  string $query     [description]
	 * @param  array  $ids       [description]
	 * @param  string $post_type [description]
	 * @return [type]            [description]
	 */
	public function get( $args = array() ) {

		add_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

		$query = ! empty( $args['query'] ) ? $args['query'] : '';
		$ids   = ! empty( $args['ids'] ) ? $args['ids'] : array();

		$query_args = array(
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'suppress_filters'    => false,
		);

		if ( $query ) {
			$query_args['s']       = $query;
			$query_args['s_title'] = $query;
		}

		if ( ! empty( $ids ) ) {
			$query_args['post__in'] = $ids;
		}

		$post_types     = array();
		$raw_post_types = get_post_types( array(), 'objects' );

		foreach ( $raw_post_types as $post_type ) {
			$post_types[] = $post_type->name;
		}

		$query_args['post_type'] = $post_types;

		$posts = get_posts( $query_args );

		remove_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

		return $posts;

	}

	/**
	 * Force query to look in post title while searching
	 *
	 * @return [type] [description]
	 */
	public function force_search_by_title( $where, $query ) {

		$args = $query->query;

		if ( ! isset( $args['s_title'] ) ) {
			return $where;
		} else {
			global $wpdb;

			$search = esc_sql( $wpdb->esc_like( $args['s_title'] ) );
			$where .= " AND {$wpdb->posts}.post_title LIKE '%$search%'";

		}

		return $where;
	}

}
