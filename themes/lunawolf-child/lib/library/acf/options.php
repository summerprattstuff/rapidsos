<?php
/**
 * @package elune
 * File for registering options page in back end
 * @link https://www.advancedcustomfields.com/resources/options-page/
 */

add_action( 'acf/init', function() {

  acf_add_options_page( [
    'page_title' => 'Theme Settings',
    'menu_slug' => 'theme-settings',
    'icon_url' => 'dashicons-tagcloud',
    'menu_title' => 'Theme Settings',
    'position' => 1000,
    'redirect' => false,
  ] );

  acf_add_options_page( [
    'page_title' => 'Global Modules',
    'menu_slug' => 'global-modules',
    'icon_url' => 'dashicons-tagcloud',
    'menu_title' => 'Global Modules',
    'parent_slug' => 'theme-settings',
    'position' => 1000,
    'redirect' => false,
  ] );
} );