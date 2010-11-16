<?php

/**
 * Get CSS variables from Scaffold, iterate down the tree
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Variable extends Scaffold_Extension_Observer {
	
	/**
	 * Loaded CSS file path
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $file;
	
	/**
	 * Path to CSS in a form appropriate for use as an array key
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $permalink;
	
	/**
	 * Variables loaded from CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $variables;
	
	/**
	 * Container for the scaffold object
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $scaffold;
	
	function __construct( $args = array() ) {
		
		$this->file = PDStyles::plugin_dir_path() . 'example/vars.css';
		$this->permalink = PDStyles::get_css_permalink( $this->file );

		$this->scaffold_init();
		
		$this->variables_load( $this->file );
		
		foreach( $this->variables as $key => &$group ) {
			
			$group = new PDStyles_Extension_Group( $group, $this->permalink );
			
			// Remove empty groups
			if ( empty( $group->variables ) ) {
				unset( $this->variables[$key] );
			}
		}
	}
	
	/**
	 * Initialize CSScaffold
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function scaffold_init() {
		// Scaffold Configuration
		$config = array(
			'extensions' => array(
				'Variables',
				// 'XMLVariables'
			)
		);

		// Setup the env
		date_default_timezone_set('GMT');
		$system = PDStyles::plugin_dir_path() . 'scaffold';
		$environment = $system.'/lib/Scaffold/Environment.php';
		
		if ( @require_once ( $environment ) ) {
			Scaffold_Environment::auto_load(true);

			// Create Scaffold instance
			$container 	= new Scaffold_Container( $system, $config );
			$this->scaffold 	= $container->build();
			
		} else {
			PDStyles::deactivate_and_die ( $environment );
		}
		
	}

	
	/**
	 * Load variables from CSS into array
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function variables_load( $file ) {
		
		// Load in the CSS file
		$source = new Scaffold_Source_File( $file );

		// Rather than parsing the whole thing through Scaffold, we just want the
		// variables that are inside that source. So to save some time, we just get them manually.
		$ext = $this->scaffold->extensions['Variables'];

		// Pull out the variables into an array 
		$this->variables = $ext->extract($source);
		
		$this->variables_cleanup();
	}
	
	/**
	 * Convert dot notation in CSS variables to PHP multi-dimensional array
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function variables_cleanup() {
		$tmp = array();
		// $css_values = $this->get_option('css_values');
		
		// Gather vars with dot notation, place into .args array
		foreach ( $this->variables as $group => $variables ) {
			foreach ( $variables as $key => $value ) {
				if ( strpos( $key, '.' ) !== false ) {
					$parts = explode( '.', $key );
					
					$tmp[ $group ][ $parts[0] ][ $parts[1] ] = $value;
					
					unset( $this->variables[$group][$key] );
				}
			}
		}
		
		// Replace default value with array, containing dot arguements and original value as key 'default'
		foreach ( $this->variables as $group => &$variables ) {
			
			$this->variables[ $group ]['key'] = $group;
			
			foreach ( $variables as $key => &$value ) {
				if ( $this->is_protected_key( $key ) ) { continue; }
				
				$tmp_args = &$tmp[ $group ][ $key ];
				if ( is_array( $tmp_args ) ) {
					
					$tmp_args['default'] = $value;
					$value = $tmp_args;
					
				}else {
					// No dot args, set bar minimum arguements for detection & display
					$value = array(
						'label'		=>	$key,
						'default'	=>	$value,
						'key'		=>	$group,
					);
				}
		
			}
		}
		
		unset($tmp);
	}
	
	function is_protected_key( $key ) {
		$protected = array(
			'key',
			'label',
		);
		
		return in_array( strtolower($key), $protected );
		
	}
	
	function output() {
		foreach ($this->variables as $variable) {
			$variable->output( "css_values[$this->permalink]" );
		}
	}
	
	/**
	 * Detect if input CSS var looks like the type this object handles
	 * 
	 * @since 0.1
	 * @return bool
	 **/
	function is_type( $args ) {
		// Never match child elements
		return false;
	}
	

} // END class 