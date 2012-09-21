# Options loading

For most users, options load from CSS that's sent by an API at stylesplugin.com, then saved in the database. For development, create a customize.css in the theme directory to override this.

/wp-content/theme/customize.css

# Actions

Any time CSS needs to be generated, you'll see this block of actions:
	
	do_action('styles_init', $this->styles );
	do_action('styles_before_process', $this->styles);
	do_action('styles_process', $this->styles);
	do_action('styles_after_process', $this->styles);
	do_action('styles_render', $this->styles );

They're basically mimicking the rendering process that existed in the CSS Pre-Processor, Scaffold, when it was standalone.

# File organization

* styles.php
  Loader -- checks PHP version and loads frontend or admin code
* classes/
  * storm-wp-frontend.php
    Minimal methods loaded on front-end.
    Sets up site.com/?scaffold to respond with parsed CSS.
    Utility methods used in frontend and admin.
    Enqueue frontend javascript (e.g., live preview)
  * storm-wp-admin.php
  	Extends storm-wp-frontend.php
  	Sets up Appearance > Options page
  	Writes cached CSS either to transient in DB or to file.
  * storm-wp-settings.php
    Register settings to display on Appearance > Styles,
      depending on selectors found by storm-css-processor.php
      in customize.css
    Sanatize settings before saving to DB
    Output (composite) form for settings
    	Problem: Active form "item" is set by the [active] field.
    	         WP customizer, at least for now, only supports one
    	         user interface per option
    Remote API connector
  * storm-styles.php
    Wrapper class that controls storm-wp-admin or storm-wp-frontend
    Dispatches CSS render requests
    Loads cached CSS files
  * storm-css-processor.php
    This class is ran twice.
    First: Parse "CSS" in customize.css to create settings/UI
    Second: Output compiled CSS once settings are input into UI

# storm-css-processor.php detail

Input customize.css:

	#page {
		group: General;
		label: Page;
		
		a {
			group: General;
			label: Default Link;
			&:focus, &:active, &:hover {
				group: General;
				label: Default Link Hover;
			}
		}
	}

Create Page, Default Link, and Default Link Hover in "General" group.
	Storm_CSS_Processor::before_process()
		
		Create variable based on CSS and "fake" attributes:
			line 117: $styles->variables[$id]['group'] = $group;
		
		Organize variables into groups:
			line 126: $styles->groups[$group][] = $id;
		
		Remove fake attributes:
			line 136: $styles->css->contents = $this->helper->remove_properties( 'value',  $styles->css->contents );

Generate settings interface based on $styles->groups
	
	Storm_WP_Settings::settings_sections()
	
	Storm_WP_Settings::settings_items()

