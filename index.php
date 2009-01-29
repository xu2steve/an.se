<?php
	// turn off WordPress themes and include the WordPress core:
    define('WP_USE_THEMES', false);
    require($_SERVER['DOCUMENT_ROOT'] . '/blogg/wp-blog-header.php');
    get_header(); ?>
<div id="content">
        <div id="main">
    	<?php query_posts('category_name=Förstasidor&showposts=1'); ?>
    	<?php while (have_posts()) : the_post(); ?>

    		<div id="post-<?php the_ID() ?>" <?php post_class() ?>>
    			<h3 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __( 'Permalink to %s', 'sandbox' ), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h3>
    			<div class="entry-content">
    				<?php the_content() ?>
    			</div>
    		</div>
    	<?php endwhile; ?>
    </div>
    <div id="side">
    	<h2>Nyheter</h2>
    	<?php query_posts('category_name=Nyheter&showposts=3'); ?>
    	<?php while (have_posts()) : the_post(); ?>
    		<div id="excerpt-post-<?php the_ID() ?>" <?php post_class() ?>>
    			<h3 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title() ?></a></h3>
    			<div class="entry-content">
    				<?php the_excerpt(__( 'Read More <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?>
    			</div>
    			<div class="entry-date"><abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'sandbox' ), the_date( '', '', '', false ), get_the_time() ) ?></abbr></div>
    		</div>
    	<?php endwhile; ?>
    	<br />
    	<hr />
    	<form class="email-form" action="/mailus">
    		<div id="newsletter">
    			<p>Prenumerera på vårt fina nyhetsbrev!</p>
    			<input class="text" type="text" maxlength="50" name="e" id="e" />
    			<input class="submit" type="submit" value="Prenumerera" />
			</div>
    	</form>
    </div>
</div>
<?php get_footer() ?>
