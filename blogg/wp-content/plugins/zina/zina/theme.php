<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * ZINA (Zina is not Andromeda)
 *
 * Zina is a graphical interface to your MP3 collection, a personal
 * jukebox, an MP3 streamer. It can run on its own, embeded into an
 * existing website, or as a Drupal/Joomla/Wordpress/etc. module.
 *
 * http://www.pancake.org/zina
 * Author: Ryan Lathouwers <ryanlath@pacbell.net>
 * Support: http://sourceforge.net/projects/zina/
 * License: GNU GPL2 <http://www.gnu.org/copyleft/gpl.html>
 *
 * TODO:
 *  - organize this file
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/*
 * YOU SHOULD NOT MODIFY THIS FILE...
 *
 * You should override all these functions in your own theme:
 *  1) Copy a theme folder
 *  2) in MYTHEME/index.php copy any function you want to
 *     override into that file
 *  3) rename that function to MYTHEME_function()
 *      - e.g. ztheme_icon -> MYTHEME_icon
 *  4) modify that function to suit your needs
 *  x) An easy way to see what a function is passing is zdbg($var);
 */

/*
 * Complete Page (Standalone)
 */
function ztheme_page_complete($zina) {
	$theme_path = $zina['theme_path'];

	#todo: ie-fix is very theme specific...put it there!
	$output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
		'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><title>'.$zina['title'].'</title>'.
		'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.
		'<link rel="shortcut icon" href="'.$theme_path.'/zina.ico" type="image/x-icon" />'.
		'<link rel="stylesheet" href="'.$theme_path.'/index.css" type="text/css" />'.
		'<!--[if lt IE 7]><link type="text/css" rel="stylesheet" media="all" href="'.$theme_path.'/fix-ie.css" /><![endif]-->'.
		$zina['head_css'].$zina['head_js'].'</head><body>';

	$output .= '<div id="header-region" class="clear-block"></div>'.
		'<div id="wrapper"><div id="container" class="clear-block">'.
		'<div id="header"><h1>'.$zina['title'].'</h1><div class="header-right">';

		if (isset($zina['dir_year']) || isset($zina['dir_genre'])) {
			$output .= '<span class="title_details">';
			if (isset($zina['dir_genre'])) $output .= $zina['dir_genre'];
			if (!empty($zina['dir_year'])) $output .= ' ('.$zina['dir_year'].')';
			$output .= '</span>';
		}

	$output .= '</div></div>'.
      '<div id="center"><div class="right-corner"><div class="left-corner">'.
		'<div class="breadcrumb">'.ztheme('breadcrumb',$zina['breadcrumb']).'</div><div class="breadcrumb-right">';

	if (isset($zina['admin_config'])) {
		$output .= zl(ztheme('icon','config.gif',zt('Settings')),$zina['admin_config']['path'],$zina['admin_config']['query']);
	}
	$lang['login'] = zt('Login');
	$lang['logout'] = zt('Logout');
	if (isset($zina['login'])) {
		$output .= zl(ztheme('icon',$zina['login']['type'].'.gif',$lang[$zina['login']['type']]), $zina['login']['path'], $zina['login']['query']);
	}

	$output .= '</div><div id="zina" class="clear-block">';
	if (!isset($zina['popup'])) {
		$output .= '<div class="subheader clear-block"><div class="subheader-left">'.$zina['searchform'].'</div>'.
			'<div class="subheader-right">'.$zina['randomplayform'].'</div></div>';
	}

	$output .= '<div id="zina_messages">'.$zina['messages'].'</div>'.$zina['content'].ztheme('page_footer',$zina).
		'</div></div></div></div></div></div></body></html>';

	return $output;
}

function ztheme_page_main($zina) {
	$path = $zina['path'];
	$output = '';

	if (isset($zina['category'])) {
		if (!empty($zina['description'])) {
			$output .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">'.
				'<tr><td valign="top" width="100%"><p>'.$zina['description'].'</p>'.
				'</td></tr></table>';
		}

		if (isset($zina['category']) && !empty($zina['category']['sort'])) {
			$output .= '<table width="100%"><tr><td width="25%">'.$zina['category']['sort'].'</td>';
			if (isset($zina['category']['navigation'])) {
				$output .= '<td width="50%" align="center">'.$zina['category']['navigation'].'</td>'.
					'<td width="25%"></td>';
			}
			$output .= '</tr></table>';
		} else {
			if (isset($zina['category']['navigation'])) $output .= $zina['category']['navigation'];
		}

		$output .= ''.$zina['category']['content'];

		if (isset($zina['category']['navigation'])) $output .= $zina['category']['navigation'];
	} else {
		$output .= '<div class="section">';
		$subdirs = ($zina['subdir_num'] > 0);
		$altdirs = ($zina['alt_num'] > 0);

		if ($subdirs || $altdirs || isset($zina['alt_list_edit'])) {
			$output .= '<div class="directory_list">';
			$form_content = '';
			if ($subdirs) $form_content .= ztheme('directory_list', $zina['subdirs'], $zina['subdir_truncate']);

			if ($altdirs || isset($zina['alt_list_edit'])) {
				$form_content .= ztheme('directory_alt_list', $zina['alt_items'], $zina['subdir_truncate']);
				if (isset($zina['alt_list_edit'])) {
					$form_content .= '<div class="small">'.zl(zt('Edit "See Also"'),$path,$zina['alt_list_edit']['query']).'</div>';
				}
			}
			$output .= (!empty($zina['list_form_opts'])) ? ztheme('form', $zina['list_form_opts'], $form_content.$zina['list_form']) : $form_content;
			if ($zina['amg']) {
				$output .= '<div class="amg">'.ztheme('amg',$zina).'</div>';
			}
			$output .= '</div>';
		}
		if (isset($zina['dir_image'])) $output .= '<div class="directory-image">'.$zina['dir_image'].'</div>';
		if (isset($zina['dir_opts'])) {
			$output .= '<p class="dir-opts">'.ztheme('dir_opts',$zina['dir_opts'],$zina['html_title']).'</p>';
		}
		$votes = (isset($zina['dir_rating']) && $zina['dir_rating']['sum_votes'] > 0);
		if ($votes || isset($zina['dir_rate'])) {
			$output .= '<p class="small">';
			if ($votes) {
				$output .= ztheme('rating_display', $zina['dir_rating']['sum_rating']).
					ztheme('votes_display',$zina['dir_rating']['sum_votes']);
				if (isset($zina['dir_rate'])) $output .= '&nbsp; | ';
			}
			if (isset($zina['dir_rate'])) $output .= zt('Rate').': '.$zina['dir_rate'];
			$output .= '</p>';
		}

		if (isset($zina['description'])) $output .= $zina['description'];
		if (isset($zina['podcast'])) $output .= '<p>'.zl(ztheme('icon','podcast.gif',zt('Podcast')),$zina['podcast']['url'],$zina['podcast']['query']).'</p>';

		if (isset($zina['dir_edit_opts'])) {
			$lang['dir'] = zt('Edit Text');
			$lang['podcast'] = zt('Edit Podcast');
			$lang['regen'] = zt('Update database');
			$options = array();
			foreach ($zina['dir_edit_opts'] as $opt => $opts) {
				$options[] = zl($lang[$opt],$opts['path'], $opts['query']);
			}
			$output .= '<p class="small">';
			$output .= implode(' | ', $options);
			$output .= '</p>';
		}

		$output .= '</div>';

		if ($subdirs) {
			$output .= '<div class="section">'.ztheme('image_list', $zina['subdirs'], $zina['subdir_cols']).'</div>';
		}

		if ($altdirs) {
			$output .= '<div class="section">'.ztheme('image_alt_list', $zina['alt_items'], $zina['subdir_cols']).'</div>';
		}
	}

	if (isset($zina['songs'])) $output .= '<div class="section">'.$zina['songs'].'</div>';

	if (isset($zina['multimedia'])) {
		$output .= '<div class="section">'.
			ztheme('multimedia_section', ztheme('multimedia_list', $zina['multimedia'])).
			'</div>';
	}
	return $output;
}

