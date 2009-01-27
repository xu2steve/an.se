<?php
/* INIT */

function bb_global_sanitize( $array, $trim = true ) {
	foreach ($array as $k => $v) {
		if ( is_array($v) ) {
			$array[$k] = bb_global_sanitize($v);
		} else {
			if ( !get_magic_quotes_gpc() )
				$array[$k] = addslashes($v);
			if ( $trim )
				$array[$k] = trim($array[$k]);
		}
	}
	return $array;
}

function bb_is_installed() { // Maybe grab all the forums and cache them
	global $bbdb;
	$bbdb->suppress_errors();
	$forums = (array) @get_forums();
	$bbdb->suppress_errors(false);
	if ( !$forums )
		return false;

	return true;
}

function bb_set_custom_user_tables() {
	global $bb;
	
	// Check for older style custom user table
	if ( !isset($bb->custom_tables['users']) ) { // Don't stomp new setting style
		if ( !$bb->custom_user_table = bb_get_option('custom_user_table') ) // Maybe get from database or old config setting
			if ( BB_LOAD_DEPRECATED && defined('CUSTOM_USER_TABLE') ) // Maybe user has set old constant
				$bb->custom_user_table = CUSTOM_USER_TABLE;
		if ( $bb->custom_user_table ) {
			if ( !isset($bb->custom_tables) )
				$bb->custom_tables = array();
			$bb->custom_tables['users'] = $bb->custom_user_table;
		}
	}

	// Check for older style custom user meta table
	if ( !isset($bb->custom_tables['usermeta']) ) { // Don't stomp new setting style
		if ( !$bb->custom_user_meta_table = bb_get_option('custom_user_meta_table') ) // Maybe get from database or old config setting
			if ( BB_LOAD_DEPRECATED && defined('CUSTOM_USER_META_TABLE') ) // Maybe user has set old constant
				$bb->custom_user_meta_table = CUSTOM_USER_META_TABLE;
		if ( $bb->custom_user_meta_table ) {
			if ( !isset($bb->custom_tables) )
				$bb->custom_tables = array();
			$bb->custom_tables['usermeta'] = $bb->custom_user_meta_table;
		}
	}

	// Check for older style wp_table_prefix
	if ( $bb->wp_table_prefix = bb_get_option('wp_table_prefix') ) { // User has set old constant
		if ( !isset($bb->custom_tables) ) {
			$bb->custom_tables = array(
				'users'    => $bb->wp_table_prefix . 'users',
				'usermeta' => $bb->wp_table_prefix . 'usermeta'
			);
		} else {
			if ( !isset($bb->custom_tables['users']) ) // Don't stomp new setting style
				$bb->custom_tables['users'] = $bb->wp_table_prefix . 'users';
			if ( !isset($bb->custom_tables['usermeta']) )
				$bb->custom_tables['usermeta'] = $bb->wp_table_prefix . 'usermeta';
		}
	}

	// Check for older style user database
	if ( !isset($bb->custom_databases) )
		$bb->custom_databases = array();
	if ( !isset($bb->custom_databases['user']) ) {
		if ( !$bb->user_bbdb_name = bb_get_option('user_bbdb_name') )
			if ( BB_LOAD_DEPRECATED && defined('USER_BBDB_NAME') ) // User has set old constant
				$bb->user_bbdb_name = USER_BBDB_NAME;
		if ( $bb->user_bbdb_name )
			$bb->custom_databases['user']['name'] = $bb->user_bbdb_name;

		if ( !$bb->user_bbdb_user = bb_get_option('user_bbdb_user') )
			if ( BB_LOAD_DEPRECATED && defined('USER_BBDB_USER') ) // User has set old constant
				$bb->user_bbdb_user = USER_BBDB_USER;
		if ( $bb->user_bbdb_user )
			$bb->custom_databases['user']['user'] = $bb->user_bbdb_user;

		if ( !$bb->user_bbdb_password = bb_get_option('user_bbdb_password') )
			if ( BB_LOAD_DEPRECATED && defined('USER_BBDB_PASSWORD') ) // User has set old constant
				$bb->user_bbdb_password = USER_BBDB_PASSWORD;
		if ( $bb->user_bbdb_password )
			$bb->custom_databases['user']['password'] = $bb->user_bbdb_password;

		if ( !$bb->user_bbdb_host = bb_get_option('user_bbdb_host') )
			if ( BB_LOAD_DEPRECATED && defined('USER_BBDB_HOST') ) // User has set old constant
				$bb->user_bbdb_host = USER_BBDB_HOST;
		if ( $bb->user_bbdb_host )
			$bb->custom_databases['user']['host'] = $bb->user_bbdb_host;

		if ( !$bb->user_bbdb_charset = bb_get_option('user_bbdb_charset') )
			if ( BB_LOAD_DEPRECATED && defined('USER_BBDB_CHARSET') ) // User has set old constant
				$bb->user_bbdb_charset = USER_BBDB_CHARSET;
		if ( $bb->user_bbdb_charset )
			$bb->custom_databases['user']['charset'] = $bb->user_bbdb_charset;

		if ( !$bb->user_bbdb_collate = bb_get_option('user_bbdb_collate') )
			if ( BB_LOAD_DEPRECATED && defined('USER_BBDB_COLLATE') ) // User has set old constant
				$bb->user_bbdb_collate = USER_BBDB_COLLATE;
		if ( $bb->user_bbdb_collate )
			$bb->custom_databases['user']['collate'] = $bb->user_bbdb_collate;

		if ( isset( $bb->custom_databases['user'] ) ) {
			if ( isset($bb->custom_tables['users']) )
				$bb->custom_tables['users'] = array('user', $bb->custom_tables['users']);
			if ( isset($bb->custom_tables['usermeta']) )
				$bb->custom_tables['usermeta'] = array('user', $bb->custom_tables['usermeta']);
		}
	}
}

/* HTTP Helpers */

/**
 * Set the headers for caching for 10 days with JavaScript content type.
 *
 * @since 1.0
 */
function bb_cache_javascript_headers() {
	$expiresOffset = 864000; // 10 days
	header( "Content-Type: text/javascript; charset=utf-8" );
	header( "Vary: Accept-Encoding" ); // Handle proxies
	header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + $expiresOffset ) . " GMT" );
}

/* Pagination */

