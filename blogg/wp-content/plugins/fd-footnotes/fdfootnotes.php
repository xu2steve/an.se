<?php
/*
Plugin Name: FD Footnotes
Plugin URI: http://flagrantdisregard.com/footnotes-plugin
Description: Elegant and easy to use footnotes
Author: John Watson
Version: 1.2
Author URI: http://flagrantdisregard.com

Copyright (C) 2008 John Watson
john@flagrantdisregard.com
http://flagrantdisregard.com/footnotes-plugin

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

// Converts footnote markup into actual footnotes
function fdfootnote_convert($content) {
	$post_id = get_the_ID();

	$n = 1;
	$notes = array();
	if (preg_match_all('/\[(\d+\. .*?)\]/s', $content, $matches)) {
		foreach($matches[0] as $fn) {
			$note = preg_replace('/\[\d+\. (.*?)\]/s', '\1', $fn);
			$notes[$n] = $note;

			$content = str_replace($fn, "<sup class='footnote'><a href='#fn-$post_id-$n' id='fnref-$post_id-$n'>$n</a></sup>", $content);
			$n++;
		}

		$content .= "<div class='footnotes'>";
		$content .= "<div class='footnotedivider'></div>";
		$content .= "<ol>";
		for($i=1; $i<$n; $i++) {
			$content .= "<li id='fn-$post_id-$i'>$notes[$i] <span class='footnotereverse'><a href='#fnref-$post_id-$i'>&#8617;</a></span></li>";
		}
		$content .= "</ol>";
		$content .= "</div>";
	}

	return($content);
}

add_action('the_content', 'fdfootnote_convert', 1);
?>