#todo: try <ul>
function ztheme_category($dirs, $cols, $images = false, $truncate = 25) {
	$output = '';
	if (!empty($dirs)) {

		if ($images) {
			return ztheme('image_list_category', $dirs, $cols);
		}

		$i = $row = 0;
		$items = sizeof($dirs);
		$rows_in_col = ceil($items/$cols);
		if ($rows_in_col < $cols) $cols = ceil($items/$rows_in_col);
		$col_width = round(100/$cols);
		$img_new = ztheme('icon','new.gif',zt('New'));

		$output .= '<table width="100%" class="category"><tr>';

		foreach ($dirs as $opts) {
			if ($row == 0) {
				$output .= '<td'.(($cols != ++$i) ? ' class="category_cols"' : '').' valign="top" nowrap="nowrap" width="'.$col_width.'%">';
			}
			$title = ztheme('truncate', $opts['title'], $truncate);
			$output .= zl($title,$opts['path'],$opts['query']);
			if ($opts['new']) $output .= '&nbsp;'.$img_new;
			$output .= '<br/>';

			$row = ++$row % $rows_in_col;
			if ($row == 0) $output .= '</td>';
		}
		if ($row != 0) $output .= '</td>';
		$output .= '</tr></table>';
	}
	return $output;
}

/*
 * Category Pages Navigation
 *
 * e.g. Start - First - 1 - 2 - Next - Last
 */
function ztheme_category_pages($path, $current, $total, $query='') {
	$output = '';
	if ($total > 1) {
		$output .= '<div class="category_pages">';
		if ($current != 1) {
			$output .= zl(ztheme('icon','first.gif',zt('First')),$path,$query.'page=1');
			$output .= zl(ztheme('icon','back.gif',zt('Previous')),$path,$query.'page='.($current-1));
		} else {
			$output .= ztheme('icon','first_un.gif',zt('First'));
			$output .= ztheme('icon','back_un.gif',zt('Previous'));
		}
		$output .= '&nbsp;';
		for($i=1;$i<=$total;$i++) {
			$arr[] = ($current == $i) ? "<strong>$i</strong>" : zl($i,$path,$query.'page='.$i);
		}
		$output .= implode(' &middot; ',$arr);
		$output .= '&nbsp;';
		if ($current == $total) {
			$output .= ztheme('icon','forward_un.gif',zt('Next'));
			$output .= ztheme('icon','last_un.gif',zt('Last'));
		} else {
			$output .= zl(ztheme('icon','forward.gif',zt('Next')),$path,$query.'page='.($current+1));
			$output .= zl(ztheme('icon','last.gif',zt('Last')),$path,$query.'page='.$total);
		}
		$output .= '</div>';
	}
	return $output;
}

/*
 * Category Alphabet Navigation
 *
 * e.g. A - B - C - D...
 */
function ztheme_category_alphabet($path, $current, $letters, $query='') {
	$output = '<div class="category_pages">';
	foreach ($letters as $key) {
		$letter = ($key == 'zzz') ? zt('Other') : $key;
		$links[] = ($current == $key) ? "<strong>$letter</strong>" : zl($letter,$path,$query.'page='.$key);
	}
	$output .= implode(' &middot; ',$links);
	return $output.'</div>';
}

/*
 * Category Split by Alphabet helper
 */
function ztheme_category_alphabet_split($dirs) {
	$splits = array();
	foreach ($dirs as $key=>$item) {
		$key = basename($key);
		if (preg_match('/^(the |a |an)/i',$key, $matches)) {
			# ignore certain thing like "the" and "a"
			$letter = strtoupper(substr($key, strlen($matches[1]), 1));
		} elseif (preg_match('/^(\W|\d)/', $key, $matches)) {
			# non-alpha characters get grouped together...
			$letter = 'zzz';
		} else {
			# upper and lowercase are same
			$letter = strtoupper($key[0]);
		}
		$splits[$letter][$key] = $item;
	}
	ksort($splits);
	return $splits;
}

/*
 * Category Sort Alpha/Date
 */
function ztheme_category_sort($path, $cat) {
	$lang['asc'] = zt('Sort Ascending');
	$lang['desc'] = zt('Sort Descending');
	return '<span class="small">'.zt('Sort').': '.
		zl(zt('Alpha'), $path, $cat['alpha']['query']).' '.
		zl(ztheme('icon','sort_'.$cat['alpha']['sort'].'.gif', $lang[$cat['alpha']['sort']]), $path,$cat['alpha']['query']).
		'&nbsp;'.
		zl(zt('Date'), $path, $cat['date']['query']).' '.
		zl(ztheme('icon','sort_'.$cat['date']['sort'].'.gif',$lang[$cat['date']['sort']]),$path,$cat['date']['query']).'</span>';
}

function ztheme_directory_list($dirs, $truncate=25) {
	$output='';
	if ($dirs) {
		$output .= '<h3>'.zt('Albums').'</h3>';
		$output .= ztheme('directory_list_helper', $dirs, $truncate);
	}
	return $output;
}

function ztheme_directory_alt_list($dirs, $truncate=25) {
	$output='';
		$output .= '<h3>'.zt('See Also').'</h3>';
	if ($dirs) {
		$output .= ztheme('directory_list_helper', $dirs, $truncate);
	}
	return $output;
}

function ztheme_directory_list_helper($dirs, $truncate=25) {
	$lang['play']      = zt('Play');
	$lang['play_lofi'] = zt('Play Low Fidelity');
	$lang['download']  = zt('Download');
	$lang['play_rec'] = zt('Play recursively');
	$lang['play_rec_rand'] = zt('Play recursively random');

	$img_new = ztheme('icon','new.gif',zt('New'));
	$output = '<ul>';
	foreach ($dirs as $opts) {
		$output .= '<li>';
		if (isset($opts['opts'])) {
			foreach ($opts['opts'] as $type=>$opt) {
				$output .= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query']);
			}
		}

		if (isset($opts['checkbox'])) $output .= ztheme('form_checkbox', $opts['checkbox']['name'], $opts['checkbox']['value'], $opts['checkbox']['checked']).'&nbsp;';
		$title = ztheme('truncate', $opts['title'], $truncate);

		$output .= zl($title,$opts['path']);
		if (!empty($opts['info']->year)) $output .= ' ('.$opts['info']->year.')';
		if ($opts['new']) $output .= '&nbsp;'.$img_new;
		$output .= '</li>';
	}
	$output .= '</ul>';
	return $output;
}

function ztheme_image_list($dirs, $cols) {
	return '<h3>'.zt('Albums').'</h3>'.ztheme('image_list_helper', $dirs, $cols);
}

function ztheme_image_alt_list($dirs, $cols) {
	return '<h3>'.zt('See Also').'</h3>'.ztheme('image_list_helper', $dirs, $cols);
}

function ztheme_image_list_helper($dirs, $cols) {
	$col = 0;
	$col_width = round(100/$cols).'%';

	$align[$cols - 1] = 'right';
	$align[0] = 'left';
	for($i=1; $i < $cols - 1; $i++) $align[$i] = 'center';

	$first = 1;
	$output = '<table cellpadding="5" width="100%">';
	foreach($dirs as $dir => $opts) {
		if ($col==0) {
			if ($first) {
				$output .= '<tr>';
				$first = false;
			} else {
				$output .= '</tr><tr>';
			}
		}
		#$title = $opts['title'];

		$output .= '<td width="'.$col_width.'" align="'.$align[$col].'" valign="top">'.$opts['image'].'</td>';
		/*
		 * outputs a play icon over album covers
		$output .= '<td width="'.$col_width.'" align="'.$align[$col].'" valign="top">'.
			$opts['image'].'<span class="image-inner">'.zl(ztheme('icon','play.gif',zt('Play')),$dir,'l=8&amp;m=0').'</span></td>';
		 */

		if ($col==$cols) $output .= '</tr>';
		$col = ++$col % $cols;
	}

	if ($col <> 0 ) {
		for ($i = $col; $i < $cols; $i++) $output .= '<td width="'.$col_width.'">&nbsp;</td>';
	}
	$output .= '</tr></table>';

	return $output;
}

function ztheme_image_list_category($dirs, $cols) {
	$col = 0;
	$col_width = round(100/$cols).'%';

	$output = '<table cellpadding="0" cellspacing="0" width="100%" class="image-list-category">';
	foreach($dirs as $dir => $opts) {
		if ($col==0) { $row = $row1 = '<tr>'; }
		$row .= '<td width="'.$col_width.'" align="center">'.$opts['image'].'</td>';
		$row1 .= '<td width="'.$col_width.'" align="center" valign="top"><h3>'.zl($opts['title'], $opts['path'], $opts['query']).'</h3></td>';
		
		if (++$col==$cols) { $output .= $row.'</tr>'.$row1.'</tr>'; }
		$col = $col % $cols;
	}

	if ($col <> 0 ) {
		for ($i = $col; $i < $cols; $i++) {
			$row .= '<td width="'.$col_width.'">&nbsp;</td>';
			$row1 .= '<td width="'.$col_width.'">&nbsp;</td>';
		}
		$output .= $row.'</tr>'.$row1.'</tr>';
	}
	$output .= '</table>';
	return $output;
}

