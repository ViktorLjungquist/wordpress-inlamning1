<?php

namespace ExtendBuilder;

class PostData {
	private $post_id = - 1;
	private $lang    = "default";

	public function __construct( $post_id = - 1, $lang = "default" ) {
		$this->post_id = $post_id;
		$this->lang    = $lang;
		log( 'init postdata with id:' . $this->post_id );
	}

	public function get_meta_value( $key, $default = null ) {
		$meta = get_post_meta( $this->post_id, 'extend_builder', true );

		log( 'meta:' . json_encode( $meta ) );

		if ( isset( $meta ) && isset( $meta[ $key ] ) ) {
			return $meta[ $key ];
		}

		return $default;
	}

	public function unset_meta_value( $key ) {
		$data = get_post_meta( $this->post_id, 'extend_builder', true );
		if ( ! is_array( $data ) ) {
			$data = array();
		}
		unset( $data[ $key ] );
		update_post_meta( $this->post_id, 'extend_builder', $data );
	}

	public function set_meta_value( $key, $value ) {

		//echo "set meta value##" . $key . "##".$value;
		//debug_print_backtrace ();
		//die();

		$data = get_post_meta( $this->post_id, 'extend_builder', true );
		if ( ! is_array( $data ) ) {
			$data = array();
		}

		$data[ $key ] = $value;
		update_post_meta( $this->post_id, 'extend_builder', $data );
	}

	public function id_in_lang( $id ) {
		if ( $this->lang == "default" || $id == - 1 ) {
			return $id;
		}

		$post_id = get_post_in_language( $id, $this->lang );
		if ( $post_id === false || $post_id === null ) {
			return $id;
		} else {
			return $post_id;
		}
	}

	private function get_key_post( $key ) {
		$key_post_id = $this->get_meta_value( $key, - 1 );

		log( 'key:' . $key . '; key_post_id:' . $key_post_id );

		$key_post = null;

		if ( $key_post_id !== - 1 ) {
			$key_post = get_post( $this->id_in_lang( $key_post_id ) );
		}

		return $key_post;
	}

	public function get_data( $key, $as_post = false, $default = null ) {
		$data_post = $this->get_key_post( $key );

		$data = $data_post;
		if ( $data_post && ! $as_post ) {
			$data = $data_post->post_content;
		}

		if ( $data === null ) {
			$data = $default;
		}

		return $data;
	}

	/*
	 *   create data key as custom post and assign it to post
	 *   @create_new - send true to force a new custom post association
	 */
	public function set_data( $key, $value, $create_new = false ) {
		add_filter( 'wp_save_post_revision_post_has_changed', '\ExtendBuilder\save_post_data_post_has_changed', 20, 3 );
		$r = $this->create_data( $key, $value, $create_new );

		if ( ! is_wp_error( $r ) ) {
			$this->set_meta_value( $key, $r->ID );
		} else {
			return $r;
		}

		remove_filter( 'wp_save_post_revision_post_has_changed', '\ExtendBuilder\save_post_data_post_has_changed', 20 );

		return $r;
	}

	/*
	 *   create data key as custom post, but don't assign it to post
	 */
	public function create_data( $key, $value, $create_new = false ) {
		$data = array(
			'post_id' => $this->post_id,
			$key      => $value,
		);

		$data = apply_filters( "extend_builder_set_post_data_$key", $data );

		$post_data = array(
			'post_type'    => "extb_post_$key",
			'post_status'  => 'publish',
			'post_content' => $data[ $key ],
		);

		// Update post if it already exists, otherwise create a new one.
		$post = ! $create_new ? $this->get_key_post( $key ) : false;

		if ( $post ) {
			$post_data['ID'] = $post->ID;
			$r               = wp_update_post( wp_slash( $post_data ), true );
		} else {
			$r = wp_insert_post( wp_slash( $post_data ), true );
//	        print_r($post_data);
//	        print_r(wp_slash($post_data));
//	        print_r($r);
//	        die();
		}

		if ( is_wp_error( $r ) ) {
			return $r;
		}

		return get_post( $r );
	}

	public function get_post() {
		if ( $this->post_id == - 1 ) {
			die( 'id -1' );
		}

		return get_post( $this->post_id );
	}

	public function get_post_content() {
		$post = get_post( $this->post_id );
		if ( $post ) {
			return $post->post_content;
		}

		return "";
	}
}
