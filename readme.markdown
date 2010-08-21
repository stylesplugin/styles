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

Useful stuff, like HTML and JS for color pickers and sliders can be found in the old proof-of-concept version of this plugin: http://pdclark.com/pd-styles-v0.zip

## Plans ##

Build UI elements in WordPress based on @variable declarations in CSS. Compile CSS based on default @variable values and input from UI elements.

Declare @variable > Generate UI automatically > Save values to WordPress options DB & vars.xml > Read XML file into CSS when compiling final CSS file
	Note: If the same variable is declared in the CSS and XML, the XML value overrides the other.
	
UI Types:
 
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