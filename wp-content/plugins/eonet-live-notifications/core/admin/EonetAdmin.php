<?php
/**
 * Class EonetAdmin
 * Handles the admin tabs and main actions
 */
namespace Eonet\Core\Admin;

if ( ! defined('ABSPATH') ) die('Forbidden');

use Eonet\Core\Eonet;
use Eonet\Core\EonetComponents;
use Eonet\Core\Admin\Pages\EonetPageExtensions;
use Eonet\Core\Admin\Pages\EonetPageSettings;
use Eonet\Core\Admin\Pages\EonetPageSupport;
use Eonet\Core\Admin\Pages\EonetPageThemes;

class EonetAdmin extends Eonet
{

    static public $option_settings_field = 'eonet_settings';

    /**
     * Construct :
     */
    public function __construct(){

        // Admin pages :
        add_action('wp_ajax_eonet_admin_get_page', array($this, 'ajaxGetPage'));
        add_action('wp_ajax_nopriv_eonet_admin_get_page', array($this, 'ajaxGetPage'));
        add_action('wp_ajax_eonet_admin_save_settings', array($this, 'ajaxSaveSettings'));
        add_action('wp_ajax_nopriv_eonet_admin_save_settings', array($this, 'ajaxSaveSettings'));
        add_action('wp_ajax_eonet_admin_reset_settings', array($this, 'ajaxResetSettings'));
        add_action('wp_ajax_nopriv_eonet_admin_reset_settings', array($this, 'ajaxResetSettings'));
        add_action('admin_menu', array($this,'adminMenus'));

    }

    /**
     * AJAX call to get a save settings.
     * @return string
     */
    public function ajaxSaveSettings() {

        // We check whether it's from our page or not.
        check_ajax_referer( 'eonet_admin_save_settings' );

        // Values we'll save :
        $eonet_settings = array();

        foreach ($_POST as $field_name=>$field_value) {
            $option_array = explode( 'eo_field_', $field_name );
            if(sizeof($option_array) == 2) {
                /**
                 * We remove backlashes
                 *
                 * @link https://codex.wordpress.org/Function_Reference/stripslashes_deep
                 */
                $eonet_settings[$option_array[1]] = stripslashes_deep($field_value);
            }
        }

	    $option_field = apply_filters('eonet_save_settings_options_field', self::$option_settings_field);

        // We send to the database :
        $value = update_option($option_field, $eonet_settings, false);

        $response = array();
        // We return true if the option have been updated or didn't changed.
        if($value || $eonet_settings == get_option($option_field)) {
            $response['status'] = 'success';
            $response['title'] = esc_html__('Done, everything is safe !', 'eonet-live-notifications');
            $response['content'] = esc_html__('Your settings have been saved successfully.', 'eonet-live-notifications');
        } else {
            $response['status'] = 'error';
            $response['title'] = esc_html__('Something went wrong...', 'eonet-live-notifications');
            $response['content'] = esc_html__("We've not been able to save your settings, please try again later.", 'eonet-live-notifications');
        }

	    do_action('eonet_admin_settings_saved', $response);

        echo json_encode($response);

        // We stop it :
        wp_die();

    }

    /**
     * AJAX call to reset settings.
     * @return string
     */
    public function ajaxResetSettings() {

        // We check whether it's from our page or not.
        check_ajax_referer( 'eonet_admin_reset_settings_nonce', 'security' );

	    $option_field = apply_filters('eonet_save_settings_options_field', self::$option_settings_field);

        // We send to the database :
        $value = ($_POST['reset'] == 'true') ? update_option($option_field, '', false) : '';

        $response = array();
        // We return true if the option have been updated or didn't changed.
        if($value) {
            $response['status'] = 'success';
            $response['title'] = esc_html__('Done, settings have been reset !', 'eonet-live-notifications');
            $response['content'] = esc_html__('The page will be refreshed in a few seconds...', 'eonet-live-notifications');
        } else {
            $response['status'] = 'error';
            $response['title'] = esc_html__('Something went wrong...', 'eonet-live-notifications');
            $response['content'] = esc_html__("We've not been able to reset your settings, please try again later.", 'eonet-live-notifications');
        }

        echo json_encode($response);

        // We stop it :
        wp_die();

    }

	/**
	 * Get an admin settings from Eonet
	 *
	 * @param string $name : name of the option to look for
	 * @param string $default : default value if it doesn't exist
	 * @param null|string $option_field the name of field that contains the option saved in the db
	 *
	 * @return string : its value
	 */
    static function getSetting($name = '', $default = '', $option_field = null) {

	    $option_field = (is_null($option_field)) ? self::$option_settings_field : $option_field;

        // We get all the settings :
        $eonet_settings = get_option( $option_field );

        // First check about the main array :
        if(!empty($eonet_settings) && is_array($eonet_settings) && isset($name) && !empty($name)) {
	        // If the option exist :
	        if ( isset( $eonet_settings[ $name ] ) ) {

		        //(TODO: It require more tests with the fields that already use a default value) If the default value isn't empty, and the saved value is, then return the default value
		        //return ( empty($default) || !empty($default) && empty($eonet_settings[ $name ]) ) ? $eonet_settings[ $name ] : $default;
		        return $eonet_settings[ $name ];
	        }

	        // TODO:
	        // if the option doesn't exist in the database, the function should return null, rather then '',
	        // otherwise it can create some troubles with the default values, for instance with a text field with a string as default and saved as an empty string
	        //
	        // In order to solve the issue, this function needs the $default = null; and return null for the case below,
	        // but all the functions renderFieldType* in EonetOptions have to be changed to check the null value rather than ''
	        // Then it needs some tests of course
	        //
	        //
	        // Note: this has sense only for the componenets, not for the theme, because the theme save the fields on the theme activation
	        //(which could be a solution for the components as well)
        }

        return $default;

    }