/**
 * Retrieve paginated links for pages.
 *
 * Technically, the function can be used to create paginated link list for any
 * area. The 'base' argument is used to reference the url, which will be used to
 * create the paginated links. The 'format' argument is then used for replacing
 * the page number. It is however, most likely and by default, to be used on the
 * archive post pages.
 *
 * The 'type' argument controls format of the returned value. The default is
 * 'plain', which is just a string with the links separated by a newline
 * character. The other possible values are either 'array' or 'list'. The
 * 'array' value will return an array of the paginated link list to offer full
 * control of display. The 'list' value will place all of the paginated links in
 * an unordered HTML list.
 *
 * The 'total' argument is the total amount of pages and is an integer. The
 * 'current' argument is the current page number and is also an integer.
 *
 * An example of the 'base' argument is "http://example.com/all_posts.php%_%"
 * and the '%_%' is required. The '%_%' will be replaced by the contents of in
 * the 'format' argument. An example for the 'format' argument is "?page=%#%"
 * and the '%#%' is also required. The '%#%' will be replaced with the page
 * number.
 *
 * You can include the previous and next links in the list by setting the
 * 'prev_next' argument to true, which it is by default. You can set the
 * previous text, by using the 'prev_text' argument. You can set the next text
 * by setting the 'next_text' argument.
 *
 * If the 'show_all' argument is set to true, then it will show all of the pages
 * instead of a short list of the pages near the current page. By default, the
 * 'show_all' is set to false and controlled by the 'end_size' and 'mid_size'
 * arguments. The 'end_size' argument is how many numbers on either the start
 * and the end list edges, by default is 1. The 'mid_size' argument is how many
 * numbers to either side of current page, but not including current page.
 *
 * It is possible to add query vars to the link by using the 'add_args' argument
 * and see {@link add_query_arg()} for more information.
 *
 * @since 1.0
 *
 * @param string|array $args Optional. Override defaults.
 * @return array|string String of page links or array of page links.
 */
function bb_paginate_links( $args = '' ) {
	$defaults = array(
		'base'         => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format'       => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
		'total'        => 1,
		'current'      => 0,
		'show_all'     => false,
		'prev_next'    => true,
		'prev_text'    => __( '&laquo; Previous' ),
		'next_text'    => __( 'Next &raquo;' ),
		'end_size'     => 1, // How many numbers on either end including the end
		'mid_size'     => 2, // How many numbers to either side of current not including current
		'type'         => 'plain',
		'add_args'     => false, // array of query args to add
		'add_fragment' => '',
		'n_title'      => __( 'Page %d' ), // Not from WP version
		'prev_title'   => __( 'Previous page' ), // Not from WP version
		'next_title'   => __( 'Next page' ) // Not from WP version
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	// Who knows what else people pass in $args
	$total = (int) $total;
	if ( $total < 2 )
		return;
	$current  = (int) $current;
	$end_size = 0 < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
	$mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
	$add_args = is_array($add_args) ? $add_args : false;
	$r = '';
	$page_links = array();
	$n = 0;
	$dots = false;

	$empty_format = '';
	if ( strpos( $format, '?' ) === 0 ) {
		$empty_format = '?';
	}

	if ( $prev_next && $current && 1 < $current ) {
		$link = str_replace( '%_%', 2 == $current ? $empty_format : $format, $base );
		$link = str_replace( '%#%', $current - 1, $link );
		$link = str_replace( '?&', '?', $link );
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$link .= $add_fragment;
		$page_links[] = '<a class="prev page-numbers" href="' . clean_url( $link ) . '" title="' . attribute_escape( $prev_title ) . '">' . $prev_text . '</a>';
	}

	for ( $n = 1; $n <= $total; $n++ ) {
		$n_display = bb_number_format_i18n( $n );
		$n_display_title =  attribute_escape( sprintf( $n_title, $n ) );
		if ( $n == $current ) {
			$page_links[] = '<span class="page-numbers current" title="' . $n_display_title . '">' . $n_display . '</span>';
			$dots = true;
		} else {
			if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) {
				$link = str_replace( '%_%', 1 == $n ? $empty_format : $format, $base );
				$link = str_replace( '%#%', $n, $link );
				$link = str_replace( '?&', '?', $link );
				if ( $add_args )
					$link = add_query_arg( $add_args, $link );
				$link .= $add_fragment;
				$page_links[] = '<a class="page-numbers" href="' . clean_url( $link ) . '" title="' . $n_display_title . '">' . $n_display . '</a>';
				$dots = true;
			} elseif ( $dots && !$show_all ) {
				$page_links[] = '<span class="page-numbers dots">&hellip;</span>';
				$dots = false;
			}
		}
	}
	if ( $prev_next && $current && ( $current < $total || -1 == $total ) ) {
		$link = str_replace( '%_%', $format, $base );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$link .= $add_fragment;
		$page_links[] = '<a class="next page-numbers" href="' . clean_url( $link ) . '" title="' . attribute_escape( $next_title ) . '">' . $next_text . '</a>';
	}
	switch ( $type ) {
		case 'array':
			return $page_links;
			break;
		case 'list':
			$r .= '<ul class="page-numbers">' . "\n\t" . '<li>';
			$r .= join( '</li>' . "\n\t" . '<li>', $page_links );
			$r .= '</li>' . "\n" . '</ul>' . "\n";
			break;
		default:
			$r = join( "\n", $page_links );
			break;
	}
	return $r;
}

function bb_get_uri_page() {
	if ( isset($_GET['page']) && is_numeric($_GET['page']) && 1 < (int) $_GET['page'] )
		return (int) $_GET['page'];

	if ( isset($_SERVER['PATH_INFO']) )
		$path = $_SERVER['PATH_INFO'];
	else
		if ( !$path = strtok($_SERVER['REQUEST_URI'], '?') )
			return 1;

	if ( $page = strstr($path, '/page/') ) {
		$page = (int) substr($page, 6);
		if ( 1 < $page )
			return $page;
	}
	return 1;
}

//expects $item = 1 to be the first, not 0
function get_page_number( $item, $per_page = 0 ) {
	if ( !$per_page )
		$per_page = bb_get_option('page_topics');
	return intval( ceil( $item / $per_page ) ); // page 1 is the first page
}

/* Time */

function bb_timer_stop($display = 0, $precision = 3) { //if called like bb_timer_stop(1), will echo $timetotal
	global $bb_timestart, $timeend;
	$mtime = explode(' ', microtime());
	$timeend = $mtime[1] + $mtime[0];
	$timetotal = $timeend - $bb_timestart;
	if ($display)
		echo bb_number_format_i18n($timetotal, $precision);
	return bb_number_format_i18n($timetotal, $precision);
}

