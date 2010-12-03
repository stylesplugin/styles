## File Structure ##

    example/							Holds code for reading/writing vars between Scaffold & PHP
    inc/								Should be moved to lib/
    lib/								lib.
      controllers/
        PDStylesAdminController.php 	Main Admin code
      css/
      helpers/							Was thinking UI classes go here. Don't really know.
        PDStylesUIColor.php 			Start of first UI class
      img/
      js/
      views/
        admin-main.php 					Main Admin view
    notes.php							Example of AJAX stuff
	pd-styles.php 						Main Class, WordPress initializer
	readme.markdown						You are here, homeh.
	scaffold/							CSS Magic. See http://github.com/anthonyshort/Scaffold
	uninstall.php 						Runs when a user uninstalls the WP plugin

Useful stuff, like HTML and JS for color pickers and sliders can be found in the old proof-of-concept version of this plugin: [pd-styles-v0.zip](http://pdclark.com/pd-styles-v0.zip)

## Proposed File Structure Changes ##
Thinking we may want to change the structure up a bit, here's what I'm headed for:

    lib/
      controllers/
        adminController.php 	                        Main Admin code
        elementsController.php                          So typically you'd have a controller per model/view, but the controller's primary function is to interface between the database and the view, based on the layout given in the model. However, since we're storing this thing as one fatty serialized array, it wouldn't make sense to me to separate these controllers. I reserve the right to deny I ever said this later though.

		I think it makes sense to have a single class that the models adopt behavior from. But I'm not one to argue -- I'm not even sure if calling the color/gradient/images "models" is correct when the real model is handled by WP.
      models/
        colorModel.php
        gradientModel.php
        imageModel.php
        ..etc
      helpers/					        Helpers that have functions that we can't classify and/or affect every/multiple type of model.
        elementsHelper.php
      views/
        adminView.php 					Main Admin view
        colorView.php                                   \
        gradientView.php                                 }-- Each of these should contain smaller UI elements
        imageView.php                                    /    that we render as we please.


## Plans ##

Build UI elements in WordPress based on @variable declarations in CSS. Compile CSS based on default @variable values and input from UI elements.

Declare @variable > Generate UI automatically > Save values to WordPress options DB & vars.xml > Read XML file into CSS when compiling final CSS file

**Note:** If the same variable is declared in the CSS and XML, the XML value overrides the other.
	
By the end, a UI should be built just by putting into CSS:

	@variables colors {
		somevar: #ffffff;
		somevar.type: color;
		somevar.group: Header;
	}
	
**AJAX**
CSS files can now be processed with the URL:
	http://site.com/?scaffold&file=XXX.css
"file" will be searched for from siteroot, the plugin directory, and the current theme directory.

The beginning of calling CSScaffold through WordPress (and having the WP database and plugin settings available to Scaffold) are in PDStylesFrontendController::parse_request. In the future, it would be nice to:

* Pull Scaffold's $config from plugin settings instead of using include 'parse.php';
  * Change Scaffold settings from GUI
* Automatically run CSS files through Scaffold using some sort of flag set in wp\_enqueue\_style
  * The wp-minify plugin does something similar: http://wordpress.org/extend/plugins/wp-minify/
  * Scaffold allows processing CSS strings. We could auto-generate strings if necessary
  * Scaffold can process (and concatenate) multiple files into one. wp\_enqueue\_style
	  would give us dependency information

## UI Types ##
I planned on starting with Color, since it's fairly simple.

* Color: A color picker
* Gradient: Two color pickers, plus horizontal/vertical switch and size. Generates image.
* Image: Uploads an image to WordPress media library, gives Scaffold the URL.
  * Sample code for this: [Yoast's Blog Icons Plugin](http://yoast.com/wordpress/blog-icons/)
  * Using Scaffold's image-replace, any HTML element can be
	resized to fit the image dimensions, with text hidden & the image
	as the background.
* Background:
  Composite of Color/Gradient/Background. Pick 1. 
* Font: A font picker. Possibly integrate with Google Fonts & TypeKit.
* Slider: jQuery slider with max, min, and default values.
* Mask: Combination of Color and Image.
  * Using PHPThumb, a grayscale mask can be composited with any color. 
  * e.g., try changing the URL parameters for [this image](http://marksautoservice.ca/wp-content/themes/thesis/custom/scaffold/plugins/Mask/libraries/phpthumb/phpThumb.php?new=6F0E0F&w=1260&h=107&f=jpg&bg=000000&q=100&fltr[]=mask|/wp-content/themes/thesis/custom/child-themes/marksautoservice.ca/img/bevel-mask.jpg)
  * Should we include css3's support for image masks here?
	  * If we could think of a syntax to serve the mask syntax to webkit/other supporting browsers, but the flat masked images to legacy, then sure. If we're generating the masked image though, it might actually be more efficient to give that final image even to new browsers? Saves a single HTTP connection anyway. 

## Organization ##

Ideally, the UI elements will self-organize into sections. Either based on @variables groups, or based on each variable having an optional .group setting. Groups and variables should have a .nicename set for display.

A backend admin is easiest at this point, but it doesn't seem like too much of a stretch to start with that, then auto-generate a front-end version later, similar to the [Headway Themes Visual Editor](http://headwaythemes.com/features/visual-editor/). I have a copy of this theme if you want it. It's GPL.

A great example of back-end GUI is [Gravity Forms](http://www.gravityforms.com/), also GPL, also have a copy you can play with.

## Code Structure ##

When organizing the new plugin, I was looking at:

* Sivel's [Shadowbox]() for class structure. Sivel runs the #wordpress IRC channel. This plugin is recommended by a lot of high-level WP developers as an example of great code structure. He has a choppy lecture on Intermediate Plugin development at [WordPress.tv](http://wordpress.tv/2009/11/14/matt-martz-plugins-nyc09/)
* Blair Williams' [Mingle](http://wordpress.org/extend/plugins/mingle/). He uses a pseudo MVC structure, and this plugin is a bit larger of a project. He lectures on it at [WordPress.tv](http://wordpress.tv/2010/04/24/blair-williams-wordpress-plugins-oc10/), but also comments that some of the methods he used weren't best practice, like lots of global vars.

## Git Stuff $$ 
Branching from master using a handy little tool called git flow ( http://github.com/nvie/gitflow ) so I can start breaking things without polluting the master branch. I'd recommend using it as well if you haven't already though - it's quite the nice tool. 

... Testing Basecamp hook