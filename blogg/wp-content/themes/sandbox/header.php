<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
	<title><?php wp_title( '-', true, 'right' ); echo wp_specialchars( get_bloginfo('name'), 1 ) ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<meta name="robots" content="noindex, nofollow" />
	<link rel="stylesheet" type="text/css" href="/style/style.css" />
	<link rel="stylesheet" type="text/css" href="/style/<?php echo get_current_page() != "" ? get_current_page() : "front" ?>.css" />
	<?php wp_head() // For plugins ?>
	<link rel="alternate" type="application/rss+xml" href="/blogg/category/nyheter/feed" title="Nyheter" />
	<link rel="alternate" type="application/rss+xml" href="/blogg/category/artiklar/feed" title="Artiklar" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="Alla inlägg" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="Kommentarer" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<link rel="shortcut icon" type="image/x-icon" href="/images-global/favicon.png" />
	<script src="/js-global/FancyZoom.js" type="text/javascript"></script>
	<script src="/js-global/FancyZoomHTML.js" type="text/javascript"></script>
	<script src="/js-global/prototype.js" type="text/javascript"></script>
	<script src="/js-global/scriptaculous.js?load=effects" type="text/javascript"></script>
	<script src="/js-global/an.js" type="text/javascript"></script>
</head>
<body onload="setupZoom();getPageHeight();getWindowHeight();fixHeight('wine')">
	<div id="bottle"></div>
	<div id="wine">
		<div id="surface"></div>
		<div id="depth"></div>
	</div>
	<div id="BIGcontainer">
		<div id="header">
			<img class="beta" src="/images-global/an-beta.png" alt="Sidan är beta." />
			<img src="/images-global/an-header.png" alt="header" />
		</div>
		<div id="container">
			<div id="navbar">
				<ul>
					<li><a id="link-hem" href="/">Hem</a></li>
					<li><a id="link-blogg" href="/blogg">Blogg</a></li>
					<li><a id="link-forum" href="/forum">Forum</a></li>
					<li><a id="link-kalender" href="/kalender">Kalender</a></li>
					<li><a id="link-info" href="/info">Info</a></li>
				</ul>
				<div id="search">
					<form class="search-form" method="get" action="<?php bloginfo('home'); ?>">
						<div>
							<input class="text" type="text" maxlength="100" name="s" id="s" />
							<input class="submit" type="submit" value="Sök" />
						</div>
					</form>
				</div>
				 <!-- #search -->
			</div> <!-- #navbar -->