// GMT -> so many minutes ago
function bb_since( $original, $do_more = 0 ) {
	$today = time();

	if ( !is_numeric($original) ) {
		if ( $today < $_original = bb_gmtstrtotime( str_replace(',', ' ', $original) ) ) // Looks like bb_since was called twice
			return $original;
		else
			$original = $_original;
	}
		
	// array of time period chunks
	$chunks = array(
		array(60 * 60 * 24 * 365 , __('year') , __('years')),
		array(60 * 60 * 24 * 30 , __('month') , __('months')),
		array(60 * 60 * 24 * 7, __('week') , __('weeks')),
		array(60 * 60 * 24 , __('day') , __('days')),
		array(60 * 60 , __('hour') , __('hours')),
		array(60 , __('minute') , __('minutes')),
		array(1 , __('second') , __('seconds')),
	);

	$since = $today - $original;

	for ($i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];

		if ( 0 != $count = floor($since / $seconds) )
			break;
	}

	$print = sprintf(__('%1$d %2$s'), $count, (1 == $count) ? $name : $names);

	if ( $do_more && $i + 1 < $j) {
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if ( 0 != $count2 = floor( ($since - $seconds * $count) / $seconds2) )
			$print .= sprintf(__(', %1$d %2$s'), $count2, (1 == $count2) ? $name2 : $names2);
	}
	return $print;
}

function bb_current_time( $type = 'timestamp' ) {
	switch ($type) {
		case 'mysql':
			$d = gmdate('Y-m-d H:i:s');
			break;
		case 'timestamp':
			$d = time();
			break;
	}
	return $d;
}

// GMT -> Local
// in future versions this could eaily become a user option.
function bb_offset_time( $time, $args = null ) {
	if ( isset($args['format']) && 'since' == $args['format'] )
		return $time;
	if ( !is_numeric($time) ) {
		if ( -1 !== $_time = bb_gmtstrtotime( $time ) )
			return gmdate('Y-m-d H:i:s', $_time + bb_get_option( 'gmt_offset' ) * 3600);
		else
			return $time; // Perhaps should return -1 here
	} else {
		return $time + bb_get_option( 'gmt_offset' ) * 3600;
	}
}

/* Permalinking / URLs / Paths */

function get_path( $level = 1, $base = false, $request = false ) {
	if ( !$request )
		$request = $_SERVER['REQUEST_URI'];
	if ( is_string($request) )
		$request = parse_url($request);
	if ( !is_array($request) || !isset($request['path']) )
		return '';

	$path = rtrim($request['path'], " \t\n\r\0\x0B/");
	if ( !$base )
		$base = rtrim(bb_get_option('path'), " \t\n\r\0\x0B/");
	$path = preg_replace('|' . preg_quote($base, '|') . '/?|','',$path,1);
	if ( !$path )
		return '';
	if ( strpos($path, '/') === false )
		return '';

	$url = explode('/',$path);
	if ( !isset($url[$level]) )
		return '';

	return urldecode($url[$level]);
}

function bb_find_filename( $text ) {
	if ( preg_match('|.*?/([a-z\-]+\.php)/?.*|', $text, $matches) )
		return $matches[1];
	else {
		$path = bb_get_option( 'path' );
		$text = preg_replace("#^$path#", '', $text);
		$text = preg_replace('#/.+$#', '', $text);
		return $text . '.php';
	}
	return false;
}

function bb_send_headers() {
	if ( bb_is_user_logged_in() )
		nocache_headers();
	@header('Content-Type: ' . bb_get_option( 'html_type' ) . '; charset=' . bb_get_option( 'charset' ));
	do_action( 'bb_send_headers' );
}

function bb_pingback_header() {
	if (bb_get_option('enable_pingback'))
		@header('X-Pingback: '. bb_get_uri('xmlrpc.php', null, BB_URI_CONTEXT_HEADER + BB_URI_CONTEXT_BB_XMLRPC));
}

// Inspired by and adapted from Yung-Lung Scott YANG's http://scott.yang.id.au/2005/05/permalink-redirect/ (GPL)
function bb_repermalink() {
	global $page;
	$location = bb_get_location();
	$uri = $_SERVER['REQUEST_URI'];
	if ( isset($_GET['id']) )
		$id = $_GET['id'];
	else
		$id = get_path();
	$_original_id = $id;

	do_action( 'pre_permalink', $id );

	$id = apply_filters( 'bb_repermalink', $id );

	switch ($location) {
		case 'front-page':
			$path = null;
			$querystring = null;
			if ($page > 1) {
				if (bb_get_option( 'mod_rewrite' )) {
					$path = 'page/' . $page;
				} else {
					$querystring = array('page' => $page);
				}
			}
			$permalink = bb_get_uri($path, $querystring, BB_URI_CONTEXT_HEADER);
			$issue_404 = true;
			break;
		case 'forum-page':
			if (empty($id)) {
				$permalink = bb_get_uri(null, null, BB_URI_CONTEXT_HEADER);
				break;
			}
			global $forum_id, $forum;
			$forum     = get_forum( $id );
			$forum_id  = $forum->forum_id;
			$permalink = get_forum_link( $forum->forum_id, $page );
			break;
		case 'topic-edit-page':
		case 'topic-page':
			if (empty($id)) {
				$permalink = bb_get_uri(null, null, BB_URI_CONTEXT_HEADER);
				break;
			}
			global $topic_id, $topic;
			$topic     = get_topic( $id );
			$topic_id  = $topic->topic_id;
			$permalink = get_topic_link( $topic->topic_id, $page );
			break;
		case 'profile-page': // This handles the admin side of the profile as well.
			global $user_id, $user, $profile_hooks, $self;
			if ( isset($_GET['id']) )
				$id = $_GET['id'];
			elseif ( isset($_GET['username']) )
				$id = $_GET['username'];
			else
				$id = get_path();
			$_original_id = $id;
			
			if ( !$id )
				$user = bb_get_current_user(); // Attempt to go to the current users profile
			elseif ( !is_numeric( $id ) && is_string( $id ) )
				$user = bb_get_user_by_nicename( $id ); // Get by the user_nicename
			else
				$user = bb_get_user( $id ); // Get by the ID

			if ( !$user || ( 1 == $user->user_status && !bb_current_user_can( 'moderate' ) ) )
				bb_die(__('User not found.'), '', 404);

			$user_id = $user->ID;
			global_profile_menu_structure();
			$valid = false;
			if ( $tab = isset($_GET['tab']) ? $_GET['tab'] : get_path(2) )
				foreach ( $profile_hooks as $valid_tab => $valid_file )
					if ( $tab == $valid_tab ) {
						$valid = true;
						$self = $valid_file;
					}
			if ( $valid ) :
				$permalink = get_profile_tab_link( $user->ID, $tab, $page );
			else :
				$permalink = get_user_profile_link( $user->ID, $page );
				unset($self, $tab);
			endif;
			break;
		case 'favorites-page':
			$permalink = get_favorites_link();
			break;
		case 'tag-page': // It's not an integer and tags.php pulls double duty.
			if ( isset($_GET['tag']) )
				$id = $_GET['tag'];
			$_original_id = $id;
			if ( !$id )
				$permalink = bb_get_tag_page_link();
			else {
				global $tag, $tag_name;
				$tag_name = $id;
				$tag = bb_get_tag( (string) $tag_name );
				$permalink = bb_get_tag_link( 0, $page ); // 0 => grabs $tag from global.
			}
			break;
		case 'view-page': // Not an integer
			if ( isset($_GET['view']) )
				$id = $_GET['view'];
			else
				$id = get_path();
			$_original_id = $id;
			global $view;
			$view = $id;
			$permalink = get_view_link( $view, $page );
			break;
		default:
			return;
			break;
	}
	
	wp_parse_str($_SERVER['QUERY_STRING'], $args);
	$args = urlencode_deep($args);
	if ( $args ) {
		$permalink = add_query_arg($args, $permalink);
		if ( bb_get_option('mod_rewrite') ) {
			$pretty_args = array('id', 'page', 'tag', 'tab', 'username'); // these are already specified in the path
			if ( $location == 'view-page' )
				$pretty_args[] = 'view';
			foreach ( $pretty_args as $pretty_arg )
				$permalink = remove_query_arg( $pretty_arg, $permalink );
		}
	}

	$permalink = apply_filters( 'bb_repermalink_result', $permalink, $location );

	$domain = bb_get_option('domain');
	$domain = preg_replace('/^https?/', '', $domain);
	$check = preg_replace( '|^.*' . trim($domain, ' /' ) . '|', '', $permalink, 1 );
	$uri = rtrim( $uri, " \t\n\r\0\x0B?" );

	global $bb_log;
	$bb_log->debug($uri, 'bb_repermalink() ' . __('REQUEST_URI'));
	$bb_log->debug($check, 'bb_repermalink() ' . __('should be'));
	$bb_log->debug($permalink, 'bb_repermalink() ' . __('full permalink'));
	$bb_log->debug($_SERVER['PATH_INFO'], 'bb_repermalink() ' . __('PATH_INFO'));

	if ( $check != $uri && $check != str_replace(urlencode($_original_id), $_original_id, $uri) ) {
		if ( $issue_404 && rtrim( $check, " \t\n\r\0\x0B/" ) !== rtrim( $uri, " \t\n\r\0\x0B/" ) ) {
			status_header( 404 );
			bb_load_template( '404.php' );
		} else {
			wp_redirect( $permalink );
		}
		exit;
	}

	do_action( 'post_permalink', $permalink );
}

