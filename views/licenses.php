<?php
// @todo: add callback for validation
register_setting( 'styles-licenses', 'styles-api-key' );

global $current_user;
$current_plugin = STYLES_SLUG;
//Perform actions
if ( isset( $_POST['actionb'] ) ) {
	$body           = array(
		'action'   => 'licenses',
		'actionb'  => $_POST['actionb'],
		'hash'     => $this->plugins['userhash'],
		'wpuser'   => $current_user->user_login,
		'username' => isset( $_POST['username'] ) ? $_POST['username'] : $this->plugins['username'],
		'password' => isset( $_POST['password'] ) ? $_POST['password'] : '',
		'try_key'  => isset( $_POST['try_key'] ) ? $_POST['try_key'] : false,
	);
	if ( $_POST['actionb'] == 'logout' ) {
		$this->plugins[$this->plugin_slug] = $current_plugin;
		$this->save_plugin_options( true );
		?>
    <div class='updated'><p><strong>You have been logged out.</strong></p></div>
	<?php
	} else {
		$response = $this->perform_remote_request( array( 'body' => $body ) );
	}

	switch ( $_POST['actionb'] ) {
		case 'license_site_manually':
		case 'license_site_existing':
		case 'license_site_new':
			//Add the key to the current site
			if ( $response->key_status == 'ok' ) {
				$current_plugin->key_status        = 'ok';
				$current_plugin->key               = $response->key;
				$this->plugins[$this->plugin_slug] = $current_plugin;
				$this->save_plugin_options();
			}
			break;
		case 'unlicense_site':
		case 'unlicense_site_existing':
			//Find out if the removed key matches the current site and remove it
			if ( $response->unset_key ) {
				if ( $_POST['try_key'] == $current_plugin->key ) {
					$current_plugin->key_status        = 'invalid';
					$current_plugin->key               = '';
					$this->plugins[$this->plugin_slug] = $current_plugin;
					$this->save_plugin_options();
				}
			}
			break;
		default:
			break;
	} //end switch

	//Output errors or messages
	if ( isset( $response->errors ) && isset( $response->message ) && $response->errors ) {
		?>
    <div class='error'><p><strong><?php echo esc_html( $response->message ); ?></strong></p></div>
	<?php
	} elseif ( isset( $response->message ) ) {
		?>
    <div class='updated'><p><strong><?php echo esc_html( $response->message ); ?></strong></p></div>
	<?php
	}
	/*$response = $this->perform_remote_request(
		array(
			'body' => array(
				'action' => 'licenses',
				'actionb' => $_POST[ 'actionb' ],
				'hash' => $current_plugin->hash,
				'wpuser' => $current_user->user_login,
				'username' => isset( $_POST[ 'username' ] ) ? $_POST[ 'username' ] : $current_plugin->username,
				'password' => isset( $_POST[ 'password' ] ) ? $_POST[ 'password' ] : '',
				'trykey' => isset( $_POST[ 'trykey' ] ) ? $_POST[ 'trykey' ] ? false,
				'key' => $current_plugin->key
			)
		)
	);
	die( print_r( $response, true ) );*/
}

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
global $current_user;
$self_url = add_query_arg( array( 'slug' => STYLES_SLUG, 'action' => STYLES_SLUG.'licenses', '_ajax_nonce' => wp_create_nonce( STYLES_SLUG.'licenses' ) ),
	admin_url( 'admin-ajax.php' ) );
$licenses = array();

//Get licensing info from the user
$hash     = isset( $this->plugins['userhash'] ) ? $this->plugins['userhash'] : '';
$username = isset( $this->plugins['username'] ) ? $this->plugins['username'] : '';
if ( empty( $hash ) && isset( $_POST['password'] ) ) {
	$hash = $current_user->user_login;
	$hash .= $_POST['password'];
	$hash .= str_replace( 'https://', 'http://', site_url() );
	$hash = md5( $hash );
}

$response = $this->perform_remote_request(
	array(
		'body' => array(
			'action'   => 'licenses',
			'actionb'  => 'login',
			'hash'     => $hash,
			'wpuser'   => $current_user->user_login,
			'username' => isset( $_POST['username'] ) ? $_POST['username'] : $username,
			'password' => isset( $_POST['password'] ) ? $_POST['password'] : ''
		)
	)
);
$licenses = is_array( $response->licenses ) ? $response->licenses : array();
if ( $response->authenticated ) {
	$this->plugins['userhash']         = $response->hash;
	$this->plugins['username']         = $response->username;
	$this->plugins[$this->plugin_slug] = $current_plugin;
	$this->save_plugin_options();
	$this->authenticated = true;
} elseif ( isset( $_POST['password'] ) && !$response->authenticated ) {
	?>
<div class='error'><p><strong>Username and/or password is invalid.</strong></p></div>
	<?php
}
?>
<body>

<div class='wrap' style="width: 97%">
    <!--User Login Form-->
	<?php if ( !$this->authenticated ) : ?>
    <div class="rounded">
        Log in with your Styles account to retrieve your license key<br /><br />

        <form method="post" action="<?php echo esc_url( $self_url ); ?>">
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
    <form method="post" id="styles-form" action="" name="post">

		<?php
		settings_errors();
		settings_fields( 'styles-licenses' ); // includes nonce
		//$api_key = $this->styles->wp->get_option( 'api_key' );
		$api_key = '';
		?>

        <input value="<?php esc_attr_e( $api_key ) ?>" name="styles_api_key" id="styles_api_key" type="text" class="regular-text" />

        <p>This license key is used for access to theme upgrades and support.

        <p class="submit">
            <input class="button-primary" type="submit" value="<?php _e( 'Save API Key' ); ?>" />
        </p>

    </form>
	<?php else: ?>

