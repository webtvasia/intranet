<?php
/**
 * Class Woffice_Autocomplete
 *
 * Used to create an autocompleting search form in the header
 * This is possible using an AJAX call and jQuery UI feature
 *
 * @since 2.1.3
 * @link http://gabrieleromanato.name/adding-jquery-ui-autocomplete-to-the-wordpress-search-form/
 * @author gabrieleromanato
 */
if( ! class_exists( 'Woffice_Autocomplete' ) ) {
    class Woffice_Autocomplete
    {

        /**
         * Woffice_Autocomplete constructor
         */
        public function __construct()
        {
            add_action( 'wp_enqueue_scripts', array($this,'woffice_autocomplete_add_scripts'));
            add_action( 'wp_ajax_woffice_search', array($this,'woffice_search'));
            add_action( 'wp_ajax_nopriv_woffice_search', array($this,'woffice_search'));
        }

        /**
         * Add scripts to the footer
         */
        public function woffice_autocomplete_add_scripts() {
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_register_script( 'woffice-autocomplete', get_template_directory_uri() . '/js/autocomplete.js', array( 'jquery', 'jquery-ui-autocomplete' ), '1.0', false );
            wp_localize_script( 'woffice-autocomplete', 'WofficeAutocomplete', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
            wp_enqueue_script( 'woffice-autocomplete' );
        }

        /**
         * Run the search in the WP_QUERY
         * Run by ajax
         *
         * @return void
         */
        public function woffice_search() {

            $term = strtolower( $_GET['term'] );

            $loop = new WP_Query( array( 's' => $term, 'post_type' => array('page', 'post', 'directory', 'project', 'wiki', 'ajde_events', 'forum', 'topic') ) );

            while( $loop->have_posts() ) {

                $loop->the_post();
                $suggestion = array();
                $suggestion['label'] = html_entity_decode(get_the_title());
                $suggestion['label'] = str_replace('”', '"', $suggestion['label']);
                $suggestion['label'] = str_replace('“', '"', $suggestion['label']);
                $suggestion['label'] = str_replace('‘', '\'', $suggestion['label']);
                $suggestion['label'] = str_replace('’', '\'', $suggestion['label']);
                $suggestion['link'] = get_permalink();
                $suggestions[] = $suggestion;

            }

            wp_reset_postdata();


            $response = json_encode( $suggestions );
            echo $response;
            exit();

        }

    }
}
/**
 * Let's fire it :
 */
new Woffice_Autocomplete();