<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * LOAD THE JAVASCRIPT FOR THE FORM
 */
if ( !is_admin() ) {

		$ext_instance = fw()->extensions->get( 'woffice-poll' );

		wp_enqueue_script(
			'fw-extension-'. $ext_instance->get_name() .'-woffice-poll-scripts',
			$ext_instance->locate_js_URI( 'woffice-poll-scripts' ),
			array( 'jquery', 'woffice-theme-plugins', 'woffice-theme-script'),
			$ext_instance->manifest->get_version(),
			true
		);


}