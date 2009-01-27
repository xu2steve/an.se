<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php bb_language_attributes( '1.1' ); ?>>
<?php if (is_bb_profile()) : global $self; ?>
<?php if (!$self) : ?>
<head profile="http://www.w3.org/2006/03/hcard">
<?php else : ?>
<head>
<?php endif; ?>
<?php else : ?>
<head>
<?php endif; ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php bb_title() ?></title>
	<?php bb_feed_head(); ?>
	<link rel="stylesheet" type="text/css" href="/style/style.css" />
	<link rel="stylesheet" type="text/css" href="/style/forum.css" />
	
<?php bb_head(); ?>
<script src="/js-global/FancyZoom.js" type="text/javascript"></script>
	<script src="/js-global/FancyZoomHTML.js" type="text/javascript"></script>
	<script src="/js-global/Placeholder.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
		function scrolled() {
  var windowHeight = 0;
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
  	/* In percent
  
	var scrollPercentage = (scrOfY / (document.body.scrollHeight - windowHeight) * 100 + 10)
	document.getElementById("wine").style.top = (scrollPercentage + "%");
	document.getElementById("wine").style.height = ((100 - scrollPercentage) + "%") */
  
	/* In pixels
	
	var scrollPixels = (scrOfY / (document.body.scrollHeight - windowHeight) * document.body.offsetHeight + 80)
	document.getElementById("surface").style.top = (scrollPixels + "px");
	document.getElementById("surface").style.height = (((document.body.offsetHeight - scrollPixels) + "px");*/
	
	var scrollPercentage = (100 - (scrOfY / (document.body.scrollHeight - windowHeight) * 100 + 10))
	if(scrollPercentage < 0) {scrollPercentage = 0}
	document.getElementById("wine").style.height = (scrollPercentage + "%");
	
   }
   </script>

</head>
 
<body id="<?php bb_location(); ?>" onload="setupZoom();activatePlaceholders()" onresize="scrolled()" onscroll="scrolled()">
	<div id="wine">
		<div id="surface">
		</div>
		<div id="depth">
		</div>
	</div>
				<div id="BIGcontainer">
					<div id="header">
						<img class="beta" src="/images-global/OBS!-Beta!.png" alt="Sidan är beta."/>
					</div>
					<div id="container">
						<div id="navbar">
							<ul>
								<li><a id="link-hem" href="/">Hem</a></li>
								<li><a id="link-blogg" href="/blogg">Blogg</a></li>
								<li><a id="link-forum" href="/design/forum">Forum</a></li>
								<li><a id="link-kalender" href="/kalender">Kalender</a></li>
								<li><a id="link-info" href="/info">Info</a></li>
								<li><?php if ( !in_array( bb_get_location(), array( 'login-page', 'register-page' ) ) ) login_form(); ?></li>
							</ul>
							<div class="search">
							<?php search_form(); ?>
						</div>
							 <!-- #search -->
						</div> <!-- #navbar -->
<?php if ( is_bb_profile() ) profile_menu(); ?>
