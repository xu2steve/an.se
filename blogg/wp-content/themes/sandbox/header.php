<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
	<title><?php wp_title( '-', true, 'right' ); echo wp_specialchars( get_bloginfo('name'), 1 ) ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<meta name="robots" content="noindex, nofollow">
	<link rel="stylesheet" type="text/css" href="/style/style.css" />
	<link rel="stylesheet" type="text/css" href="/style/<?php echo get_current_page() != "" ? get_current_page() : "front" ?>.css" />
	<?php wp_head() // For plugins ?>
	<link rel="alternate" type="application/rss+xml" href="/blogg/category/nyheter/feed" title="Nyheter" />
	<link rel="alternate" type="application/rss+xml" href="/blogg/category/artiklar/feed" title="Artiklar" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="Alla inlägg" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="Kommentarer" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<link rel="shortcut icon" type="image/x-icon" href="/images-global/favicon.ico" />
	<script src="/js-global/FancyZoom.js" type="text/javascript"></script>
	<script src="/js-global/FancyZoomHTML.js" type="text/javascript"></script>
	<script src="/js-global/Placeholder.js" type="text/javascript"></script>
	<script src="/js-global/prototype.js" type="text/javascript"></script>
	<script src="/js-global/scriptaculous.js?load=effects" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
		var timer = 0;
		var windowHeight = 0;
		var pageHeight = 0;
		
		window.onresize = windowHeight;
		
		function scrolled() {
			clearTimeout(timer)
			timer = setTimeout("animateBg", 500);
		}
		
		function getWindowHeight() {
			if( typeof( window.innerWidth ) == 'number' ) {
	 			  //Non-IE
	 			  windowHeight = window.innerHeight;
	 			} else if( document.documentElement && document.documentElement.clientHeight ) {
	 			  //IE 6+ in 'standards compliant mode'
	 			  windowHeight = document.documentElement.clientHeight;
	 			} else if( document.body && document.body.clientHeight ) {
	 			  //IE 4 compatible
	 			  windowHeight = document.body.clientHeight;
	 			}
		}
		
		function animateBg() {
 			var scrOfY = 0;
 			if( typeof( window.pageYOffset ) == 'number' ) {
 			  //Netscape compliant
 			  scrOfY = window.pageYOffset;
 			} else if( document.body && document.body.scrollTop ) {
 			  //DOM compliant
 			  scrOfY = document.body.scrollTop;
 			} else if( document.documentElement && document.documentElement.scrollTop ) {
 			  //IE6 standards compliant mode
 			  scrOfY = document.documentElement.scrollTop;
 			}
 			
 			var endTop = ((scrOfY / (pageHeight - windowHeight) * pageHeight) + 100);
 			var endHeight = (pageHeight - endTop);
			$('wine').morph('top:' + endTop + 'px; height:' + endHeight + 'px;');
		}
		
		function fixHeight(id) {
			$(id).setStyle({
				display: 'block'
			});
			var top = parseInt($(id).getStyle('top'));
			$(id).setStyle({
				height: ((pageHeight - top) + 'px'),
			});
		}
		
		function getPageHeight(){
			if (window.innerHeight && window.scrollMaxY) {// Firefox
				pageHeight = window.innerHeight + window.scrollMaxY;
			} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
				pageHeight = document.body.scrollHeight;
			} else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari
				pageHeight = document.body.offsetHeight;
		  	}
		 }
 	</script>
</head>
<body id="body" onload="setupZoom();activatePlaceholders();getPageHeight();fixHeight('wine');getWindowHeight()" onscroll="scrolled()">
	<div id="rightbg"></div>
	<div id="bottle"></div>
	<div id="leftbg"></div>
	<div id="wine">
		<div id="surface"></div>
		<div id="depth"></div>
	</div>
				<div id="BIGcontainer">
					<div id="header">
						<img class="beta" src="/images-global/OBS!-Beta!.png" alt="Sidan är beta." />
						<img src="/images-global/AN-top.png" alt="header" />
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