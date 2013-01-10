<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title><?php esc_html_e( 'Styles Licenses', 'styles' ); ?></title>
	<?php
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
<body class="wp-admin wp-core-ui no-js auto-fold admin-bar branch-3-5 version-3-5 admin-color-fresh locale-en-us no-customize-support>

<div class='wrap' style="width: 97%">
    <!--User Login Form-->

    <div id="poststuff">

    <div class="rounded">
        <div id="icon-options-general" class="icon32"></div>
	    <h2><?php esc_html_e( 'Styles Plugin Licenses', 'styles' ); ?></h2>
        <h3><?php esc_html_e( 'Log in with your Styles account to retrieve your license key', 'styles' ); ?></h3>

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
                    <td colspan='3'><input type="submit" class="button-primary" name="login" value="<?php esc_attr_e( 'Log In', 'styles' ); ?>" /></td>
                </tr>
            </table>
        </form>
    </div>

    <div class="rounded">
        <h3><?php esc_html_e( 'Manually enter your license key', 'styles' ); ?></h3>

        <form method="post" id="styles-form" action="">

			<?php $this->api_key_field(); ?>

            <p class="submit">
                <input class="button-secondary" type="submit" value="<?php _e( 'Save API Key', 'styles' ); ?>" />
            </p>

        </form>
	</div>

	</div>

</div>
<!-- /. wrap-->
<?php
do_action( 'admin_footer', '' );
do_action( 'admin_print_footer_scripts' );
?>
</body>
</html>