<?php
/**
 * Main framework class
 * This file belongs to the Eonet Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Eonet\Core;

use Eonet\Core\Admin\EonetAdmin;
use Eonet\Core\EonetComponents;

if ( ! defined('ABSPATH') ) die('Forbidden') ;

if ( ! class_exists('Eonet') ){
    class Eonet{

        public $version = "1.0.0";
        public $name = "Eonet";

        /**
         * Initializing the Eonet framework :
         *
         * @since 1.0.0
         */
        public function __construct(){

            // Style & Scripts :
            add_action('admin_enqueue_scripts', array($this,'loadScriptsAdmin'));
            add_action('wp_enqueue_scripts', array($this,'loadScriptsFront'));
            add_action('admin_enqueue_scripts', array($this,'loadFont'));
            add_action('wp_enqueue_scripts', array($this,'loadFont'));

        }

        /**
         * Eonet load font :
         */
        public function loadFont() {
            $family = 'family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i';
            wp_enqueue_style( 'eonet-fonts', '//fonts.googleapis.com/css?'.$family, false );
        }

        /**
         * Eonet load backend CSS :
         */
        public function loadScriptsAdmin() {
            wp_enqueue_style('eonet-admin-ui-css', EONET_ASSETS_URL.'/css/eonet_ui_admin.min.css');
            wp_enqueue_script('eonet-admin-ui-js', EONET_ASSETS_URL.'/js/eonet_ui.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script('eonet-admin-js', EONET_ASSETS_URL.'/js/eonet_admin.min.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, true );

        }

        /**
         * Eonet load frontend CSS & JS :
         */
        public function loadScriptsFront() {
            // Font :
            $query_args = array(
                'family' => 'Roboto:300,300i,400,400i,600,600i,700,700i,900,900i',
                'subset' => 'latin,latin-ext'
            );
	        wp_register_style( 'eonet-fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );

            // UI stylesheet :
            wp_enqueue_style('eonet-ui-css', EONET_ASSETS_URL.'/css/eonet_ui_frontend.min.css');

            // UI javascript :
	        if (!eo_is_current_theme_by_alkaweb())
	            wp_enqueue_script('eonet-bootstrap-js', EONET_ASSETS_URL.'/js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script('eonet-frontend-ui-js', EONET_ASSETS_URL.'/js/eonet_ui.min.js', array( 'jquery' ), $this->version, false );

        }

        /**
         * Eonet Version number of the framework :
         *
         * @return int
         * @since 1.0.0
         */
        public function getVersion() {
            return $this->version;
        }



    }
}
