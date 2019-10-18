<?php

namespace ExtendBuilder;

function get_default_language()
{
    if (function_exists('colibri_get_default_language')) {
        $default = colibri_get_default_language();
        if ($default) {
            return $default;
        }
    }
    return "default";
}

function get_current_language()
{
    if (function_exists('colibri_get_current_language')) {
        return colibri_get_current_language();
    }
    return "default";
}

function get_post_language($post_id, $default = "default")
{
    $lang = "";

    if (function_exists('colibri_get_post_language')) {
        $lang = colibri_get_post_language($post_id);
    }

    if (!$lang) {
        $lang = $default;
    }
    return $lang;
}

function set_post_language($post_id, $lang)
{
    if (function_exists('pll_set_post_language')) {
        return pll_set_post_language($post_id, $lang);
    }
}

function get_post_in_language($post_id, $lang, $default = true)
{
    if ($lang != "default") {
        if (function_exists('pll_get_post')) {
            $post_id_lang = pll_get_post($post_id, $lang);
        }

        if (class_exists('SitePress')) {
            $post_id_lang = apply_filters('wpml_object_id', $post_id, "any", false, $lang);
            $translations = apply_filters('wpml_get_element_translations', null, $post_id, 'post_post');
            if (!$post_id_lang && isset($translations[$lang])) {
                $post_id_lang = $translations[$lang]->element_id;
            }

            //icl_object_id( $post_id, get_post($post_id)->post_type, false, $lang );
        }

        if ($post_id_lang !== false && $post_id_lang !== null) {
            return $post_id_lang;
        }
    }

    if ($default === true) {
        return $post_id;
    }

    return $default;
}

if (file_exists(__DIR__ . "/multilanguage/multilanguage-mods.php")) {

    add_action('plugins_loaded', function () {
        if (function_exists('pll_current_language') || class_exists('SitePress')) {
            require_once __DIR__ . "/multilanguage/multilanguage-mods.php";
        }

        // load support for polylang
        if (function_exists('pll_current_language')) {
            require_once __DIR__ . "/multilanguage/polylang-options.php";
        }
        // load support for WPML
        if (class_exists('SitePress')) {
            require_once __DIR__ . "/multilanguage/wpml-options.php";
        }
    });



    add_filter('wpml_duplicate_generic_string', function ($value_to_filter, $target_language, $meta_data) {
        $context = $meta_data['context'];
        if ($value_to_filter !== '' && $context) {
            $prefix = '';

            $attribute = $meta_data['attribute'];
            if ($context == 'custom_field' && $attribute == 'value') {
                $is_serialized = $meta_data['is_serialized'];

                if ($is_serialized) {
                    $value = unserialize($value_to_filter);

                    $post_data = new PostData($meta_data['master_post_id']);
                    $new_post_data = new PostData($meta_data['post_id']);

                    if (isset($value['json'])) {
                        $new_json_post = $new_post_data->create_data("json", $post_data->get_data("json"), true);
                        $value['json_from'] = $value['json'];
                        $value['json'] = $new_json_post->ID;
                        return $value;
                    }
                }
            }
        }

        return $value_to_filter;
    }, 10, 3);


    add_action('icl_make_duplicate', function ($master_post_id, $lang, $post_array, $id) {
        global $iclTranslationManagement;
        if ($iclTranslationManagement) {
            $iclTranslationManagement->reset_duplicate_flag($id);
        }

    }, 10, 4);


    add_filter('pll_translate_post_meta', function ($value, $key, $lang, $from, $to) {
        if ($key == "extend_builder") {
            $post_data = new PostData($from);
            $new_post_data = new PostData($to);
            $old_post = $post_data->get_post();
            $new_post = get_post($to);
            $new_post->post_content = $old_post->post_content;
            wp_update_post(wp_slash(array(
//            'post_status' => 'publish',
                'post_content' => $old_post->post_content,
                'ID'           => $new_post->ID
            )));


            if (isset($value['json'])) {
                $new_json_post = $new_post_data->create_data("json", $post_data->get_data("json"), true);
                $value['json_from'] = $value['json'];
                $value['json'] = $new_json_post->ID;
            }
            $content = wp_json_encode($new_post->post_content);
            ob_start();
            ?>
          <script>
            function colibriMultipleLanguageInitialPage() {
              var content = <?php echo $content; ?>;
              if (wp.data.dispatch('core/editor') && wp.data.dispatch('core/editor').hasOwnProperty("resetBlocks")) {
                wp.data.dispatch('core/editor').resetBlocks(wp.blocks.parse(content));
              }
            }

            wp.domReady(function () {
              setTimeout(function () {
                colibriMultipleLanguageInitialPage();
              }, 1000);
            });

          </script>
            <?php
            $set_guttenberg_content_script = ob_get_clean();
            $set_guttenberg_content_script = str_replace("<script>", "", $set_guttenberg_content_script);
            $set_guttenberg_content_script = str_replace("</script>", "", $set_guttenberg_content_script);
            wp_add_inline_script('wp-edit-post', $set_guttenberg_content_script);
        }
        return $value;
    }, 10, 5);



    function link_post_translations($source, $dest)
    {
        if (function_exists('pll_save_post_translations')) {
            set_post_language($source['id'], $source['lang']);
            set_post_language($dest['id'], $dest['lang']);
            pll_save_post_translations(array(
                $source['lang'] => $source['id'],
                $dest['lang']   => $dest['id']
            ));
        }

        if (class_exists('SitePress')) {

            $get_language_args = array('element_id' => $source['id'], 'element_type' => 'post');
            $original_post_language_info = apply_filters('wpml_element_language_details', null, $get_language_args);

            $set_language_args = array(
                'element_id'           => $dest['id'],
                'element_type'         => "post_post",//.get_post($dest['id'])->post_type,
                'trid'                 => $original_post_language_info->trid,
                'language_code'        => $dest['lang'],
                'source_language_code' => $original_post_language_info->language_code
            );

            do_action('wpml_set_element_language_details', $set_language_args);
        }
    }

}



