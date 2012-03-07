<?php
/**
 * Upgrades for versions below 0.5.0
 */

// Declare legacy classes to avoid __PHP_Incomplete_Class
class StormStyles_Extension_Variable{}
class StormStyles_Extension_Group{}
class StormStyles_Extension_Background{}
class StormStyles_Extension_Font{}

// Map depreciated IDs to current equivalents
$key_map = array(
	// TwentyEleven
	'general.default.font'          => 'general.window',
	'content.content'               => 'content.wrapper',
	
	// TwentyTen
	'general.window.background'     => 'general.window',
	'general.page.background'       => 'general.page',
	'general.logo'                  => 'header.site.title',
	'general.search.form.bg'        => 'header.search',
	'general.search.form.active.bg' => 'header.search.focus',
	'general.default.font'          => 'general.window',
	'navigation.background'         => 'main.menu.wrapper',
	'navigation.link.bg'            => 'main.menu.link.top.level',
	'navigation.link.hover.bg'      => 'main.menu.link.top.level.hover',
	'navigation.link.font'          => 'main.menu.link.top.level',
	'footer.footer'                 => 'footer.wrapper',
	'footer.widget'                 => 'footer.widget.content',
	'footer.site.generator'         => 'footer.theme.credit',
	'content.article.title'         => 'content.entry.title',
	'content.blog.entry.meta'       => 'content.entry.meta',
	'content.heading.1.and.2'       => 'content.heading.1',
	// 'content.author.info'           => '',

);

$old = get_option('StormStyles');
$new = array();

$ref = get_option('styles');

if ( is_array($old['variables']->variables) ) {
	foreach( $old['variables']->variables as $group_key => $group ) {
		if ( is_array( $group->variables ) ) {
			foreach( $group->variables as $var_key => $var ) {
				// Welcome to $old['variables']->variables['group_key']->variables
				// O.O Can't imagine why we might want to clean this up...

				$group = $var->group;
				$label = $var->label;

				// ID setup from storm-wp-bridge.php:95 - before_process()
				// Strip non alpha-numeric
				$id_mask = '/[^a-zA-Z0-9\s]/';
				$id = preg_replace($id_mask, '', $group).'.'.preg_replace($id_mask, '', $label);
				// Replace white-space of any length with a hyphen
				$id = preg_replace('/[\s]+/', '.', strtolower($id));
				
				// Map depreciated IDs to current equivalents
				if ( array_key_exists($id, $key_map) ){
					$id = $key_map[$id];
				}

				if ( is_a( $var, 'StormStyles_Extension_Background' ) ) {
					$active = $css = $image = $color = $stops = '';
					extract( $var->values ); 

					if( !empty($active) ) $new[$id]['values']['active']   = $active;
					if( !empty($css)    ) $new[$id]['values']['css']      = $css;
					if( !empty($image)  ) $new[$id]['values']['image']    = $image;
					if( !empty($color)  ) $new[$id]['values']['bg_color'] = $color;
					if( !empty($stops)  ) $new[$id]['values']['stops']    = $stops;

				}else if ( is_a( $var, 'StormStyles_Extension_Font' ) ) {
					$font_size = $color = $font_family = $font_weight = $font_style = $text_transform = $line_height = '';
					extract( $var->values );

					if( !empty($color)          ) $new[$id]['values']['color']          = $color;
					if( !empty($font_size)      ) $new[$id]['values']['font_size']      = $font_size;
					if( !empty($font_family)    ) $new[$id]['values']['font_family']    = $font_family;
					if( !empty($font_weight)    ) $new[$id]['values']['font_weight']    = $font_weight;
					if( !empty($font_style)     ) $new[$id]['values']['font_style']     = $font_style;
					if( !empty($text_transform) ) $new[$id]['values']['text_transform'] = $text_transform;
					if( !empty($line_height)    ) $new[$id]['values']['line_height']    = $line_height;

				}

			} // end foreach vars
		}
	} // end foreach groups
}

// Backup the old settings for 30 days in case something went horribly wrong
set_transient( 'styles-backup-0.4.0', $old, 30*(60*60*24) ); // 30*(1 day)

// Set new values
update_option( 'styles', $new );
update_option( 'styles-settings', $this->defaults() );

// Update version in Styles object to avoid infinite loop
$this->options = get_option('styles-settings');

// Delete old options
delete_option('StormStyles');
delete_option('StormStyles-preview');

// Rename cache file
$upload_dir = wp_upload_dir();
$cache_file = '/styles/cache.css';
@rename( $upload_dir['basedir'].'/styles/styles.css', $upload_dir['basedir'].'/styles/cache.css');

// Rewrite the cache file / DB entry
$this->force_recache();

// Yay. We're done.