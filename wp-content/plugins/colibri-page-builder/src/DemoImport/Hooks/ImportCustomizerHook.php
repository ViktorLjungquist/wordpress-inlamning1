<?php


namespace ColibriWP\PageBuilder\DemoImport\Hooks;


use function ExtendBuilder\array_get_value;
use function ExtendBuilder\array_set_value;

class ImportCustomizerHook extends ImportHook {

	function transientKey() {
		return 'customizer';
	}

	public function run() {
		$self = $this;
		add_action( 'wp_ajax_ocdi_import_customizer_data', function () use ( $self ) {
			add_filter( 'pre_update_option_active_plugins', array( $self, 'installPlugins' ) );
		}, 0 );
	}

	public function afterImport( $data ) {
		$extend_builder_theme = get_option( 'extend_builder_theme' );
		$default_partials     = array_get_value( $extend_builder_theme, 'defaults.partials' );
		$colibri_posts_map    = $this->getGlobalTransient( 'colibri_posts_map', array() );

		foreach ( $default_partials as $area => $data ) {
			foreach ( $data as $partial => $id ) {
				if ( isset( $colibri_posts_map[ $id ] ) ) {
					$default_partials[ $area ][ $partial ] = $colibri_posts_map[ $id ];
				}
			}
		}
		array_set_value( $extend_builder_theme, 'defaults.partials', $default_partials );
		update_option( 'extend_builder_theme', $extend_builder_theme );
	}


	public function installPlugins( $plugins ) {

		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin ) {
			if ( ! in_array( $plugin, $active_plugins ) && file_exists( WP_PLUGIN_DIR . "/{$plugin}" ) ) {
				$active_plugins[] = $plugin;
			}
		}

		return array_unique( $active_plugins );

	}
}