#TODO: rename? and then have song_list call "genreic" func?
function ztheme_song_list($songs, $various = false, $images = false, $stats = false) {
	if (empty($songs)) return '';

	$img_new = ztheme('icon','new.gif',zt('New'));
	$lang['download']  = zt('Download');
	$lang['play']      = zt('Play');
	$lang['play_lofi'] = zt('Play Low Fidelity');
	$lang['edit']      = zt('Edit');
	$lang['more']      = zt('More');
	#remove next two - > used to be for playlists...test?
	$lang['rename']    = zt('Rename');
	$lang['delete']    = zt('Delete');

	$lang['play_lofi'] = zt('Play low fidelity');
	$lang['play_lofi_custom'] = zt('Play custom low fidelity');
	$lang['play_rec'] = zt('Play recursively');
	$lang['play_rec_rand'] = zt('Play recursively random');

	$i = $stat_prev = 0;
	$p = 1;

	$output = '<table cellpadding="5" cellspacing="0" width="100%">';

	foreach($songs as $song) {
		#todo: $class = ++$i % 2 == 0 ? 'even' : 'odd';
		$v = (isset($song['description']) && !$images) ? ' valign="top"' : '';
		$output .= '<tr class="row'.$i.'"'.$v.'><td nowrap="nowrap" class="song'.$i.'">';
		if (isset($song['opts'])) {
			foreach ($song['opts'] as $type=>$opt) {
				$output .= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query']);
			}
		}
		if (isset($song['extras'])) {
			foreach ($song['extras'] as $type=>$extra) {
				$output .= zl(ztheme('icon',$type.'.gif',$extra['text']),$extra['path'],$extra['query'],NULL,FALSE,$extra['attr']);
			}
		}
		if (isset($song['opts_edit']) || isset($song['extras_edit'])) {
			$output .= '</td><td nowrap="nowrap" class="song'.$i.'">';
		}
		if (isset($song['opts_edit'])) {
			foreach ($song['opts_edit'] as $type=>$opt) {
				$output .= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query']);
			}
		}
		if (isset($song['extras_edit'])) {
			foreach ($song['extras_edit'] as $type=>$extra) {
				$output .= zl(ztheme('icon',$type.'_edit.gif',$extra['text']),$extra['path'],$extra['query'],NULL,FALSE,$extra['attr']);
			}
		}
		$output .= '</td><td nowrap="nowrap" class="song'.$i.'">';
		if ($song['checkbox']) {
			$output .= ztheme('form_checkbox', $song['checkbox']['name'], $song['checkbox']['value'], $song['checkbox']['checked']);
		}
		#if (isset($song['track'])) $output .= '</td><td align="right" nowrap="nowrap" class="song'.$i.'">'. $song['track'];

		if ($images) {
			$output .= '</td><td class="song'.$i.'"'.$v.'>';
			if (isset($song['image']) && !empty($song['image'])) {
				$output .= '<div class="search-results">';
				if (isset($song['title_link'])) {
					$output .= zl($song['image'],$song['title_link']['path'],$song['title_link']['query']);
				} else {
					$ouput .= $song['image'];
				}
			}
		}
		
		$td = '<td align="right" nowrap="nowrap" class="song_info'.$i.'"'.$v.'>';

		if ($stats) {
			$output .= $td.(($stat_prev == $song['stat']) ? '' : $p++.'.');
			$stat_prev = $song['stat'];
		}

		$output .= '</td><td width="100%" class="song'.$i.'"'.$v.'>';

		$title = $song['title'];
		if ($various) {
			if (isset($song['info']->artist)) $title = $song['info']->artist.' - '.$title;
		}
		if (isset($song['title_link'])) {
			$output .= zl($title,$song['title_link']['path'],$song['title_link']['query']);
		} else {
			$output .= '<span class="song_title">'.$title.'</span>';
		}
		if ($song['new']) $output .= $img_new;
		if (isset($song['description'])) {
			$output .= '<br/><span class="song_blurb'.$i.'">'.$song['description'].'</span>';
		}
		$output .= '</td>';

		if ($stats) {
			if ($song['stat_type'] == 'votes' || $song['stat_type'] == 'rating') {
				$output .= $td.'<span id="zina_rating">'.ztheme('votes_display',$song['votes']).'</span></td>';
				$output .= '<td nowrap="nowrap" class="song_info'.$i.'"'.$v.'>'.ztheme('rating_display', $song['stat']).'</td>';
			} elseif ($song['stat_type'] == 'latest' || $song['stat_type'] == 'added') {
				$output .= $td.ztheme('stat_date', $song['stat']).'</td>';
			} else {
				$output .= $td.number_format($song['stat']).'</td>';
			}
		} else {
			if ($song['ratings']) {
				$output .= $td.'<span id="zina_rating">'.ztheme('votes_display',$song['sum_votes']).'</span></td>';
				$output .= '<td nowrap="nowrap" class="song_info'.$i.'"'.$v.'>'.
					'<div class="stars-song">'.ztheme('rating_display', $song['sum_rating']).'</div></td>';

				if (isset($song['full_path']))
					$output .= $td.'<div class="stars-song">'.ztheme('vote', zina_get_vote_url($song['full_path']), $song['user_rating']).'</div></td>';
				else
					$output .= $td.'&nbsp;'.'</td>';
				/*
				 * If you are using statistics, there are also
				 * $song['sum_plays'] & $songs['sum_downloads']
				 */
			} else {
					$output .= $td.'&nbsp;'.'</td>';
					$output .= $td.'&nbsp;'.'</td>';
					$output .= $td.'&nbsp;'.'</td>';
			}

			if (isset($song['info']->info)) {
				$info = &$song['info'];
				$output .= $td.((isset($info->time) && !empty($info->time)) ? $info->time : '&nbsp;').'</td>';
				$output .= $td.((isset($info->bitrate) && !empty($info->bitrate)) ? $info->bitrate.' kbps' : '&nbsp;').'</td>';
				$output .= $td.((isset($info->frequency) && !empty($info->frequency)) ? round($info->frequency/1000,1).' kHz' : '&nbsp;').'</td>';
				$output .= $td.((isset($info->filesize) && !empty($info->filesize)) ? sprintf("%.2f", round($info->filesize/1000000,2)).' Mb' : '&nbsp;').'</td>';
			} else {
				$output .= $td.'</td>';
				$output .= $td.'</td>';
				$output .= $td.'</td>';
				$output .= $td.((isset($song['info']->filesize)) ? sprintf("%.2f", round($song['info']->filesize/1000000,2)).' Mb' : '&nbsp;').'</td>';
			}
		}
		$output .= '</tr>';
		$i = ++$i % 2;
	}
	zevenodd($i);
	$output .= '</table>';
	return $output;
}

function ztheme_stats_list($items, $various = false, $images = false) {
	return ztheme_song_list($items, $various, $images, true);
}

function ztheme_breadcrumb($links) {
	return implode(' >&nbsp;',$links);
}

function ztheme_breadcrumb_home($links) {
	return implode(' / ', $links);
}

function ztheme_page_title($links) {
	return strip_tags(implode(' &#8226; ',$links));
}

function ztheme_search_song_titles($titles) {
	return implode(' / ', $titles);
}

/*
 * Theme file to use for missing image
 * $type is 'dir' or 'sub'
 */
function ztheme_missing_image($type) {
	return 'missing_'.$type.'.jpg';
}

/*
 * Theme for rating (currently "stars")
 * $rating is float with one decimal place, e.g. 4.5
 */
function ztheme_rating_display($rating) {
	$output='';
	if ($rating > 0) {
		$output .= '<span class="stars">';
		$star = ztheme('icon','star.gif','*');
		$rating = number_format($rating,1);
		$arr = explode('.',$rating);
		$output .= str_repeat($star, $arr[0]);
		if ($arr[1] >= 5) $output .= ztheme('icon','star-half.gif','½');
		$output .= '</span>';
	}
	return $output;
}

/*
 * Theme for voting
 *
 * currently "stars" and utilizes ajax
 */