Goal: Modify these to register with WP Customizer, not settings API.
An example attempt at starting in Storm_WP_Settings:

	function __construct( $styles ) {
		global $wp_version;
		$this->styles = $styles;
		if ( version_compare('3.4', $wp_version, '>=') || false !== strpos($wp_version, 'alpha') || false !== strpos($wp_version, 'beta') ) {
			// WordPress 3.4+
			add_action( 'customize_register', array($this, 'customize_sections'), 10 );
			add_action( 'customize_register', array($this, 'customize_items'),    11 );
		}else {
			// WordPress < 3.4
			add_action( 'styles_settings', array($this, 'settings_sections'), 10 );
			add_action( 'styles_settings', array($this, 'settings_items'), 20 );
			add_action( 'styles_init', array($this, 'remote_api'), 0 );
		}

		// Sanatize before DB commit
		add_filter( 'styles_before_save_element_values', array($this, 'before_save_element_values'), 10 );

		return true;
	}

	/**
	 * Register sections with WordPress theme customizer in WordPress 3.4+
	 * e.g., General, Header, Footer, Content, Sidebar
	 */
	function customize_sections($wp_customize) {
		// Maybe move to storm-wp-admin.php
		do_action('styles_init', $this->styles);
		do_action('styles_before_process', $this->styles);
		do_action('styles_process', $this->styles);
		do_action('styles_after_process', $this->styles);

		// General
		$wp_customize->add_section( 'styles-General', array(
			'title'          => __( 'General Styles', 'storm' ),
			'priority'       => 940,
		) );

		// GUI
		foreach( $this->styles->groups as $group => $elements ) {	
			$wp_customize->add_section( $group, array( // Namespace as storm_$group in future
				'title'          => __( $group, 'storm' ),
				'priority'       => 950,
			) );		
		}
	}

	/**
	 * Register individual customize fields in WordPress 3.4+
	 */
	public function customize_items( $wp_customize ) {
		FB::log(__FUNCTION__);

		// FB::log($this->styles->groups, '$this->styles->groups');
		// FB::log($this->styles->variables, '$this->styles->variables');

		// GUI
		foreach( $this->styles->variables as $key => $element ){
			if ( empty( $element['selector']) ) { 
				// Skip items that don't exist in the current theme
				continue;
			}
			
			// $form_id, $form_name, $id, $label, $group,$selector
			// $values[ active,css,image,bg_color,stops,$color,
			// 	$font_size, $font_family, $font_weight,
			// 	$font_style, $text_transform, $line_height ]
			extract($element);
			$js_id = str_replace('.', '_', $id);

			$wp_customize->add_setting( "styles-test[$id][values][css]", array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'edit_theme_options',
				// 'transport'      => 'postMessage',
			) );

			foreach( $enable as $type ) {
				switch ( $type ){
					case 'bg_color':
						$suffix = ' BG Color';
						$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "storm_$js_id", array(
							'label'   => __( $label.$suffix, 'styles' ),
							'section' => "styles-$group",
							'settings'   => "styles-test[$id][values][css]",
						) ) );
					break;
			}
			}

			/*add_settings_field(
				$key,                   // Unique ID
				$label,                 // Label
				array($this, 'form_element'), // Display callback
				'styles-gui', // Form page
				$group,                 // Form section
				$element                // Args passed to callback
			);*/
		}

	}

Note: The forms output for each setting are currently storing all options into a single array element per-selector.

For example, a dump of get_option('styles') after saving a color would show:
<?php 
	array(
		'general.window' => array(
			'group' => 'General',
			'label' => 'Window',
			'id' => 'general.window',
			'enable' => 'all',
			'form_name' => 'variables[general.window][values]',
			'form_id' => 'st_cad8f13f76ad23e1e9e57eda5eb0b7a1',
			'values' => array(
				'active' => 'bg_color',
				'css' => '#752f75',
				'image' => false,
				'image_replace' => false,
				'bg_color' => '#752f75',
				'stops' => '#000000 0%,#ffffff 100%',
				'color' => false,
				'font_size' => false,
				'font_family' => false,
				'font_weight' => false,
				'font_style' => false,
				'text_transform' => false,
				'line_height' => false,
			)
		)
	);
?>

In a version using WP Customizer, the "values" items need to be split out into individual settings keys, e.g., general_window_bg_color, rather than storing all possible options in one array.

Likewise, Storm_CSS_Processor::process() needs to write CSS based on the new array type, rather than depending on the value for $active in the switch one storm-css-processor.php line 178.


Below is content you can put in  wp-content/themes/twentyeleven/customize.css for testing.

It will likely make sense to add another property in addition to group and label, like type.

