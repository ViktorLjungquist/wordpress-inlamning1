<?php

namespace ExtendBuilder;
use ColibriWP\PageBuilder\Utils\Utils;

class PartialsApi {
	static $generated_paths = array(
		"theme"    => array( "cssById", "cssByPartialId" ),
		"partials" => array( "data.html", "data.css" )
	);

	// TODO: move from partials//
	    public function import_images( $data ) {
	        $urls       = $data['urls'];
	        $result = array();
	        foreach ($urls as $url) {
	            $result[]= import_colibri_image($url);
	        }

	        echo json_encode($result);
	    }

	public function index( $data ) {
		\ExtendBuilder\log( "Api::list_templates -> data = " . json_encode( $data ) );

		$type      = $data['type'];
		$templates = get_partials_of_type( $type );

		echo json_encode( $templates );
	}

	public function all( $options = array(), $return = false ) {

		$exclude_generated = get_key_value( $options, 'exclude_generated', false );

		$partials = get_partials_of_type( array_keys( partials_types() ) );

		$args = array(
			'posts_per_page' => - 1,
			"post_type"      => 'page',
			'meta_key'       => 'extend_builder'
		);

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$partial = get_partial_details( $post, "content" );
				array_push( $partials, $partial );
			}
		}

		$theme_data = get_theme_data();

		if ( $exclude_generated ) {
			foreach ( $partials as &$partial ) {
				foreach ( self::$generated_paths['partials'] as $path ) {
					array_unset_value( $partial, $path );
				}
			}
			foreach ( self::$generated_paths['theme'] as $path ) {
				array_unset_value( $theme_data, $path );
			}
		}


		$data = array(
			"theme"    => $theme_data,
			"partials" => $partials
		);

		if ( $return ) {
			return base64_encode( gzcompress( json_encode( $data ) ) );
		}
		echo "'" . base64_encode( gzcompress( json_encode( $data ) ) ) . "'";
	}

	public function update( $data ) {
		$data = json_decode( Utils::inflate( $data ), true );

		if ( isset( $data['partials'] ) ) {
			$partials = $data['partials'];
			foreach ( $partials as $partial ) {
				if ( isset( $partial['id'] ) ) {
					update_partial( $partial['id'], $partial );
				}
			}
		}

		if ( isset( $data['theme'] ) ) {
			save_theme_data( $data['theme'], true );
		}

        	Regenerate::end();
		return;
	}


	public function insert( $data ) {
		\ExtendBuilder\log( "Api::list_templates -> data = " . json_encode( $data ) );

		$type         = $data['type'];
		$name         = $data['name'];
		$partial_data = $data['data'];

		$post_id = create_partial( $type, $partial_data, $name );
		$post    = get_post( $post_id );

		wp_send_json( get_partial_details( $post ) );
	}

	public function delete( $data ) {
		$id = $data['id'];
		wp_delete_post( $id );
	}


	public function assign( $data ) {
		\ExtendBuilder\log( "Api::assign_header -> data = " . json_encode( $data ) );

		$type       = $data['type'];
		$partial_id = $data['id'];
		$post_id    = $data['post_id'];

		assign_partial( $type, $post_id, $partial_id );
	}

	public function setDefault( $data ) {
		$type = $data['type'];
		$id   = $data['id'];
		$key  = $data['key'];
		maybe_set_as_default_partial( $type, $id, $key );
	}
}


Api::addEndPoint( "partials", new PartialsApi() );