function ztheme_vote($url, $user_rating = 0) {
	static $num = 0;
	if ($num == 0) zina_set_js('inline', ztheme('vote_js').'var zv = new Array();');
	$pr = 'zv'.$num;

	zina_set_js('inline', 'zv['.$num.']=\''.$url.'\';');
	$remove = '';
	if ($user_rating > 0) {
		$remove = zl(ztheme('icon','delete.gif',zt('Remove Vote')),'javascript: void 0;', null, null, null, ' onClick="ajax(zv['.$num.']+\'0\',\''.$pr.'\');"');
	}

	$output = '<span id="'.$pr.'">'.$remove.ztheme('icon','stars-0.gif',NULL,NULL,'name="'.$pr.'" height="14" width="70" usemap="#'.$pr.'map"').
		'</span>'.
		'<map name="'.$pr.'map">';
	for($i=1;$i<=5;$i++) {
		$output .= '<area coords="'.(($i-1)*14).',0,'.($i*14).',14" onMouseOver="imgOn(\''.$pr.'\','.$i.
			');" onMouseOut="imgOff(\''.$pr.'\');" title="'.$i.'" alt="'.$i.'" '.
			'href="javascript: void 0;" onClick="vote(\''.$pr.'\','.$i.');ajax('.
			'zv['.$num.']+\''.$i.'\',\''.$pr.'\');">';
	}
	$output .= '</map>';
	if ($user_rating > 0) $output .= '<script type="text/javascript">vote(\''.$pr.'\','.$user_rating.');</script>';
	$num++;
	return $output;
}

/*
 * Star voting javascript
 *
 * gets put in html head
 */
function ztheme_vote_js() {
	$theme = zpath_to_theme().'/icons/stars-';
	$output = '';

	for($i=0;$i<=5;$i++) {
		$output .= "onImgArr[$i]=new Image(70,14);onImgArr[$i].src=zImgBase+'$i.gif';";
	}

	return 'var onImgArr=new Array();var zImgBase=\''.$theme.'\';'.
		$output.'var aVote=new Array();'.
		'function imgOn(img,i){if(aVote[img]==null)document.images[img].src=onImgArr[i].src;}'.
		'function imgOff(img){if(aVote[img]==null)document.images[img].src=zImgBase+\'0.gif\';}'.
		'function vote(img,i){aVote[img]=null;imgOn(img,i);aVote[img]=i;}';
}
/*
 * Voting plurality
 *
 * "(1 vote)" or "(2 votes)"
 */
function ztheme_votes_display($votes) {
	if (!empty($votes)) {
		return ' ('.$votes.' '.(($votes == 1) ? zt('vote') : zt('votes')) .')';
	}
	return '';
}

function ztheme_genres($dirs, $cols, $images, $truncate, $navigation = false) {
	$output = '';
	if ($navigation) $output .= $navigation;

	$output .= ztheme('category', $dirs, $cols, $images, $truncate);
	return $output;
}

function ztheme_multimedia_section($list) {
	return '<h3>'.zt('Multimedia').'</h3>'.$list;
}

function ztheme_stats_page($form_attr, $stats, $stat_opts) {
	if (empty($stats)) {
		return '<h3>'.zt('No data found for this period.').'</h3>';
	}
	return ztheme('form', $form_attr, $stats.'<div class="section">'.$stat_opts.'</div>');
}

function ztheme_stats_section($title, $list, $rss_path = false) {
	if ($rss_path) {
		return '<div class="section"><div class="stats">'.
			'<div class="stats-left"><h3>'.$title.'</h3></div>'.
			'<div class="stats-right">'.zl(ztheme('icon','rss.png',zt('RSS Feed')),$rss_path).'</div></div>'.
			$list.'</div>';

	} else {
		return '<div class="section"><h3>'.$title.'</h3>'.$list.'</div>';
	}
}

function ztheme_stat_date($time) {
	return date("M j, Y g:i a", $time);
}


/*
 * NEXT THREE FUNCTIONS:
 * Take an array, expect an array back
 *  - unset ones you don't want
 *  - reorder, change text, etc.
 */
function ztheme_stats_pages($pages) { return $pages; }
function ztheme_stats_periods($periods) { return $periods; }
function ztheme_stats_types($types) { return $types; }

function ztheme_stats_list_opts($items, $form) {
	return ztheme('song_list_opts',$items, $form);
}

function ztheme_search_list($items, $images = false) {
	return ztheme('song_list',$items, false, $images);
}

function ztheme_playlists_list($items, $reorder = false) {
	if (empty($items)) return '';

	if ($reorder) {
		zina_add_tabledrag('table-playlist', 'order', 'sibling', 'order');
		zina_set_css('file', 'extras/tabledrag.css');
	}

	$songs = &$items;

	$lang['download']  = zt('Download');
	$lang['play']      = zt('Play');
	$lang['more']      = zt('More');
	$lang['rename']    = zt('Rename');
	$lang['delete']    = zt('Delete');

	$i = 0;
	$j = 1;
	$output = '<table cellpadding="5" cellspacing="0" id="table-playlist">';
	foreach($songs as $song) {
		$v = (isset($song['description'])) ? ' valign="top"' : '';
		$output .= '<tr class="draggable row'.$i.'"'.$v.' id="'.($j).'"><td nowrap="nowrap" width="90" class="song'.$i.'">';
		if (isset($song['opts'])) {
			foreach ($song['opts'] as $type=>$opt) {
				$output .= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query']);
			}
		}

		if (isset($song['checkbox'])) {
			$output .= '</td><td nowrap="nowrap" width="20" class="song'.$i.'">'.
				ztheme('form_checkbox', $song['checkbox']['name'], $song['checkbox']['value'], $song['checkbox']['checked']).'</td><td style="display:none;">'.
				'<input type="hidden" name="order[]" value="'.($j).'" size="2" class="order" />';
		}
		$output .= '</td><td class="song'.$i.'"'.$v.'>';

		$title = $song['title'];

		if (isset($song['title_link'])) {
			$output .= zl($title,$song['title_link']['path'],$song['title_link']['query']);
		} else {
			$output .= $title;
		}
		$output .= '</td>';
		$output .= '</tr>';
		$i = ++$i % 2;
		$j++;
	}
	zevenodd($i);
	$output .= '</table>';

	return $output;
}

function ztheme_form_checkbox($name, $value, $check = false) {
	$checked = ($check) ? ' checked="checked"' : '';
	return '<input type="checkbox" name="'.$name.'" value="'.$value.'"'.$checked.'/>';
}

function ztheme_form_hidden($name, $value) {
	return '<input type="hidden" name="'.$name.'" value="'.$value.'"/>';
}

function ztheme_multimedia_list($items) {
	if (empty($items)) return '';
	$img_new = ztheme('icon','new.gif',zt('New'));
	$lang['edit'] = zt('Edit');

	$i = 0;
	$output = '<table cellpadding="5" cellspacing="0" width="100%">';
	foreach($items as $song) {
		$v = (isset($song['description'])) ? ' valign="top"' : '';
		$output .= '<tr class="row'.$i.'"'.$v.'><td nowrap="nowrap" class="song'.$i.'">';
		if (isset($song['opts'])) {
			if (isset($song['opts']['play']) && $song['opts']['play']['local']) {
				$output .= zl(ztheme('icon','mm/'.$song['ext'].'.gif',zt('Play')),$song['opts']['play']['path'],$song['opts']['play']['query']);
			}
			if (isset($song['opts']['download'])) {
				$icon = (isset($song['opts']['play']) && $song['opts']['play']['local']) ? 'download' : 'mm/'.$song['ext'];
				$output .= zl(ztheme('icon',$icon.'.gif',zt('Download')),$song['opts']['download']['path'],$song['opts']['download']['query']);
			}
		}

		if (isset($song['opts_edit'])) {
			$output .= '</td><td nowrap="nowrap" class="song'.$i.'">';
			foreach ($song['opts_edit'] as $type=>$opt) {
				$output .= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query']);
			}
		}

		$output .= '</td><td width="100%" nowrap="nowrap" class="song'.$i.'"'.$v.'>';
		$title = $song['title'];

		if (isset($song['title_link'])) {
			$output .= zl($title,$song['title_link']['path'],$song['title_link']['query']);
		} else {
			$output .= $title;
		}
		if ($song['new']) $output .= $img_new;
		if (isset($song['description'])) {
			$output .= '<br/><span class="song_blurb'.$i.'">'.$song['description'].'</span>';
		}
		$output .= '</td>';
		$output .= '</tr>';
		$i = ++$i % 2;
	}
	zevenodd($i);
	$output .= '</table>';
	return $output;
}

function ztheme_playlists_section($title,$form_attr,$list,$list_opts, $opts = array()) {

	$lang['play'] = zt('Play');
	$lang['playlist'] = zt('View Playlists');
	$lang['rename'] = zt('Rename');
	$lang['delete'] = zt('Delete');

	$output = '<h3>'.$title.'</h3>';
	if (!empty($opts)) {
		$output .= '<div>';
		foreach ($opts as $type => $opt) {
			$output .= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query']);
		}
		$output .= '</div>';
	}
	$output .= ztheme('form', $form_attr, $list.$list_opts);
	return $output;

}

