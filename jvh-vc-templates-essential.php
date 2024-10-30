<?php
/**
 * Plugin Name:       JVH VC Templates Essentials
 * Description:       This plugin allows you to create custom VC Templates that will be shown along the Essential VC templates
 * Version:           1.1.0
 * Author:            JVH webbouw
 * Author URI:        https://jvhwebbouw.nl
 * License:           GPL-v3
 * Requires PHP:      7.3
 * Requires at least: 5.0
 */

foreach ( glob( __DIR__ . '/inc/*.php' ) as $file ) {
    require_once $file;
}

$plugin = new \JVH\VcTemplates\Plugin();
$plugin->setup();
