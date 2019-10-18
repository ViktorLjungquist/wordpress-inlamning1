<?php

namespace ColibriWP\PageBuilder\Customizer\Settings;


use ColibriWP\PageBuilder\Utils\Utils;

class ContentSetting extends \ColibriWP\PageBuilder\Customizer\BaseSetting
{

    public static $pageIDRegex = '/<!--@@CPPAGEID\[(.*)\]@@-->/s';

    public function update($value)
    {
        if (is_string($value)) {
        
            $to_decode     = Utils::inflate($value);
            $pages_content = json_decode($to_decode, true);


        } else {
            $pages_content = $value;
        }

        foreach ($pages_content as $page_id => $page_content) {
            do_action('colibri_page_builder/content_setting_update', $page_id, $page_content);
        }

        parent::update(array());
    }

    public function value()
    {
        if ($this->is_previewed) {
            $value = $this->post_value(null);
            return $value;
        } else {
            return array();
        }
    }
}