e.g.,
type: background-color;
type: background-image;

	/* General */
	body, input, textarea {
		group: General;
		label: Window;
	}
	#page {
		group: General;
		label: Page;
		
		a {
			group: General;
			label: Default Link;
			&:focus, &:active, &:hover {
				group: General;
				label: Default Link Hover;
			}
		}
		
		/* Header */
		#branding {
			group: Header;
			label: Background;
			
			#site-title {
				a {
					group: Header;
					label: Site Title;
				}
			}
			#site-description {
				group: Header;
				label: Tagline;
			}
			#searchform #s {
				group: Header;
				label: Search;
				&:focus {
					group: Header;
					label: Search Focus;
				}
			}
		}
		
		/* Main Menu */
		#access {
			group: Main Menu;
			label: Wrapper;
			ul > li {
				a {
					group: Main Menu;
					label: Link Top Level;
				}
				a:hover, li:hover > a, a:focus {
					group: Main Menu;
					label: Link Top Level Hover;
				}
			}
			.sub-menu {
				group: Main Menu;
				label: Submenu;
				li {
					a {
						group: Main Menu;
						label: Submenu Link;
					}
					a:hover, li:hover > a, a:focus {
						group: Main Menu;
						label: Submenu Link Hover;
					}
				}
			}
		}
		
		/* Body */
		#main {
			group: Content;
			label: Wrapper;
		}
		
		/* Content */
		#content {
			
			.page-title, .entry-title, .singular .entry-title {
				group: Content;
				label: Entry Title;
				a { 
					group: Content;
					label: Entry Title Link;
				}
			}
			.entry-meta {
				group: Content;
				label: Entry Meta;
				a {
					group: Content;
					label: Entry Meta Link;
					&:hover {
						group: Content;
						label: Entry Meta Link Hover
					}
				}
			}

			.entry-content, .comment-content {
				h1 { group: Content; label: Heading 1; }
				h2 { group: Content; label: Heading 2; }
				h3 { group: Content; label: Heading 3; }
				h4 { group: Content; label: Heading 4; }
				h5 { group: Content; label: Heading 5; }
				h6 { group: Content; label: Heading 6; }
			}

		}
		
		/* Sidebar */
		#secondary {
			group: Sidebar;
			label: Wrapper;
		
			.widget {
				group: Sidebar;
				label: Widget;
				.widget-title {
					group: Sidebar;
					label: Widget Title;
				}
				ul {
					group: Sidebar;
					label: Widget Content;
					a {
						group: Sidebar;
						label: Widget Link;
						&:hover{ group: Sidebar; label: Widget Link Hover; }
					}
				}
			}
		}
		
		/* Comments */
		#comments {
			
			group: Comment Listing;
			label: Wrapper;
			
			#comments-title {
				group: Comment Listing;
				label: Title;
			}
			
			.commentlist {
				li {
					group: Comment Listing;
					label: Wrapper;
					.comment-meta {
						group: Comment Listing;
						label: Comment Meta;
						a {
							group: Comment Listing;
							label: Comment Meta Link;
							&:hover { group: Comment Listing; label: Comment Meta Link Hover;}
						}
					}
					.comment-content {
						group: Comment Listing;
						label: Comment Content;
					}
					.comment-reply-link {
						group: Comment Listing;
						label: Comment Reply Link;
					}
				}
				.even {
					group: Comment Listing;
					label: Even Comment Wrapper;
				}
				.odd {
					group: Comment Listing;
					label: Odd Comment Wrapper;
				}
			}
			
			/* Response Form */
			#respond {
				group: Comment Response Form;
				label: Response Wrapper;
				
				#reply-title {
					group: Comment Response Form;
					label: Response Title;
				}
				
				.comment-form-comment {
					label {
						group: Comment Response Form;
						label: Response Label;
					}
					textarea#comment {
						group: Comment Response Form;
						label: Response Textarea;
					}
					#submit {
						group: Comment Response Form;
						label: Response Submit Button;
					}
				}
			}
		}
		
		/* Footer */
		#colophon {
			
			group: Footer;
			label: Wrapper;
			
			#supplementary {
				.widget-area {
					group: Footer;
					label: Widget Area;
				}
				#first.widget-area {
					group: Footer;
					label: First Widget Area;
						
					h3.widget-title {
						group: Footer;
						label: Widget Title;
					}
					ul {
						group: Footer;
						label: Widget Content;
						a {
							group: Footer;
							label: Widget Link;
						}
					}
				}
			}
			#site-generator {
				group: Footer;
				label: Theme Credit;
			}
		}
	}
