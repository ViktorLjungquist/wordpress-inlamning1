<?php

namespace ExtendBuilder;

function wp_colibri_v1_newsletter_get_default_shortcode()
{
    return get_mailchimp_form_shortcode();
}


add_action('rest_api_init', function () {
    register_rest_route('colibri/v1', '/newsletter/default-shortcode', array(
        'methods' => 'GET',
        'callback' => '\ExtendBuilder\wp_colibri_v1_newsletter_get_default_shortcode',
    ));
});
