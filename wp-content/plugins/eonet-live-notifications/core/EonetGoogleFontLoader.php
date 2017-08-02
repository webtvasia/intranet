<?php
namespace Eonet\Core;

if ( ! defined('ABSPATH') ) die('Forbidden');

/**
 * Class EonetGoogleFontLoader
 *
 * Wrap all the functions used to load the google fonts on the pages
 * 
 * @todo it works only with theme settings fields at moment
 *
 * @package Eonet\Core
 */
class EonetGoogleFontLoader
{

	/**
	 * @var array the list of available font names
	 */
	static protected $font_names = array();

	/**
	 * Load asynchronously all fonts selected by the users, only one time per font
	 *
	 * @param array $settings
	 */
	public static function loadFonts( $settings ) {

		$typography_fields = array();

		foreach( $settings as $group ) {

			foreach( $group['settings'] as $field) {

				if( $field['type'] == 'typography')
					array_push($typography_fields, $field['name']);

			}

		}

		$fonts_used = array();
		foreach ($typography_fields as $field) {

			$field_saved = eo_get_theme_option($field);

			if(empty($field_saved) || !isset($field_saved['font-family']) || in_array($field_saved['font-family'], $fonts_used) || $field_saved['font-family'] == '')
				continue;

			array_push($fonts_used, $field_saved['font-family']);
		}

		if( empty($fonts_used))
			return;

		?>
		<script>
			/* You can add more configuration options to webfontloader by previously defining the WebFontConfig with your options */
			if ( typeof WebFontConfig === "undefined" ) {
				WebFontConfig = new Object();
			}
			WebFontConfig['google'] = {families: [<?php echo static::makeGoogleWebfontString ( $fonts_used ) ?>]};

			(function() {
				var wf = document.createElement( 'script' );
				wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.5.3/webfont.js';
				wf.type = 'text/javascript';
				wf.async = 'true';
				var s = document.getElementsByTagName( 'script' )[0];
				s.parentNode.insertBefore( wf, s );
			})();
		</script>
		<?php
		
	}

	/**
	 * Return an array with all the names of the available fonts
	 *
	 * @return array
	 */
	static public function getFontsNames() {

		if( !empty(static::$font_names ))
			return static::$font_names;

		$all_google_fonts = eonet_get_google_fonts();

		return array_keys( $all_google_fonts );
	}

	/**
	 * Get font details from name of the font
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	static function getFont( $name ) {

		$all_google_fonts = eonet_get_google_fonts();

		return $all_google_fonts[$name];

	}

	/**
	 * Return the formatted string for the asynchronous loading of the fonts
	 *
	 * @param $fonts
	 *
	 * @return string
	 */
	static function makeGoogleWebfontString( $fonts ) {
		$link    = "";

		foreach ( $fonts as $family => $font ) {

			$font_settings = static::getFont($font);
			$link .= "', '". str_replace(' ', '+', $font_settings['family']);

			if ( ! empty( $font_settings['variants'] ) ) {
				$link .= ':';
				$link .= implode( ',', $font_settings['variants'] );
			}
		}

		if($link) {
			//Remove the first comma
			$link = ltrim( $link, "', '" );
		}

		return "'" . $link . "'";
	}
}
