<?php

class SFM_Image_Preview {

	/**
	 * @var string $_GET key that triggers this class to run
	 */
	protected $action_key = 'styles-font-preview';

	/**
	 * @var array Display attributes for the preview image and font
	 */
	var $preview_attributes = array(
		'font_size' => 48,
		'font_baseline' => 64, // y-coordinate to place font baseline
		'left_margin' => 5,
		'width' => 500,
		'height' => 90,
		'background_color' => array( 255, 255, 255 ),
		'font_color' => array( 0, 0, 0 ),
	);

	public function __construct() {
 		add_action( 'wp_ajax_styles-font-preview', array( $this, 'wp_ajax_styles_font_preview' ) );
	}

	/**
	 * Load Google font specified in $_GET request.
	 * 
	 * @param string $_GET['font-family'] Required. Name of the font to render
	 * @param string $_GET['variant'] Optional. Name of the variant to render
	 * @return null Output URL to image as string
	 */
	public function wp_ajax_styles_font_preview() {
		$plugin = SFM_Plugin::get_instance();
		$font_family = ( isset( $_GET[ 'font-family' ] ) ) ? $_GET[ 'font-family' ] : false;

		// Load font family from Google Fonts
		$this->font = $plugin->google_fonts->get_font_by_name( $font_family );

		if ( !$this->font ) {
			wp_die( 'Font not found: ' . $this->font_family );
		}

		// Output PNG URL
		if ( !$this->font->get_png_url() ) {
			$this->generate_image();
		}

		echo $this->font->get_png_url();
		exit;
	}

	/**
	 * Create PNG of font name written with font TTF.
	 */
	public function generate_image() {
		$width = $height = $font_size = $left_margin = $font_baseline = $background_color = $font_color = false;
		extract( $this->preview_attributes, EXTR_IF_EXISTS );
		
		// Text Mask
		$mask = imageCreate($width, $height);

		$background = imageColorAllocate($mask, $background_color[0], $background_color[1], $background_color[2]);
		$foreground = imageColorAllocate($mask, $font_color[0], $font_color[1], $font_color[2]);

		$ttf_path = $this->font->maybe_get_remote_ttf();
		if ( !file_exists( $ttf_path ) ) {
			wp_die( 'Could not load $ttf_path: ' . $ttf_path );
		}

		// Text
		imagettftext($mask, $font_size, 0, $left_margin, $font_baseline, $foreground, $ttf_path, $this->font->family );

		// White fill
		$white = imageCreate($width, $height);
		$background = imageColorAllocate($white, $background_color[0], $background_color[1], $background_color[2]);

		// Image
		$image = imagecreatetruecolor($width, $height);
		imagesavealpha( $image, true );
		imagefill( $image, 0, 0, imagecolorallocatealpha( $image, 0, 0, 0, 127 ) );

		// Apply Mask to Image
		for( $x = 0; $x < $width; $x++ ) {
      for( $y = 0; $y < $height; $y++ ) {
        $alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
        $alpha = 127 - floor( $alpha[ 'red' ] / 2 );
        $color = imagecolorsforindex( $white, imagecolorat( $white, $x, $y ) );
        imagesetpixel( $image, $x, $y, imagecolorallocatealpha( $image, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
      }
    }

		ob_start();
		imagePNG($image);
		$image = ob_get_clean();

		$this->save_image( $image );

		// header("Content-type: image/png");
		// echo $image;
	}

	/**
	 * Save preview image file.
	 */
	public function save_image( $image ) {
		if ( !function_exists('WP_Filesystem')) { require ABSPATH . 'wp-admin/includes/file.php'; }
		global $wp_filesystem; WP_Filesystem();

		$dir = dirname( $this->font->get_png_cache_path() );

		if ( !is_dir( $dir ) && !wp_mkdir_p( $dir ) ) { 
			wp_die( "Please check permissions. Could not create directory $dir" );
		}

		$image_file = $wp_filesystem->put_contents( $this->font->get_png_cache_path(), $image, FS_CHMOD_FILE ); // predefined mode settings for WP files

		if ( !$image_file ) {
			wp_die( "Please check permissions. Could not write image to $dir" );
		}
	}

}