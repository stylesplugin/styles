<?php

define('PDM_CONSTANTS_XML', PDM_LIB.'/cache/constants.xml');

/*
	Define CSS Constants structure and meta
*/
function pdm_options_css_structure() {
	$c = new stdClass();
	
	###	Tabs
	$c->top_tabs->type							= 'header';
	$c->top_tabs->title							= 'Top Tabs';
	
	$c->tab_padding_top->type					= 'slider';
	$c->tab_padding_top->title					= 'Top Padding';
	$c->tab_padding_top->description			= 'You know... for the tabs';
	$c->tab_padding_top->unit					= 'px';
	                                    		
	$c->tab_padding_right->type					= 'slider';
	$c->tab_padding_right->unit					= 'px';
	                                    		
	$c->tab_padding_bottom->type				= 'slider';
	$c->tab_padding_bottom->unit				= 'px';
	                                    		
	$c->tab_padding_left->type					= 'slider';
	$c->tab_padding_left->unit					= 'px';
	
	$c->tab_border_top_width->type				= 'slider';  
	$c->tab_border_left_width->type 			= 'slider';
	$c->tab_border_right_width->type			= 'slider';
	$c->tab_border_bottom_width->type			= 'slider';
	$c->tab_border_top_width->unit				= 'px';  
	$c->tab_border_left_width->unit 			= 'px';
	$c->tab_border_right_width->unit			= 'px';
	$c->tab_border_bottom_width->unit			= 'px';
	
	$c->tab_a_height->type						= 'slider'; 
	$c->tab_a_height->unit						= 'px'; 
	
	$c->tab_a_font_size->type					= 'slider';
	$c->tab_a_font_size->unit					= 'px';
	
	$c->tab_border_top_color->type				= 'hex';  
	$c->tab_border_left_color->type 			= 'hex';
	$c->tab_border_right_color->type			= 'hex';
	$c->tab_border_bottom_color->type			= 'hex';
	
	$c->a_text->type							= 'hex';
	$c->tab_background_hover->type				= 'hex';
	$c->tab_background->type					= 'hex';
	
	###	Sub menu
	$c->sub_menu->type							= 'header';
	$c->sub_menu->title							= 'Sub menu';
	
	$c->sub_a_height->type						= 'slider'; 
	$c->sub_a_height->unit						= 'px'; 
	    
	$c->sub_a_font_size->type					= 'slider';
	$c->sub_a_font_size->unit					= 'px';
	                                    	
	$c->sub_width->type							= 'slider';
	$c->sub_width->unit							= 'em';
	                                    		
	$c->sub_top->unit							= 'px';
	                                    		
	$c->sub_background->type					= 'hex';
	$c->sub_sub_background->type				= 'hex';
	
	###	Constants
	$c->fontpx									= '10';
	
	foreach( $c as $key => $v ) {
		if (empty($v->default)) {
			if (
				strpos($key, 'width') !== false
				|| strpos($key, 'padding') !== false
			) {
				$v->default = '0';
			}
			
			if ( strpos($key, 'color') !== false ) {
				$v->default = 'transparent';
			}
		}
	}
	
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


function pdm_css_constants_calculate_dependencies($o) {
	$o = (object) $o;
	$c = pdm_options_css_structure();
	
	// Line up top of submenu with bottom of tabs
	$o->sub_top = $o->tab_a_height + $o->tab_padding_top + $o->tab_padding_bottom + $o->tab_border_top_width + $o->tab_border_bottom_width ;
	
	return (array) $o;
}
add_filter('pdm_css_constants_array', 'pdm_css_constants_calculate_dependencies', 30);

function pdm_css_constants_add_units($opts) {
	$c = pdm_options_css_structure();
	
	foreach ($opts as $key => $val) {
		if (!empty($c->$key->unit)) {
			// Convert PX to EMs
			if ($c->$key->unit == 'px') {
				$opts[$key] = ( (int)$val / $c->fontpx ).'em';
			}else {
				$opts[$key] = $val.$c->$key->unit;
			}
			
			
		}
	}
	
	return $opts;
}
add_filter('pdm_css_constants_array', 'pdm_css_constants_add_units', 50);

function pdm_css_constants_format_hex($o) {
	$c = pdm_options_css_structure();
	
	foreach ($o as $key => $val) {
		if ($c->$key->type == 'hex') {
			$o[$key] = '#'.str_replace('#','',$val); // Regex filter would be nicer
		}
	}
	
	return $o;
}
add_filter('pdm_css_constants_array', 'pdm_css_constants_format_hex', 60);

function pdm_css_constants_set_defaults($o) {
	$c = pdm_options_css_structure();
	
	foreach ($o as $key => $val) {
		if (empty($val)) {
			$o[$key] = $c->$key->default;
		}
	}
	
	return $o;
}
add_filter('pdm_css_constants_array', 'pdm_css_constants_set_defaults', 70);


function pdm_options_quick_css_fields() {
	$structure = pdm_options_css_structure();
	extract( pdm_get_options( $options, $options_excluded ) ); // Returns $css array of values
	$option = 'pdm_options[css]';
	
	foreach ($structure as $key => $s) {
		$id = "{$option}[{$key}]";
		switch($s->type) {
			case 'header':
				?>
				<tr valign="top">
					<th scope="row" class="pdm_form-h2"><h2><?php echo $s->title ?>:</h2></th>
					<td class="pdm_form-update"><p class="submit pdm_submit"><input type="submit" name="Submit" value="Update &raquo;" /></p></td>
				</tr>
				<?php
				break;
				
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
						<input class="pdm_hex" type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $value ?>" size="8" maxlength="8" />
					</td>
				</tr>
				<?php
				break;
		}
	}
}

