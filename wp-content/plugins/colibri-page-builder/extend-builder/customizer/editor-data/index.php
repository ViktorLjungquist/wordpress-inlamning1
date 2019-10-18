<?php

namespace ExtendBuilder;

use ColibriWP\PageBuilder\PageBuilder;

require_once __DIR__ . '/page-list.php';

add_filter( 'colibri_page_builder/customizer/preview_data', function ( $value ) {
	$current_page_title = get_the_title();
	$site_title         = get_bloginfo( 'name' );
	if ( ! $current_page_title ) {
		$current_page_title = $site_title;
	}

	//temporary, some sites like 404, search don't show a good title, so for now we show the site title everywhere
	$current_page_title        = $site_title;
	$value['currentPageTitle'] = $current_page_title;

	return $value;
} );

add_filter( 'colibri_page_builder/customizer/preview_data', function ( $value ) {

	$value['currentPageIsPost'] = is_single();

	return $value;
} );
add_filter( 'colibri_page_builder/customizer/preview_data', function ( $value ) {

    $value['pageFeaturedImage'] = colibri_get_post_featured_img();

    return $value;
} );
add_filter( 'extendbuilder_wp_data', function ( $data ) {
	$data['default_search_form'] = colibri_output_sidebar_search_form();

	return $data;
} );
add_filter( 'extendbuilder_wp_data', function ( $value ) {
	if ( ! defined( 'EXTEND_BUILDER_DEBUG' ) ) {
		$value["assets_url"] = assetsUrl() . "/";
	} else {
		$value["assets_url"] = devUrl( "" );
	}
	$value['ajax_url'] = admin_url( 'admin-ajax.php' );
	$value["version"]  = version();

	return $value;
} );

add_filter( 'extendbuilder_wp_data', function ( $value ) {
	$value['colibri-show-tour'] = get_option( "colibri-show-tour", false );
	return $value;
} );

add_filter( 'extendbuilder_wp_data', function ( $value ) {
  if (\function_exists('\is_plugin_active')) {
      $value['mailchimp_is_active'] = \is_plugin_active( 'mailchimp-for-wp/mailchimp-for-wp.php' );
  }
	return $value;
} );

add_filter( 'extendbuilder_wp_data', function ( $value ) {

	$value['page_list'] = PagesList::get_page_list();

	return $value;
} );

add_filter( 'extendbuilder_wp_data', function ( $value ) {

	$value['current_user_id'] = get_current_user_id();

	return $value;
} );
add_filter( 'extendbuilder_wp_data', function ( $value ) {

	$value['home_page_url'] = get_option( 'home' );

	return $value;
} );
add_filter( 'extendbuilder_wp_data', function ( $value ) {

	$value['attachment_sizes'] = apply_filters( 'image_size_names_choose', array(
		'thumbnail'      => __( 'Thumbnail' ),
		'medium'         => __( 'Medium' ),
		'large'          => __( 'Large' ),
		'full'           => __( 'Full Size' ),
		'post-thumbnail' => __( 'Post Thumbnail' ),
		'medium_large'   => __( 'Medium Large' ),
	) );

	return $value;
} );
add_action('plugins_loaded', function () {
    add_filter('extendbuilder_wp_data', function ($value) {

        $plugin_name = 'mailchimp-for-wp';
        $manager = \colibriwp_theme()->getPluginsManager();
        $value['newsletter_plugin_data'] = [
        "status" => $manager->getPluginState($plugin_name),
        "install_url" => $manager->getInstallLink($plugin_name),
        "activate_url" => $manager->getActivationLink($plugin_name)
        ];

        return $value;
    });
});
add_filter( 'extendbuilder_wp_data', function ( $value ) {

	$shortcode = "";

	if ( class_exists( '\WPCF7_ContactForm' ) ) {
		$first_form = \WPCF7_ContactForm::find( array(
			'posts_per_page' => 1,
		) );

		if ( count( $first_form ) ) {
			/** @var WPCF7_ContactForm $first_form */
			$first_form = $first_form[0];
			$shortcode  = $first_form->shortcode();

		}
	}

	$value['defaults']['contact-form-7'] = $shortcode;

	return $value;
} );

add_filter( 'extendbuilder_wp_data', function ( $value ) {


    $value['defaults']['mailchimp-signup-form'] = get_mailchimp_form_shortcode();

    return $value;
} );


add_action( 'customize_controls_print_scripts', function () {

	$debug = defined( 'COLIBRI_SCRIPT_DEBUG' ) && COLIBRI_SCRIPT_DEBUG;
	?>
    <script>
        var _extendBuilderWPData = <?php echo json_encode( (object) apply_filters( 'extendbuilder_wp_data',
			array(
				'debug'       => $debug,
				'upgrade_url' => apply_filters( 'colibri_page_builder/upgrade_url', 'https://colibriwp.com/go/upgrade' ),
        			'try_url' => apply_filters( 'colibri_page_builder/try_url', 'https://colibriwp.com/go/try' ),
				'plugin_url'  => PageBuilder::instance()->rootURL(),
				'defaults'    => array()
			) ) ); ?>;
    </script>
	<?php
}, PHP_INT_MAX );
