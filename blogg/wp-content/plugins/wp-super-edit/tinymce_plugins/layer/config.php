<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'layer', 
	'nicename' => __('Layers (DIV) Plugin'), 
	'description' => __('Insert layers using DIV HTML tag. This plugin will change the editor to allow all DIV tags. Provides the Insert Layer, Move Layer Forward, Move Layer Backward, and Toggle Layer Positioning Buttons.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'insertlayer', 
	'nicename' => __('Insert Layer'), 
	'description' => __('Insert a layer using the DIV HTML tag. Be careful layers are tricky to position.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'moveforward', 
	'nicename' => __('Move Layer Forward'), 
	'description' => __('Move selected layer forward in stacked view.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'movebackward', 
	'nicename' => __('Move Layer Backward'), 
	'description' => __('Move selected layer backward in stacked view.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'absolute', 
	'nicename' => __('Toggle Layer Positioning'), 
	'description' => __('Toggle the layer positioning as absolute or relative. Be careful layers are tricky to position.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no'
));

?>