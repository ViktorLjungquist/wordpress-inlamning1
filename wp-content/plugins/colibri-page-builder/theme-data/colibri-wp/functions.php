<?php


add_action('colibri_page_builder/activated/colibri-wp', function ($builder) {

    /** @var  \ColibriWP\PageBuilder\PageBuilder $builder */
    $builder->__createFrontPage();
});


add_action('colibri_page_builder/deactivated/colibri-wp', function ($builder) {
    /** @var  \ColibriWP\PageBuilder\PageBuilder $builder */
    $builder->restoreFrontPage();
});

function colibri_builder_customize_reorganize($wp_customize)
{
    $generalSettingsSections = array(
        'title_tagline',
        'colors',
        'general_site_style',
        'background_image',
        'static_front_page',
        'custom_css',
        'user_custom_widgets_areas',
        'header_image',
        'nav_menus'
    );


    /** @var \WP_Customize_Manager $wp_customize */
    $wp_customize->add_panel('general_settings', array(
        'title'    => esc_html__('General Settings', 'colibri-page-builder'),
        'priority' => 5,
    ));


    $priority = 1;
    foreach ($generalSettingsSections as $section_id) {
        $section = $wp_customize->get_section($section_id);

        if ($section) {
            $section->panel    = 'general_settings';
            $section->priority = $priority;
            $priority++;
        }

    }
}

add_filter('colibri_page_builder/license_data', function ($data) {

    $data = array_merge($data, array(
        'license_active_endpoint' => 'https://app.colibriwp.com/api/license/activate',
        'license_check_endpoint'  => 'https://app.colibriwp.com/api/license/check',
        'product_update_endpoint' => 'https://app.colibriwp.com/api/product/update',
        'dashboard_url'           => 'https://app.colibriwp.com/',
    ));

    return $data;
});

add_action('customize_register', 'colibri_builder_customize_reorganize', PHP_INT_MAX, 1);
add_filter('colibriwp_theme_customizer_skip_boot', '__return_true');

// FREE VERSION UPDATE SYSTEM - TO BE REMOVED WHEN ADDED ON WP.ORG
add_filter('colibri_page_builder/custom_update_endpoint', '__return_true');
