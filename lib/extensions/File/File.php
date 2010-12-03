<?php

/**
 * Manage detection of scaffold files & IDs
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_File {
	
	/**
	 * IDs of found files
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $queue = array();
	
	/**
	 * Key of file in $queue we're currently working on
	 * 
	 * @since 0.1.3
	 * @var string
	 **/
	var $active_id;
	
	/**
	 * Reference to active file object in queue
	 * 
	 * @since 0.1.3
	 * @var PDStyles_Extension_Variable
	 **/
	var $active_file;
	
	/**
	 * Include paths. Used for searching for files.
	 * @var array
	 */
	public $_load_paths = array();
	
	function __construct( ) {
		$this->_load_paths = array(
			untrailingslashit( get_stylesheet_directory() ),
			untrailingslashit( dirname( dirname( dirname( __FILE__ ) ) ) ),
		);
		
		$this->find_css();
		
		// Setup active CSS path. If none set, default to first file in queue
		reset( $this->queue );
		$this->active_id = ( isset( $_REQUEST['active_id'] ) ) ? $_REQUEST['active_id'] : key( $this->queue );
		
		$this->active_file =& $this->queue[ $this->active_id ];
		
	}
	
	/**
	 * Return a unique hash based on the Blog ID & file path
	 * Strip out ABSPATH to avoid data corruption from site migration
	 * 
	 * @since 0.1.3
	 * @return string
	 **/
	function get_id( $path ) {
		global $wpdb;

		return 'b' . $wpdb->blogid . '_' . preg_replace( '/[^a-zA-Z0-9_-]/', '', basename( $path ) ) . '_' . md5( str_replace( ABSPATH, '', $path ) );
	}
	
	/**
	 * Finds a file on the filesystem using the load paths
	 * and the document root. 		
	 * @access public
	 * @param $file
	 * @param $required boolean
	 * @throws Scaffold_Loader_Exception
	 * @return mixed
	 */
	public function find_file($file,$required = true)
	{
		$real = realpath($file);
		
		// We've already found it
		if(is_file($real))
		{
			$file = $real;
		}
		
		// Docroot relative file
		elseif($file[0] == '/' OR $file[0] == '\\')
		{
			$file = $_SERVER['DOCUMENT_ROOT'] . $file;
		}
		
		// Look in the load paths
		else
		{
			// Go through each of the include paths to find it
			foreach($this->_load_paths as $path)
			{
				$search = $path.DIRECTORY_SEPARATOR.$file;
				
				if(is_file($search))
				{
					$file = $search;
					break;
				}
			}
		}
		
		if(!is_file($file) AND $required === true)
		{
			throw new Exception('The file cannot be found ['.$file.']');
		}
		elseif(!is_file($file) AND $required === false)
		{
			return false;
		}

		return $file;
	}
	
	/**
	 * Search for scaffold URLs and replace src with cached files if they exist
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function find_css() {
		global $wp_styles;
		
		if ( ! ( is_a( $wp_styles, 'WP_Styles' ) && is_array( $wp_styles->queue ) ) ) {
			return;
		}

		foreach( $wp_styles->queue as $key ) {
			FB::log($key, '$key');
			$path = $wp_styles->registered[$key]->src;
			if ( 'scss' !== pathinfo( $path , PATHINFO_EXTENSION ) ) { continue; }
			
			$abspath = $this->find_file($path);
			if ( !$abspath ) { 
				FB::error('File not found: '.$path);
				continue;
			}
			$id = $this->get_id( $abspath );
			
			$url = site_url().'/?scaffold&active_id=' . $id;
			
			// Add to file index
			$this->queue[ $id ] = new PDStyles_Extension_Variable( array(
				'file' => $abspath,
				'permalink' => $id,
			) );
			
			$wp_styles->registered[$key]->src = $url;
			FB::log($wp_styles, '$wp_styles');
		}
	}

} // END class PDStyles_Extension_File