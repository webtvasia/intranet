<?php

if(!function_exists('woffice_autocomplete_add_scripts')) {
	/*
	 * INSPIRED BY http://gabrieleromanato.name/adding-jquery-ui-autocomplete-to-the-wordpress-search-form/
	 * COPYRIGHT gabrieleromanato
	 */
	function woffice_autocomplete_add_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		//wp_register_style( 'jquery-ui-styles','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
		//wp_enqueue_style( 'jquery-ui-styles' );
		wp_register_script( 'woffice-autocomplete', get_template_directory_uri() . '/js/autocomplete.js', array(
			'jquery',
			'jquery-ui-autocomplete'
		), '1.0', false );
		wp_localize_script( 'woffice-autocomplete', 'WofficeAutocomplete', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'woffice-autocomplete' );
	}
}
add_action( 'wp_enqueue_scripts', 'woffice_autocomplete_add_scripts' );

if(!function_exists('woffice_search')) {
	function woffice_search() {
		$term        = strtolower( $_GET['term'] );
		$suggestions = array();

        /**
         * Filters the search query object
         *
         * @param string
         * @param $term string
         */
		$query = apply_filters('woffice_search_query_args', 's=' . $term);

		$loop = new WP_Query( $query );

		while ( $loop->have_posts() ) {
			$loop->the_post();
			$suggestion          = array();
			$suggestion['label'] = get_the_title();
			$suggestion['link']  = get_permalink();

			$suggestions[] = $suggestion;
		}

		wp_reset_postdata();


		$response = json_encode( $suggestions );
		echo $response;
		exit();

	}
}

add_action( 'wp_ajax_woffice_search', 'woffice_search' );
add_action( 'wp_ajax_nopriv_woffice_search', 'woffice_search' );