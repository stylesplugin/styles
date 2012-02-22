=== Styles ===
Contributors: brainstormmedia, pdclark
Plugin URI: http://stylesplugin.com
Author URI: http://brainstormmedia.com
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FD4GKBGQFUZC8
Tags: css, css3, scss, sass, scaffold, less, admin, gui, color picker, gradients, image upload, google fonts, user interface, twentyten, twentyeleven
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: 0.4.0

Change the appearance of supported themes using zero code. Creates appearance options for images, colors, gradients, and fonts.

== Description ==

Styles allows you to edit the appearance of themes that don't provide an interface in the WordPress admin for doing so. It provides:

* Background images
* WordPress Media Uploader
* Photoshop-like Gradient Picker
* Color Picker
* Google Fonts
* Font size, color, styling, and capitalization

The plugin includes full support for TwentyTen and TwentyEleven out of the box. Just enable the plugin, and you can start playing with your site's look under Appearance > Styles.

For experienced developers, other themes can be integrated using only CSS. A full theme GUI can be created in less than an hour by those familiar with CSS syntax. An example of how fast this process is can be seen in the video below.

[youtube http://www.youtube.com/watch?v=-Iw8d0g_ltQ]

**Warning**: While we have spent a lot of time testing and using Styles, it is still experimental! We may add or remove features or change syntax in future updates. This plugin requires [PHP 5.2](https://codex.wordpress.org/Switching_to_PHP5), which is the minimum supported version as of WordPress 3.2.

== Installation ==

1. Upload the `styles` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If using a supported theme, start using the plugin under Appearance > Styles
1. If creating a GUI for a theme of your own, copy one of the example stylesheets in `styles/themes` to `wp-content/themes/your-theme/styles-admin.css` and start editing!

== Screenshots ==

1. Select background images from the WordPress Media Library. Theme elements can also be replaced by images, e.g., Styles can hide the site title text and replace it with an image, and resize it to fit.
2. Multi-stop gradient picker. Gradients are output in formats for all browsers, including Internet Explorer.
3. Background color picker.
4. Google Fonts and other typographic settings.

== FAQ ==

= Will this plugin slow down my site? =

No! Styles is very careful about only loading what is absolutely required to get its job done. CSS settings are processed once when you click the "save" button. After that, all the work is done. A single compressed CSS file is all that's added to your site's front-end.

= Can I integrate Styles with my theme? =

Styles will work with any theme that uses CSS for its appearance, and that's 100% of the themes out there. If you'd like to create your own GUI for a theme that's not supported out of the box, copy one of the example stylesheets in `styles/themes` to `wp-content/themes/your-theme/styles-admin.css` and start editing!

Adding theme settings from scratch can be a daunting task requiring hundreds of lines of code. Using styles, you can have a fully working GUI with 1 line of CSS.

= Styles says it can't write to a file when I try to save =

That's not a question, but don't worry about it too much. If `wp-content/uploads` is not writable by the server, Styles will cache to the database instead. While caching to a file is ideal, there should not be a significant difference in performance.

= How is WordPress able to interpret CSS this way? =

Styles uses portions of Anthony Short's [Scaffold](https://github.com/anthonyshort/scaffold), which is a CSS pre-processor similar to Less, SASS, or Compass. Basically, Scaffold understands CSS and is written in PHP. WordPress is also written in PHP, so Scaffold is able to communicate to WordPress about the structure and content of CSS files.

= Can I use Styles as a replacement for Less, SASS, or Compass? =

We used Styles + Scaffold as our main CSS Processor for a very long time, but the plugin as it exists today would not be appropriate for that purpose. We've pared the CSS processing libraries down to a bare minimum to keep things as simple and stable as possible to solve one problem: creating a theme UI. For general purpose CSS processing, you'll be much better off using one of the many desktop-based libraries.

== Changelog ==

= 0.4.0 =
* Initial public release.

== License ==

Copyright 2011 Brainstorm Media - Released under the  GNU General Public License
 - [License details](http://www.gnu.org/licenses/gpl.html)