function ztheme_form($attr, $content, $method = 'post') {
	return '<form method="'.$method.'" '.$attr.'>'.$content.zina_get_form_token().'</form>';
}

function ztheme_song_section($form_attr,$song_list,$song_list_opts) {
	return '<h3>'.zt('Songs').'</h3>'.ztheme('form', $form_attr, $song_list.$song_list_opts);

}

function ztheme_search_page($results, $total, $navigation, $form_attr, $list, $list_opts) {
	$output = '<h3>'.zt('Search Results').'</h3>';
	if (empty($results)) {
		$output .= '<p><em>'.zt('0 total items found').'</em></p>';
	} else {
		$output .= '<p><em>'.zt('@total items found', array('@total'=>$total)).'</em></p>';
		if ($navigation) $output .= $navigation;
		$output .= ztheme('form', $form_attr, $list.$list_opts);
	}
	return $output;
}

function ztheme_genre_page($genre, $results, $total, $navigation, $genre_navigation, $form_attr, $list, $list_opts, $opts) {
	$output = '';
	if (empty($results)) {
		$output .= '<h3>'.zt('Search Results').'</h3>';
		$output .= '<p><em>'.zt('0 total items found').'</em></p>';
	} else {

		$output .= '<div class="genre-page clear-block">';
		if ($opts['image']) $output .= ztheme('image', zina_get_genre_image_path($genre), $genre, null, 'class="genre-image"');
		if ($genre_navigation) $output .= '<div class="genre-select">'.$genre_navigation.'</div>';
		#$output .= '<h3>'.zt($genre).'</h3>';
		$output .= '<p>'.
			zl(ztheme('icon','play.gif',zt('Play')),'',$opts['play_query']).
			zl(zt('Play @genre', array('@genre'=>$genre)),'', $opts['play_query']).
			'</p>';
		$output .= '<p><em>'.zt('@total items found', array('@total'=>$total)).'</em></p>';

		if ($opts['description'] && !empty($opts['description'])) {
			$output .= '<p>'.$opts['description'].'</p>';
		}
		if ($opts['genre_edit']) {
			$output .= '<p class="small">'.zl(zt('Edit Genre'),$opts['genre_edit']['path'], $opts['genre_edit']['query']).'</p>';
		}

		$output .= '</div>';
		if ($navigation) $output .= $navigation;
		$output .= ztheme('form', $form_attr, $list.$list_opts);
	}
	return $output;
}

function ztheme_playlist_list_opts($items, $form) {
	if (empty($items)) return '';
	$submit = ($items['submit']) ?  '<input type="submit" value="'.zt('Update Playlist').'"/>' : '';
	if ($items['checkbox']) {
		$checkbox = ' '.zt('Check').': &nbsp;'.
		zl(zt('All'),'javascript: void 0;',NULL,NULL,FALSE,' onClick="CheckBoxes(\''.$form.'\',true);"').' | '.
		zl(zt('None'),'javascript:void 0;',NULL,NULL,FALSE,' onclick="CheckBoxes(\''.$form.'\',false);"');
	} else {
		$checkbox = '';
	}

	$output = '<table cellpadding="5" cellspacing="0" width="100%"><tr class="row'.zevenodd().'">';
	$output .= '<td>'.$submit.$checkbox.'</td>';
	$output .= '<td align="right">'.((isset($items['playlist_form_elements'])) ? $items['playlist_form_elements'] : '&nbsp;').'</td>';
	$output .= '</tr></table>';

	return $output;
}

function ztheme_search_list_opts($items, $form) {
	return ztheme('song_list_opts',$items, $form);
}

function ztheme_song_list_opts($items, $form) {
	if (empty($items)) return '';

	$output = '<table cellpadding="5" cellspacing="0" width="100%"><tr class="row'.zevenodd().'">';
	if (isset($items['checkboxes'])) {
		$submit = '';
		if (isset($items['submit'])) {
			$lang = ($items['submit']['exists']) ? zt('Update custom playlist') : zt('Create custom playlist');
			$submit = '<input type="submit" value="'.$lang.'" onClick="'.$items['submit']['js'].'"/>&nbsp;';
			#$submit = '<a href="javascript:'.$items['submit']['js'].'">'.$lang.'</a>&nbsp;';
		}
		$output .= '<td>'.$submit.zt('Check').': &nbsp;'.
			zl(zt('All'),'javascript: void 0;',NULL,NULL,FALSE,' onClick="CheckBoxes(\''.$form.'\',true);"').' | '.
			zl(zt('None'),'javascript:void 0;',NULL,NULL,FALSE,' onclick="CheckBoxes(\''.$form.'\',false);"').'</td>';
	}

	if (isset($items['opts'])) {
		$lang['download']  = zt('Download');
		$lang['play']      = zt('Play');
		$lang['play_lofi'] = zt('Play Low Fidelity');
		$lang['play_rec_rand'] = zt('Play random');
		$output .= '<td >'.zt('Selected').': ';
		foreach($items['opts'] as $type=>$opt) {
			$output.= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query'],NULL,FALSE,$opt['attr']);
		}
		$output .= '</td>';
	}
	$output .= '<td align="right">'.((isset($items['playlist_form_elements'])) ? $items['playlist_form_elements'] : '&nbsp;').'</td>';
	$output .= '</tr></table>';
	return $output;
}

function ztheme_song_extra($type, $title, $content) {
	return '<div class="extras '.$type.'"><h3>'.$title.'</h3><p>'.$content.'</p></div>';
}

function ztheme_page_footer($zina) {
	return '<div class="footer">'.zl(ztheme('icon','zina.gif','Zina Icon','Powered by Zina v.'.ZINA_VERSION.
		' ('.round(microtime(true) - $zina['time'],3).'s'. ((isset($zina['cached'])) ? ' cached' : '') .')'
		),'http://www.pancake.org/zina').'</div>';
}

function ztheme_amg($zina) {
	$selected = array_fill(1,3,'');
	$count = substr_count($zina['path'], '/')+1;
	$selected[$count] = ' selected="selected"';
	return '<form action="http://www.allmusic.com/cg/amg.dll" method="post" target="AMG">'.
		'<input type="hidden" name="P" value="amg"/>'.
		'<input type="text" name="sql" size="20" maxlength="30" value="'.$zina['html_title'].'"/>'.
		'<select name="opt1"><option value="1"'.$selected[1].'>'.$zina['lang']['main_dir_title'].'</option>'.
		'<option value="2"'.$selected[2].'>'.zt('Albums').'</option>'.
		'<option value="3"'.$selected[3].'>'.zt('Songs').'</option>'.
		'</select><input type="submit" value="AMG"/></form>';
}

/*
 * Directory Play/Download Options (icons,text,link)
 */
function ztheme_dir_opts($options, $title){
	if (empty($options)) return null;

	$lang['play'] = zt('Play');
	$lang['play_custom'] = zt('Play custom playlist');
	$lang['play_lofi'] = zt('Play low fidelity');
	$lang['play_lofi_custom'] = zt('Play custom low fidelity');
	$lang['play_rec'] = zt('Play recursively');
	$lang['play_rec_rand'] = zt('Play recursively random');
	$lang['download']  = zt('Download');
	$lang['download_custom']  = zt('Download custom files');

	$output = '';
	foreach ($options as $type=>$opt) {
		$output .= zl(ztheme('icon',$type.'.gif',$lang[$type]),$opt['path'],$opt['query']);
	}

	# Play "Directory" Text / Link
	if (isset($options['play'])) {
		$output .= ' '.zl(zt('Play !title',array('!title'=>$title)),$options['play']['path'],$options['play']['query']);
	} elseif (isset($options['play_rec'])) {
		$output .= ' '.zl(zt('Play !title',array('!title'=>$title)),$options['play_rec']['path'],$options['play_rec']['query']);
	} elseif (isset($options['play_rec_rand'])) {
		$output .= ' '.zl(zt('Play !title',array('!title'=>$title)),$options['play_rec_rand']['path'],$options['play_rec_rand']['query']);
	} else {
		$output .= $title;
	}

	return $output;
}

/*
 * Login Display
 */
function ztheme_login($form) {
	#todo: css this
	return '<div style="width:300px;margin:auto;">'.ztheme('form_table', $form).'</div>';
}

function ztheme_newplaylist($form) {
	return ztheme('form_table', $form);
}
function ztheme_config($form) {
	return ztheme('form_table', $form);
}
function ztheme_clean($form) {
	return ztheme('form_table', $form);
}