/* Profile/Admin */

function global_profile_menu_structure() {
	global $user_id, $profile_menu, $profile_hooks;
	// Menu item name
	// The capability required for own user to view the tab ('' to allow non logged in access)
	// The capability required for other users to view the tab ('' to allow non logged in access)
	// The URL of the item's file
	// Item name for URL (nontranslated)
	$profile_menu[0] = array(__('Edit'), 'edit_profile', 'edit_users', 'profile-edit.php', 'edit');
	$profile_menu[5] = array(__('Favorites'), 'edit_favorites', 'edit_others_favorites', 'favorites.php', 'favorites');

	// Create list of page plugin hook names the current user can access
	$profile_hooks = array();
	foreach ($profile_menu as $profile_tab)
		if ( can_access_tab( $profile_tab, bb_get_current_user_info( 'id' ), $user_id ) )
			$profile_hooks[bb_sanitize_with_dashes($profile_tab[4])] = $profile_tab[3];

	do_action('bb_profile_menu');
	ksort($profile_menu);
}

function add_profile_tab($tab_title, $users_cap, $others_cap, $file, $arg = false) {
	global $profile_menu, $profile_hooks, $user_id;

	$arg = $arg ? $arg : $tab_title;

	$profile_tab = array($tab_title, $users_cap, $others_cap, $file, $arg);
	$profile_menu[] = $profile_tab;
	if ( can_access_tab( $profile_tab, bb_get_current_user_info( 'id' ), $user_id ) )
		$profile_hooks[bb_sanitize_with_dashes($arg)] = $file;
}

function can_access_tab( $profile_tab, $viewer_id, $owner_id ) {
	global $bb_current_user;
	$viewer_id = (int) $viewer_id;
	$owner_id = (int) $owner_id;
	if ( $viewer_id == bb_get_current_user_info( 'id' ) )
		$viewer =& $bb_current_user;
	else
		$viewer = new BP_User( $viewer_id );
	if ( !$viewer )
		return '' === $profile_tab[2];

	if ( $owner_id == $viewer_id ) {
		if ( '' === $profile_tab[1] )
			return true;
		else
			return $viewer->has_cap($profile_tab[1]);
	} else {
		if ( '' === $profile_tab[2] )
			return true;
		else
			return $viewer->has_cap($profile_tab[2]);
	}
}

//meta_key => (required?, Label, hCard property).  Don't use user_{anything} as the name of your meta_key.
function get_profile_info_keys() {
	return apply_filters( 'get_profile_info_keys', array(
		'first_name' => array(0, __('First name')),
		'last_name' => array(0, __('Last name')),
		'display_name' => array(1, __('Display name as')),
		'user_email' => array(1, __('Email'), 'email'),
		'user_url' => array(0, __('Website'), 'url'),
		'from' => array(0, __('Location')),
		'occ' => array(0, __('Occupation'), 'role'),
		'interest' => array(0, __('Interests')),
	) );
}

function get_profile_admin_keys() {
	global $bbdb;
	return apply_filters( 'get_profile_admin_keys', array(
		$bbdb->prefix . 'title' => array(0, __('Custom Title'))
	) );
}

function get_assignable_caps() {
	$caps = array();
	if ( $throttle_time = bb_get_option( 'throttle_time' ) )
		$caps['throttle'] = sprintf( __('Ignore the %d second post throttling limit'), $throttle_time );
	return apply_filters( 'get_assignable_caps', $caps );
}

/* Views */

function bb_get_views() {
	global $bb_views;

	$views = array();
	foreach ( (array) $bb_views as $view => $array )
		$views[$view] = $array['title'];

	return $views;
}

function bb_register_view( $view, $title, $query_args = '', $feed = TRUE ) {
	global $bb_views;

	$view  = bb_slug_sanitize( $view );
	$title = wp_specialchars( $title );

	if ( !$view || !$title )
		return false;

	$query_args = wp_parse_args( $query_args );

	if ( !$sticky_set = isset($query_args['sticky']) )
		$query_args['sticky'] = 'no';

	$bb_views[$view]['title']  = $title;
	$bb_views[$view]['query']  = $query_args;
	$bb_views[$view]['sticky'] = !$sticky_set; // No sticky set => split into stickies and not
	$bb_views[$view]['feed'] = $feed;
	return $bb_views[$view];
}

