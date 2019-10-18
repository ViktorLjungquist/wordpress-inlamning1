<?php

namespace ExtendBuilder;

function isDev()
{
    return defined('EXTEND_BUILDER_DEBUG');
}

function rootURL()
{
    if (defined('COLIBRI_URL')) {
        $url = COLIBRI_URL;
    } else {
        $url = \ColibriWP\PageBuilder\PageBuilder::instance()->rootURL();
    }
    return apply_filters('colibri_page_builder/assets_url', $url);
}

function assetUrl($url)
{
    $url = "static/" . $url;

    if (isDev()) {
        $dev_map = array(
            "static/js/theme.js" => "theme.js",
            "static/css/theme.css" => "theme.css"
        );

        if (isset($dev_map[$url])) {
            $url = $dev_map[$url];
        }


        return devUrl($url);
    } else {
        return rootURL() . "/extend-builder/assets/" . $url;
    }
}

function assetsUrl()
{
    return rootURL() . "/extend-builder/assets";
}

function devUrl($file, $path = "")
{
    if (defined('COLIBRI_LOCALHOST')) {
        $ip = COLIBRI_LOCALHOST;
    } else {
        $ip = "localhost";
    }

    return "http://$ip:8080" . ($path ? "/" . $path : "") . "/" . $file;
}

function builderAssetPath($file, $path = "")
{
    $static_path = __DIR__ . "/assets/static/";
    return $static_path . ($path ? "/" . $path : "") . "/" . $file;
}

function builderUrl($file, $path = "")
{
    $static_url = rootURL() . "/extend-builder/assets/static/";
    return $static_url . ($path ? "/" . $path : "") . "/" . $file;
}

function version()
{
    $companion = \ColibriWP\PageBuilder\PageBuilder::instance();

    return $companion->getVersion();
}

function registerBuilderAssets()
{
    $ver = version();

    if (!isDev()) {
        wp_enqueue_script('h-vendor', builderUrl("vendor.js", "js"), array(), $ver);
    }
}

function init_data()
{
    $init = \ExtendBuilder\get_current_data();
    $init_data = apply_filters(prefix('init'), array());
    $init['data'] = $init_data;
    return $init;
}

add_filter(prefix('init'), function ($data) {
    $data["defaultTypes"] = get_page_default_types();
    return $data;
});


function enqueue_assets($assets, $ver)
{
    foreach ($assets as $asset) {
        $handle = $asset['handle'];
        $type = isset($asset['type']) ? $asset['type'] : "js";
        $deps = isset($asset['deps']) ? $asset['deps']
            : ($type == "js" ? array("colibri") : array());
        $src = assetUrl($asset['src']);

        if ($type == "js") {
            wp_enqueue_script($handle, $src, $deps, $ver);
        } else {
            wp_enqueue_style($handle, $src, $deps, $ver);
        }
    }
}

// register at runtime//
add_action('colibri_page_builder/template/load_assets',
    function ($companion) {
        $ver = $companion->getVersion();

        $assets = array(
            array(
                'handle' => 'extend-builder-css',
                'type' => 'css',
                'src' => "css/theme.css"
            ),

            array(
                'handle' => 'colibri',
                'src' => "colibri.js",
                'deps' => array('jquery', "masonry")
            ),

            array(
                'handle' => 'typed',
                'src' => "typed.js"
            ),

            array(
                'handle' => 'fancybox',
                'src' => "fancybox/jquery.fancybox.min.js"
            ),

            array(
                'handle' => 'fancybox',
                'type' => 'css',
                'src' => "fancybox/jquery.fancybox.min.css"
            )
        );

        if (!is_customize_preview()) {
            array_push($assets,  array(
                'handle' => 'extend-builder-js',
                'src' => "js/theme.js"
            ));
        }

        $assets = \apply_filters('colibri_page_builder/assets/list', $assets);
        enqueue_assets($assets, $ver);

    });

$colibri_current_css = array();

function register_css($handler, $css, $priority = 10)
{
    global $colibri_current_css;

    if (!isset($colibri_current_css[$priority])) {
        $colibri_current_css[$priority] = array();
    }

    array_push(
        $colibri_current_css[$priority],
        array('handler' => $handler, 'css' => $css)
    );
}

function all_css()
{
    global $colibri_current_css;

    $css = "/* page css */\r\n";

    foreach ((array)$colibri_current_css as $index => $value) {
        foreach ($value as $index => $part_css) {
            $css .= "/* part css : " . $part_css['handler'] . " */\r\n";
            $css .= $part_css['css'];
        }
    }

    return $css;
}


function get_current_partials($data)
{
    $partials = [];

    foreach ($data['page'] as $key => $value) {
        if ($value) {
            array_push($partials, $value);
        }
    }

    return $partials;
}

function collect_device_rules($rules, $byDevice)
{
    if ($rules) {
        foreach ($rules as $index => $rule) {
            foreach ($rule as $device => $css) {
                if (trim($css)) {
                    array_push($byDevice[$device], trim($css));
                }
            }
        }
    }

    return $byDevice;
}

function render_page_css()
{
    $data = get_current_data();
    $theme = array_get_value($data, 'meta.theme');

    $local_rules = get_key_value($theme, 'cssByPartialId', array());
    $shared_rules = get_key_value($theme, 'cssById', array());
    $medias = get_key_value($theme, 'medias', array());

    $byDevice = array(
        "mobile" => array(),
        "tablet" => array(),
        "desktop" => array()
    );

    $partials = get_current_partials($data);
    $can_filter_rules = true;

    foreach ($partials as $partial) {
        if (get_key_value($partial, 'meta.styleRefs', false) === false) {
            $can_filter_rules = false;
        }
    }

    if (!$can_filter_rules) {
        $byDevice = collect_device_rules($shared_rules, $byDevice);
    }

    foreach ($partials as $partial) {
        $partial_id = $partial['id'];

        if ($can_filter_rules) {
            $partial_style_refs = get_key_value($partial, 'meta.styleRefs', array());
            $partial_shared_rules = array_intersect_key($shared_rules, array_flip($partial_style_refs));
            $byDevice = collect_device_rules($partial_shared_rules, $byDevice);
        }

        if (isset($local_rules[$partial_id])) {
            $byDevice = collect_device_rules(
                $local_rules[$partial_id], $byDevice
            );
        }
    }

    $css = "";

    if ($medias) {
        $css = join($byDevice["desktop"], "\r\n") . "\r\n";
        foreach ($byDevice as $device => $rules) {
            $media = $medias[$device];
            if (isset($media['query'])) {
                $css .= $media['query'] . "{\r\n";
                $css .= join($rules, "\r\n");
                $css .= "}\r\n";
            }
        }
    }

    return $css;
}

add_action(
    'get_header', function () {
    $data = get_current_data();

    if (!is_customize_preview()) {
        $theme_css = array_get_value($data, 'meta.theme.css');
        register_css("theme", $theme_css, 0);
        register_css("page", render_page_css(), 0);
    }

    $options = array();

    $partials_types = partials_types_list(true);
    foreach ($partials_types as $name) {
        if (isset($data['page'][$name])) {
            $part = $data['page'][$name];
            if (isset($part['meta']) && $part['meta']) {
                $meta = $part['meta'];
                unset($meta['styleRefs']);
                $options = array_merge($options, $meta);
            }
        }
    }

    wp_localize_script("colibri", 'colibriData', $options);
}
);

add_action(
    'wp_enqueue_scripts', function () {
    wp_add_inline_style('extend-builder-css', all_css());
}, PHP_INT_MAX
);

