=== Styles ===
Contributors: brainstormmedia, pdclark
Plugin URI: http://stylesplugin.com
Author URI: http://brainstormmedia.com
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FD4GKBGQFUZC8
Tags: css, stylesheet, scss, sass, scaffold, less, admin, gui, color picker, gradient, image upload, google fonts, user interface, twentyten, twentyeleven
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: 0.5.0

Change the appearance of supported themes using zero code. Creates appearance options for images, colors, gradients, and fonts.

== Description ==

Styles allows you to edit the appearance of themes that don't provide an interface in the WordPress admin for doing so. Themes supported by the free version include:

* TwentyTen
* TwentyEleven

Theme support is provided by a remote API, so the plugin will update as more themes are added. We plan to release support for more themes in as a paid add-on to Styles. Have a specific theme you'd like to see supported? [Vote on themes here](https://www.google.com/moderator/?authuser=2#16/e=1f6d0a).

Styles provides:

* Transfer of settings between themes
* Background images using WordPress Media Library
* Photoshop-like Gradient Picker
* Color Picker
* Google Fonts
* Font size, color, styling, and capitalization
* Replace HTML elements with an image
* Live preview when site open in a second window

Enable the plugin to start playing with your site's look under `Appearance > Styles`.

Here's a quick demo of how it works in TwentyEleven:

[youtube http://www.youtube.com/watch?v=2gbRFFxx1GY]

This plugin requires [PHP 5.2](https://codex.wordpress.org/Switching_to_PHP5), the minimum supported version as of WordPress 3.2.

== Installation ==

1. Upload the `styles` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Start using the plugin under `Appearance > Styles`
1. Preview your changes by opening your site an a second window.

== Screenshots ==

1. Select background images from the WordPress Media Library. Theme elements can also be replaced by images, e.g., Styles can hide the site title text and replace it with an image, and resize it to fit.
2. Multi-stop gradient picker. Gradients are output in formats for all browsers, including Internet Explorer.
3. Background color picker.
4. Google Fonts and other typographic settings.

== FAQ ==

= What features do you plan to charge for? =

Support for WordPress default themes, such as TwentyTen and TwentyEleven, will always be free. We plan to charge in the future for additional features such as:

* Additional themes
* Access to support forums
* Documentation on how to setup custom themes or additional elements

= Will this plugin slow down my site? =

No! Styles is very careful about only loading what is absolutely required to get its job done. CSS settings are processed once when you click the "save" button. After that, all the work is done. A single compressed CSS file is all that's added to your site's front-end.

= Styles says it can't write to a file when I try to save =

That's not a question, but don't worry about it too much. If `wp-content/uploads` is not writable by the server, Styles will cache to the database instead. While caching to a file is ideal, there should not be a significant difference in performance.

= How is WordPress able to interpret CSS this way? =

Styles uses portions of Anthony Short's [Scaffold](https://github.com/anthonyshort/scaffold), which is a CSS pre-processor similar to Less, SASS, or Compass. Basically, Scaffold understands CSS and is written in PHP. WordPress is also written in PHP, so Scaffold is able to communicate to WordPress about the structure and content of CSS files.

= Can I use Styles as a replacement for Less, Sass, or Compass? =

For general purpose CSS processing, you'll be much better off using one of the many desktop-based libraries. We used Styles + Scaffold as our main CSS Processor for a very long time. However, the plugin as it exists today would not be appropriate for that purpose. We've pared the CSS processing libraries down to a bare minimum to keep things as simple and stable as possible. The plugin as it is today is meant only to create a theme user interface.

== Changelog ==

= 0.5.0 =
* Load themes from API: Allows for theme support to be added without plugin updates.
* New data structure: Allow settings to migrate from theme to theme
* Automatically rebuild CSS when theme is switched
* Wrap font settings into other options. This sets the stage to simplify the user interface in a future version.
* Add option to hide text and replace with image on any element
* Expand TwentyTen options
* Gradient Picker: Fix add marker, drag to remove marker
* Massive code clean-up: Removed 1,198 lines of code; modified 2,488
* Minor UI tweaks

= 0.4.1 =
* Fix saved CSS being one update behind
* Fix initial values of fields & background color picker
* Fix preview update stall
* Fix background value when no image selected
* Fix background replacement matching

= 0.4.0 =
* Initial public release.

== License ==

Copyright 2011 Brainstorm Media - Released under the  GNU General Public License
 - [License details](http://www.gnu.org/licenses/gpl.html)