function ztheme_blurb($form) {
	return ztheme('form_table', $form);
}

function ztheme_renameplaylist($form) {
	return ztheme('form_table', $form);
}

function ztheme_messages($display = NULL) {
  $output = '';
  foreach (zina_get_messages($display) as $type => $messages) {
    $output .= "<div class=\"messages $type\">";
    if (count($messages) > 1) {
		$output .= ztheme('list', $messages);
    } else {
      $output .= $messages[0];
    }
    $output .= "</div>";
  }
  return $output;
}

function ztheme_list($list) {
	$output = '';
	if (!empty($list)) {
		$output .= '<ul>';
		foreach ($list as $item) {
			$output .= '<li>'. $item .'</li>';
		}
		$output .= '</ul>';
	}
	return $output;
}

/*
 * For error messages...
 */
function ztheme_placeholder($text) {
  return '<em>'. zcheck_plain($text) .'</em>';
}

/*
 * Zina Generic Form/Table Display
 *
 * Mainly administrative displays
 */
function ztheme_form_table($form) {
	$output='';
	if (isset($form['title'])) $output .= '<h3>'.$form['title'].'</h3>';

	$output .= '<table>';
	foreach($form['rows'] as $row) {
		$output .= '<tr>';

		if (!empty($row['label'])) {
			if ($row['label'] == -1) {
				$output .= '<td colspan="2">';
			} else {
				$output .= '<td valign="top" valign="top">'.$row['label'].':';
				if (isset($row['desc'])) $output .= '<div class="small">'.$row['desc'].'</div>';

				$output .= '</td><td valign="top">';
			}
		} else {
			$output .= '<td>&nbsp;</td><td>';
		}

		$output .= $row['item'].'</td></tr>';
		#if (isset($row['desc'])) $output .= '<tr><td colspan="2"><span class="small">'.$row['desc'].'</span></td></tr>';
	}
	$output .= '</table>';
	if (isset($form['hidden'])) $output .= $form['hidden'];
	return ztheme('form', $form['attr'], $output);
}

function ztheme_page_image($zina, $path, $images, $image, $captions = array()) {
	return '<div class="directory-image-full" align="center">'.
		ztheme('images', 'full', $zina, $path, $images, null, $image, $captions).
		'</div>';
}

/*
 * Directory Image
 */
function ztheme_images($type, $zina, $path, $images, $query = null, $image = null, $captions = array()) {
   $output = '';
	$num = sizeof($images);
	if ($num > 1) {
		#if ($type != 'full') $captions = array();
		$img_js3 = '';
		$img_js = 'zImages=new Array(';
		$img_js2 = 'zImagesURL=new Array(';
		if (!empty($captions)) $img_js3 = 'zImagesText=new Array(';

		foreach($images as $img) {
			$img_js .= '"'.str_replace("&amp;","&",zina_get_image_url($path, $img, $type)).'",';
			$img_js2 .= '"'.zurl((empty($path) ? $img : $path.'/'.$img), $query).'",';
			if (!empty($captions)) $img_js3 .= '"'.addslashes($captions[$img]).'",';
		}
		$img_js = substr($img_js,0,-1).');';
		$img_js2 = substr($img_js2,0,-1).');';
		if (!empty($img_js3)) $img_js3 = substr($img_js3,0,-1).');';

		if (($key = array_search($image, $images)) === false) {
			$key = 0;
		} else {
			$img_js .= 'zi='.$key.';';
		}
		zina_set_js('inline', $img_js.$img_js2.$img_js3);

		$image = ztheme('image',zina_get_image_url($path, $images[$key], $type), $zina['title'], $zina['title'],'id="zImage" class="directory-image"');

		if ($type == 'full') {
			$output .= zl($image, 'javascript:nextImage()');
		} else {
			$output .= zl($image, 'JavaScript:viewImage();');
		}
		
		if (!empty($captions))
			$output .= '<div id="zImageText" class="directory-image-'.$type.'-text">'.$captions[$images[$key]].'</div>';

		$output .= '<div class="image-browser">'.
			zl(zt('Previous'),'javascript:prevImage();').' | '.
			zl(zt('Stop'),'javascript:stopSlideShow();').' | '.
			zl(zt('Start'),'javascript:startSlideShow(1800);').' | '.
			zl(zt('Next'),'javascript:nextImage();').'</div>';
		return $output;
	} else {
		#if (empty($images[0])) { #missing image
		$file = (($num == 1) ? $images[0] : $images);
		$image = ztheme('image', zina_get_image_url($path, $file, $type), $zina['title']);
		if ($type == 'full' || empty($images[0])) {
			return $image;
		} else {
			return zl($image, ((empty($path)) ? $file : $path.'/'.$file), $query);
		}
	}
}

function ztheme_mm($player, $src) {
	return '<div align="center">'.ztheme('mm_'.$player, $src, 600, 450).'</div>';
}
/*
 * Only seems to work if src is actual www path to file =(
 */
function ztheme_mm_QT($src, $w, $h) {
	return '<object CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" WIDTH="'.$w.'" HEIGHT="'.$h.'" CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0">'.
		'<param NAME="controller" VALUE="true">'.
		'<param NAME="autoplay" VALUE="true">'.
		'<param NAME="pluginspage" VALUE="http://www.apple.com/quicktime/download/">'.
		'<param NAME="src" VALUE="'.$src.'">'.
		'<embed WIDTH="'.$w.'" HEIGHT="'.$h.'" AUTOPLAY="true" SRC="'.$src.'" '.
			'PLUGINSPAGE="http://www.apple.com/quicktime/download"></embed>'.
		'</object>';
}

/*
 * Only seems to work if src is actual www path to file =(
 */
function ztheme_mm_WMP($src, $w, $h) {
	return '<OBJECT ID=MediaPlayer '.
		'CLASSID=CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6 '.
		'TYPE="application/x-oleobject" width="'.$w.'" height="'.$h.'">'.
		'<PARAM NAME="url" VALUE="'.$src.'">'.
		'<embed type="application/x-mplayer2" '.
		'pluginspage="http://www.microsoft.com/windows/windowsmedia/download/" '.
		'filename="'.$src.'" src="'.$src.'" '.
		'autosize="1" '.
		'width="'.$w.'" height="'.$h.'"> </embed>' .
		'</OBJECT>';
}
/*
 * Search Form
 */
function ztheme_searchform($opts, $term='') {
	$js = '';

	if (!function_exists('drupal_get_js')) zina_set_js('file', 'extras/jquery.js');
	zina_set_js('file', 'extras/jquery.autocomplete.js');
	zina_set_css('file', 'extras/jquery.autocomplete.css');

	$js = '$().ready(function(){'.
		'function zinaSearchItem(row){';

	if (isset($opts['images'])) {
		$js .= 'var output = \'<div class="zina-live-search">\';'.
			'if (row[6] != ""){output += row[6];}'.
			'output += \'<p>\'+row[0]+\'<br><span class="ac_results-sub">\';'.
			'if (row[2] != \'\'){output += row[2]+"<br>";}'.
			'output += row[1].charAt(0).toUpperCase()+row[1].substr(1).toLowerCase();'.
			'if (row[5]!=\'\')output += \' | \'+row[5];'.
			'return output += "</span></p></div>";';
	} else {
		$js .= 'var output = row[0]+\'<br><span class="ac_results-sub">\'+row[1].charAt(0).toUpperCase()+row[1].substr(1).toLowerCase();'.
			'if (row[2] != \'\') output += \' | \'+ row[2];'.
			'output += \'</span>\';'.
			'return output;';
	}
	$js .= '}'.
		'$("#remotesearch").autocomplete("'.$opts['live_url'].'", {'.
			'max:'.$opts['search_live_limit'].','.
			'minChars:'.$opts['search_min_chars'].','.
			'selectFirst:false,'.
			'scroll:false,'.
			'cacheLength:20,'.
			'width:350,'.
			'formatItem:zinaSearchItem'.
		'});'.
			
		'$("#remotesearch").result(function(event,data,formatted){'.
			'if(data){'.
				'$("input#searchtype").val(data[1]);'.
				'$("input#searchid").val(data[3]);'.
				'$("img.icon-search-play").css("display","inline");'.
			'}'.
		'});'.
		'$("form#zinasearch").submit(function(){'.
			'searchClear=true;'.
		'});'.
	'});';

	if (empty($term)) {
		$term = zt('Search');
		$searchClear = 'true';
	} else {
		$searchClear = 'false';
	}
 
	$js .= 'var searchClear='.$searchClear.';function zinaClearSearch(e){if(searchClear){e.value="";document.forms.zinasearch.searchid.value="";searchClear=false;}}';
	zina_set_js('inline', $js);

	return '<div class="search-form">'.
		ztheme('form', 'id="zinasearch" action="'.$opts['action'].'"',
			'<input name="searchterm" type="text" size="25" value="'.$term.'" id="remotesearch" onClick="zinaClearSearch(this);" />'.
			'<input name="searchtype" type="hidden" id="searchtype" />'.
			'<input name="searchid" type="hidden" id="searchid" />'.
			zl(ztheme('icon','more.gif',zt('More')),"javascript:zinaSubmit('zinasearch','m=browse');").
			zl(ztheme('image', zpath_to_theme().'/icons/play.gif', zt('Play'), zt('Play'), 'style="display:none;" class="icon icon-search-play"'),"javascript:zinaSubmit('zinasearch','m=play');")
		).'</div>';
}

