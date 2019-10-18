<?php


namespace ColibriWP\PageBuilder\DemoImport;


use ColibriWP\PageBuilder\DemoImport\Hooks\ImportContentHook;
use ColibriWP\PageBuilder\DemoImport\Hooks\ImportCustomizerHook;
use ColibriWP\PageBuilder\DemoImport\Hooks\PreparationHook;
use ColibriWP\PageBuilder\DemoImport\Views\PageView;
use ExtendBuilder\PartialsApi;
use OCDI\Helpers;
use OCDI\OneClickDemoImport;

class DemoImport {

	private static $instance;

	private $data_api = false;

	public function __construct() {
		PreparationHook::init();
		ImportContentHook::init();
		ImportCustomizerHook::init();

		$this->data_api = new DataApi();

		$this->addAdminFilters();


		new PageView( $this );

	}

	public function addAdminFilters() {
		add_filter( 'pt-ocdi/plugin_page_setup', '__return_false' );
		add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

		add_filter( 'colibriwp_theme_theme_plugins', function ( $plugins ) {

			$plugins['one-click-demo-import'] = array(
				'name'        => 'One Click Demo Import',
				'description' => 'One Click Demo Import plugin allows the demo sites to be imported into your WordPress',
				'priority'    => 11
			);

			return $plugins;
		} );

		add_filter( 'pt-ocdi/upload_file_path', function ( $path ) {
			$template = get_template();

			$newPath = "{$path}/{$template}-ocdi/";

			$newPath = wp_normalize_path( $newPath );
			$newPath = trailingslashit( $newPath );

			if ( ! is_dir( $newPath ) ) {
				if ( ! wp_mkdir_p( $newPath ) ) {
					$newPath = $path;
				}
			}

			return $newPath;
		} );

		add_action( 'wp_ajax_colibri_page_builder_active_ocdi', function () {

			$plugins   = get_option( 'active_plugins', array() );
			$plugins[] = 'one-click-demo-import/one-click-demo-import.php';
			$plugins   = array_unique( $plugins );
			update_option( 'active_plugins', $plugins );

			wp_send_json( array() );
		} );

		add_action( 'wp_ajax_get_after_import_builder_data', function () {
			$api = new PartialsApi();;

			$result = array(
				'data'    => array(
					'_extendBuilderWPData'      => (object) apply_filters( 'extendbuilder_wp_data',
						array( 'defaults' => array() ) ),
					'_colibriAllPartialsExport' => $api->all( array( "exclude_generated" => true ), true )
				),
				'success' => true
			);

			wp_send_json( $result );
		} );
	}

	public static function load() {
		static::$instance = new static();
	}

	public static function log_info( $message = '', $context = array() ) {
		static::log( 'info', $message, $context );
	}

	public static function log( $level = 'info', $message = '', $context = array() ) {
		$data = OneClickDemoImport::get_instance()->get_current_importer_data();

		$log_file = $data['log_file_path'];

		Helpers::append_to_file(
			sprintf( "[%s] - %s", strtoupper( $level ), $message ),
			$log_file,
			" Colibri Importer "
		);


	}

	public static function log_debug( $message = '', $context = array() ) {
		static::log( 'debug', $message, $context );
	}

	public static function log_error( $message = '', $context = array() ) {
		static::log( 'error', $message, $context );
	}

	public static function log_notice( $message = '', $context = array() ) {
		static::log( 'notice', $message, $context );
	}

	public static function log_warning( $message = '', $context = array() ) {
		static::log( 'warning', $message, $context );
	}

	public function getImporterFiles() {
		return $this->data_api->registerImportFiles();
	}


}
