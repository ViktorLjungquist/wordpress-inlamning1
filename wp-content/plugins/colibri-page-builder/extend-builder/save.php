<?php

namespace ExtendBuilder;

add_filter( 'wp_revisions_to_keep', function ( $revisions, $post ) {
	$extend_builder_revisions   = 20;
	$extend_builder_posts_types = array( 'extb_post_json', 'extb_post_main' );

	/** @var \WP_Post $post */
	if ( in_array( $post->post_type, $extend_builder_posts_types ) ) {
		return $extend_builder_revisions;
	}

	$meta = get_post_meta( $post->ID, 'extend_builder', true );

	if ( $meta ) {
		return $extend_builder_revisions;
	}


	return $revisions;
}, 20, 2 );

add_action( '_wp_put_post_revision', function ( $revision_id ) {

	global $extb_post_revisions;

	$extb_post_revisions = is_array( $extb_post_revisions ) ? $extb_post_revisions : array();
	$extb_handled_posts  = array( 'page', 'post', 'product', 'extb_post_main' );

	$revision    = get_post( $revision_id );
	$parent_post = get_post( $revision->post_parent );

	if ( $parent_post->post_type === "extb_post_json" ) {
		$extb_post_revisions[ $parent_post->ID ] = array(
			'post_id'     => $parent_post->ID,
			'revision_id' => $revision_id,
			'post_type'   => $parent_post->post_type,
			'json'        => false
		);
	}

	if ( in_array( $parent_post->post_type, $extb_handled_posts ) ) {
		$meta = get_post_meta( $parent_post->ID, 'extend_builder', true );
		$json = - 1;

		if ( is_array( $meta ) && isset( $meta['json'] ) ) {
			$json = $meta['json'];
		}

		$extb_post_revisions[ $parent_post->ID ] = array(
			'post_id'     => $parent_post->ID,
			'revision_id' => $revision_id,
			'post_type'   => $parent_post->post_type,
			'json'        => $json
		);
	}

} );

register_shutdown_function( function () {
	global $extb_post_revisions, $post;

	if ( ! $post ) {
		return;
	}
	if ( $extb_post_revisions && is_admin() ) {

		foreach ( $extb_post_revisions as $post_id => $data ) {
			if ( isset( $data['json'] ) && $data['json'] !== false ) {
				$extb_post_json_id          = $data['json'];
				$post_revision_id           = $data['revision_id'];
				$extb_post_json_revision_id = array_get_value( $extb_post_revisions, "{$extb_post_json_id}.revision_id", false );

				if ( $extb_post_json_revision_id ) {
					continue;
				}

				if ( $extb_post_json_revision_id ) {
					update_metadata( 'post', $post_revision_id, 'extb_json_revision_id', $extb_post_json_revision_id );
				}

			}
		}

	}

} );

add_action( 'wp_restore_post_revision', function ( $post_id, $revision_id ) {
	$extb_json_revision_id = get_post_meta( $revision_id, 'extb_json_revision_id', true );

	if ( $extb_json_revision_id ) {
		wp_restore_post_revision( $extb_json_revision_id );
	}

}, 10, 2 );

function save_post_data_post_has_changed( $post_has_changed, $last_revision, $post ) {
	return true;
}

function save_post_data( $post_id, $data, $type ) {

	if ( $type != "content" ) {
		$save_lang = isset( $data['lang'] ) ? $data['lang'] : "default";
		$post_lang = get_post_language( $post_id, get_default_language() );

		if ( $save_lang != "default" && $save_lang !== $post_lang ) {

			$lang_post_id = get_post_in_language( $post_id, $save_lang, false );

			if ( ! $lang_post_id ) {
				$lang_post_id = create_partial( $type, $data );
				link_post_translations( array( "lang" => $post_lang, "id" => $post_id ), array(
					"lang" => $save_lang,
					"id"   => $lang_post_id
				) );

				$new_post = new \ExtendBuilder\PostData( $lang_post_id, $save_lang );

				return $new_post;
			} else {
				$post_id = $lang_post_id;
			}
		}

		return update_partial( $post_id, $data );
	} else {
		if ( ! empty( $data ) ) {
			$post = get_post( $post_id );
			if ( $post->post_type === "page" ) {
				$page_template = apply_filters( 'colibri_maintainable_default_template', "page-templates/full-width-page.php" );
				update_post_meta( $post_id, '_wp_page_template', $page_template );
				wp_publish_post( $post_id );
			}
		}
	}

	return update_partial( $post_id, $data );
}

function update_menu_data( $data ) {
	$menu                   = $data['theme']['menu'];
	$locations              = $menu["locations"];
	$locations_to_add       = $menu["locationsToAdd"];
	$locations_to_delete    = $menu["locationsToDelete"];
	$default_location_names = array(
		$menu["defaultLocations"]["header"]["name"],
		$menu["defaultLocations"]["footer"]["name"],
	);

	for ( $i = 0; $i < count( $locations_to_delete ); $i ++ ) {
		$isDefaultLocation = false;
		foreach ( $default_location_names as $default_location_name ) {
			if ( $locations_to_delete[ $i ] === $default_location_name ) {
				$isDefaultLocation = true;
			}
		}
		//dont delete from the locations vector the default locations
		if ( $isDefaultLocation ) {
			array_splice( $locations_to_delete, $i, 1 );
		}
	}

	$new_data = $data;

	$new_locations = array_diff( $locations, $locations_to_delete );
	$new_locations = array_merge( $new_locations, $locations_to_add );

	$new_menu = $menu;

	$default_locations = array(
		"header-menu",
		"footer-menu",
	);
	$defaultLocations  = array(
		"header" => array(
			"name"    => "header-menu",
			"hasMenu" => false,
		),
		"footer" => array(
			"name"    => "footer-menu",
			"hasMenu" => false,
		),
	);

	$new_menu["locations"]         = $new_locations;
	$new_menu["locationsToAdd"]    = array();
	$new_menu["locationsToDelete"] = array();

	$new_data['theme']['menu'] = $new_menu;

	return $new_data;
}

function save_post_custom_data( $post_id, $page_content ) {


	// save theme//
	$data = get_key_value( $page_content, 'data', array(
		"theme" => array(),
	) );

	// save theme data//
	update_theme_data( $data["theme"] );

	$page_data = get_key_value( $page_content, 'page', array() );

	// save page content and data//
	$content = get_key_value( $page_data, 'content', array() );

	$post = save_post_data( $post_id, $content, "content" );

	$partials_types = partials_types_list();

	// save header/footer//
	foreach ( $partials_types as $key => $name ) {

		$data = get_key_value( $page_data, $name, false );

		if ( $data !== false ) {


			$id = isset( $data['id'] ) ? $data['id'] : - 1;


			if ( $id == - 1 ) {

				$custom_post = $post->set_data( $name, '', true );
				if ( $custom_post ) {
					$id = $custom_post->ID;
				}
			}


			save_post_data( $id, $data, $name );
		}
	}


}

add_action( 'colibri_page_builder/content_setting_update', '\ExtendBuilder\save_post_custom_data', 1, 2 );
