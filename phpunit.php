<html>
<head>
	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
	<style>
		body { margin: 20px; }
	</style>
</head>
<body>

	<pre><?php

		$remove = array(
			'[37;41m[2K',
			'[0m[2K',
			'[0m',
			'[31;1m',
		);

		$phpunit = '/Applications/MAMP/bin/php/php5.3.14/bin/phpunit';
		$config = dirname( __FILE__ ) . '/phpunit.xml';

		exec( "$phpunit -c '$config'", $output );

		$start = false;
		foreach ( $output as $line ) {
			if ( false !== strpos($line, 'phpunit.xml') ) {
				$start = true;
				$line = trim($line);
			}
			if ( $start ) {
				echo str_replace( $remove, '', $line) . '<br>';
			}
		}
		?>
	</pre>

</body>
</html>