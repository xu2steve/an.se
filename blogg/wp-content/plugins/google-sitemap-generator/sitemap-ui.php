<?php
/*
 
 $Id: sitemap-ui.php 82176 2008-12-24 04:25:18Z arnee $

*/

class GoogleSitemapGeneratorUI {

	/**
	 * The Sitemap Generator Object
	 *
	 * @var GoogleSitemapGenerator
	 */
	var $sg = null;
	
	var $mode = 21;

	function GoogleSitemapGeneratorUI($sitemapBuilder) {
		global $wp_version;
		$this->sg = $sitemapBuilder;
		
		if(floatval($wp_version) >= 2.7) {
			$this->mode = 27;
		}
	}
	
	function HtmlRegScripts() {
	
		
	}
	
	function HtmlPrintBoxHeader($id, $title, $right = false) {
		if($this->mode == 27) {
			?>
			<div id="<?php echo $id; ?>" class="postbox">
				<h3 class="hndle"><span><?php echo $title ?></span></h3>
				<div class="inside">
			<?php
		} else {
			?>
			<fieldset id="<?php echo $id; ?>" class="dbx-box">
				<?php if(!$right): ?><div class="dbx-h-andle-wrapper"><?php endif; ?>
				<h3 class="dbx-handle"><?php echo $title ?></h3>
				<?php if(!$right): ?></div><?php endif; ?>
				
				<?php if(!$right): ?><div class="dbx-c-ontent-wrapper"><?php endif; ?>
					<div class="dbx-content">
			<?php
		}
	}
	
	function HtmlPrintBoxFooter( $right = false) {
			if($this->mode == 27) {
			?>
				</div>
			</div>
			<?php
		} else {
			?>
					<?php if(!$right): ?></div><?php endif; ?>
				</div>
			</fieldset>
			<?php
		}
	}
	
