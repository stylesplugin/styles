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
	 * Loaded SCSS file path
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $file;
	
	/**
	 * Where to save compiled CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $cache_file;
	
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
		if ( empty( $args['file'] ) ) {
			FB::error('No file specified in '.__FILE__);
			return false;
		}
		
		$this->file = $args['file'];
		$this->cache_file = $args['cache_file'];
		$this->permalink = $args['permalink'];

		$this->scaffold_init();
		
		$this->variables_load( $this->file );
		
		foreach( $this->variables as $key => &$group ) {

			$group['form_name'] = "variables[$this->permalink]";
			$group['key'] = $key;
			$group = new PDStyles_Extension_Group( $group );
			
			// Remove empty groups
			if ( empty( $group->variables ) ) {
				unset( $this->variables[$key] );
			}
		}
		
		return true;
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
				'AbsoluteUrls',
				'Variables',
				'Import',
				'WordPressBridge',
				'NestedSelectors',
				'Properties',
				// 'XMLVariables'
			)
		);
		$config['import_paths'] = array(
			untrailingslashit( get_stylesheet_directory() ),
			untrailingslashit( get_stylesheet_directory() ).'/css',
		);
		
		$config['load_paths'] = array(
			untrailingslashit( dirname( dirname( dirname( __FILE__ ))) ),
		);
		
		if ( isset( $_GET['preview'] ) ) {
			$config['WordPressBridge']['preview'] = true;
		}

		// Setup the env
		date_default_timezone_set('GMT');
		$system = PDStyles::plugin_dir_path() . 'scaffold';
		$environment = $system.'/lib/Scaffold/Environment.php';
		
		if ( @require_once ( $environment ) ) {
			Scaffold_Environment::auto_load(true);

			// Create Scaffold instance
			$Container = Scaffold_Container::getInstance($system,$config);

			$this->scaffold 	= $Container->build();
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

		// Parse imports
		$import = $this->scaffold->extensions['Import'];
		$import->replace_rules($source, $this->scaffold);
		
		// Parse variables
		$variables = $this->scaffold->extensions['Variables'];
		// Pull out the variables into an array 
		// $this->variables = $variables->extract($source);
		$this->variables = &$this->scaffold->extensions['WordPressBridge']->found;

		$this->scaffold->compile($source);
		
		// FB::log($this->variables, '$this->variables');
		// $this->variables_cleanup();
		// FB::log($this->variables, '$this->variables');
		// FB::log($this->scaffold->extensions['WordPressBridge']->found, 'WordPressBridge->found');
	}
	
	/**
	 * Detect functions/arguments. Convert to Array.
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function variables_cleanup() {
		$tmp = array();
		
		foreach ( $this->variables as $group => &$variables ) {
			
			// Tell children what the parent key is, for building form names
			$this->variables[ $group ]['key'] = $group;
			
			foreach ( $variables as $key => &$value ) {
				if ( $this->is_protected_key( $key ) ) { continue; }
				
				$found = preg_match('/
						(^[^\(\\s]*)\\s*\(  # 1 function_name (
						[\'"\\s]?           # maybe quote or space
						([^\'\"\)]*)        # 2 = arguements
						[\'"\\s]?           # maybe end quote or space
						(?:\\s*\\))?        # maybe )
					/xs',
					$value,
					$match
				);

				if (!$found) {
					continue; 
				}
				
				$args = array(
					'method' => 'css_'.str_replace( '-', '_', $match[1] ),
					'key' => $group,
					'label' => $key,
				);
				
				$tmp_args = $match[2];

				// Extract Arguments into key=>value array
				$tmp_args = explode(',', $tmp_args);			
				foreach( $tmp_args as $tmp_val ) {

					$tmp_val = explode('=', $tmp_val);

					$arg_key = trim( $tmp_val[0] );
					$arg_val = trim( $tmp_val[1] );

					$args[ $arg_key ] = $arg_val;
				}
				
				$value = $args;
		
			}
		}
		unset($tmp);
	}
	
	/**
	 * Remove object elements that don't need to be stored in database
	 * 
	 * @since 0.1.3
	 * @return void
	 **/
	function db_cleanup() {
		unset( $this->scaffold );
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
			$variable->output();
		}
	}
	
	/**
	 * Update values of all children
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function set( $values ) {
		if ( empty( $values ) ) {
			return;
		}
		if ( !array_key_exists( $this->permalink, (array) $values )) {
			FB::error('$this->permalink not found in $values.');
			FB::error($this->permalink, '$this->permalink');
			FB::error($values, '$values');
			FB::error(debug_backtrace(), 'debug_backtrace()');
			return;
		}
		
		foreach ($this->variables as $variable) {
			$variable->set( $values[$this->permalink] );
		}
	}
	
	/**
	 * Recursively get all child values for CSS
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function get( $context = null ) {
		$values = array();
		foreach ($this->variables as $variable) {
			$values[ $variable->key ] = $variable->get( $context );
		}
		return $values;
	}	

} // END class 