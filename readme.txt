=== Styles ===
Contributors: brainstormmedia, pdclark, elusivelight
Plugin URI: http://stylesplugin.com
Author URI: http://brainstormmedia.com
Tags: css, stylesheet, styles, appearance, customize, customizer, colors, color picker, images, image upload, background, fonts, google fonts, user interface, twentyten, twentyeleven, twentytwelve, twentythirteen
Requires at least: 3.4
Tested up to: 3.5.2
Stable tag: 1.0.3

Be creative with colors and fonts in your theme. Styles changes everything.

== Description ==

WordPress has lots of beautiful themes, but personalizing a design can be difficult and time-intensive. Styles changes that. Styles gives you creative control in one consistent interface – the WordPress theme customizer. Styles lets you make your site your own. :)

**Features of the plugin include:**

* Instant previews
* Text size
* Google Fonts
* Text colors
* Border colors
* Background colors
* Hundreds of options
* Consistent interface and names in every theme
* Built on WordPress customizer, so Styles grows as WordPress grows

Styles and options for all built-in WordPress themes are free. More themes are available at [StylesPlugin.com](http://stylesplugin.com).

**Free Themes include:**

* [TwentyTen](http://wordpress.org/extend/plugins/styles-twentyten)
* [TwentyEleven](http://wordpress.org/extend/plugins/styles-twentyeleven)
* [TwentyTwelve](http://wordpress.org/extend/plugins/styles-twentytwelve)
* [TwentyThirteen](http://wordpress.org/extend/plugins/styles-twentythirteen)

== Installation ==

1. Upload the `styles` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Install an additional plugin for your current theme.
1. Edit your site under `Appearance > Customize`

== Screenshots ==

1. TwentyEleven header settings.
2. All TwentyEleven sections.
3. TwentyEleven menu settings.

== Frequently Asked Questions == 

= Why is this plugin free? =

We believe life is better when we work together. :) Support for WordPress default themes, like TwentyTwelve and TwentyThirteen, will always be free. Continued development in Styles will be supported by:

* Plugins for additional themes
* Plugins for add-on features
* Premium support and updates
* Documentation for using Styles in your own themes

= Does Styles support my theme? =

Maybe! We have additional themes available available at [StylesPlugin.com](http://stylesplugin.com). If you don't find your theme there, we also have documentation on how to create options using styles available for developers. Adding one option takes only one line of code.

= Will this plugin slow down my site? =

No! Styles is very careful about only loading what is needed to get its job done. Once you're done editing, stylesheets are cached and loaded for your sites users as quickly as possible.

== Changelog ==

= 1.0.3 =
* Fix: Google fonts loading correctly once saved.

= 1.0.2 =
* Fix: Remove front-end PHP notice. (Minor; Only matters if WP_DEBUG is enabled.)

= 1.0.1 =
* Fix: Remove Customizer PHP notice. (Minor; Only matters if WP_DEBUG is enabled.)

= 1.0 =
* New: Completely rewrite Styles to use the WordPress Customizer API. Whew! That was a lot of work, but will allow us to grow closely with WordPress going forward.
* New: Styles 1.0 does not import 0.5.2 settings. However, you can always [reinstall Styles 0.5.3](http://downloads.wordpress.org/plugin/styles.0.5.3.zip) to get your old settings back. 
* New: TwentyTwelve support.
* New: TwentyThirteen support.
* New: Remove gradients and background images. Sorry! We had to simpify and prioritize in the rewrite. If many users find Styles useful, we hope to bring these features back!
* New: Instant preview for many options using Customizer postMessage

= 0.5.2 =
* Fix: Display of icons in cases where WordPress is installed in a subdirectory or wp-content is moved. Fixes http://bit.ly/Jc7oyd

= 0.5.1.1 =
* Fix: Transparent background option
* Fix: Display of unavailable theme options

= 0.5.1 =
* Fix: Issue that would prevent Font styles from outputting if another option wasn't set on the same element.

= 0.5.0 =
* New: Load themes from API: Allows for theme support to be added without plugin updates.
* New: Data structure: Allow settings to migrate from theme to theme
* New: Automatically rebuild CSS when theme is switched
* New: Wrap font settings into other options. This sets the stage to simplify the user interface in a future version.
* New: Add option to hide text and replace with image on any element
* New: Expand TwentyTen options
* New: Gradient Picker: Fix add marker, drag to remove marker
* Fix: Massive code clean-up: Removed 1,198 lines of code; modified 2,488
* Fix: Minor UI tweaks

= 0.4.1 =
* Fix: Saved CSS being one update behind
* Fix: Initial values of fields & background color picker
* Fix: Preview update stall
* Fix: Background value when no image selected
* Fix: Background replacement matching

= 0.4.0 =
* Initial public release.

== Upgrade Notice ==

**1.0.3**
* Fix: Google fonts loading correctly once saved.

**1.0**
Completely rewrote Styles to use the WordPress Customizer API. Whew! That was a lot of work, but will allow us to grow closely with WordPress going forward.

Styles 1.0 does not import 0.5.2 settings. However, you can always [reinstall Styles 0.5.3](http://downloads.wordpress.org/plugin/styles.0.5.3.zip) to get your old settings back.
