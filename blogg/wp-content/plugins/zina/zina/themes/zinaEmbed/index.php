<?php
# you can override default theme functions by including them here

function zinaEmbed_page_complete($zina) {
	$embed = $zina['embed'];

	$output = '<div id="zina" class="clear-block '.$embed.'">';

	if ($embed == 'drupal') {
		$output .= '<table border="0" cellpadding="5" cellspacing="0" width="100%" class="drupal"><tr>'.
			'<td nowrap="nowrap" valign="top" width="34%">';

		if (isset($zina['dir_year']) || isset($zina['dir_genre'])) {
			$output .= '<span class="zina-title-details">';
			if (isset($zina['dir_genre'])) $output .= $zina['dir_genre'];
			if (!empty($zina['dir_year'])) $output .= ' ('.$zina['dir_year'].')';
			$output .= '</span>';
		}
		$output .= '</td><td align="center" nowrap="nowrap" valign="top" width="33%">'.$zina['searchform'].'</td>'.
			'<td align="right" nowrap="nowrap" valign="top" width="33%">'.$zina['randomplayform'].'</td></tr></table>';

	} else {
		$title = '<h1>'.$zina['title'].'</h1>';

		switch ($embed) {
			case 'wordpress':
		#wordpress
		# - H2
				break;
			case 'joomla':
				$title = '<div class="componentheading">'.$zina['title'].'</div>';
				break;
			default:
		}

		$output .= '<div class="zina-header"><div class="zina-header-left">'.$title;

			if (isset($zina['dir_year']) || isset($zina['dir_genre'])) {
				$output .= '<div class="zina-title-details">';
				if (isset($zina['dir_genre'])) $output .= $zina['dir_genre'];
				if (!empty($zina['dir_year'])) $output .= ' ('.$zina['dir_year'].')';
				$output .= '</div>';
			}
		$output .= '</div><div class="zina-header-right">';
		$output .= $zina['searchform'];

		if (isset($zina['admin_config'])) {
			$output .= zl(ztheme('icon','config.gif',zt('Settings')),$zina['admin_config']['path'],$zina['admin_config']['query']);
		}
		$lang['login'] = zt('Login');
		$lang['logout'] = zt('Logout');
		if (isset($zina['login'])) {
			$output .= zl(ztheme('icon',$zina['login']['type'].'.gif',$lang[$zina['login']['type']]), $zina['login']['path'], $zina['login']['query']);
		}

		$output .= '</div></div>';

		$output .= '<div class="zina-subheader"><div class="zina-subheader-left">'.ztheme('breadcrumb',$zina['breadcrumb']).'</div>'.
			'<div class="zina-subheader-right">'.$zina['randomplayform'].'</div></div>';
	}

	if (!isset($zina['popup'])) { }

	$output .= '<div class="zina-content clear-block">'.
		'<div id="zina-messages">'.$zina['messages'].'</div>'.
		$zina['content'].
		ztheme('page_footer',$zina).
		'</div></div>';

	return $output;
}
?>
