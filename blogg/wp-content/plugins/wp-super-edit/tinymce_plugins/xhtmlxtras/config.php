<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'xhtmlxtras', 
	'nicename' => __('XHTML Extras Plugin'), 
	'description' => __('Allows access to interfaces for some XHTML tags like CITE, ABBR, ACRONYM, DEL and INS. Also can give access to advanced XHTML properties such as javascript events. Provides the Citation, Abbreviation, Acronym, Deletion, Insertion, and XHTML Attributes Buttons.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'cite', 
	'nicename' => __('Citation'), 
	'description' => __('Indicate a citation using the HTML CITE tag.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlxtras', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'abbr', 
	'nicename' => __('Abbreviation'), 
	'description' => __('Indicate an abbreviation using the HTML ABBR tag.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlxtras', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'acronym', 
	'nicename' => __('Acronym'), 
	'description' => __('Indicate an acronym using the HTML ACRONYM tag.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlxtras', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'del', 
	'nicename' => __('Deletion'), 
	'description' => __('Use the HTML DEL tag to indicate recently deleted content.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlxtras', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'ins', 
	'nicename' => __('Insertion'), 
	'description' => __('Use the HTML INS tag to indicate newly inserted content.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlxtras', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'attribs', 
	'nicename' => __('XHTML Attributes'), 
	'description' => __('Modify advanced attributes and javascript events.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlxtras', 
	'status' => 'no'
));

?>