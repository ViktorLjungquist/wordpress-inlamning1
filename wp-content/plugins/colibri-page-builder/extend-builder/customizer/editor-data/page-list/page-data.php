<?php

namespace ExtendBuilder;

class PageData
{
    public $label;
    public $url;
    public $category;

    function __construct($label, $url, $category)
    {
        $this->label = $label;
        $this->url = $this->get_customizer_url($url);
        $this->category = $category;
    }

    static function get_customizer_url($url)
    {
        $encodedUrl = rawurlencode($url);
        return get_option('home') . "/wp-admin/customize.php?url=" . $encodedUrl;
    }
}