    /**
     * AJAX call to get a page content.
     * @return mixed
     */
    public function ajaxGetPage() {

        // We check whether it's from our page or not.
        check_ajax_referer( 'eonet_admin_get_page_nonce', 'security' );

        // We build the class name dynamically :
        $className = 'Eonet\Core\Admin\Pages\EonetPage'.ucwords($_POST['slug']);
	    $className = apply_filters( 'eonet_admins_pages_get_class_from_slug', $className, $_POST['slug']);

        // We invoke it :
        $pageInstance = new $className();

        // We return it, it does contain HTML but no data from the user :
        echo $pageInstance->getPageContent();

        // We stop it :
        wp_die();

    }

    /**
     * Eonet add menus.
     */
    public function adminMenus(){

	    add_menu_page(
		    esc_html__( 'Eonet Dashboard', 'eonet-live-notifications' ),
		    esc_html__( 'Eonet','eonet-live-notifications' ),
		    'manage_options',
		    'eonet',
		    array($this,'admin_page_callback'),
		    EONET_ASSETS_URL.'/images/menu_icon.png',
		    30
	    );

	    // Sub pages :
	    $active_extensions = EonetComponents::getActiveComponents();
	    foreach ($active_extensions as $extension_slug=>$extension) {
		    $extension_config = EonetComponents::getConfig($extension_slug);
		    add_submenu_page(
			    'eonet',
			    $extension_config['name'],
			    $extension_config['name'],
			    'manage_options',
			    '?page=eonet&eo_settings='.$extension_slug
		    );
	    }

    }

    /**
     * Eonet add callback :
     */
    public function admin_page_callback(){
        $admin = new EonetAdmin();
        $admin->build();
    }


    /**
     * Get the current path to make it easier
     * @return string
     */
    public function getPath()
    {
        return EONET_DIR.'/core/admin/';
    }

    /**
     * Build the main HTML Structure;
     * @return mixed
     */
    public function build()
    {
        $args = array(
            'plugin_name' => $this->name,
            'plugin_version' => $this->version,
        );
        echo eonet_render_view($this->getPath().'views/wrapper.php', $args);
    }

    /**
     * Get all compatible themes :
     * @return array
     */
    static public function getThemes() {
        $themes = array(
            array(
                'name' => esc_html__('Woffice Intranet/Extranet theme', 'eonet-live-notifications'),
                'link_features' => 'https://themeforest.net/item/woffice-intranetextranet-wordpress-theme/11671924?ref=alkaweb',
                'link_demo' => 'https://woffice.io/',
                'thumb' => EONET_ASSETS_URL.'/images/themes/woffice.png',
                'premium' => true,
                'features' => array(
                    esc_html__('Projects Management','eonet-live-notifications'),
                    esc_html__('Complete Wiki/Knowledge base','eonet-live-notifications'),
                    esc_html__('Members map & directory','eonet-live-notifications'),
                    esc_html__('Responsive layout (mobile & tablet)','eonet-live-notifications'),
                    esc_html__('Cover picture','eonet-live-notifications'),
                    esc_html__('Private / Public pages','eonet-live-notifications'),
                    esc_html__('Files Manager','eonet-live-notifications'),
                    esc_html__('Events calendar','eonet-live-notifications'),
                )
            ),

	        array(
		        'name' => esc_html__('Eonet - Multi-Purpose Community / Network WordPress Theme', 'eonet-live-notifications'),
		        'link_features' => 'https://themeforest.net/item/eonet-multipurpose-community-network-wordpress-theme/19557463?ref=alkaweb',
		        'link_demo' => 'https://demos.alka-web.com/eonet/',
		        'thumb' => EONET_ASSETS_URL.'/images/themes/eonet.jpg',
		        'premium' => true,
		        'features' => array(
			        esc_html__('Designed for communities','eonet-live-notifications'),
			        esc_html__('1-to-1 live chat','eonet-live-notifications'),
			        esc_html__('Super fast loading','eonet-live-notifications'),
			        esc_html__('Restrict pages and contents','eonet-live-notifications'),
			        esc_html__('Fully customizable','eonet-live-notifications'),
			        esc_html__('Members map','eonet-live-notifications'),
			        esc_html__('Social login','eonet-live-notifications'),
			        esc_html__('Files Manager','eonet-live-notifications'),
		        )
	        )
        );

        return $themes;
    }


}