	/**
	 * Displays the option page
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	 */
	function HtmlShowOptionsPage() {
		global $wp_version;
		$this->sg->Initate();
			
		//All output should go in this var which get printed at the end
		$message="";
		
		if(isset($_GET['sm_hidedonate'])) {
			$this->sg->SetOption('i_hide_donated',true);
			$this->sg->SaveOptions();
		}
		if(isset($_GET['sm_donated'])) {
			$this->sg->SetOption('i_donated',true);
			$this->sg->SaveOptions();
		}
		if(isset($_GET['sm_hide_note'])) {
			$this->sg->SetOption('i_hide_note',true);
			$this->sg->SaveOptions();
		}
		if(isset($_GET['sm_hidedonors'])) {
			$this->sg->SetOption('i_hide_donors',true);
			$this->sg->SaveOptions();
		}
		
		if(isset($_GET['sm_donated']) || ($this->sg->GetOption('i_donated')===true && $this->sg->GetOption('i_hide_donated')!==true)) {
			?>
			<div class="updated">
				<strong><p><?php _e('Thank you very much for your donation. You help me to continue support and development of this plugin and other free software!','sitemap'); ?> <a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_hidedonate=true"; ?>"><small style="font-weight:normal;"><?php _e('Hide this notice', 'sitemap'); ?></small></a></p></strong>
			</div>
			<?php
		} else if($this->sg->GetOption('i_donated') !== true && $this->sg->GetOption('i_install_date')>0 && $this->sg->GetOption('i_hide_note')!==true && time() > ($this->sg->GetOption('i_install_date') + (60*60*24*30))) {
			?>
			<div class="updated">
				<strong><p><?php echo str_replace("%s",$this->sg->GetRedirectLink("sitemap-donate-note"),__('Thanks for using this plugin! You\'ve installed this plugin over a month ago. If it works and your are satisfied with the results, isn\'t it worth at least one dollar? <a href="%s">Donations</a> help me to continue support and development of this <i>free</i> software! <a href="%s">Sure, no problem!</a>','sitemap')); ?> <a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_hide_note=true"; ?>" style="float:right; display:block; border:none;"><small style="font-weight:normal; "><?php _e('No thanks, please don\'t bug me anymore!', 'sitemap'); ?></small></a></p></strong>
				<div style="clear:right;"></div>
			</div>
			<?php
		}
		
		if(function_exists("wp_next_scheduled")) {
			$next = wp_next_scheduled('sm_build_cron');
			if($next) {
				$diff = (time()-$next)*-1;
				if($diff <= 0) {
					$diffMsg = __('Your sitemap is being refreshed at the moment. Depending on your blog size this might take some time!','sitemap');
				} else {
					$diffMsg = str_replace("%s",$diff,__('Your sitemap will be refreshed in %s seconds. Depending on your blog size this might take some time!','sitemap'));
				}
				?>
				<div class="updated">
					<strong><p><?php echo $diffMsg ?></p></strong>
					<div style="clear:right;"></div>
				</div>
				<?php
			}
		}
		
		if(!empty($_REQUEST["sm_rebuild"])) { //Pressed Button: Rebuild Sitemap
			check_admin_referer('sitemap');
			if(isset($_GET["sm_do_debug"]) && $_GET["sm_do_debug"]=="true") {
				
				//Check again, just for the case that something went wrong before
				if(!current_user_can("administrator")) {
					echo '<p>Please log in as admin</p>';
					return;
				}
				
				$oldErr = error_reporting(E_ALL);
				$oldIni = ini_set("display_errors",1);

				echo '<div class="wrap">';
				echo '<h2>' .  __('XML Sitemap Generator for WordPress', 'sitemap') .  " " . $this->sg->GetVersion(). '</h2>';
				echo '<p>This is the debug mode of the XML Sitemap Generator. It will show all PHP notices and warnings as well as the internal logs, messages and configuration.</p>';
				echo '<p style="font-weight:bold; color:red; padding:5px; border:1px red solid; text-align:center;">DO NOT POST THIS INFORMATION ON PUBLIC PAGES LIKE SUPPORT FORUMS AS IT MAY CONTAIN PASSWORDS OR SECRET SERVER INFORMATION!</p>';
				echo "<h3>WordPress and PHP Information</h3>";
				echo '<p>WordPress ' . $GLOBALS['wp_version'] . ' with ' . ' DB ' . $GLOBALS['wp_db_version'] . ' on PHP ' . phpversion() . '</p>';
				echo '<p>Plugin version: ' . $this->sg->GetVersion() . ' (' . $this->sg->_svnVersion . ')';
				echo '<h4>Environment</h4>';
				echo "<pre>";
				$sc = $_SERVER;
				unset($sc["HTTP_COOKIE"]);
				print_r($sc);
				echo "</pre>";
				echo "<h4>WordPress Config</h4>";
				echo "<pre>";
				$opts = array();
				if(function_exists('wp_load_alloptions')) {
					$opts = wp_load_alloptions();
				} else {
					global $wpdb;
					$os = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options");
					foreach ( (array) $os as $o ) $opts[$o->option_name] = $o->option_value;
				}
				
				$popts = array();
				foreach($opts as $k=>$v) {
					//Try to filter out passwords etc...
					if(preg_match("/(pass|login|pw|secret|user|usr)/si",$v)) continue;
					$popts[$k] = htmlspecialchars($v);
				}
				print_r($popts);
				echo "</pre>";
				echo '<h4>Sitemap Config</h4>';
				echo "<pre>";
				print_r($this->sg->_options);
				echo "</pre>";
				echo '<h3>Errors, Warnings, Notices</h3>';
				echo '<div>';
				$status = $this->sg->BuildSitemap();
				echo '</div>';
				echo '<h3>MySQL Queries</h3>';
				if(defined('SAVEQUERIES') && SAVEQUERIES) {
					echo '<pre>';
					var_dump($GLOBALS['wpdb']->queries);
					echo '</pre>';
					
					$total = 0;
					foreach($GLOBALS['wpdb']->queries as $q) {
						$total+=$q[1];
					}
					echo '<h4>Total Query Time</h4>';
					echo '<pre>' . count($GLOBALS['wpdb']->queries) . ' queries in ' . round($total,2) . ' seconds.</pre>';
				} else {
					echo '<p>Please edit wp-db.inc.php in wp-includes and set SAVEQUERIES to true if you want to see the queries.</p>';
				}
				echo "<h3>Build Process Results</h3>";
				echo "<pre>";
				print_r($status);
				echo "</pre>";
				echo '<p>Done. <a href="' . wp_nonce_url($this->sg->GetBackLink() . "&sm_rebuild=true&sm_do_debug=true",'sitemap') . '">Rebuild</a> or <a href="' . $this->sg->GetBackLink() . '">Return</a></p>';
				echo '<p style="font-weight:bold; color:red; padding:5px; border:1px red solid; text-align:center;">DO NOT POST THIS INFORMATION ON PUBLIC PAGES LIKE SUPPORT FORUMS AS IT MAY CONTAIN PASSWORDS OR SECRET SERVER INFORMATION!</p>';
				echo '</div>';
				@error_reporting($oldErr);
				@ini_set("display_errors",$oldIni);
				return;
			} else {
				$this->sg->BuildSitemap();
				//Redirect so the sm_rebuild GET parameter no longer exists.
				@header("location: " . $this->sg->GetBackLink());
				//If there was already any other output, the header redirect will fail
				echo '<script type="text/javascript">location.replace("' . $this->sg->GetBackLink() . '");</script>';
				echo '<noscript><a href="' . $this->sg->GetBackLink() . '">Click here to continue</a></noscript>';
				exit;
			}
		} else if (!empty($_POST['sm_update'])) { //Pressed Button: Update Config
			check_admin_referer('sitemap');
			
			if($_POST['sm_b_style'] == $this->sg->getDefaultStyle()) {
				$_POST['sm_b_style_default'] = true;
				$_POST['sm_b_style'] = '';
			}
			
			foreach($this->sg->_options as $k=>$v) {
				//Check vor values and convert them into their types, based on the category they are in
				if(!isset($_POST[$k])) $_POST[$k]=""; // Empty string will get false on 2bool and 0 on 2float
				
				//Options of the category "Basic Settings" are boolean, except the filename and the autoprio provider
				if(substr($k,0,5)=="sm_b_") {
					if($k=="sm_b_filename" || $k=="sm_b_fileurl_manual" || $k=="sm_b_filename_manual" || $k=="sm_b_prio_provider" || $k=="sm_b_manual_key" || $k == "sm_b_yahookey" || $k == "sm_b_style" || $k == "sm_b_memory") {
						if($k=="sm_b_filename_manual" && strpos($_POST[$k],"\\")!==false){
							$_POST[$k]=stripslashes($_POST[$k]);
						}
						
						$this->sg->_options[$k]=(string) $_POST[$k];
					} else if($k=="sm_b_location_mode") {
						$tmp=(string) $_POST[$k];
						$tmp=strtolower($tmp);
						if($tmp=="auto" || $tmp="manual") $this->sg->_options[$k]=$tmp;
						else $this->sg->_options[$k]="auto";
					} else if($k == "sm_b_time" || $k=="sm_b_max_posts") {
						if($_POST[$k]=='') $_POST[$k] = -1;
						$this->sg->_options[$k] = intval($_POST[$k]);
					} else if($k== "sm_i_install_date") {
						if($this->sg->GetOption('i_install_date')<=0) $this->sg->_options[$k] = time();
					} else if($k=="sm_b_exclude") {
						$IDss = array();
						$IDs = explode(",",$_POST[$k]);
						for($x = 0; $x<count($IDs); $x++) {
							$ID = intval(trim($IDs[$x]));
							if($ID>0) $IDss[] = $ID;
						}
						$this->sg->_options[$k] = $IDss;
					} else if($k == "sm_b_exclude_cats") {
						
						$exCats = array();
						foreach((array) $_POST["post_category"] AS $vv) {
							if(!empty($vv) && is_numeric($vv)) $exCats[] = intval($vv);
						}
						$this->sg->_options[$k] = $exCats;
					} else {
						$this->sg->_options[$k]=(bool) $_POST[$k];

						if($k == "sm_b_auto_delay" && $this->sg->_options[$k] == false) {
							//If cron doesn't work and the user disables it, clear any remaining hooks
							if(function_exists('wp_clear_scheduled_hook')) wp_clear_scheduled_hook('sm_build_cron');
						}
					}
				//Options of the category "Includes" are boolean
				} else if(substr($k,0,6)=="sm_in_") {
					$this->sg->_options[$k]=(bool) $_POST[$k];
				//Options of the category "Change frequencies" are string
				} else if(substr($k,0,6)=="sm_cf_") {
					$this->sg->_options[$k]=(string) $_POST[$k];
				//Options of the category "Priorities" are float
				} else if(substr($k,0,6)=="sm_pr_") {
					$this->sg->_options[$k]=(float) $_POST[$k];
				}
			}
			
			//No Mysql unbuffered query for WP < 2.2
			if(floatval($wp_version) < 2.2) {
				$this->sg->SetOption('b_safemode',true);
			}
			
			//No Wp-Cron for WP < 2.1
			if(floatval($wp_version) < 2.1) {
				$this->sg->SetOption('b_auto_delay',false);
			}
			
			//Apply page changes from POST
			$this->sg->_pages=$this->sg->HtmlApplyPages();
			
			if($this->sg->SaveOptions()) $message.=__('Configuration updated', 'sitemap') . "<br />";
			else $message.=__('Error while saving options', 'sitemap') . "<br />";
			
			if($this->sg->SavePages()) $message.=__("Pages saved",'sitemap') . "<br />";
			else $message.=__('Error while saving pages', 'sitemap'). "<br />";
			
		} else if(!empty($_POST["sm_reset_config"])) { //Pressed Button: Reset Config
			check_admin_referer('sitemap');
			$this->sg->InitOptions();
			$this->sg->SaveOptions();
			
			$message.=__('The default configuration was restored.','sitemap');
		}
		
		//Print out the message to the user, if any
		if($message!="") {
			?>
			<div class="updated"><strong><p><?php
			echo $message;
			?></p></strong></div><?php
		}
		?>
				
		<style type="text/css">
		
		li.sm_hint {
			color:green;
		}
		
		li.sm_optimize {
			color:orange;
		}
		
		li.sm_error {
			color:red;
		}
		
		input.sm_warning:hover {
			background: #ce0000;
			color: #fff;
		}
		
		a.sm_button {
			padding:4px;
			display:block;
			padding-left:25px;
			background-repeat:no-repeat;
			background-position:5px 50%;
			text-decoration:none;
			border:none;
		}
		
		a.sm_button:hover {
			border-bottom-width:1px;
		}

		a.sm_donatePayPal {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-paypal.gif);
		}
		