function bb_deregister_view( $view ) {
	global $bb_views;

	$view = bb_slug_sanitize( $view );
	if ( !isset($bb_views[$view]) )
		return false;

	unset($GLOBALS['bb_views'][$view]);
	return true;
}

function bb_view_query( $view, $new_args = '' ) {
	global $bb_views;

	$view = bb_slug_sanitize( $view );
	if ( !isset($bb_views[$view]) )
		return false;

	if ( $new_args ) {
		$new_args = wp_parse_args( $new_args );
		$query_args = array_merge( $bb_views[$view]['query'], $new_args );
	} else {
		$query_args = $bb_views[$view]['query'];
	}

	return new BB_Query( 'topic', $query_args, "bb_view_$view" );
}

function bb_get_view_query_args( $view ) {
	global $bb_views;

	$view = bb_slug_sanitize( $view );
	if ( !isset($bb_views[$view]) )
		return false;

	return $bb_views[$view]['query'];
}

function bb_register_default_views() {
	// no posts (besides the first one), older than 2 hours
	bb_register_view( 'no-replies', __('Topics with no replies'), array( 'post_count' => 1, 'started' => '<' . gmdate( 'YmdH', time() - 7200 ) ) );
	bb_register_view( 'untagged'  , __('Topics with no tags')   , array( 'tag_count'  => 0 ) );
}

/* Feeds */

/**
 * Send status headers for clients supporting Conditional Get
 *
 * The function sends the Last-Modified and ETag headers for all clients. It
 * then checks both the If-None-Match and If-Modified-Since headers to see if
 * the client has used them. If so, and the ETag does matches the client ETag
 * or the last modified date sent by the client is newer or the same as the
 * generated last modified, the function sends a 304 Not Modified and exits.
 *
 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.3
 * @param string $bb_last_modified Last modified time. Must be a HTTP-date
 */
function bb_send_304( $bb_last_modified ) {
	$bb_etag = '"' . md5($bb_last_modified) . '"';
	@header("Last-Modified: $bb_last_modified");
	@header	("ETag: $bb_etag");

	// Support for Conditional GET
	if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) $client_etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
	else $client_etag = false;

	$client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE']);
	// If string is empty, return 0. If not, attempt to parse into a timestamp
	$client_modified_timestamp = $client_last_modified ? bb_gmtstrtotime($client_last_modified) : 0;

	// Make a timestamp for our most recent modification...	
	$bb_modified_timestamp = bb_gmtstrtotime($bb_last_modified);

	if ( ($client_last_modified && $client_etag) ?
		 (($client_modified_timestamp >= $bb_modified_timestamp) && ($client_etag == $bb_etag)) :
		 (($client_modified_timestamp >= $bb_modified_timestamp) || ($client_etag == $bb_etag)) ) {
		status_header( 304 );
		exit;
	}
}

/* Nonce */

function bb_nonce_url($actionurl, $action = -1) {
	return add_query_arg( '_wpnonce', bb_create_nonce( $action ), $actionurl );
}

function bb_nonce_field($action = -1, $name = "_wpnonce", $referer = true) {
	$name = attribute_escape($name);
	echo '<input type="hidden" name="' . $name . '" value="' . bb_create_nonce($action) . '" />';
	if ( $referer )
		wp_referer_field();
}

function bb_nonce_ays( $action ) {
	$title = __( 'bbPress Failure Notice' );
	$html .= "\t<div id='message' class='updated fade'>\n\t<p>" . wp_specialchars( bb_explain_nonce( $action ) ) . "</p>\n\t<p>";
	if ( wp_get_referer() )
		$html .= "<a href='" . remove_query_arg( 'updated', clean_url( wp_get_referer() ) ) . "'>" . __( 'Please try again.' ) . "</a>";
	$html .= "</p>\n\t</div>\n";
	$html .= "</body>\n</html>";
	bb_die( $html, $title );
}

function bb_install_header( $title = '', $header = false ) {
	if ( empty($title) )
		if ( function_exists('__') )
			$title = __('bbPress');
		else
			$title = 'bbPress';
		
		$uri = false;
		if ( function_exists('bb_get_uri') && !BB_INSTALLING ) {
			$uri = bb_get_uri();
			$uri_stylesheet = bb_get_uri('bb-admin/install.css', null, BB_URI_CONTEXT_LINK_STYLESHEET_HREF + BB_URI_CONTEXT_BB_INSTALLER);
			$uri_stylesheet_rtl = bb_get_uri('bb-admin/install-rtl.css', null, BB_URI_CONTEXT_LINK_STYLESHEET_HREF + BB_URI_CONTEXT_BB_INSTALLER);
			$uri_logo = bb_get_uri('bb-admin/images/install-logo.gif', null, BB_URI_CONTEXT_IMG_SRC + BB_URI_CONTEXT_BB_INSTALLER);
		}
		
		if (!$uri) {
			$uri = preg_replace('|(/bb-admin)?/[^/]+?$|', '/', $_SERVER['PHP_SELF']);
			$uri_stylesheet = $uri . 'bb-admin/install.css';
			$uri_stylesheet_rtl = $uri . 'bb-admin/install-rtl.css';
			$uri_logo = $uri . 'bb-admin/images/install-logo.gif';
		}
	
	header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php if ( function_exists( 'bb_language_attributes' ) ) bb_language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>
	<meta name="robots" content="noindex, nofollow" />
	<link rel="stylesheet" href="<?php echo $uri_stylesheet; ?>" type="text/css" />
<?php
	if ( function_exists( 'bb_get_option' ) && 'rtl' == bb_get_option( 'text_direction' ) ) {
?>
	<link rel="stylesheet" href="<?php echo $uri_stylesheet_rtl; ?>" type="text/css" />
<?php
	}
?>
</head>
<body>
	<div id="container">
		<div class="logo">
			<img src="<?php echo $uri_logo; ?>" alt="bbPress Installation" />
		</div>
<?php
	if ( !empty($header) ) {
?>
		<h1>
			<?php echo $header; ?>
		</h1>
<?php
	}
}

function bb_install_footer() {
?>
	</div>
	<p id="footer">
		<?php _e('<a href="http://bbpress.org/">bbPress</a> - simple, fast, elegant'); ?>
	</p>
</body>
</html>
<?php
}

