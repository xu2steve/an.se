<?php

function bb_default_scripts( &$scripts ) {
	$scripts->base_url = bb_get_uri(BB_INC, null, BB_URI_CONTEXT_SCRIPT_SRC);
	$scripts->base_url_admin = bb_get_uri('bb-admin/', null, BB_URI_CONTEXT_SCRIPT_SRC + BB_URI_CONTEXT_BB_ADMIN);
	$scripts->default_version = bb_get_option( 'version' );
	
	$scripts->add( 'fat',              $scripts->base_url . 'js/fat.js', array('add-load-event'), '1.0-RC1_3660' );
	$scripts->add( 'prototype',        $scripts->base_url . 'js/prototype.js', false, '1.5.0' );
	$scripts->add( 'wp-ajax',          $scripts->base_url . 'js/wp-ajax-js.php', array('prototype'), '2.1-beta2' );
	$scripts->add( 'listman',          $scripts->base_url . 'js/list-manipulation-js.php', array('add-load-event', 'wp-ajax', 'fat'), '440' );
	$scripts->add( 'wp-ajax-response', $scripts->base_url . 'js/wp-ajax-response.js', array('jquery'), '20080316' );
	$scripts->localize( 'wp-ajax-response', 'wpAjax', array(
		'noPerm' => __('You do not have permission to do that.'),
		'broken' => __('An unidentified error has occurred.')
	) );
	$scripts->add( 'wp-lists',         $scripts->base_url . 'js/wp-lists.js', array('wp-ajax-response','jquery-color'), '20080826' );
	$scripts->localize( 'wp-lists', 'wpListL10n', array(
		'url' => $scripts->base_url_admin . 'admin-ajax.php'
	) );
	$scripts->add( 'topic',                   $scripts->base_url . 'js/topic.js', array('wp-lists'), '20080506' );
	$scripts->add( 'jquery',                  $scripts->base_url . 'js/jquery/jquery.js', false, '1.2.6');
	$scripts->add( 'interface',               $scripts->base_url . 'js/jquery/interface.js', array('jquery'), '1.2.3');
	$scripts->add( 'jquery-color',            $scripts->base_url . 'js/jquery/jquery.color.js', array('jquery'), '2.0-4561' );
	$scripts->add( 'add-load-event',          $scripts->base_url . 'js/add-load-event.js' );
	$scripts->add( 'password-strength-meter', $scripts->base_url . 'js/password-strength-meter.js', array('jquery'), '20070405' );
	$scripts->localize( 'password-strength-meter', 'pwsL10n', array(
		'short' => __('Too short'),
		'bad' => __('Bad'),
		'good' => __('Good'),
		'strong' => __('Strong')
	));
	$scripts->add( 'profile-edit',   $scripts->base_url . 'js/profile-edit.js', array('password-strength-meter'), '20080721' );
	$scripts->add( 'content-forums', $scripts->base_url_admin . 'js/content-forums.js', array('wp-lists', 'interface'), '20080826b' );
	$scripts->localize( 'content-forums', 'bbSortForumsL10n', array(
		'handleText' => __('drag'),
		'saveText' => __('Save Forum Order &#187;'),
		'editText' => __('Edit Forum Order &#187;')
	));
}

function bb_prototype_before_jquery( $js_array ) {
	if ( false === $jquery = array_search( 'jquery', $js_array ) )
		return $js_array;

	if ( false === $prototype = array_search( 'prototype', $js_array ) )
		return $js_array;

	if ( $prototype < $jquery )
		return $js_array;

	unset($js_array[$prototype]);

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

function bb_just_in_time_script_localization() {
	wp_localize_script( 'topic', 'bbTopicJS', array(
		'currentUserId' => bb_get_current_user_info( 'id' ),
		'topicId' => get_topic_id(),
		'favoritesLink' => get_favorites_link(),
		'isFav' => (int) is_user_favorite( bb_get_current_user_info( 'id' ) ),
		'confirmPostDelete' => __("Are you sure you wanna delete this post?"),
		'confirmPostUnDelete' => __("Are you sure you wanna undelete this post?"),
		'favLinkYes' => __( 'favorites' ),
		'favLinkNo' => __( '?' ),
		'favYes' => __( 'This topic is one of your %favLinkYes% [%favDel%]' ),
		'favNo' => __( '%favAdd% (%favLinkNo%)' ),
		'favDel' => __( '&times;' ),
		'favAdd' => __( 'Add this topic to your favorites' )
	));
}

add_action( 'wp_default_scripts', 'bb_default_scripts' );
add_filter( 'wp_print_scripts', 'bb_just_in_time_script_localization' );
add_filter( 'print_scripts_array', 'bb_prototype_before_jquery' );
