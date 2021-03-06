<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_uri(); ?>"><?php bb_option('name'); ?></a> &raquo; <?php _e('Log in'); ?></h3>

<h2 id="userlogin"><?php isset($_POST['user_login']) ? _e('Log in Failed') : _e('Log in') ; ?></h2>

<form method="post" action="<?php bb_uri('bb-login.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS); ?>">
<fieldset>
<table>
<?php
	$user_login_error = $bb_login_error->get_error_message( 'user_login' );
	$user_email_error = $bb_login_error->get_error_message( 'user_email' );
	$user_password_error = $bb_login_error->get_error_message( 'password' );
?>
	<tr valign="top" class="<?php if ( $user_login_error || $user_email_error ) echo 'error'; ?>">
		<th scope="row"><label for="user_login"><?php _e('Username:'); ?></label></th>
		<td><input name="user_login" id="user_login" type="text" value="<?php echo $user_login; ?>" /><?php
		if ( $user_login_error )
			echo "<br />$user_login_error";
		if ( $user_email_error )
			echo "<br />$user_email_error";
		?></td>
	</tr>
	<tr valign="top" class="<?php if ( $user_password_error ) echo 'error'; ?>">
		<th scope="row"><label for="password"><?php _e('Password:'); ?></label></th>
		<td><input name="password" id="password" type="password" /><?php
		if ( $user_password_error )
			echo "<br />$user_password_error";
		?></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="remember"><?php _e('Remember me:'); ?></label></th>
		<td><input name="remember" type="checkbox" id="remember" value="1"<?php echo $remember_checked; ?> /></td>
	</tr>
	<tr>
		<th scope="row">&nbsp;</th>
		<td>
			<input name="re" type="hidden" value="<?php echo $redirect_to; ?>" />
			<input type="submit" value="<?php echo attribute_escape( isset($_POST['user_login']) ? __('Try Again &raquo;'): __('Log in &raquo;') ); ?>" />
			<?php wp_referer_field(); ?>
		</td>
	</tr>
</table>
</fieldset>
</form>

<h2 id="passwordrecovery"><?php _e( 'Password Recovery' ); ?></h2>
<form method="post" action="<?php bb_uri('bb-reset-password.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS); ?>">
<fieldset>
	<p><?php _e('To recover your password, enter your information below.'); ?></p>
	<table>
		<tr valign="top">
			<th scope="row">
				<label for="user_login_reset_password"><?php _e( 'Username:' ); ?></label>
			</th>
			<td>
				<input name="user_login" id="user_login_reset_password" type="text" value="<?php echo $user_login; ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"></th>
			<td>
				<input type="submit" value="<?php echo attribute_escape( __('Recover Password &raquo;') ); ?>" />
			</td>
		</tr>
	</table>
</fieldset>
</form>

<?php bb_get_footer(); ?>