function bb_die( $message, $title = '', $header = 0 ) {
	global $bb_locale;

	if ( $header && !headers_sent() )
		status_header( $header );

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
		if ( empty( $title ) ) {
			$error_data = $message->get_error_data();
			if ( is_array( $error_data ) && isset( $error_data['title'] ) )
				$title = $error_data['title'];
		}
		$errors = $message->get_error_messages();
		switch ( count( $errors ) ) :
		case 0 :
			$message = '';
			break;
		case 1 :
			$message = "<p>{$errors[0]}</p>";
			break;
		default :
			$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
			break;
		endswitch;
	} elseif ( is_string( $message ) ) {
		$message = "<p>$message</p>";
	}

	if ( empty($title) )
		$title = __('bbPress &rsaquo; Error');
	
	bb_install_header( $title );
?>
	<p><?php echo $message; ?></p>
<?php
	if ($uri = bb_get_uri()) {
?>
	<p class="last"><?php printf( __('Back to <a href="%s">%s</a>.'), $uri, bb_get_option( 'name' ) ); ?></p>
<?php
	}
	bb_install_footer();
	die();
}

function bb_explain_nonce($action) {
	if ( $action !== -1 && preg_match('/([a-z]+)-([a-z]+)(_(.+))?/', $action, $matches) ) {
		$verb = $matches[1];
		$noun = $matches[2];

		$trans = array();
		$trans['create']['post'] = array(__('Your attempt to submit this post has failed.'), false);
		$trans['edit']['post'] = array(__('Your attempt to edit this post has failed.'), false);
		$trans['delete']['post'] = array(__('Your attempt to delete this post has failed.'), false);

		$trans['create']['topic'] = array(__('Your attempt to create this topic has failed.'), false);
		$trans['resolve']['topic'] = array(__('Your attempt to change the resolution status of this topic has failed.'), false);
		$trans['delete']['topic'] = array(__('Your attempt to delete this topic has failed.'), false);
		$trans['close']['topic'] = array(__('Your attempt to change the status of this topic has failed.'), false);
		$trans['stick']['topic'] = array(__('Your attempt to change the sticky status of this topic has failed.'), false);
		$trans['move']['topic'] = array(__('Your attempt to move this topic has failed.'), false);

		$trans['add']['tag'] = array(__('Your attempt to add this tag to this topic has failed.'), false);
		$trans['rename']['tag'] = array(__('Your attempt to rename this tag has failed.'), false);
		$trans['merge']['tag'] = array(__('Your attempt to submit these tags has failed.'), false);
		$trans['destroy']['tag'] = array(__('Your attempt to destroy this tag has failed.'), false);
		$trans['remove']['tag'] = array(__('Your attempt to remove this tag from this topic has failed.'), false);

		$trans['toggle']['favorite'] = array(__('Your attempt to toggle your favorite status for this topic has failed.'), false);

		$trans['edit']['profile'] = array(__("Your attempt to edit this user's profile has failed."), false);

		$trans['add']['forum'] = array(__("Your attempt to add this forum has failed."), false);
		$trans['update']['forums'] = array(__("Your attempt to update your forums has failed."), false);
		$trans['delete']['forums'] = array(__("Your attempt to delete that forum has failed."), false);

		$trans['do']['counts'] = array(__("Your attempt to recount these items has failed."), false);

		$trans['switch']['theme'] = array(__("Your attempt to switch themes has failed."), false);

		if ( isset($trans[$verb][$noun]) ) {
			if ( !empty($trans[$verb][$noun][1]) ) {
				$lookup = $trans[$verb][$noun][1];
				$object = $matches[4];
				if ( 'use_id' != $lookup )
					$object = call_user_func($lookup, $object);
				return sprintf($trans[$verb][$noun][0], wp_specialchars( $object ));
			} else {
				return $trans[$verb][$noun][0];
			}
		}
	}

	return apply_filters( 'bb_explain_nonce_' . $verb . '-' . $noun, __('Your attempt to do this has failed.'), $matches[4] );
}

/* DB Helpers */

function bb_count_last_query( $query = '' ) {
	global $bbdb, $bb_last_countable_query;

	if ( $query )
		$q = $query;
	elseif ( $bb_last_countable_query )
		$q = $bb_last_countable_query;
	else
		$q = $bbdb->last_query;

	if ( false === strpos($q, 'SELECT') )
		return false;

	if ( false !== strpos($q, 'SQL_CALC_FOUND_ROWS') )
		return (int) $bbdb->get_var( "SELECT FOUND_ROWS()" );

	$q = preg_replace(
		array('/SELECT.*?\s+FROM/', '/LIMIT [0-9]+(\s*,\s*[0-9]+)?/', '/ORDER BY\s+.*$/', '/DESC/', '/ASC/'),
		array('SELECT COUNT(*) FROM', ''),
		$q
	);

	if ( preg_match( '/GROUP BY\s+(\S+)/', $q, $matches ) )
		$q = str_replace( array( 'COUNT(*)', $matches[0] ), array( "COUNT(DISTINCT $matches[1])", '' ), $q );

	if ( !$query )
		$bb_last_countable_query = '';
	return (int) $bbdb->get_var($q);
}

function no_where( $where ) {
	return;
}

/* Plugins/Themes utility */

function bb_basename($file, $directories) {
	if (strpos($file, '#') !== false)
		return $file; // It's already a basename
	foreach ($directories as $type => $directory)
		if (strpos($file, $directory) !== false)
			break; // Keep the $file and $directory set and use them below, nifty huh?
	list($file, $directory) = str_replace('\\','/', array($file, $directory));
	list($file, $directory) = preg_replace('|/+|','/', array($file,$directory));
	$file = preg_replace('|^.*' . preg_quote($directory, '|') . '|', $type . '#', $file);
	return $file;
}

/* Plugins */

function bb_plugin_basename($file) {
	return bb_basename( $file, array('user' => BB_PLUGIN_DIR, 'core' => BB_CORE_PLUGIN_DIR) );
}

function bb_register_plugin_activation_hook($file, $function) {
	$file = bb_plugin_basename($file);
	add_action('bb_activate_plugin_' . $file, $function);
}

function bb_register_plugin_deactivation_hook($file, $function) {
	$file = bb_plugin_basename($file);
	add_action('bb_deactivate_plugin_' . $file, $function);
}

function bb_get_plugin_uri( $plugin = false ) {
	if ( !$plugin ) {
		$plugin_uri = BB_PLUGIN_URL;
	} else {
		$plugin_uri = str_replace(
			array('core#', 'user#'),
			array(BB_CORE_PLUGIN_URL, BB_PLUGIN_URL),
			$plugin
		);
		$plugin_uri = dirname($plugin_uri) . '/';
	}
	return apply_filters( 'bb_get_plugin_uri', $plugin_uri, $plugin );
}

