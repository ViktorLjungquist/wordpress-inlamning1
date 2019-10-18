<?php

namespace ExtendBuilder;

function colibri_user_can_customize() {
	return is_user_logged_in() && current_user_can( 'edit_theme_options' );
}

// we are in browser preview
function is_customize_changeset_preview() {
	return \is_customize_preview()
	       && ! isset( $_GET['customize_messenger_channel'] );
}

function is_customize_preview() {
	$in_customizer       = \is_customize_preview()
	                       && isset( $_GET['customize_messenger_channel'] );
	$is_shortcode_render = apply_filters( 'mesmerize_is_shortcode_refresh',
		false );

	return ( $in_customizer || $is_shortcode_render );

}

function extend_builder_path() {
	return __DIR__;
}

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/multilanguage.php';
require_once __DIR__ . '/custom-posts.php';
require_once __DIR__ . '/data/index.php';

require_once __DIR__ . '/save.php';
if ( file_exists( __DIR__ . '/../pro/index.php' ) ) {
	require_once __DIR__ . '/../pro/index.php';
}
require_once __DIR__ . '/assets.php';
require_once __DIR__ . '/fonts.php';

require_once __DIR__ . '/partials/index.php';

require_once __DIR__ . '/api/index.php';
require_once __DIR__ . '/shortcodes/index.php';

require_once __DIR__ . '/register.php';
require_once __DIR__ . '/gutenberg.php';

require_once __DIR__ . '/import.php';
require_once __DIR__ . '/customizer/index.php';
require_once __DIR__ . '/admin/index.php';


function colibri_editor_add_editor_role() {

	if ( get_role( 'colibri_content_editor' ) ) {
		return;
	}

	$editor       = get_role( 'editor' );
	$capabilities = array_merge(
		$editor->capabilities,
		array(
			'edit_theme_options' => true,
			'customize'          => true,
		)
	);

	add_role(
		'colibri_content_editor',
		__( 'Content Editor' ),
		$capabilities
	);
}

colibri_editor_add_editor_role();


function colibri_theme_default_theme_data() {
	$front_page_design = get_option( 'colibriwp_predesign_front_page_index', 0 );
	if ( $front_page_design != 0 ) {
		return;
	}

	include( 'import_theme_data.php' );
	colibri_theme_import_theme_data();
   	Regenerate::schedule();
}

add_action( 'colibri_page_builder/default_theme_data', 'ExtendBuilder\colibri_theme_default_theme_data' );

add_filter( 'colibri_page_builder/plugin-activated',  function($response, $slug, $plugin_data) {
    if($slug === 'mailchimp-for-wp') {

        //load plugin
//        if( !class_exists('\MC4WP_Forms_Admin')){
//            $path =  ABSPATH . PLUGINDIR .'/' . $plugin_data['plugin_path'];
//            if(file_exists($path)) {
//                require_once $path;
//            }
//        }

        $response[$slug] = get_mailchimp_form_shortcode();
    }
    return $response;
},10, 3 );

