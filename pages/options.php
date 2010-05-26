<?php 
/*
Read Creating Option Pages - WordPress Codex: http://codex.wordpress.org/Creating_Options_Pages

Wordpress handles the management of options automatically.  We have made this so it will be easy for you
to add options to your plugin.  The code has been created to handle multiple option pages, so feel free
to create additional option pages using this sample.
*/


/**
 * $pdm_options_current_page
 * 
 * Defines all the options used that are used in the <form> on this page.
 * 
 * Note: There are procedures that run inside the pdm_options_html <form> that do the following:
 *       1) Automatically extract the values for each element in the array so they can be used as variables. 
 *       2) Automatically creates the hidden <input> 'option_page' and 'action'. Sets their values 
 * 			so Wordpress can handle saving the values in the option page automatically.
 * 			
 * 
 * $pdm_options_current_page_excluded
 * 
 * Excludes all the options used above that will not be displayed in the form on this page.
 * Note: If you are not using one or more of the options listed above, you must specify them in this array
 *       otherwise their values will be reset to blank or zero.  If you don't want to create an exception, 
 *       then you must create an invisible <input> and add the current value to it inside the form. 
 */

require_once(PDM_LIB.'/xml_constants/xml_constants.php');

pdm_options_page_options();

function pdm_options_page_options() {

	$options = array (
		'pdm_options' => array(
			'purge_data' => pdm_PURGE_DATA, #  When plugin is deactivated, if 'true', all tables, and options will be removed.
			
			'css' => pdm_object_to_array( pdm_options_css_structure() ),
			
			'pdm_dev_force_update' => '0',
			
			// ------------------------------- //
			'sample_1' => 'Sample Text #1',
			'sample_2' => 1,
			'sample_3' => 1,
			'sample_4' => 'Item 2',
			'sample_5' => 'orange',
			'sample_5_text' => '',
			'sample_6' => '#000000',
			'sample_7' => '#000000',
			'facebox_sample1' => 'click the blue circle',
			'facebox_sample2' => 'click the blue circle',
		),
	);

	pdm_merge_all_options( $options );
	
	return $options;
}



pdm_options_page_options_excluded();

function pdm_options_page_options_excluded() {

	$options = array();
	
	return $options;
}


function pdm_options_page_submenu() {

	pdm_options_page_html(); 	// display the options page.
}


/**
 *
 * Displays the HTML code for this page
 *
 */