function bb_get_plugin_directory( $plugin = false, $path = false ) {
	if ( !$plugin ) {
		$plugin_directory = BB_PLUGIN_DIR;
	} else {
		$plugin_directory = str_replace(
			array('core#', 'user#'),
			array(BB_CORE_PLUGIN_DIR, BB_PLUGIN_DIR),
			$plugin
		);
		if ( !$path ) {
			$plugin_directory = dirname($plugin_directory) . '/';
		}
	}
	return apply_filters( 'bb_get_plugin_directory', $plugin_directory, $plugin, $path );
}

function bb_get_plugin_path( $plugin = false ) {
	$plugin_path = bb_get_plugin_directory( $plugin, true );
	return apply_filters( 'bb_get_plugin_path', $plugin_path, $plugin );
}

/* Themes / Templates */

function bb_get_active_theme_directory() {
	return apply_filters( 'bb_get_active_theme_directory', bb_get_theme_directory() );
}

function bb_get_theme_directory($theme = false) {
	if (!$theme) {
		$theme = bb_get_option( 'bb_active_theme' );
	}
	if ( !$theme ) {
		$theme_directory = BB_DEFAULT_THEME_DIR;
	} else {
		$theme_directory = str_replace(
			array('core#', 'user#'),
			array(BB_CORE_THEME_DIR, BB_THEME_DIR),
			$theme
		) . '/';
	}
	return $theme_directory;
}

function bb_get_themes() {
	$r = array();
	$theme_roots = array(
		'core' => BB_CORE_THEME_DIR,
		'user' => BB_THEME_DIR
	);
	foreach ( $theme_roots as $theme_root_name => $theme_root )
		if ( $themes_dir = @dir($theme_root) )
			while( ( $theme_dir = $themes_dir->read() ) !== false )
				if ( is_dir($theme_root . $theme_dir) && is_readable($theme_root . $theme_dir) && '.' != $theme_dir{0} )
					$r[$theme_root_name . '#' . $theme_dir] = $theme_root_name . '#' . $theme_dir;
	ksort($r);
	return $r;
}

function bb_theme_basename($file) {
	$file = bb_basename( $file, array('user' => BB_THEME_DIR, 'core' => BB_CORE_THEME_DIR) );
	$file = preg_replace('|/+.*|', '', $file);
	return $file;
}

function bb_register_theme_activation_hook($file, $function) {
	$file = bb_theme_basename($file);
	add_action('bb_activate_theme_' . $file, $function);
}

function bb_register_theme_deactivation_hook($file, $function) {
	$file = bb_theme_basename($file);
	add_action('bb_deactivate_theme_' . $file, $function);
}

/* Search Functions */
// NOT bbdb::prepared
function bb_user_search( $args = '' ) {
	global $bbdb, $bb_last_countable_query;

	if ( $args && is_string($args) && false === strpos($args, '=') )
		$args = array( 'query' => $args );

	$defaults = array( 'query' => '', 'append_meta' => true, 'user_login' => true, 'display_name' => true, 'user_nicename' => false, 'user_url' => true, 'user_email' => false, 'user_meta' => false, 'users_per_page' => false, 'page' => false );

	extract(wp_parse_args( $args, $defaults ), EXTR_SKIP);

	$query = trim( $query );
	if ( $query && strlen( preg_replace('/[^a-z0-9]/i', '', $query) ) < 3 )
		return new WP_Error( 'invalid-query', __('Your search term was too short') );

	if ( !$page )
		$page = $GLOBALS['page'];

	$page = (int) $page;

	$query = $bbdb->escape( $query );

	$limit = 0 < (int) $users_per_page ? (int) $users_per_page : bb_get_option( 'page_topics' );
	if ( 1 < $page )
		$limit = ($limit * ($page - 1)) . ", $limit";

	$likeit = preg_replace('/\s+/', '%', $query);

	$fields = array();

	foreach ( array('user_login', 'display_name', 'user_nicename', 'user_url', 'user_email') as $field )
		if ( $$field )
			$fields[] = $field;

	if ( $query && $user_meta ) :
		$sql = "SELECT user_id FROM $bbdb->usermeta WHERE meta_value LIKE ('%$likeit')";
		if ( empty($fields) )
			$sql .= " LIMIT $limit";
		$user_meta_ids = $bbdb->get_col($sql);
		if ( empty($fields) ) :
			bb_cache_users( $user_meta_ids );
			$users = array();
			foreach( $user_meta_ids as $user_id )
				$users[] = bb_get_user( $user_id );
			return $users;
		endif;
	endif;

	$sql = "SELECT * FROM $bbdb->users";

	$sql_terms = array();
	if ( $query )
		foreach ( $fields as $field )
			$sql_terms[] = "$field LIKE ('%$likeit%')";

	if ( isset($user_meta_ids) && $user_meta_ids )
		$sql_terms[] = "ID IN (". join(',', $user_meta_ids) . ")";

	if ( $query && empty($sql_terms) )
		return new WP_Error( 'invalid-query', __('Your query parameters are invalid') );

	$sql .= ( $sql_terms ? ' WHERE ' . implode(' OR ', $sql_terms) : '' ) . " LIMIT $limit";

	$bb_last_countable_query = $sql;

	if ( ( $users = $bbdb->get_results($sql) ) && $append_meta )
		return bb_append_meta( $users, 'user' );

	return $users ? $users : false;
}

function bb_tag_search( $args = '' ) {
	global $page, $wp_taxonomy_object;

	if ( $args && is_string($args) && false === strpos($args, '=') )
		$args = array( 'search' => $args );

	$defaults = array( 'search' => '', 'number' => false );

	$args = wp_parse_args( $args );
	if ( isset( $args['query'] ) )
		$args['search'] = $args['query'];
	if ( isset( $args['tags_per_page'] ) )
		$args['number'] = $args['tags_per_page'];
	unset($args['query'], $args['tags_per_page']);
	$args = wp_parse_args( $args, $defaults );

	extract( $args, EXTR_SKIP );

	$number = (int) $number;
	$search = trim( $search );
	if ( strlen( $search ) < 3 )
		return new WP_Error( 'invalid-query', __('Your search term was too short') );

	$number = 0 < $number ? $number : bb_get_option( 'page_topics' );
	if ( 1 < $page )
		$offset = ( intval($page) - 1 ) * $number;

	$args = array_merge( $args, compact( 'number', 'offset', 'search' ) );

	$terms = $wp_taxonomy_object->get_terms( 'bb_topic_tag', $args );
	if ( is_wp_error( $terms ) )
		return false;

	for ( $i = 0; isset($terms[$i]); $i++ )
		_bb_make_tag_compat( $terms[$i] );

	return $terms;
}

