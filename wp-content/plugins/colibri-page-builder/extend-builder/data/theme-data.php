<?php

namespace ExtendBuilder;

function save_theme_data($data, $backup = false)
{
	if ($backup) {
		update_option("extend_builder_theme_".time(), get_option("extend_builder_theme", array()));
	}
    update_option("extend_builder_theme", $data);
}

function update_theme_data($data)
{
    $old = get_theme_data();
    foreach ($data as $key => $value) {
        array_set_value($old, $key, $value);
    }
    save_theme_data($old);
}

function get_theme_path($path, $use_current_data = false)
{
    return get_theme_data($path, $use_current_data);
}

function get_current_theme_path($path)
{
	return get_theme_data($path, true);
}


function set_theme_path($path, $value)
{
    $old = get_theme_data();
    array_set_value($old, $path, $value);
    save_theme_data($old);
}

function get_theme_data($key = false, $use_current_data = false)
{
    
    $defaults = get_theme_data_defaults();

    if ($use_current_data) {
    	$current_data = get_current_data(-1, true);
	    $value = array_get_value($current_data, 'meta.theme');
    } else {
		$value = get_option("extend_builder_theme", array());
    }

    $data  = array_replace_recursive($defaults, $value);

    if ($key) {
        return array_get_value($data, $key);
    }

    return $data;
}

function get_current_theme_data($path)
{
	return get_theme_data($path, true);
}