<div id="pluginbuddy-tabs">
			<ul>
                <li><a href="#pluginbuddy-tabs-1"><span>Site License</span></a></li>
                <li><a href="#pluginbuddy-tabs-2"><span>Manage Licenses</span></a></li>
            </ul>
			<div class="tabs-borderwrap">
				<div id="pluginbuddy-tabs-1">
                    <h3>Existing Site License</h3>
					<?php if ( $current_plugin->key_status == 'ok' ) : /* Active license for the site */ ?>
                    <p><label>Status: </label><strong>Active License</strong> ( <?php echo esc_attr( $current_plugin->key ); ?> )</p><br />
                    This site is currently licensed so it qualifies for automatic upgrades & support for this product.
                    If you no longer wish to use this product on this site you may detach the license to use elsewhere.<br />
                    <form method="post" action="<?php echo esc_url( $self_url ); ?>">
                        <input type="hidden" name="actionb" value="unlicense_site" />
                        <input type="hidden" name="userhash" value="<?php echo esc_attr( $this->plugins['userhash'] ); ?>" />
                        <input type="hidden" name="try_key" value="<?php echo esc_attr( $current_plugin->key ); ?>" /><br />
                        <input class="button-secondary" type="submit" name="submit" value="Detach License from Site" />
                    </form>
					<?php else:
					//No License
					?>
                    <p><label>Status: </label><strong>No License</strong><br />This site is currently not licensed. Please generate a new license key for this site.</p>
                    <form method="post" action="<?php echo esc_url( $self_url ); ?>">
                        <input type="hidden" name="actionb" value="license_site_new" />
                        <input class="button-primary" type="submit" name="submit" value="Generate Key" />
                    </form>
					<?php
				endif;
					?>
                    <form method="post" id="styles-form" action="" name="post">

						<?php
						settings_errors();
						settings_fields( 'styles-licenses' ); // includes nonce
						//$api_key = $this->styles->wp->get_option( 'api_key' );
						$api_key = '';
						?>

                        <input value="<?php esc_attr_e( $api_key ) ?>" name="styles_api_key" id="styles_api_key" type="text" class="regular-text" />

                        <p>This license key is used for access to theme upgrades and support.

                        <p class="submit">
                            <input class="button-primary" type="submit" value="<?php _e( 'Save API Key' ); ?>" />
                        </p>

                    </form>
                </div><!-- end tab 1-->
				<div id="pluginbuddy-tabs-2">
					<h3>Manage Existing Licenses</h3>
	<?php
	/* Existing licenses */
	if ( count( $licenses ) <= 0 ) {
		?>
        <p><strong>There are no licenses to manage yet.</strong></p>
		<?php
	}
	$key_count = 0;
	foreach ( $licenses as $license ) {
		if ( $key_count == 0 ) {
			?>
            <strong>Existing Keys & Associated Sites:</strong>
							<div class='rounded'>
							<?php
		} else {
			?>
							<div class='keys'>
							<?php
		}

		if ( $license->license_status == '1' ) {
			?>
            <form style="float: right;" method="post" action="<?php echo esc_url( $self_url ); ?>">
                <input type="hidden" name="actionb" value="unlicense_site_existing" />
                <input type="hidden" name="try_key" value="<?php echo esc_attr( $license->key ); ?>" />
                <input type="hidden" name="userhash" value="<?php echo esc_attr( $this->plugins['userhash'] ); ?>" />
                <input class="button-secondary" type="submit" name="submit" value="Detach key from site" style="float: right;" />
            </form>
			<?php
		} else {
			?>
            <div style='float:right'>
                <form method="post" action="<?php echo esc_url( $self_url ); ?>">
                    <input type="hidden" name="try_key" value="<?php echo esc_attr( $license->key ); ?>" />
                    <input type="hidden" name="userhash" value="<?php echo esc_attr( $this->plugins['userhash'] ); ?>" />
                    <input type="hidden" name="actionb" value="license_site_existing" />
                    <input class="button-secondary" type="submit" name="submit" value="Attach key to this site" />
                </form>
                <form method="post" action="<?php echo esc_url( $self_url ); ?>">
                    <input type="hidden" name="try_key" value="<?php echo esc_attr( $license->key ); ?>" />
                    <input type="hidden" name="userhash" value="<?php echo esc_attr( $this->plugins['userhash'] ); ?>" />
                    <input type="hidden" name="actionb" value="delete_expired" />
                    <input class="button-secondary" type="submit" name="delete" value="Delete this key" />
                </form>
            </div>
			<?php
		}
		echo esc_html( $license->key ).'<br />';
		echo '<small>'.esc_url( $license->siteurl ).'<br />';
		if ( $license->license_status == '1' ) {
			echo 'Active';
		} else {
			echo 'Inactive';
		}
		echo '</small>';
		if ( $key_count > 0 ) {
			echo '</div>';
		}

		$key_count++;
	} //end foreach licenses
	?>
				</div><!-- end tab 2-->
			</div><!-- end tab container-->

	<?php endif; /* End if ! authenticated */ ?>
	<?php if ( $this->authenticated ) : ?>
    <form method="post" action="<?php echo esc_url( $self_url ); ?>">
        <input type="hidden" name="actionb" value="logout" />
        <input class="button-secondary" style="display: block; float: right; margin-right: 10px;" type="submit" name="submit" value="Logout" />
    </form>
	<?php endif; ?>
</div><!-- /. wrap-->
	<?php
	do_action( 'admin_footer', '' );
	do_action( 'admin_print_footer_scripts' );
	?>
</body>
</html>