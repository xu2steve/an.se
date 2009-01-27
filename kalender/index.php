<?php
	// turn off WordPress themes and include the WordPress core:
    define('WP_USE_THEMES', false);
    require($_SERVER['DOCUMENT_ROOT'] . '/blogg/wp-blog-header.php');
    get_header(); ?>
<div id="content">
    <div id="side">
    </div>
    <div id="main">
		<iframe src="//www.google.com/calendar/hosted/anonymanykterister.se/embed?showTitle=0&amp;showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;showTz=0&amp;mode=AGENDA&amp;height=600&amp;wkst=2&amp;bgcolor=%23ffffff&amp;src=anonymanykterister.se_vpgfrvdj7pc95hlsj11g8alo3c%40group.calendar.google.com&amp;color=%237A367A&amp;ctz=Europe%2FStockholm" style=" border-width:0 " width="800" height="600" frameborder="0" scrolling="no"></iframe>
    </div>
</div>
<?php get_footer() ?>	
</body>
</html>