		a.sm_donateAmazon {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-amazon.gif);
		}
		
		a.sm_pluginHome {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-arne.gif);
		}
		
		a.sm_pluginList {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-email.gif);
		}
		
		a.sm_pluginSupport {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-wordpress.gif);
		}
		
		a.sm_pluginBugs {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-trac.gif);
		}
		
		a.sm_resGoogle {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-google.gif);
		}
		
		a.sm_resYahoo {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-yahoo.gif);
		}
		
		a.sm_resLive {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-live.gif);
		}
		
		div.sm-update-nag p {
			margin:5px;
		}
		
		</style>
		
		<?php
			if($this->mode == 27): ?>
			<style type="text/css">
		
				.sm-padded .inside {
					margin:12px!important;
				}
				.sm-padded .inside ul {
					margin:6px 0 12px 0;
				}
				
				.sm-padded .inside input {
					padding:1px;
					margin:0;
				}
			</style>
				
			<?php elseif(version_compare($wp_version,"2.5",">=")): ?>
				<style type="text/css">
					div#moremeta {
						float:right;
						width:200px;
						margin-left:10px;
					}
					div#advancedstuff {
						width:770px;
					}
					div#poststuff {
						margin-top:10px;
					}
					fieldset.dbx-box {
						margin-bottom:5px;
					}
					
					div.sm-update-nag {
						margin-top:10px!important;
					}
				</style>
				<!--[if lt IE 7]>
					<style type="text/css">
						div#advancedstuff {
							width:735px;
						}
					</style>
				<![endif]-->
				
			<?php else: ?>
				<style type="text/css">
					div.updated-message {
						margin-left:0; margin-right:0;
					}
				</style>
			<?php endif;
		?>
		
		<div class="wrap" id="sm_div">
			<form method="post" action="<?php echo $this->sg->GetBackLink() ?>">
				<h2><?php _e('XML Sitemap Generator for WordPress', 'sitemap'); echo " " . $this->sg->GetVersion() ?> </h2>
		<?php
		if(function_exists("wp_update_plugins") && (!defined('SM_NO_UPDATE') || SM_NO_UPDATE == false)) {
			wp_update_plugins();
			
			$file = GoogleSitemapGeneratorLoader::GetBaseName();
			
			$plugin_data = get_plugin_data(GoogleSitemapGeneratorLoader::GetPluginFile());
			$current = get_option( 'update_plugins' );
			if(isset($current->response[$file])) {
				$r = $current->response[$file];
				?><div id="update-nag" class="sm-update-nag"><?php
				if ( !current_user_can('edit_plugins') || version_compare($wp_version,"2.5","<") )
					printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a>.','default'), $plugin_data['Name'], $r->url, $r->new_version);
				else if ( empty($r->package) )
					printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> <em>automatic upgrade unavailable for this plugin</em>.','default'), $plugin_data['Name'], $r->url, $r->new_version);
				else
					printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> or <a href="%4$s">upgrade automatically</a>.','default'), $plugin_data['Name'], $r->url, $r->new_version, wp_nonce_url("update.php?action=upgrade-plugin&amp;plugin=$file", 'upgrade-plugin_' . $file) );

				?></div><?php
			}
		}
		?>
				
				<?php if(version_compare($wp_version,"2.5","<")): ?>
				<script type="text/javascript" src="../wp-includes/js/dbx.js"></script>
				<script type="text/javascript">
				//<![CDATA[
				addLoadEvent( function() {
					var manager = new dbxManager('sm_sitemap_meta_33');
					
					//create new docking boxes group
					var meta = new dbxGroup(
						'grabit', 		// container ID [/-_a-zA-Z0-9/]
						'vertical', 	// orientation ['vertical'|'horizontal']
						'10', 			// drag threshold ['n' pixels]
						'no',			// restrict drag movement to container axis ['yes'|'no']
						'10', 			// animate re-ordering [frames per transition, or '0' for no effect]
						'yes', 			// include open/close toggle buttons ['yes'|'no']
						'open', 		// default state ['open'|'closed']
						<?php echo "'" . js_escape(__('open')); ?>', 		// word for "open", as in "open this box"
						<?php echo "'" . js_escape(__('close')); ?>', 		// word for "close", as in "close this box"
						<?php echo "'" . js_escape(__('click-down and drag to move this box')); ?>', // sentence for "move this box" by mouse
						<?php echo "'" . js_escape(__('click to %toggle% this box')); ?>', // pattern-match sentence for "(open|close) this box" by mouse
						<?php echo "'" . js_escape(__('use the arrow keys to move this box')); ?>', // sentence for "move this box" by keyboard
						<?php echo "'" . js_escape(__(', or press the enter key to %toggle% it')); ?>',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
						'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
						);

					var advanced = new dbxGroup(
						'advancedstuff', 		// container ID [/-_a-zA-Z0-9/]
						'vertical', 		// orientation ['vertical'|'horizontal']
						'10', 			// drag threshold ['n' pixels]
						'yes',			// restrict drag movement to container axis ['yes'|'no']
						'10', 			// animate re-ordering [frames per transition, or '0' for no effect]
						'yes', 			// include open/close toggle buttons ['yes'|'no']
						'open', 		// default state ['open'|'closed']
						<?php echo "'" . js_escape(__('open')); ?>', 		// word for "open", as in "open this box"
						<?php echo "'" . js_escape(__('close')); ?>', 		// word for "close", as in "close this box"
						<?php echo "'" . js_escape(__('click-down and drag to move this box')); ?>', // sentence for "move this box" by mouse
						<?php echo "'" . js_escape(__('click to %toggle% this box')); ?>', // pattern-match sentence for "(open|close) this box" by mouse
						<?php echo "'" . js_escape(__('use the arrow keys to move this box')); ?>', // sentence for "move this box" by keyboard
						<?php echo "'" . js_escape(__(', or press the enter key to %toggle% it')); ?>',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
						'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
						);
				});
				//]]>
				</script>
				<?php endif; ?>

				<?php if($this->mode == 27): ?>
				<div id="poststuff" class="metabox-holder">
					<div class="inner-sidebar">
						<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
				<?php else: ?>
				<div id="poststuff">
					<div id="moremeta">
						<div id="grabit" class="dbx-group">
				<?php endif; ?>
				
						<?php $this->HtmlPrintBoxHeader('sm_pnres',__('About this Plugin:','sitemap'),true); ?>
							<a class="sm_button sm_pluginHome"    href="<?php echo $this->sg->GetRedirectLink('sitemap-home'); ?>"><?php _e('Plugin Homepage','sitemap'); ?></a>
							<a class="sm_button sm_pluginHome"    href="<?php echo $this->sg->GetRedirectLink('sitemap-feedback'); ?>"><?php _e('Suggest a Feature','sitemap'); ?></a>
							<a class="sm_button sm_pluginList"    href="<?php echo $this->sg->GetRedirectLink('sitemap-list'); ?>"><?php _e('Notify List','sitemap'); ?></a>
							<a class="sm_button sm_pluginSupport" href="<?php echo $this->sg->GetRedirectLink('sitemap-support'); ?>"><?php _e('Support Forum','sitemap'); ?></a>
							<a class="sm_button sm_pluginBugs"    href="<?php echo $this->sg->GetRedirectLink('sitemap-bugs'); ?>"><?php _e('Report a Bug','sitemap'); ?></a>
							
							<a class="sm_button sm_donatePayPal"  href="<?php echo $this->sg->GetRedirectLink('sitemap-paypal'); ?>"><?php _e('Donate with PayPal','sitemap'); ?></a>
							<a class="sm_button sm_donateAmazon"  href="<?php echo $this->sg->GetRedirectLink('sitemap-amazon'); ?>"><?php _e('My Amazon Wish List','sitemap'); ?></a>
							<?php if(__('translator_name','sitemap')!='translator_name') {?><a class="sm_button sm_pluginSupport" href="<?php _e('translator_url','sitemap'); ?>"><?php _e('translator_name','sitemap'); ?></a><?php } ?>
						<?php $this->HtmlPrintBoxFooter(true); ?>
						
						<?php $this->HtmlPrintBoxHeader('sm_smres',__('Sitemap Resources:','sitemap'),true); ?>
							<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-gwt'); ?>"><?php _e('Webmaster Tools','sitemap'); ?></a>
							<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-gwb'); ?>"><?php _e('Webmaster Blog','sitemap'); ?></a>
							
							<a class="sm_button sm_resYahoo"     href="<?php echo $this->sg->GetRedirectLink('sitemap-yse'); ?>"><?php _e('Site Explorer','sitemap'); ?></a>
							<a class="sm_button sm_resYahoo"     href="<?php echo $this->sg->GetRedirectLink('sitemap-ywb'); ?>"><?php _e('Search Blog','sitemap'); ?></a>
							
							<a class="sm_button sm_resLive"     href="<?php echo $this->sg->GetRedirectLink('sitemap-lwt'); ?>"><?php _e('Webmaster Tools','sitemap'); ?></a>
							<a class="sm_button sm_resLive"     href="<?php echo $this->sg->GetRedirectLink('sitemap-lswcb'); ?>"><?php _e('Webmaster Center Blog','sitemap'); ?></a>
							<br />
							<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-prot'); ?>"><?php _e('Sitemaps Protocol','sitemap'); ?></a>
							<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-ofaq'); ?>"><?php _e('Official Sitemaps FAQ','sitemap'); ?></a>
							<a class="sm_button sm_pluginHome"   href="<?php echo $this->sg->GetRedirectLink('sitemap-afaq'); ?>"><?php _e('My Sitemaps FAQ','sitemap'); ?></a>
						<?php $this->HtmlPrintBoxFooter(true); ?>
						
						<?php $this->HtmlPrintBoxHeader('dm_donations',__('Recent Donations:','sitemap'),true); ?>
											
							<?php if($this->sg->GetOption('i_hide_donors')!==true) { ?>
								<iframe border="0" frameborder="0" scrolling="no" allowtransparency="yes" style="width:100%; height:80px;" src="<?php echo $this->sg->GetRedirectLink('sitemap-donorlist'); ?>">
								<?php _e('List of the donors','sitemap'); ?>
								</iframe><br />
								<a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_hidedonors=true"; ?>"><small><?php _e('Hide this list','sitemap'); ?></small></a><br /><br />
							<?php } ?>
							<a style="float:left; margin-right:5px; border:none;" href="javascript:document.getElementById('sm_donate_form').submit();"><img style="vertical-align:middle; border:none; margin-top:2px;" src="<?php echo $this->sg->GetPluginUrl(); ?>img/icon-donate.gif" border="0" alt="PayPal" title="Help me to continue support of this plugin :)" /></a>
							<span><small><?php _e('Thanks for your support!','sitemap'); ?></small></span>
							<div style="clear:left; height:1px;"></div>
						<?php $this->HtmlPrintBoxFooter(true); ?>
				
						</div>
					</div>
					
					<?php if($this->mode == 27): ?>
						<div class="has-sidebar sm-padded" >
					
							<div id="post-body-content" class="has-sidebar-content">
						
								<div class="meta-box-sortabless">
					<?php else: ?>
						<div id="advancedstuff" class="dbx-group" >
					<?php endif; ?>
					
					<!-- Rebuild Area -->
					<?php $this->HtmlPrintBoxHeader('sm_rebuild',__('Status', 'sitemap')); ?>
						<ul>
							<?php
	
							//#type $status GoogleSitemapGeneratorStatus
							$status = GoogleSitemapGeneratorStatus::Load();
							if($status == null) {
								
								echo "<li>" . str_replace("%s",wp_nonce_url($this->sg->GetBackLink() . "&sm_rebuild=true",'sitemap'),__('The sitemap wasn\'t built yet. <a href="%s">Click here</a> to build it the first time.','sitemap')) . "</li>";
							}  else {
								if($status->_endTime !== 0) {
									if($status->_usedXml) {
										if($status->_xmlSuccess) {
											$ft = filemtime($status->_xmlPath);
											echo "<li>" . str_replace("%url%",$status->_xmlUrl,str_replace("%date%",date(get_option('date_format'),$ft) . " " . date(get_option('time_format'),$ft),__("Your <a href=\"%url%\">sitemap</a> was last built on <b>%date%</b>.",'sitemap'))) . "</li>";
										} else {
											echo "<li class=\"sm_error\">" . str_replace("%url%",$this->sg->GetRedirectLink('sitemap-help-files'),__("There was a problem writing your sitemap file. Make sure the file exists and is writable. <a href=\"%url%\">Learn more</a",'sitemap')) . "</li>";
										}
									}
									
									if($status->_usedZip) {
										if($status->_zipSuccess) {
												$ft = filemtime($status->_zipPath);
												echo "<li>" . str_replace("%url%",$status->_zipUrl,str_replace("%date%",date(get_option('date_format'),$ft) . " " . date(get_option('time_format'),$ft),__("Your sitemap (<a href=\"%url%\">zipped</a>) was last built on <b>%date%</b>.",'sitemap'))) . "</li>";
										} else {
											echo "<li class=\"sm_error\">" . str_replace("%url%",$this->sg->GetRedirectLink('sitemap-help-files'),__("There was a problem writing your zipped sitemap file. Make sure the file exists and is writable. <a href=\"%url%\">Learn more</a",'sitemap')) . "</li>";
										}
									}
									
									if($status->_usedGoogle) {
										if($status->_gooogleSuccess) {
											echo "<li>" .__("Google was <b>successfully notified</b> about changes.",'sitemap'). "</li>";
											$gt = $status->GetGoogleTime();
											if($gt>4) {
												echo "<li class=\sm_optimize\">" . str_replace("%time%",$gt,__("It took %time% seconds to notify Google, maybe you want to disable this feature to reduce the building time.",'sitemap')) . "</li>";
											}
										} else {
											echo "<li class=\"sm_error\">" . str_replace("%s",$status->_googleUrl,__('There was a problem while notifying Google. <a href="%s">View result</a>','sitemap')) . "</li>";
										}
									}
									
									if($status->_usedYahoo) {
										if($status->_yahooSuccess) {
											echo "<li>" .__("YAHOO was <b>successfully notified</b> about changes.",'sitemap'). "</li>";
											$yt = $status->GetYahooTime();
											if($yt>4) {
												echo "<li class=\sm_optimize\">" . str_replace("%time%",$yt,__("It took %time% seconds to notify YAHOO, maybe you want to disable this feature to reduce the building time.",'sitemap')) . "</li>";
											}
										} else {
											echo "<li class=\"sm_error\">" . str_replace("%s",$status->_yahooUrl,__('There was a problem while notifying YAHOO. <a href="%s">View result</a>','sitemap')) . "</li>";
										}
									}
									
									if($status->_usedMsn) {
										if($status->_msnSuccess) {
											echo "<li>" .__("MSN was <b>successfully notified</b> about changes.",'sitemap'). "</li>";
											$at = $status->GetMsnTime();
											if($at>4) {
												echo "<li class=\sm_optimize\">" . str_replace("%time%",$at,__("It took %time% seconds to notify MSN.com, maybe you want to disable this feature to reduce the building time.",'sitemap')) . "</li>";
											}
										} else {
											echo "<li class=\"sm_error\">" . str_replace("%s",$status->_msnUrl,__('There was a problem while notifying MSN.com. <a href="%s">View result</a>','sitemap')) . "</li>";
										}
									}
									
									if($status->_usedAsk) {
										if($status->_askSuccess) {
											echo "<li>" .__("Ask.com was <b>successfully notified</b> about changes.",'sitemap'). "</li>";
											$at = $status->GetAskTime();
											if($at>4) {
												echo "<li class=\sm_optimize\">" . str_replace("%time%",$at,__("It took %time% seconds to notify Ask.com, maybe you want to disable this feature to reduce the building time.",'sitemap')) . "</li>";
											}
										} else {
											echo "<li class=\"sm_error\">" . str_replace("%s",$status->_askUrl,__('There was a problem while notifying Ask.com. <a href="%s">View result</a>','sitemap')) . "</li>";
										}
									}
									
									$et = $status->GetTime();
									$mem = $status->GetMemoryUsage();
									
									if($mem > 0) {
										echo "<li>" .str_replace(array("%time%","%memory%"),array($et,$mem),__("The building process took about <b>%time% seconds</b> to complete and used %memory% MB of memory.",'sitemap')). "</li>";
									} else {
										echo "<li>" .str_replace("%time%",$et,__("The building process took about <b>%time% seconds</b> to complete.",'sitemap')). "</li>";
									}
									
									if(!$status->_hasChanged) {
										echo "<li>" . __("The content of your sitemap <strong>didn't change</strong> since the last time so the files were not written and no search engine was pinged.",'sitemap'). "</li>";
									}
													
								} else {
									if($this->sg->GetOption("b_auto_delay")) {
										$st = ($status->GetStartTime() - time()) * -1;
										//If the building process runs in background and was started within the last 45 seconds, the sitemap might not be completed yet...
										if($st < 45) {
											echo '<li class="">'. __("The building process might still be active! Reload the page in a few seconds and check if something has changed.",'sitemap') . '</li>';
										}
									}
									echo '<li class="sm_error">'. str_replace("%url%",$this->sg->GetRedirectLink('sitemap-help-memtime'),__("The last run didn't finish! Maybe you can raise the memory or time limit for PHP scripts. <a href=\"%url%\">Learn more</a>",'sitemap')) . '</li>';
									if($status->_memoryUsage > 0) {
										echo '<li class="sm_error">'. str_replace(array("%memused%","%memlimit%"),array($status->GetMemoryUsage(),ini_get('memory_limit')),__("The last known memory usage of the script was %memused%MB, the limit of your server is %memlimit%.",'sitemap')) . '</li>';
									}
									
									if($status->_lastTime > 0) {
										echo '<li class="sm_error">'. str_replace(array("%timeused%","%timelimit%"),array($status->GetLastTime(),ini_get('max_execution_time')),__("The last known execution time of the script was %timeused% seconds, the limit of your server is %timelimit% seconds.",'sitemap')) . '</li>';
									}
									
									if($status->GetLastPost() > 0) {
										echo '<li class="sm_optimize">'. str_replace("%lastpost%",$status->GetLastPost(),__("The script stopped around post number %lastpost% (+/- 100)",'sitemap')) . '</li>';
									}
								}
								echo "<li>" . str_replace("%s",wp_nonce_url($this->sg->GetBackLink() . "&sm_rebuild=true",'sitemap'),__('If you changed something on your server or blog, you should <a href="%s">rebuild the sitemap</a> manually.','sitemap')) . "</li>";
							}
							echo "<li>" . str_replace("%d",wp_nonce_url($this->sg->GetBackLink() . "&sm_rebuild=true&sm_do_debug=true",'sitemap'),__('If you encounter any problems with the build process you can use the <a href="%d">debug function</a> to get more information.','sitemap')) . "</li>";
							?>

						</ul>
					<?php $this->HtmlPrintBoxFooter(); ?>
						
					<!-- Basic Options -->
					<?php $this->HtmlPrintBoxHeader('sm_basic_options',__('Basic Options', 'sitemap')); ?>
					
						<b><?php _e('Sitemap files:','sitemap'); ?></b> <a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-files'); ?>"><?php _e('Learn more','sitemap'); ?></a>
						<ul>
							<li>
								<label for="sm_b_xml">
									<input type="checkbox" id="sm_b_xml" name="sm_b_xml" <?php echo ($this->sg->GetOption("b_xml")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Write a normal XML file (your filename)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_b_gzip">
									<input type="checkbox" id="sm_b_gzip" name="sm_b_gzip" <?php if(function_exists("gzencode")) { echo ($this->sg->GetOption("b_gzip")==true?"checked=\"checked\"":""); } else echo "disabled=\"disabled\"";  ?> />
									<?php _e('Write a gzipped file (your filename + .gz)', 'sitemap') ?>
								</label>
							</li>
						</ul>
						<b><?php _e('Building mode:','sitemap'); ?></b> <a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-process'); ?>"><?php _e('Learn more','sitemap'); ?></a>
						<ul>
							<li>
								<label for="sm_b_auto_enabled">
									<input type="checkbox" id="sm_b_auto_enabled" name="sm_b_auto_enabled" <?php echo ($this->sg->GetOption("b_auto_enabled")==true?"checked=\"checked\"":""); ?> />
									<?php _e('Rebuild sitemap if you change the content of your blog', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_b_manual_enabled">
									<input type="hidden" name="sm_b_manual_key" value="<?php echo $this->sg->GetOption("b_manual_key"); ?>" />
									<input type="checkbox" id="sm_b_manual_enabled" name="sm_b_manual_enabled" <?php echo ($this->sg->GetOption("b_manual_enabled")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Enable manual sitemap building via GET Request', 'sitemap') ?>
								</label>
								<a href="javascript:void(document.getElementById('sm_manual_help').style.display='');">[?]</a>
								<span id="sm_manual_help" style="display:none;"><br />
								<?php echo str_replace("%1",trailingslashit(get_bloginfo('siteurl')) . "?sm_command=build&amp;sm_key=" . $this->sg->GetOption("b_manual_key"),__('This will allow you to refresh your sitemap if an external tool wrote into the WordPress database without using the WordPress API. Use the following URL to start the process: <a href="%1">%1</a> Please check the logfile above to see if sitemap was successfully built.', 'sitemap')); ?>
								</span>
							</li>
						</ul>
						<b><?php _e('Update notification:','sitemap'); ?></b> <a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-ping'); ?>"><?php _e('Learn more','sitemap'); ?></a>
						<ul>
							<li>
								<input type="checkbox" id="sm_b_ping" name="sm_b_ping" <?php echo ($this->sg->GetOption("b_ping")==true?"checked=\"checked\"":"") ?> />
								<label for="sm_b_ping"><?php _e('Notify Google about updates of your Blog', 'sitemap') ?></label><br />
								<small><?php echo str_replace("%s",$this->sg->GetRedirectLink('sitemap-gwt'),__('No registration required, but you can join the <a href="%s">Google Webmaster Tools</a> to check crawling statistics.','sitemap')); ?></small>
							</li>
							<li>
								<input type="checkbox" id="sm_b_pingmsn" name="sm_b_pingmsn" <?php echo ($this->sg->GetOption("b_pingmsn")==true?"checked=\"checked\"":"") ?> />
								<label for="sm_b_pingmsn"><?php _e('Notify MSN Live Search about updates of your Blog', 'sitemap') ?></label><br />
								<small><?php echo str_replace("%s",$this->sg->GetRedirectLink('sitemap-lwt'),__('No registration required, but you can join the <a href="%s">MSN Live Webmaster Tools</a> to check crawling statistics.','sitemap')); ?></small>
							</li>
							<li>
								<input type="checkbox" id="sm_b_pingask" name="sm_b_pingask" <?php echo ($this->sg->GetOption("b_pingask")==true?"checked=\"checked\"":"") ?> />
								<label for="sm_b_pingask"><?php _e('Notify Ask.com about updates of your Blog', 'sitemap') ?></label><br />
								<small><?php _e('No registration required.','sitemap'); ?></small>
							</li>
							<li>
								<input type="checkbox" id="sm_b_pingyahoo" name="sm_b_pingyahoo" <?php echo ($this->sg->GetOption("b_pingyahoo")==true?"checked=\"checked\"":"") ?> />
								<label for="sm_b_pingyahoo"><?php _e('Notify YAHOO about updates of your Blog', 'sitemap') ?></label><br />
								<label for="sm_b_yahookey"><?php _e('Your Application ID:', 'sitemap') ?> <input type="text" name="sm_b_yahookey" id="sm_b_yahookey" value="<?php echo $this->sg->GetOption("b_yahookey"); ?>" /></label><br />
								<small><?php echo str_replace(array("%s1","%s2"),array($this->sg->GetRedirectLink('sitemap-ykr'),' (<a href="http://developer.yahoo.net/about/">Web Services by Yahoo!</a>)'),__('Don\'t you have such a key? <a href="%s1">Request one here</a>! %s2','sitemap')); ?></small>
							</li>
							<li>
								<label for="sm_b_robots">
								<input type="checkbox" id="sm_b_robots" name="sm_b_robots" <?php echo ($this->sg->GetOption("b_robots")==true?"checked=\"checked\"":"") ?> />
								<?php _e("Add sitemap URL to the virtual robots.txt file.",'sitemap'); ?>
								</label>

								<br />
								<small><?php _e('The virtual robots.txt generated by WordPress is used. A real robots.txt file must NOT exist in the blog directory!','sitemap'); ?></small>
							</li>
						</ul>
						<b><?php _e('Advanced options:','sitemap'); ?></b> <a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-adv'); ?>"><?php _e('Learn more','sitemap'); ?></a>
						<ul>
							<li>
								<label for="sm_b_max_posts"><?php _e('Limit the number of posts in the sitemap:', 'sitemap') ?> <input type="text" name="sm_b_max_posts" id="sm_b_max_posts" style="width:40px;" value="<?php echo ($this->sg->GetOption("b_max_posts")<=0?"":$this->sg->GetOption("b_max_posts")); ?>" /></label> (<?php echo __('Newer posts will be included first', 'sitemap'); ?>)
							</li>
							<li>
								<label for="sm_b_memory"><?php _e('Try to increase the memory limit to:', 'sitemap') ?> <input type="text" name="sm_b_memory" id="sm_b_memory" style="width:40px;" value="<?php echo $this->sg->GetOption("b_memory"); ?>" /></label> (<?php echo htmlspecialchars(__('e.g. "4M", "16M"', 'sitemap')); ?>)
							</li>
							<li>
								<label for="sm_b_time"><?php _e('Try to increase the execution time limit to:', 'sitemap') ?> <input type="text" name="sm_b_time" id="sm_b_time" style="width:40px;" value="<?php echo ($this->sg->GetOption("b_time")===-1?'':$this->sg->GetOption("b_time")); ?>" /></label> (<?php echo htmlspecialchars(__('in seconds, e.g. "60" or "0" for unlimited', 'sitemap')) ?>)
							</li>
							<li>
								<?php $useDefStyle = ($this->sg->GetDefaultStyle() && $this->sg->GetOption('b_style_default')===true); ?>
								<label for="sm_b_style"><?php _e('Include a XSLT stylesheet:', 'sitemap') ?> <input <?php echo ($useDefStyle?'disabled="disabled" ':'') ?> type="text" name="sm_b_style" id="sm_b_style"  value="<?php echo $this->sg->GetOption("b_style"); ?>" /></label>
								(<?php _e('Full or relative URL to your .xsl file', 'sitemap') ?>) <?php if($this->sg->GetDefaultStyle()): ?><label for="sm_b_style_default"><input <?php echo ($useDefStyle?'checked="checked" ':'') ?> type="checkbox" id="sm_b_style_default" name="sm_b_style_default" onclick="document.getElementById('sm_b_style').disabled = this.checked;" /> <?php _e('Use default', 'sitemap') ?> <?php endif; ?>
							</li>
							<li>
								<label for="sm_b_safemode">
									<?php $forceSafeMode = (floatval($wp_version)<2.2); ?>
									<input type="checkbox" <?php if($forceSafeMode):?>disabled="disabled"<?php endif; ?> id="sm_b_safemode" name="sm_b_safemode" <?php echo ($this->sg->GetOption("b_safemode")==true||$forceSafeMode?"checked=\"checked\"":""); ?> />
									<?php _e('Enable MySQL standard mode. Use this only if you\'re getting MySQL errors. (Needs much more memory!)', 'sitemap') ?>
									<?php if($forceSafeMode):?> <br /><small><?php _e("Upgrade WordPress at least to 2.2 to enable the faster MySQL access",'sitemap'); ?></small><?php endif; ?>
								</label>
							</li>
							<li>
								<label for="sm_b_auto_delay">
								<?php $forceDirect = (floatval($wp_version) < 2.1);?>
									<input type="checkbox" <?php if($forceDirect):?>disabled="disabled"<?php endif; ?> id="sm_b_auto_delay" name="sm_b_auto_delay" <?php echo ($this->sg->GetOption("b_auto_delay")==true&&!$forceDirect?"checked=\"checked\"":""); ?> />
									<?php _e('Build the sitemap in a background process (You don\'t have to wait when you save a post)', 'sitemap') ?>
									<?php if($forceDirect):?> <br /><small><?php _e("Upgrade WordPress at least to 2.1 to enable background building",'sitemap'); ?></small><?php endif; ?>
								</label>
							</li>
						</ul>
						
					<?php $this->HtmlPrintBoxFooter(); ?>
					
					<?php $this->HtmlPrintBoxHeader('sm_pages',__('Additional pages', 'sitemap')); ?>
		
						<?php
						_e('Here you can specify files or URLs which should be included in the sitemap, but do not belong to your Blog/WordPress.<br />For example, if your domain is www.foo.com and your blog is located on www.foo.com/blog you might want to include your homepage at www.foo.com','sitemap');
						echo "<ul><li>";
						echo "<strong>" . __('Note','sitemap'). "</strong>: ";
						_e("If your blog is in a subdirectory and you want to add pages which are NOT in the blog directory or beneath, you MUST place your sitemap file in the root directory (Look at the &quot;Location of your sitemap file&quot; section on this page)!",'sitemap');
						echo "</li><li>";
						echo "<strong>" . __('URL to the page','sitemap'). "</strong>: ";
						_e("Enter the URL to the page. Examples: http://www.foo.com/index.html or www.foo.com/home ",'sitemap');
						echo "</li><li>";
						echo "<strong>" . __('Priority','sitemap') . "</strong>: ";
						_e("Choose the priority of the page relative to the other pages. For example, your homepage might have a higher priority than your imprint.",'sitemap');
						echo "</li><li>";
						echo "<strong>" . __('Last Changed','sitemap'). "</strong>: ";
						_e("Enter the date of the last change as YYYY-MM-DD (2005-12-31 for example) (optional).",'sitemap');
						
						echo "</li></ul>";
						
						
						?>
						<script type="text/javascript">
							//<![CDATA[
							<?php
							$freqVals = "'" . implode("','",array_keys($this->sg->_freqNames)). "'";
							$freqNames = "'" . implode("','",array_values($this->sg->_freqNames)). "'";
							?>

							var changeFreqVals = new Array( <?php echo $freqVals; ?> );
							var changeFreqNames= new Array( <?php echo $freqNames; ?> );
							
							var priorities= new Array(0 <?php for($i=0.1; $i<1; $i+=0.1) { echo "," .  $i; } ?>);
							
							var pages = [ <?php
								if(count($this->sg->_pages)>0) {
									for($i=0; $i<count($this->sg->_pages); $i++) {
										$v=&$this->sg->_pages[$i];
										if($i>0) echo ",";
										echo '{url:"' . $v->getUrl() . '", priority:"' . $v->getPriority() . '", changeFreq:"' . $v->getChangeFreq() . '", lastChanged:"' . ($v!=null && $v->getLastMod()>0?date("Y-m-d",$v->getLastMod()):"") . '"}';
									}
								}
							?> ];
							//]]>
						</script>
						<script type="text/javascript" src="<?php echo $this->sg->GetPluginUrl(); ?>img/sitemap.js"></script>
						<table width="100%" cellpadding="3" cellspacing="3" id="sm_pageTable">
							<tr>
								<th scope="col"><?php _e('URL to the page','sitemap'); ?></th>
								<th scope="col"><?php _e('Priority','sitemap'); ?></th>
								<th scope="col"><?php _e('Change Frequency','sitemap'); ?></th>
								<th scope="col"><?php _e('Last Changed','sitemap'); ?></th>
								<th scope="col"><?php _e('#','sitemap'); ?></th>
							</tr>
							<?php
								if(count($this->sg->_pages)<=0) { ?>
									<tr>
										<td colspan="5" align="center"><?php _e('No pages defined.','sitemap') ?></td>
									</tr><?php
								}
							?>
						</table>
						<a href="javascript:void(0);" onclick="sm_addPage();"><?php _e("Add new page",'sitemap'); ?></a>
					<?php $this->HtmlPrintBoxFooter(); ?>
					
					
					<!-- AutoPrio Options -->
					<?php $this->HtmlPrintBoxHeader('sm_postprio',__('Post Priority', 'sitemap')); ?>
	
						<p><?php _e('Please select how the priority of each post should be calculated:', 'sitemap') ?></p>
						<ul>
							<li><p><input type="radio" name="sm_b_prio_provider" id="sm_b_prio_provider__0" value="" <?php echo $this->sg->HtmlGetChecked($this->sg->GetOption("b_prio_provider"),"") ?> /> <label for="sm_b_prio_provider__0"><?php _e('Do not use automatic priority calculation', 'sitemap') ?></label><br /><?php _e('All posts will have the same priority which is defined in &quot;Priorities&quot;', 'sitemap') ?></p></li>
							<?php
							for($i=0; $i<count($this->sg->_prioProviders); $i++) {
								echo "<li><p><input type=\"radio\" id=\"sm_b_prio_provider_$i\" name=\"sm_b_prio_provider\" value=\"" . $this->sg->_prioProviders[$i] . "\" " .  $this->sg->HtmlGetChecked($this->sg->GetOption("b_prio_provider"),$this->sg->_prioProviders[$i]) . " /> <label for=\"sm_b_prio_provider_$i\">" . call_user_func(array(&$this->sg->_prioProviders[$i], 'getName'))  . "</label><br />" .  call_user_func(array(&$this->sg->_prioProviders[$i], 'getDescription')) . "</p></li>";
							}
							?>
						</ul>
					<?php $this->HtmlPrintBoxFooter(); ?>
				
						
					<!-- Location Options -->
					<?php $this->HtmlPrintBoxHeader('sm_location',__('Location of your sitemap file', 'sitemap')); ?>
		
						<div>
							<b><label for="sm_location_useauto"><input type="radio" id="sm_location_useauto" name="sm_b_location_mode" value="auto" <?php echo ($this->sg->GetOption("b_location_mode")=="auto"?"checked=\"checked\"":"") ?> /> <?php _e('Automatic detection','sitemap') ?></label></b>
							<ul>
								<li>
									<label for="sm_b_filename">
										<?php _e('Filename of the sitemap file', 'sitemap') ?>
										<input type="text" id="sm_b_filename" name="sm_b_filename" value="<?php echo $this->sg->GetOption("b_filename"); ?>" />
									</label><br />
									<?php _e('Detected Path', 'sitemap') ?>: <?php echo $this->sg->getXmlPath(true); ?><br /><?php _e('Detected URL', 'sitemap') ?>: <a href="<?php echo $this->sg->getXmlUrl(true); ?>"><?php echo $this->sg->getXmlUrl(true); ?></a>
								</li>
							</ul>
						</div>
						<div>
							<b><label for="sm_location_usemanual"><input type="radio" id="sm_location_usemanual" name="sm_b_location_mode" value="manual" <?php echo ($this->sg->GetOption("b_location_mode")=="manual"?"checked=\"checked\"":"") ?>  /> <?php _e('Custom location','sitemap') ?></label></b>
							<ul>
								<li>
									<label for="sm_b_filename_manual">
										<?php _e('Absolute or relative path to the sitemap file, including name.','sitemap');
										echo "<br />";
										_e('Example','sitemap');
										echo ": /var/www/htdocs/wordpress/sitemap.xml"; ?><br />
										<input style="width:70%" type="text" id="sm_b_filename_manual" name="sm_b_filename_manual" value="<?php echo (!$this->sg->GetOption("b_filename_manual")?$this->sg->getXmlPath():$this->sg->GetOption("b_filename_manual")); ?>" />
									</label>
								</li>
								<li>
									<label for="sm_b_fileurl_manual">
										<?php _e('Complete URL to the sitemap file, including name.','sitemap');
										echo "<br />";
										_e('Example','sitemap');
										echo ": http://www.yourdomain.com/sitemap.xml"; ?><br />
										<input style="width:70%" type="text" id="sm_b_fileurl_manual" name="sm_b_fileurl_manual" value="<?php echo (!$this->sg->GetOption("b_fileurl_manual")?$this->sg->getXmlUrl():$this->sg->GetOption("b_fileurl_manual")); ?>" />
									</label>
								</li>
							</ul>
						</div>
						
					<?php $this->HtmlPrintBoxFooter(); ?>
					
					<!-- Includes -->
					<?php $this->HtmlPrintBoxHeader('sm_includes',__('Sitemap Content', 'sitemap')); ?>
					
						<ul>
							<li>
								<label for="sm_in_home">
									<input type="checkbox" id="sm_in_home" name="sm_in_home"  <?php echo ($this->sg->GetOption("in_home")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include homepage', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_posts">
									<input type="checkbox" id="sm_in_posts" name="sm_in_posts"  <?php echo ($this->sg->GetOption("in_posts")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include posts', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_posts_sub">
									<input type="checkbox" id="sm_in_posts_sub" name="sm_in_posts_sub"  <?php echo ($this->sg->GetOption("in_posts_sub")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include following pages of multi-page posts (&lt;!--nextpage--&gt;)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_pages">
									<input type="checkbox" id="sm_in_pages" name="sm_in_pages"  <?php echo ($this->sg->GetOption("in_pages")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include static pages', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_cats">
									<input type="checkbox" id="sm_in_cats" name="sm_in_cats"  <?php echo ($this->sg->GetOption("in_cats")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include categories', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_arch">
									<input type="checkbox" id="sm_in_arch" name="sm_in_arch"  <?php echo ($this->sg->GetOption("in_arch")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include archives', 'sitemap') ?>
								</label>
							</li>
							<?php if($this->sg->IsTaxonomySupported()): ?>
							<li>
								<label for="sm_in_tags">
									<input type="checkbox" id="sm_in_tags" name="sm_in_tags"  <?php echo ($this->sg->GetOption("in_tags")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include tag pages', 'sitemap') ?>
								</label>
							</li>
							<?php endif; ?>
							<li>
								<label for="sm_in_auth">
									<input type="checkbox" id="sm_in_auth" name="sm_in_auth"  <?php echo ($this->sg->GetOption("in_auth")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include author pages', 'sitemap') ?>
								</label>
							</li>
						</ul>
						
					<?php $this->HtmlPrintBoxFooter(); ?>
					
					<!-- Excluded Items -->
					<?php $this->HtmlPrintBoxHeader('sm_excludes',__('Excluded items', 'sitemap')); ?>
					
						<b><?php _e('Excluded categories', 'sitemap') ?>:</b>
						<?php if(version_compare($wp_version,"2.5",">=")): ?>
						<cite style="display:block; margin-left:40px;"><?php _e("Note","sitemap") ?>: <?php _e("Using this feature will increase build time and memory usage!","sitemap"); ?></cite>
						<div style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; margin:5px 0px 5px 40px; overflow:auto; padding:0.5em 0.5em;">
						<ul>
							<?php wp_category_checklist(0,0,$this->sg->GetOption("b_exclude_cats"),false); ?>
						</ul>
						</div>
						<?php else: ?>
							<ul><li><?php  echo sprintf(__("This feature requires at least WordPress 2.5, you are using %s","sitemap"),$wp_version); ?></li></ul>
						<?php endif; ?>
						
						<b><?php _e("Exclude posts","sitemap"); ?>:</b>
						<div style="margin:5px 0 13px 40px;">
							<label for="sm_b_exclude"><?php _e('Exclude the following posts or pages:', 'sitemap') ?> <small><?php _e('List of IDs, separated by comma', 'sitemap') ?></small><br />
							<input name="sm_b_exclude" id="sm_b_exclude" type="text" style="width:400px;" value="<?php echo implode(",",$this->sg->GetOption("b_exclude")); ?>" /></label><br />
							<cite><?php _e("Note","sitemap") ?>: <?php _e("Child posts will not automatically be excluded!","sitemap"); ?></cite>
						</div>
						
					<?php $this->HtmlPrintBoxFooter(); ?>
					
					<!-- Change frequencies -->
					<?php $this->HtmlPrintBoxHeader('sm_change_frequencies',__('Change frequencies', 'sitemap')); ?>

						<p>
							<b><?php _e('Note', 'sitemap') ?>:</b>
							<?php _e('Please note that the value of this tag is considered a hint and not a command. Even though search engine crawlers consider this information when making decisions, they may crawl pages marked "hourly" less frequently than that, and they may crawl pages marked "yearly" more frequently than that. It is also likely that crawlers will periodically crawl pages marked "never" so that they can handle unexpected changes to those pages.', 'sitemap') ?>
						</p>
						<ul>
							<li>
								<label for="sm_cf_home">
									<select id="sm_cf_home" name="sm_cf_home"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_home")); ?></select>
									<?php _e('Homepage', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_posts">
									<select id="sm_cf_posts" name="sm_cf_posts"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_posts")); ?></select>
									<?php _e('Posts', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_pages">
									<select id="sm_cf_pages" name="sm_cf_pages"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_pages")); ?></select>
									<?php _e('Static pages', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_cats">
									<select id="sm_cf_cats" name="sm_cf_cats"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_cats")); ?></select>
									<?php _e('Categories', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_arch_curr">
									<select id="sm_cf_arch_curr" name="sm_cf_arch_curr"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_arch_curr")); ?></select>
									<?php _e('The current archive of this month (Should be the same like your homepage)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_arch_old">
									<select id="sm_cf_arch_old" name="sm_cf_arch_old"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_arch_old")); ?></select>
									<?php _e('Older archives (Changes only if you edit an old post)', 'sitemap') ?>
								</label>
							</li>
							<?php if($this->sg->IsTaxonomySupported()): ?>
							<li>
								<label for="sm_cf_tags">
									<select id="sm_cf_tags" name="sm_cf_tags"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_tags")); ?></select>
									<?php _e('Tag pages', 'sitemap') ?>
								</label>
							</li>
							<?php endif; ?>
							<li>
								<label for="sm_cf_auth">
									<select id="sm_cf_auth" name="sm_cf_auth"><?php $this->sg->HtmlGetFreqNames($this->sg->GetOption("cf_auth")); ?></select>
									<?php _e('Author pages', 'sitemap') ?>
								</label>
							</li>
						</ul>
						
					<?php $this->HtmlPrintBoxFooter(); ?>
					
					<!-- Priorities -->
					<?php $this->HtmlPrintBoxHeader('sm_priorities',__('Priorities', 'sitemap')); ?>
					
						<ul>
							<li>
								<label for="sm_pr_home">
									<select id="sm_pr_home" name="sm_pr_home"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_home")); ?></select>
									<?php _e('Homepage', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_posts">
									<select id="sm_pr_posts" name="sm_pr_posts"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_posts")); ?></select>
									<?php _e('Posts (If auto calculation is disabled)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_posts_min">
									<select id="sm_pr_posts_min" name="sm_pr_posts_min"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_posts_min")); ?></select>
									<?php _e('Minimum post priority (Even if auto calculation is enabled)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_pages">
									<select id="sm_pr_pages" name="sm_pr_pages"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_pages")); ?></select>
									<?php _e('Static pages', 'sitemap'); ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_cats">
									<select id="sm_pr_cats" name="sm_pr_cats"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_cats")); ?></select>
									<?php _e('Categories', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_arch">
									<select id="sm_pr_arch" name="sm_pr_arch"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_arch")); ?></select>
									<?php _e('Archives', 'sitemap') ?>
								</label>
							</li>
							<?php if($this->sg->IsTaxonomySupported()): ?>
							<li>
								<label for="sm_pr_tags">
									<select id="sm_pr_tags" name="sm_pr_tags"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_tags")); ?></select>
									<?php _e('Tag pages', 'sitemap') ?>
								</label>
							</li>
							<?php endif; ?>
							<li>
								<label for="sm_pr_auth">
									<select id="sm_pr_auth" name="sm_pr_auth"><?php $this->sg->HtmlGetPriorityValues($this->sg->GetOption("pr_auth")); ?></select>
									<?php _e('Author pages', 'sitemap') ?>
								</label>
							</li>
						</ul>
						
					<?php $this->HtmlPrintBoxFooter(); ?>
					
					</div>
					<div>
						<p class="submit">
							<?php wp_nonce_field('sitemap') ?>
							<input type="submit" name="sm_update" value="<?php _e('Update options', 'sitemap'); ?>" />
							<input type="submit" onclick='return confirm("Do you really want to reset your configuration?");' class="sm_warning" name="sm_reset_config" value="<?php _e('Reset options', 'sitemap'); ?>" />
						</p>
					</div>
				
				<?php if($this->mode == 27): ?>
				</div>
				</div>
				<?php endif; ?>
				</div>
				<script type="text/javascript">if(typeof(sm_loadPages)=='function') addLoadEvent(sm_loadPages); </script>
			</form>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="sm_donate_form">
				<input type="hidden" name="cmd" value="_xclick" />
				<input type="hidden" name="business" value="<?php echo "donate" /* N O S P A M */ . "@" . "arnebra" . "chhold.de"; ?>" />
				<input type="hidden" name="item_name" value="Sitemap Generator for WordPress. Please tell me if if you don't want to be listed on the donator list." />
				<input type="hidden" name="no_shipping" value="1" />
				<input type="hidden" name="return" value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->sg->GetBackLink(); ?>&amp;sm_donated=true" />
				<input type="hidden" name="item_number" value="0001" />
				<input type="hidden" name="currency_code" value="USD" />
				<input type="hidden" name="bn" value="PP-BuyNowBF" />
				<input type="hidden" name="lc" value="US" />
				<input type="hidden" name="rm" value="2" />
				<input type="hidden" name="on0" value="Your Website" />
				<input type="hidden" name="os0" value="<?php echo get_bloginfo("home"); ?>"/>
			</form>
		</div>
		<?php
	}
}