// TODO
function bb_related_tags( $_tag = false, $number = 40 ) {
	return array();

	global $bbdb, $tag;
	if ( false === $_tag )
		$_tag = $tag;
	else
		$_tag = bb_get_tag( $_tag );

	if ( !$_tag )
		return false;

	$number = (int) $number;

	$sql = $bbdb->prepare(
		"SELECT tag.tag_id, tag.tag, tag.raw_tag, COUNT(DISTINCT t.topic_id) AS tag_count
	           FROM $bbdb->tagged AS t
	           JOIN $bbdb->tagged AS tt  ON (t.topic_id = tt.topic_id)
	           JOIN $bbdb->tags   AS tag ON (t.tag_id = tag.tag_id)
	        WHERE tt.tag_id = %d AND t.tag_id != %d GROUP BY t.tag_id ORDER BY tag_count DESC LIMIT %d",
		$_tag->tag_id, $_tag->tag_id, $number
	);

	foreach ( (array) $tags = $bbdb->get_results( $sql ) as $_tag ) {
		wp_cache_add( $tag->tag, $tag, 'bb_tag' );
		wp_cache_add( $tag->tag_id, $tag->tag, 'bb_tag_id' );
	}

	return $tags;
}

/* Slugs */

function bb_slug_increment( $slug, $existing_slug, $slug_length = 255 ) {
	if ( preg_match('/^.*-([0-9]+)$/', $existing_slug, $m) )
		$number = (int) $m[1] + 1;
	else
		$number = 1;

	$r = bb_encoded_utf8_cut( $slug, $slug_length - 1 - strlen($number) );
	return apply_filters( 'bb_slug_increment', "$r-$number", $slug, $existing_slug, $slug_length );
}

function bb_get_id_from_slug( $table, $slug, $slug_length = 255 ) {
	global $bbdb;
	$tablename = $table . 's';

	list($_slug, $sql) = bb_get_sql_from_slug( $table, $slug, $slug_length );

	if ( !$_slug || !$sql )
		return 0;

	return (int) $bbdb->get_var( "SELECT ${table}_id FROM {$bbdb->$tablename} WHERE $sql" );
}

function bb_get_sql_from_slug( $table, $slug, $slug_length = 255 ) {
	global $bbdb;

	// Look for new style equiv of old style slug
	$_slug = bb_slug_sanitize( (string) $slug );
	if ( strlen( $_slug ) < 1 )
		return '';

	if ( strlen($_slug) > $slug_length && preg_match('/^.*-([0-9]+)$/', $_slug, $m) ) {
		$_slug = bb_encoded_utf8_cut( $_slug, $slug_length - 1 - strlen($number) );
		$number = (int) $m[1];
		$_slug =  "$_slug-$number";
	}

	return array( $_slug, $bbdb->prepare( "${table}_slug = %s", $_slug ) );
}	

/* Utility */

function bb_flatten_array( $array, $cut_branch = 0, $keep_child_array_keys = true ) {
	if ( !is_array($array) )
		return $array;
	
	if ( empty($array) )
		return null;
	
	$temp = array();
	foreach ( $array as $k => $v ) {
		if ( $cut_branch && $k == $cut_branch )
			continue;
		if ( is_array($v) ) {
			if ( $keep_child_array_keys ) {
				$temp[$k] = true;
			}
			$temp += bb_flatten_array($v, $cut_branch, $keep_child_array_keys);
		} else {
			$temp[$k] = $v;
		}
	}
	return $temp;
}

function bb_get_common_parts($string1 = false, $string2 = false, $delimiter = '', $reverse = false) {
	if (!$string1 || !$string2) {
		return false;
	}
	
	if ($string1 === $string2) {
		return $string1;
	}
	
	$string1_parts = explode( $delimiter, (string) $string1 );
	$string2_parts = explode( $delimiter, (string) $string2 );
	
	if ($reverse) {
		$string1_parts = array_reverse( $string1_parts );
		$string2_parts = array_reverse( $string2_parts );
		ksort( $string1_parts );
		ksort( $string2_parts );
	}
	
	$common_parts = array();
	foreach ( $string1_parts as $index => $part ) {
		if ( $string2_parts[$index] == $part ) {
			$common_parts[] = $part;
		} else {
			break;
		}
	}
	
	if (!count($common_parts)) {
		return false;
	}
	
	if ($reverse) {
		$common_parts = array_reverse( $common_parts );
	}
	
	return join( $delimiter, $common_parts );
}

function bb_get_common_domains($domain1 = false, $domain2 = false) {
	if (!$domain1 || !$domain2) {
		return false;
	}
	
	$domain1 = strtolower( preg_replace( '@^https?://([^/]+).*$@i', '$1', $domain1 ) );
	$domain2 = strtolower( preg_replace( '@^https?://([^/]+).*$@i', '$1', $domain2 ) );
	
	return bb_get_common_parts( $domain1, $domain2, '.', true );
}

function bb_get_common_paths($path1 = false, $path2 = false) {
	if (!$path1 || !$path2) {
		return false;
	}
	
	$path1 = preg_replace('@^https?://[^/]+(.*)$@i', '$1', $path1);
	$path2 = preg_replace('@^https?://[^/]+(.*)$@i', '$1', $path2);
	
	if ($path1 === $path2) {
		return $path1;
	}
	
	$path1 = trim( $path1, '/' );
	$path2 = trim( $path2, '/' );
	
	$common_path = bb_get_common_parts( $path1, $path2, '/' );
	
	if ($common_path) {
		return '/' . $common_path . '/';
	} else {
		return '/';
	}
}

function bb_match_domains($domain1 = false, $domain2 = false) {
	if (!$domain1 || !$domain2) {
		return false;
	}
	
	$domain1 = strtolower( preg_replace( '@^https?://([^/]+).*$@i', '$1', $domain1 ) );
	$domain2 = strtolower( preg_replace( '@^https?://([^/]+).*$@i', '$1', $domain2 ) );
	
	if ( (string) $domain1 === (string) $domain2 ) {
		return true;
	}
	
	return false;
}

function bb_glob($pattern) {
	// On fail return an empty array so that loops don't explode
	
	if (!$pattern)
		return array();
	
	// May break if pattern contains forward slashes
	$directory = dirname( $pattern );
	
	if (!$directory)
		return array();
	
	if (!file_exists($directory))
		return array();
	
	if (!is_dir($directory))
		return array();
	
	if (!function_exists('glob'))
		return array();
	
	if (!is_callable('glob'))
		return array();
	
	$glob = glob($pattern);
	
	if (!is_array($glob))
		$glob = array();
	
	return $glob;
}
