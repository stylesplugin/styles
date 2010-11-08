<?php

/**
 * Format and iterate variable groups
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Group extends Scaffold_Extension_Observer {
	
	/**
	 * Form element ID and Name
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $id;
	
	/**
	 * Nice text name for display in element label
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $label;
	
	/**
	 * Variable group name used in the CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $key;
	
	/**
	 * Variable objects in this group
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $variables;
	
	function __construct( $args = array(), $permalink = null ) {
		if ( empty($args) ) return;

		$this->key = $args['key'];
		$this->label = ( empty( $args['label'] ) ) ? $args['key'] : $args['label'];
		
		unset( $args['label'], $args['key'] );
		
		$this->create_objects( $args, $permalink );
	}
	
	function create_objects( $variables, $permalink ) {
		global $PDStylesAdminController;
		
		// Load Values from DB
		$css_values = $PDStylesAdminController->get_option('css_values');
		foreach ( (array) $css_values[ $permalink ][ $this->key ] as $key => $value ) {
			$variables[ $key ][ 'value' ] = $value;
		}
		
		// Instantiate Objects
		foreach ( $variables as $key => $args ) {
			if ( is_array($args) ) {
				foreach ( $PDStylesAdminController->extensions as $ext ){
					
					if ( $ext->is_type( $args ) ) {
						$args['key'] = $key;
						$ext_class = get_class($ext);
						$this->variables[ $key ] = new $ext_class( $args );
					}
					
				}
			}
		}

		// Remove anything that wasn't recognised as an object
		foreach ( (array) $this->variables as $key => $object ) {
			if ( !is_object( $object ) ) {
				unset( $this->variables[ $key ] );
			}
		}

	}
	
	function output( $permalink ) {
		$id = 'pds_'.md5($permalink).$this->key;
		?>
		<div id="<?php echo $id; ?>" class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span><?php echo $this->label; ?></span></h3>
			<div class="inside">
				<table class="form-table">
				<?php 
				foreach ( $this->variables as $variable ) {
					$variable->output( "{$permalink}[$this->key]");
				}
				?>
				</table>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Detect if input CSS var looks like the type this object handles
	 * 
	 * @since 0.1
	 * @return bool
	 **/
	function is_type( $args ) {
		// Never match child elements to Group
		return false;
	}
	

} // END class 