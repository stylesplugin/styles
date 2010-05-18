<?php
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
	
	$flat = array_flatten_sep('_', $opts);

	foreach ($flat as $key => $val) {
		$out .= '	<constant>'."\r"
				."		<name>$key</name>\r"
				."		<value>$val</value>\r"
				.'	</constant>'."\r";
	}
	
	$out = '<?xml version="1.0" ?>'."\r"
			.'<constants>'."\r\r"
				.$out
			."\r".'</constants>';
	
	$file = PDM_LIB.'/cache/constants.xml';
	$fh = fopen($file, 'w') or die("Could not open constants.xml. Please make sure permissions are set to 666 for $file");
	fwrite($fh, $out);
	fclose($fh);
	
}

$css_options = apply_filters( 'pdm_xml_constants_option', 'pdm_options' );
add_action('update_option_'.$css_options, 'pdm_write_css_constants');

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