function ztheme_search_description($path_links, $opts) {
	$output = (!empty($path_links)) ? $path_links.'<br/>' : '';
	if (isset($opts['genre'])) $opts['genre'] = '<em>'.$opts['genre'].'</em>';
	$output .= implode(' | ', $opts);
	return $output;
}

function ztheme_genre_hierarchy($genres, $next_id, $attr) {
 	zina_add_tabledrag('genres', 'match', 'parent', 'genre-pid', 'genre-pid', 'genre-id', TRUE, 8);
	zina_set_css('file', 'extras/tabledrag.css');
	
	$output = '';
	$output .= '<script type="text/javascript">'.
		'var zinaNextGenreID='.$next_id.';'.
		'function zinaAddTableRow(table){'.
		'var lastRow=$(table+\' tr:last\').clone();'.
		"$('input.genre-id',lastRow).val(zinaNextGenreID);".	
		"$('input.genre-pid',lastRow).val(0);".	
		"$('select.genre-weight',lastRow).val(0);".	
		"$('input.genre',lastRow).val('');".	
		"$('span.genre-hidden',lastRow).css('display','block');".	
		"$('span.genre-title',lastRow).css('display','none');".	
		"$('span.genre-operations',lastRow).css('display','none');".	
		"$('input.genre',lastRow).attr('name','id:'+zinaNextGenreID+'\[genre\]');".	
		"$('input.genre-pid',lastRow).attr('name','id:'+zinaNextGenreID+'\[pid\]');".	
		"$('input.genre-id',lastRow).attr('name','id:'+zinaNextGenreID+'\[id\]');".	
		"$('select.genre-weight',lastRow).attr('name','id:'+zinaNextGenreID+'\[weight\]');".	
		"$(table).removeClass('tabledrag-processed');".

		'$(table).append(lastRow);'.
		"$('div.warning').remove();".
		"$('a').remove('.tabledrag-handle');".

		'Drupal.behaviors.tableDrag();'.
		'zinaNextGenreID++;'.
		'return true;'.
		'}</script>';
	
	#$delta = (int)(sizeof($genres)/2)+25;
	$delta = 1;

	$output .= '<p>'.zt('If you create a hierarchy here, it will be used when playing random songs via genres.').'</p>';
	$output .= '<ul><li><a href="#" onclick="zinaAddTableRow(\'#genres\'); return false;">'.zt('Add a genre').'</a></li></ul>';

	$output .= '<table id="genres">';
	$output .= '<thead><tr><th>Genre</th><th>Operations</th></thead></tr><tbody>';
	foreach($genres as $genre) {
		$id = $genre['id'];
		$row = '<tr class="draggable odd"><td>'.ztheme('indentation',substr_count($genre['path'],'/'));
		$input = '<input type="text" name="id:'.$id.'[genre]" class="genre" value="'.htmlentities($genre['genre']).'">';
		if ($genre['actual']) {
			$row .= '<span class="genre-title">'.zl(htmlentities($genre['genre']),'','l=13&pl='.rawurlencode($genre['genre'])) .'</span><span style="display:none;" class="genre-hidden">'.$input.'</span>';
		} else {
			$row .= $input;
		}
		$row .= '</td>';
		$row .= '<td style="display:none;"><select name="id:'.$id.'[weight]" class="genre-weight">'.
			ztheme('delta',$delta, $genre['weight']).'</select>'.
			'<input type="hidden" name="id:'.$id.'[pid]" value="'.$genre['pid'].'" class="genre-pid">'.
			'<input type="hidden" name="id:'.$id.'[id]" value="'.$id.'" class="genre-id"></td>';
		
		$row .= '<td><span class="genre-operations">';
		if (!$genre['actual'])$row .= '<a href="'.zurl('','l=52&amp;m='.$id).'">'.zt('Delete').'</a>';
		$row .= '</span></td>'.
			'</tr>';
		$output .= $row;
	}
	$output .= '</tbody></table>';
	$output .= '<input type="Submit" value="Save Settings">';

	return ztheme('form',$attr, $output);
}

function ztheme_delta($delta, $item) {
	$output = '';
	for ($i = (-1 * $delta); $i <= $delta; $i++) {
		$output .= '<option value="'.$i.'"'.(($item == $i) ? ' selected="selected"':'').'>'.$i.'</option>';
	}	
	return $output;
}

/*
 * Genre search select
 */
function ztheme_genreform($display, $action, $genres, $current) {
	$output = '';
	if ($display) {
		$form_id = 'zinagenreform';
		$output = '<form id="'.$form_id.'" method="post" action="'.$action.'">'.
			'<select name="pl" onchange="this.form.submit();">';
		if (!empty($genres)) {
			foreach($genres as $genre) {
				$sel = ($genre == $current) ? ' selected="selected"' : '';
				$output .= '<option value="'.$genre.'"'.$sel.'>'.htmlentities($genre).'</option>';
			}
		}
		$output .= '</select>&nbsp;'.zl(ztheme('icon','more.gif',zt('More')),'javascript:document.forms.'.$form_id.'.submit();').'</form>';
	}

	return $output;
}

/*
 * Playlist form elements/options
 */
function ztheme_playlist_form_elements($display, $form, $options, $opts_url, $playlists) {
	$output = '';
	if ($display) {
		$output = '<div class="playlist-form-elements">';
		$elements = array(
			'd'=>zl(ztheme('icon','delete.gif',zt('Delete')),$opts_url['d']),
			'a'=>zl(ztheme('icon','add.gif',zt('Add To')),$opts_url['a']),
			'v'=>zl(ztheme('icon','more.gif',zt('More')),$opts_url['v']),
			'p'=>zl(ztheme('icon','play.gif',zt('Play')),$opts_url['p']),
			'q'=>zl(ztheme('icon','play_lofi.gif',zt('Play Low Fidelity')),$opts_url['q']),
			'l'=>zl(ztheme('icon','playlist.gif',zt('Playlists')),'',$opts_url['l']),
			'x'=>$playlists
			);

		foreach($options as $opt) {
			$output .= $elements[$opt];
		}
		$output .= '</div>';
	}
	return $output;
}

function ztheme_playlist_form_select($name, $playlists, $current, $new, $session) {
	$output = '<select name="'.$name.'">';
	if ($new) {
		$output .= '<option value="new_zina_list"> - '.zt('New Playlist').' - </option>';
	}

	if ($session) {
		$output .= '<option value="zina_session_playlist"> - '.zt('Session Playlist').' - </option>';
	}

	if (!empty($playlists)) {
		foreach($playlists as $playlist) {
			$selected = ($playlist == $current) ? " selected='selected'" : '';
			$output .= '<option value="'.$playlist.'"'.$selected.'>'.ztheme('truncate', $playlist,15).'</option>';
		}
	}
	$output .= '</select>';
	return $output;
}

function ztheme_admin_section_header($title, $anchor, $query = null) {
	$output = '<a name="'.$anchor.'"></a><div class="cfg-cat"><span class="cfg-left">'.$title.'</span>';
	if ($query) {
		$output .= '<span class="cfg-right">'.zl(zt('Top'),'',$query,'top').'</span>';
	}
	$output .= '</div>';
	return $output;
}

/*
 * Random drop-down form
 *
 * array key numbers are critical (e.g. 4,5,11,12)
 * todo: pass array keys?
 * todo: but, pass less crap, wtf?
 */
