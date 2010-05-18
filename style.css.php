<?php
	header("Content-type: text/css");
	
	$find_replace = array(
		'mc' => '.pdn1',
		'a_col' => 'red',
		'li_div_padding' => '1em',
		'ul_width' => '10em',
		'sub_li_top' => '4.5em',
	);
	foreach ($find_replace as $key => $val) {
		$find_replace['$'.$key] = $val;
		unset($find_replace[$key]);
	}
	
	ob_start();
?>

<? include('lib/superfish/css/superfish.css'); ?>

$mc li {
	list-style-type: none;
	margin:0;
	padding:0;
	float:left;
}
$mc li div {
	padding: $li_div_padding;
	border:1px solid red;
}
$mc a {
	color: $a_col !important;
}

<?php 
	$css = ob_get_contents();
	ob_end_clean();
	echo str_replace(array_keys($find_replace), array_values($find_replace), $css);
?>