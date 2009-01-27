<?php
// turn off WordPress themes and include the WordPress core:
define('WP_USE_THEMES', false);
require($_SERVER['DOCUMENT_ROOT'] . '/blogg/wp-blog-header.php');
get_header(); ?>
<div id="content">
	<div id="main">
		<p>Här också! Massvis med info! Egentligen borde det vara mer info här än vad det är där, för det här är ju trots allt Huvudspalten. Annars kan det hända att Huvudspalten känner sig lite kränkt; den <em>ska</em> ju vara bättre än Sidospalten (tveksamt om den ens förtjänar ett stort S)!</p>
		<p>Det hörs ju på namnet: Huvudspalten. Den Viktiga Spalten. Spalten Som Alla Tittar På. Till skillnad från den andra: Sidospalten. Den Obetydliga Spalten. Spalten Som Får Stå I Skymundan.</p>
	</div>
	<div id="side">
		<p><em>Här var det info! Ja, inte lika mycket info som i Huvudspalten, förstås.</em> Sidospalten rodnar försynt vid tanken på den stora, ståtliga, mäktiga Huvudspalten. Sedan slutar den. <em>Tänk om Huvudspalten såg! Den står ju precis här bredvid!</em> Sidospalten stelnar till och återvänder sedan till sina dagdrömmar.</p>
		<p><em>Tänk, den som ändå fick bli Huvudspalt en dag! Ett så ärofyllt jobb, att bara stå där och se vacker ut, och  ... bara stå.</em> Sidospalten suckar igen, lite djupare den här gången, men nu finns där också något annat. Hopp. Hoppet om att på något sätt, någon gång, kanske kunna bli Anonyma Nykteristers Infosidas Huvudspalt <span style="color: #f00; font-weight: bold">(OBS! Beta!)</span>.</p>
	</div>
</div>
<?php get_footer() ?>