<?php

define('PDM_CONSTANTS_XML', PDM_LIB.'/cache/constants.xml');

/*
	Define CSS Constants structure and meta
*/
function pdm_options_css_structure() {
	$c = new stdClass();
	
	$c->ul_padding_top->type          = 'slider';
	$c->ul_padding_top->default       = '10';
	$c->ul_padding_top->title         = 'Top Padding';
	$c->ul_padding_top->description   = 'You know... for the tabs';
	$c->ul_padding_top->unit          = 'px';
	
	$c->ul_padding_right->type        = 'slider';
	$c->ul_padding_right->default     = '10';
	$c->ul_padding_right->unit        = 'px';
	
	$c->ul_padding_bottom->type       = 'slider';
	$c->ul_padding_bottom->default    = '10';
	$c->ul_padding_bottom->unit       = 'px';
	
	$c->ul_padding_left->type         = 'slider';
	$c->ul_padding_left->default      = '10';
	$c->ul_padding_left->unit         = 'px';
	
	$c->ul_width->type                = 'slider';
	$c->ul_width->default             = '2.5';
	$c->ul_width->unit       	      = 'em';
	
	$c->sub_li_top->type              = 'slider';
	$c->sub_li_top->default           = '3';
	$c->sub_li_top->unit              = 'em';
	
	$c->some_color->type              = 'hex';
	
	return (object)$c;
}

/**
 * Write constants.xml to be read by CSScaffold:
	<?xml version="1.0" ?>
	<constants>

		<constant>
			<name>foo</name>
			<value>bar</value>
		</constant>

	</constants>
 *
 */
function pdm_write_css_constants($oldvalue) {
	$opts = pdm_get_options();
	$opts = apply_filters('pdm_css_constants_array', $opts['css']);

	foreach ($opts as $key => $val) {
		$out .= '	<constant>'."\r"
				."		<name>$key</name>\r"
				."		<value>$val</value>\r"
				.'	</constant>'."\r";
	}
	
	$out = '<?xml version="1.0" ?>'."\r"
			.'<constants>'."\r\r"
				.$out
			."\r".'</constants>';
	
	$fh = fopen(PDM_CONSTANTS_XML, 'w') or die('Could not save constants. Please make sure permissions are set to 666 for '.PDM_CONSTANTS_XML);
	fwrite($fh, $out);
	fclose($fh);
	
}
$css_options = apply_filters( 'pdm_xml_constants_option', 'pdm_options' );
add_action('update_option_'.$css_options, 'pdm_write_css_constants');


function pdm_css_constants_add_units($opts) {
	$c = pdm_options_css_structure();
	
	foreach ($opts as $key => $val) {
		if (!empty($c->$key->unit)) {
			// Convert PX to EMs
			if ($c->$key->unit == 'px') {
				$opts[$key] = ((int)$val/10).'em';
			}else {
				$opts[$key] = $val.$c->$key->unit;
			}
			
			
		}
	}
	
	return $opts;
}
add_filter('pdm_css_constants_array', 'pdm_css_constants_add_units');

function pdm_options_quick_css_fields() {
	$structure = pdm_options_css_structure();
	extract( pdm_get_options( $options, $options_excluded ) ); // Returns $css array of values
	$option = 'pdm_options[css]';
	
	foreach ($structure as $key => $s) {
		$id = "{$option}[{$key}]";
		switch($s->type) {
			case 'slider':
				?>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo $id; ?>">
							<?php if (empty($s->title)) { echo $key; }else { echo $s->title; } ?>:
						</label>
					</th>
					<td>
						<input class="slider" type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $css[$key]; ?>" />
						<?php echo $s->description; ?>
					</td>
				</tr>
				<?php
				break;
				
			case 'hex':
				$id_color = "{$option}[{$key}_color]";
				$value = preg_replace('/^0x/', '', $css[$key]);
				?>
		       <tr valign="top" class="hex">
					<th scope="row">
						<label for="<?php echo $id; ?>">
							<?php if (empty($s->title)) { echo $key; }else { echo $s->title; } ?>:
						</label>
					</th>
					<td>
						<input class="pdm_colorpicker_text" type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $value ?>" size="8" maxlength="8" />&nbsp;&nbsp;
						<input class="pdm_colorpicker" readonly="true" <?php /* name="<?php echo $id_color; ?>" */ ?> style="background:<?php echo $value; ?>" />
						(Click on the square to change the color.)
					</td>
				</tr>
				<?php
				break;
		}
	}
}


function array_flatten_sep($sep, $array) {
	$result = array();
	$stack = array();
	array_push($stack, array("", $array));

	while (count($stack) > 0) {
		list($prefix, $array) = array_pop($stack);

		foreach ($array as $key => $value) {
			$new_key = $prefix . strval($key);

			if (is_array($value)) {
				array_push($stack, array($new_key . $sep, $value));
			} else {
				$result[$new_key] = $value;
			}
		}
	}

	return $result;
}