function ztheme_random($zc, $db, $genres, $action, $url_low, $url_play) {
	#key == m=
	$r_type = array(4=>zt('Albums'),5=>zt('Songs'));
	if ($db && $zc['rating_random']) {
		$r_type[11] = zt('Songs (via Rated Albums)');
		$r_type[12] = zt('Songs (via Rated Artists)');
	}
	$all = $zc['cache'] ? '<option value="0">'.zt('All') : '';
	$low = '';
	if ($zc['low']) {
		$low .= zl(ztheme('icon','play_lofi.gif',zt('Play Low Fidelity')),"javascript:SubmitForm('random','".$url_low."');");
	}
	$form_id = 'zinarandomform';
	$output = '<form id="'.$form_id.'" method="post" action="'.$action.'"><span class="small">'.zt('Random').': </span>'.
		'<select name="n">';
	foreach($zc['ran_opts'] as $option) {
		$selected = '';
		if ($option == $zc['ran_opts_def']) $selected = ' selected="selected"';
		$output .= '<option value="'.$option.'"'.$selected.'>';
		$output .= ($option != 0) ? $option : zt('All');
		$output .= '</option>';
	}
	$output .= '</select><select name="m">';
	foreach($r_type as $key=>$type) {
		$selected = '';
		if ($key == 5) $selected = ' selected="selected"';
		$output .= '<option value="'.$key.'"'.$selected.'>'.$type.'</option>';
	}
	$output .= '</select>';

	if (!empty($genres)) {
		$output .= '<select name="playlist"><option value="zina" selected>'.zt('All').'</option>';
		foreach($genres as $genre) {
			$output .= '<option value="'.$genre.'">'.ztheme('truncate', $genre, 20).'</option>';
		}
		$output .= '</select>';
	}

	if ($db && $zc['rating_random']) {
		$opts = explode(',',$zc['rating_random_opts']);
		$output .= '<select name="rating"><option value="zina" selected>'.zt('All').'</option>';
		foreach ($opts as $opt) $output .= '<option value="'.$opt.'">'.$opt.'</option>';
		$output .= '</select>';
	}

	$output .= zl(ztheme('icon','play.gif',zt('Play')),"javascript:SubmitForm('$form_id','".$url_play."');").
		$low.'</form>';

	return $output;
}
/*
 * Optional for statistic blocks
 */
function ztheme_zina_block($items, $type) {
	if (!empty($items)) {
		$output = '<ul>';
		foreach($items as $item) {
			$output .= '<li>';
			if ($type == 'f') {
				$output .= (isset($item['url'])) ? '<a href="'.$item['url'].'">'.$item['filename'].'</a>' : $title;
			} else {
				$output .= $item['display'];
			}
			$output .= '</li>';
		}
		$output .= '</ul>';
	} else {
		$output = zt('No results');
	}
	return $output;
}

/*
 * Following four functions for formating
 */
function ztheme_artist_song($song, $artist = false) {
	return ($artist) ? $artist.' - '.$song : $song;
}

function ztheme_title($title) {
	return str_replace('_',' ',
		preg_replace('/^[0-9][0-9][ ]?-[ ]?/i','',htmlentities($title))
	);
}

function ztheme_song_title($song) {
	return str_replace('_',' ',
		preg_replace('/^.*?[0-9][0-9][ ]?-[ ]?/i','',htmlentities(rtrim($song,"\r\n")))
	);
}

function ztheme_truncate($str, $length, $replace = '...') {
	return (strlen($str) > $length) ? substr($str,0,$length).$replace : $str;
}

/*
 * Sitemap
 */
function ztheme_sitemap($items) {
	$output = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
		'<urlset xmlns="http://www.google.com/schemas/sitemap/0.84"	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
		'xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">'."\n";

	foreach($items as $item) {
		$output .= "\t<url>\n".
			"\t\t<loc>".$item['url']."</loc>\n".
			"\t\t<lastmod>".date('Y-m-d\TH:i:s',$item['mtime'])."</lastmod>\n".
			"\t\t<changefreq>".(time() - $item['mtime'])."</changefreq>\n".
			"\t\t<priority>".((isset($item['priority'])) ? $item['priority'] : .5 )."</priority>\n".
			"\t</url>\n";
	}
	$output .= '</urlset>'."\n";
	return $output;
}

function ztheme_admin_nav($nav) {
	return implode(' | ', $nav);
}

/*
 * RSS
 */
function ztheme_rss($zina, $podcast = true) {
	global $zc;
	$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
		'<?xml-stylesheet href="'.zpath_to_theme().'/rss.xsl" type="text/xsl" media="screen"?>'."\n";

	if ($podcast)
		$xml .= '<rss xmlns:itunes="http://example.com/DTDs/Podcast-1.0.dtd" version="2.0">'."\n";
	else 
		$xml .= '<rss version="2.0">'."\n";

	$xml .= '<cssurl>'.zpath_to_theme().'/rss.css</cssurl>'."\n";

	$xml .='<channel>'."\n".
		'<title>'.$zina['title'].'</title>'."\n".
		'<link rel="alternate" type="text/html" href="'.$zina['page_url'].'" />'."\n".
		'<link rel="self" href="'.$zina['link_url'].'" type="application/rss+xml" />'."\n";

	if (isset($zina['desc'])) {
		$xml .= '<description>'.$zina['desc'].'</description>'."\n";
		if ($podcast) $xml .= '<itunes:summary>'.$zina['desc'].'</itunes:summary>'."\n";
	}

	$xml .= '<language>'.$zina['lang_code'].'</language>'."\n".
		#'<pubDate>'.date('r').'</pubDate>'."\n".
		'<lastBuildDate>'.date('r').'</lastBuildDate>'."\n";

	if (isset($zina['image_url']) && $zina['image_url']) {
		$xml .= '<image>'."\n".
			'<title>'.$zina['title'].'</title>'."\n".
			'<link>'.$zina['image_url'].'</link>'."\n".
			'<url>'.$zina['page_url'].'</url>'."\n".
			'</image>'."\n";
		if ($podcast) $xml .= '<itunes:image>'.$zina['image_url'].'</itunes:image>'."\n";
	}

	if ($zina['items']) {
		foreach ($zina['items'] as $item) {
			$xml .= '<item>'."\n".
				'<title>'.$item['title'].'</title>'."\n";
			if (isset($item['url'])) $xml .= '<link>'.$item['url'].'</link>'."\n";
			if (isset($item['description']) && $item['description']) {
				$xml .= '<description>'.$item['description'].'</description>'."\n";
				if ($podcast) $xml .= '<itunes:subtitle>'.$item['description'].'</itunes:subtitle>'."\n";
			}

			if (isset($item['url'])) $xml .= '<guid>'.$item['url'].'</guid>'."\n";
			if (isset($item['pub'])) $xml .= '<pubDate>'.$item['pub'].'</pubDate>'."\n";
			if (isset($item['stat'])) $xml .= '<stat>'.$item['stat'].'</stat>'."\n";

			if ($podcast) {
				$xml .= '<enclosure url="'.$item['url'].'" length="'.$item['size'].'" type="'.$item['type'].'"/>'."\n".
					'<itunes:author>'.$item['artist'].'</itunes:author>'."\n".
					'<itunes:duration>'.$item['duration'].'</itunes:duration>'."\n";
			}
			$xml .= '</item>'."\n";
		}
	}

	$xml .= '</channel>'."\n".'</rss>';

	return $xml;
}

function ztheme_artist_album($path, $rss = false) {
	$x = explode('/', $path);
	$len = sizeof($x);

	if ($len == 2) {
		if ($rss) 
			return zina_rss_decode(ztheme('title',$x[0])).' - '.zina_rss_decode(ztheme('title', $x[1]));
		else
			return ztheme('title',$x[0]).' - '.ztheme('title', $x[1]);
	} else {
		return ($rss) ? zina_rss_decode(ztheme('title', $path)) : ztheme('title', $path);
	}
}

function ztheme_indentation($size = 1) {
  $output = '';
  for ($n = 0; $n < $size; $n++) {
    $output .= '<div class="indentation">&nbsp;</div>';
  }
  return $output;
}

/*
 * Generic Image Tag
 */
function ztheme_image($src, $alt='', $title='', $attributes='') {
	if (empty($title)) $title = $alt;
	return '<img src="'.$src.'" alt="'.htmlspecialchars($alt).'" title="'.htmlspecialchars($title).'" '.$attributes.'/>';
}

/*
 * Generic Icon
 */
function ztheme_icon($file, $alt='', $title='', $attributes='') {
	return ztheme('image', zpath_to_theme().'/icons/'.$file, $alt, $title, $attributes.' class="icon"');
}

/*
 * Genre filename (path is THEME/images
 */
function ztheme_image_genre($genre) {
	return 'genre_'.strtolower($genre).'.jpg';
}

/*
 * Keeps track of row number for alternating row colors on separate tables
 */
function zevenodd($i=null) {
	static $class = 0;
	if (empty($i)) return $class;
	$class = $i;
}
?>
