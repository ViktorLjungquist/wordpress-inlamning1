<?php

namespace ExtendBuilder;

use ColibriWP\PageBuilder\Utils\Utils;

$colibri_cached_posts_data = array();
function show_page_content()
{
    $value = is_front_page() || is_page();
    $colibri_show_page_content = apply_filters('colibri_show_page_content', $value);
    return $colibri_show_page_content;
}


function get_page_partials_default($post_id = -1)
{
    $partials = partials_types_list();
    $defaults = array();
    foreach ($partials as $partial) {
        if (post_supports_partial($post_id, $partial)) {
            $defaults[$partial] = partial_template_default_structure($partial);
        }
    }
    return $defaults;
}

function get_default_data($post_id)
{
    return array(
        "page" => get_page_partials_default($post_id),
        "meta" => array(
            "theme" => array(),
        ),
    );
}

function get_partial_data($post_id, $type = "")
{
    $lang = get_current_language();
    if ($type === "content") {
        $post_data = new PostData($post_id, $lang);
        $post_id   = $post_data->id_in_lang($post_id);
    } else {
        $post_id_in_lang = get_post_in_language($post_id, $lang);
        $post_data       = new PostData($post_id_in_lang);
        $post_id         = $post_id_in_lang;
    }

    $data = array(
        'css' => $post_data->get_data('css'),
        'json' => $post_data->get_data('json', false, false),
        'meta' => $post_data->get_meta_value('meta'),
        'html' => $post_data->get_post_content(),
        'id' => $post_id,
        'lang' => $lang,
        "dynamic" => ($type == "main" || $type == "sidebar") ? true : false,
    );

    return $data;
}

function get_current_page_id()
{
    global $wp_query;
    return $wp_query->post ? $wp_query->post->ID : -1;
}

function isset_path($array, $path)
{
    $parts = is_string($path) ? explode(".", $path) : $path;
    $key   = array_shift($parts);

    if (count($parts)) {
        if (isset($array[$key])) {
            isset_path($array[$key], $parts);
        } else {
            return false;
        }
    } else {
        return isset($array[$key]);
    }


}

function apply_partials_data(&$data, $post_id = -1)
{
	if ($post_id !== -1 && show_page_content()) {
		$data['content'] = get_partial_data($post_id, "content");
	}

	$partials_types_list = partials_types_list();
	foreach ($partials_types_list as $type) {
		if (post_supports_partial($post_id, $type)) {
			$partial_post = get_current_partial_post($type, get_default_language());
			if ($partial_post) {
				$data[$type] = get_partial_data($partial_post->ID, $type);
			}
		}
	}
}
function get_current_data($post_id = -1, $theme_only = false)
{
    if ($post_id == -1) {
        $post_id = get_current_page_id();
    }

	$is_preview = \is_customize_preview();
	$data_key = $post_id."-".$theme_only."-".$is_preview;
	global $colibri_cached_posts_data;
	if (isset($colibri_cached_posts_data[$data_key])) {
		return $colibri_cached_posts_data[$data_key];
	}
    $data       = get_default_data($post_id);


    //TODO @adi : rename page content to something more general//
    $instance_data = get_theme_mod('page_content', array());

    $use_theme_mod_version = $is_preview && $instance_data && !empty($instance_data);
    if ($use_theme_mod_version) {
        $pages_content = $instance_data;

        if ($pages_content && is_string($pages_content) && !empty($pages_content)) {
            $pages_content = json_decode(Utils::inflate($pages_content), true);
        }

        if ($pages_content && !empty($pages_content) && isset($pages_content[$post_id])) {
            $data = $pages_content[$post_id];

        $data['source'] = "mod";
		    //TODO: this was overwritten by next line, check if it was used//
		    //$data['meta'] = isset($data['meta']) ? $data['meta'] : array();
        $data['meta'] = $data['data'];

    } else {
		    $use_theme_mod_version = false;
	    }
    }

    if (!$use_theme_mod_version) {



        $data['source'] = "post";


        $data['meta'] = array(
            "theme" => get_theme_data(),
        );
	    if (!$theme_only) {
			apply_partials_data($data['page'], $post_id);
	    }
    }

    if (isset($data['page']['main'])) {
        $data['page']['main']['dynamic'] = false;
    }
    if (isset($data['page']['sidebar'])) {
        $data['page']['sidebar']['dynamic'] = true;
    }


    $data['ID'] = $post_id;

	$colibri_cached_posts_data[$data_key] = $data;
    return $data;
}