function pdm_options_page_html() {

	$options = pdm_options_page_options();
	$options_excluded = pdm_options_page_options_excluded();

	extract( pdm_get_options( $options, $options_excluded ) );
	FB::log($css, '$css');
	?>

  	<div class="wrap">
  	
	<?php pdm_header_icon(); ?>
	<h2>PD Menu Options</h2>
	
	<form method="post" id="pdm_form" action="options.php" enctype="multipart/form-data" name="post">

	<?php wp_nonce_field('update-options'); ?>
	
	<p>This is the PD Menu Setup and Configuration page.&nbsp;&nbsp;When you have completed your enteries, click on the Update button to save your changes.</p>
	
	<?php do_action('pd_test'); ?>
	
	<!-- Default Settings: -->
	<table class="form-table pdm_form-table">
		
		<input name="pdm_options[pdm_dev_force_update]" value="<?php echo rand(1,1000); ?>" type="hidden" />
		
		<?php pdm_options_quick_css_fields(); ?>
		
		
		<?php /*
		
		<tr valign="top">
			<th scope="row" class="pdm_form-h2"><h2>Default Settings:</h2></th>
			<td class="pdm_form-update"><p class="submit pdm_submit"><input type="submit" name="Submit" value="Update &raquo;" /></p></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="pdm_options[sample_1]">Sample #1 - Text:</label></th>
			<td>
				<input type="text" name="pdm_options[sample_1]" value="<?php echo $sample_1; ?>"/>
				&nbsp;&nbsp; Description for Sample #1.
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="pdm_options_sample_2">Sample #2 - Checkbox:</label></th>
			<td>
				<input type="checkbox" name="pdm_options[sample_2]" id="pdm_options_sample_2" value="1" <?php echo (!strcmp($sample_2, 'On' ) || !strcmp($sample_2, '1' )) ? ' checked="checked"' : ''; ?> />
				&nbsp;&nbsp;Description for Sample #2.
			</td>
		</tr>
	
		<tr valign="top">
			<th scope="row"><label for="pdm_options_sample_3">Sample #3 - Checkbox Inactive:</label></th>
			<td>
				<input type="checkbox" <!-- name="pdm_options_sample_3" --> id="pdm_options_sample_3" value="1" disabled="disabled" <?php echo (!strcmp($sample_3, 'On' ) || !strcmp($sample_3, '1' )) ? ' checked="checked"' : ''; ?> />
				&nbsp;&nbsp;Sample #3 checkbox has been disabled.  Use <strong>[disabled="disabled"]</strong> inside the HTML <code>&#8249;input&#8250;</code> tag.
				<input type='hidden' name='pdm_options[sample_3]' value='<?php echo $sample_3; ### Required, for a disabled input value. If this hidden field is not included the value will be reset to fales and show as inactive/not checked. ?>' />
			</td>
		</tr>			
		
		<?php $sample_4_list = array('Item 1', 'Item 2', 'Item 3', 'Item 4'); ?>
		<tr valign="top">
			<th scope="row"><label for="pdm_options_sample_4">Sample #4 - Select list:</label></th>
			<td>
				<select name="pdm_options[sample_4]" id="pdm_options_sample_4" />
					<?php foreach ( $sample_4_list as $option ) : ?> 
						<option <?php if (!strcmp( $sample_4, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option>
					<?php endforeach;?>
				</select>
				&nbsp;&nbsp;Sample #4 has a routine that automatically chooses the selected item in the list.
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label>Sample #5 - Radio Buttons:</label></th>
			<td>
				<div>
					<input id="pdm_options_sample_5" type="radio"<?php echo ((empty($sample_5))||($sample_5 == 'apple')) ? ' checked="checked"' : '' ?> name="pdm_options[sample_5]" value="apple" /> <label for="pdm_options_sample_5">Apple</label> (Description of apple.)
				</div>
				<div>
					<input id="pdm_options_sample_5" type="radio"<?php echo ($sample_5 == 'banana') ? ' checked="checked"' : '' ?> name="pdm_options[sample_5]" value="banana" /> <label for="pdm_options_sample_5">Banana</label> (Description of banana.)
				</div>
				<div>
					<input id="pdm_options_sample_5" type="radio"<?php echo ($sample_5 == 'orange') ? ' checked="checked"' : '' ?> name="pdm_options[sample_5]" value="orange" /> <label for="pdm_options_sample_5">Orange</label> (Description of orange.)
				</div>
				<div>
					<input id="pdm_options_sample_5" type="radio"<?php echo ($sample_5 == 'custom') ? ' checked="checked"' : '' ?> name="pdm_options[sample_5]" value="custom" /> <label for="pdm_options_sample_5">Choose your own fruit:</label> (Describe details below)
					<BR><textarea style="padding-left:20px;" rows="4" cols="40" name="pdm_options[sample_5_text]"><?php echo $sample_5_text; ?></textarea>
				</div>
			</td>
		</tr>
				
       <!-- Start: Fabrastic Color Picker -->     
       <tr valign="top">
			<th scope="row"><label for="pdm_options_sample_6">Sample #6 - Color Selection:</label></th>
			<td>
				<input class="pdm_colorpicker_text" type="text" name="pdm_options[sample_6]" id="pdm_options_sample_6" value="<?php echo preg_replace('/^0x/', '', $sample_6);?>" size="8" maxlength="8" />&nbsp;&nbsp;
				<input class="pdm_colorpicker" readonly="true" name="pdm_options[sample_6_color]" style="background:<?php echo preg_replace('/^0x/', '', $sample_6);?>" />&nbsp;&nbsp;(Click on the square to change the color.)
			</td>
		</tr>
		<!-- End: Fabrastic Color Picker -->
	
		<!-- Start: Fabrastic Color Picker -->     
       <tr valign="top">
			<th scope="row"><label for="pdm_options_sample_7">Sample #7 - Color Selection:</label></th>
			<td>
				<input class="pdm_colorpicker_text" type="text" name="pdm_options_sample_7" id="pdm_options_sample_7" value="<?php echo preg_replace('/^0x/', '', $sample_7);?>" size="8" maxlength="8" />&nbsp;&nbsp;
				<input class="pdm_colorpicker" readonly="true"  name="pdm_options_sample_7_color" style="background:<?php echo preg_replace('/^0x/', '', $sample_7);?>" />&nbsp;&nbsp;(Click on the square to change the color.)
			</td>
		</tr>
		<!-- End: Fabrastic Color Picker -->
		

		
       <!-- Start: Facebox Text Sample -->     
       <tr valign="top">
			<th scope="row">
				<label for="pdm_options_facebox_sample1">
					<a class="help-label" href="#info" rel="facebox">Facebox Text Sample:<img class="help-label-img" src="<?php echo plugins_url('images/information.png', pdm_FILE_PATH); ?>"></a>		
				</label>
				<div id="info" style="display:none;">
				    <h4>Facebox Text Sample</h4>
				    <p>This is great for adding comments to your plugin.&nbsp;&nbsp;You can provide a detailed explanation as to what this input field does. </p>
				    <p><a href="http://famspam.com/facebox" target="_blank">Facebox</a> is a great popup widget and you can do more with it.&nbsp;&nbsp;Please take the time to check out their <a href="http://famspam.com/facebox" target="_blank">webpage</a>.</p>
				</div>
				</div>
			</th>
			<td>
				<input type="text" name="pdm_options_facebox_sample1" value="<?php echo $facebox_sample1; ?>"/>
				&nbsp;&nbsp; Click on the circular 'i' to get a text popup.
			</td>
		</tr>
		<!-- End: Facebox Text Sample -->
		
		
       <!-- Start: Facebox Popup Sample -->     
       <tr valign="top">
			<th scope="row">
				<label for="pdm_options_facebox_sample2">
					<a class="help-label" href="<?php echo plugins_url('widgets/facebox/stairs.jpg', pdm_FILE_PATH); ?>" rel="facebox">Facebox Image Sample:<img class="help-label-img" src="<?php echo plugins_url('images/information.png', pdm_FILE_PATH); ?>"></a>		
				</label>
			</th>
			<td>
				<input type="text" name="pdm_options_facebox_sample2" value="<?php echo $facebox_sample2; ?>"/>
				&nbsp;&nbsp; Click on the circular 'i' to get an image popup.
			</td>
		</tr>
		<!-- End: Facebox Popup Sample -->
		*/ ?>
	</table>
	

	<!-- Start: Purge Data -->
	<table class="form-table pdm_form-table pdm_form-table-highlight">
		<tr valign="top">
			<th scope="row" class="pdm_form-h2"><h2>Deactivation:</h2></th>
			<td class="pdm_form-update"><p class="submit pdm_submit"><input type="submit" name="Submit" value="Update &raquo;" /></p></td>
		</tr>
		<tr valign="top" class="pdm_highlight-option">
			<th scope="row"><label for="pdm_options_purge_data">Delete All Data Upon Deactivation:</label></th>
			<td class="td_deactivate">
				<input type="checkbox" name="pdm_options_purge_data" id="pdm_options_purge_data" value="1" <?php echo (!strcmp($purge_data, 'On' ) || !strcmp($purge_data, '1' )) ? ' checked="checked"' : ''; ?> />&nbsp;&nbsp;<?php _e("All data and options created by PD Menu will be purged when the plugin is deactivated if selected"); ?>
			</td>
		</tr>			
	</table>
	<!-- End: Purge Data -->
	
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="<?php pdm_get_option_list( $options, $options_excluded ); ?>" />
	
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Update'); ?>" />
	</p>
	
	</form>
	</div>
	
<?php } ?>