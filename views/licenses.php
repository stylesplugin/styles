<?php
add_settings_field(
	'styles-api-key', // Unique ID
	'Support License Key', // Label
	'api_key_field', // Display callback
	'styles-licenses', // Form page
	'', // Form section
	null // Args passed to callback
);

function api_key_field() {
	//$api_key = $this->styles->wp->get_option( 'api_key' );
$api_key = '';
	?>

<input value="<?php esc_attr_e( $api_key ) ?>" name="styles_api_key" id="styles_api_key" type="text" class="regular-text" />
<p>This license key is used for access to theme upgrades and support.

<?php
}