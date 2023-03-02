<?php

/*
Plugin Name: Demo plugin
Plugin URI:
Description: This is a demo plugin
Version: 1.0
Author: Bogdan Bocioaca
Author URI: http://github.com/iambib
*/

//Plugin ID
$plugin_id = basename(__FILE__);

$hooks = [
    [
        'hook' => 'info_notice',
        'function' => 'demo_plugin',
        'priority'=> 0,
    ],
];

function demo_plugin()
{
    echo "<div class='alert alert-info alert-dismissible' role='alert' style='margin-bottom:0px!important'>
		<center>Ok.. this seems to work!!</center>
	</div>";
}
