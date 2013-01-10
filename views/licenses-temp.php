<?php
// @todo: add callback for validation
//register_setting( 'styles-licenses', 'styles-api-key' );

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title><?php esc_html_e( 'Styles Licenses', 'styles' ); ?></title>
	<?php
	wp_admin_css( 'global' );
	wp_admin_css( 'admin' );
	wp_admin_css();
	wp_admin_css( 'colors' );
	do_action( 'admin_print_styles' );
	do_action( 'admin_print_scripts' );
	do_action( 'admin_head' );
	?>
</head>
<?php
if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
	$this->validate_user_pass( $_POST['username'], $_POST['password'] );
}
if ( isset( $_POST['license'] ) ) {
	$this->validate_manual_license( $_POST['license'] );
}

?>
<body>

<div class='wrap' style="width: 97%">
    <!--User Login Form-->

    <div class="rounded">
        Log in with your Styles account to retrieve your license key<br /><br />

        <form method="post" action="">
            <table>
                <tr>
                    <td><label for='username'>Username</label></td>
                    <td>&nbsp;&nbsp;</td>
                    <td><label for='password'>Password</label></td>
                </tr>
                <tr>
                    <td><input type="text" id='username' name="username" size="15" value="" /></td>
                    <td>&nbsp;&nbsp;</td>
                    <td><input type="password" id='password' name="password" size="15" /></td>
                </tr>
                <tr>
                    <td colspan='3'><input type="submit" class="button-secondary" name="login" value="Log In" class="label" /></td>
                </tr>
            </table>
        </form>
    </div>

	<h2>Manually enter your license key</h2>
    <form method="post" id="styles-form" action="">

		<?php
		//settings_errors();
		//settings_fields( 'styles-licenses' ); // includes nonce
		//$api_key = $this->styles->wp->get_option( 'api_key' );
	    //@todo use api_key_field from class
		$api_key = '';
		?>

        <input value="<?php esc_attr_e( $api_key ) ?>" name="license" id="license" type="text" class="regular-text" />

        <p>This license key is used for access to theme upgrades and support.

        <p class="submit">
            <input class="button-primary" type="submit" value="<?php _e( 'Save API Key' ); ?>" />
        </p>

    </form>

</div><!-- /. wrap-->
	<?php
	do_action( 'admin_footer', '' );
	do_action( 'admin_print_footer_scripts' );
	?>
</body>
</html>