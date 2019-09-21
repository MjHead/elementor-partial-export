<?php
namespace EPEX;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin_Page manager class
 */
class Admin_Page {

	public $page_slug = 'elementor-partial-export';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_ajax_epex_get_posts', array( $this, 'search_posts' ) );
		add_action( 'wp_ajax_epex_export', array( $this, 'export_posts' ) );
	}

	/**
	 * Export required posts
	 *
	 * @return void
	 */
	public function export_posts() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Access denied' );
		}

		$request = $_REQUEST;

		unset( $request['action'] );

		$exporter = new Export( $request );

		$exporter->send_file();

	}

	/**
	 * AJAX-callback to seearch posts gor export
	 *
	 * @return [type] [description]
	 */
	public function search_posts() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Access denied' );
		}

		$search = new Posts_Search();

		$posts = $search->get( array(
			'query'     => ! empty( $_REQUEST['query'] ) ? esc_attr( $_REQUEST['query'] ) : '',
			'ids'       => ! empty( $_REQUEST['ids'] ) ? explode( ',', $_REQUEST['ids'] ) : array(),
		) );

		$result = array();

		if ( ! empty( $posts ) ) {

			foreach ( $posts as $post ) {
				$result[] = array(
					'value' => (string) $post->ID,
					'label' => $post->post_title,
				);
			}

		}

		wp_send_json( $result );

	}

	/**
	 * Register menu page
	 *
	 * @return void
	 */
	public function register_menu_page() {

		add_management_page(
			'Elementor Export',
			'Elementor Export',
			'manage_options',
			$this->page_slug,
			array( $this, 'render_page' )
		);

	}

	/**
	 * Render admin page
	 *
	 * @return [type] [description]
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<div id="epex_app"><epex-main></epex-main></div>
		</div>
		<?php
	}

	/**
	 * Wizard assets
	 *
	 * @return void
	 */
	public function assets( $hook ) {

		if ( 'tools_page_' . $this->page_slug !== $hook ) {
			return;
		}

		require_once EPEX_PATH . 'framework/vue-ui/cherry-x-vue-ui.php';

		$ui = new \CX_Vue_UI( array(
			'url'  => EPEX_URL . 'framework/vue-ui/',
			'path' => EPEX_PATH . 'framework/vue-ui/',
		) );

		$ui->enqueue_assets();

		wp_enqueue_script(
			'epex-main',
			EPEX_URL . 'assets/js/main.js',
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			EPEX_VERSION,
			true
		);

		$ajaxurl = esc_url( admin_url( 'admin-ajax.php' ) );

		wp_localize_script( 'epex-main', 'EPEXConfig', array(
			'get_posts'      => add_query_arg( array( 'action' => 'epex_get_posts' ), $ajaxurl ),
			'export_content' => add_query_arg( array( 'action' => 'epex_export' ), $ajaxurl ),
		) );

		add_action( 'admin_footer', array( $this, 'print_js_templates' ) );

	}

	/**
	 * Print JS templates for current page
	 *
	 * @return [type] [description]
	 */
	public function print_js_templates() {

		$templates = array(
			'main'
		);

		foreach ( $templates as $template ) {

			ob_start();
			include EPEX_PATH . 'views/' . $template . '.php';
			$content = ob_get_clean();

			printf(
				'<script type="text/x-template" id="epex_%1$s">%2$s</script>',
				$template,
				$content
			);
		}

	}

}
