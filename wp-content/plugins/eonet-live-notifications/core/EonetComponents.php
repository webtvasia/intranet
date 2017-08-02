<?php
/**
 * Class Components
 * Handles the admin tabs and main actions
 */
namespace Eonet\Core;

if ( ! defined('ABSPATH') ) die('Forbidden');


if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
}
use Plugin_Upgrader;

use ComponentLiveSearch\EonetLiveSearch;
use ComponentFrontendPublisher\EonetFrontendPublisher;
use Eonet\Core\EonetOptions;

class EonetComponents{

	/**
	 * Array containing the name of the classes already instanced.
	 *
	 * @var array
	 */
	protected static $instances = array();

    /**
     * The current component
     * @var string
     */
    public $current_component = '';


    protected function __construct($component_slug = '')
    {

	    //I18n
	    add_action( 'admin_init', array($this, 'load_textdomain') );
	    add_action( 'init', array($this, 'load_textdomain') );

        // State Change for the extension :
        add_action('wp_ajax_eonet_admin_state_component', array($this, 'ajaxStateChange'));
        add_action('wp_ajax_nopriv_eonet_admin_state_component', array($this, 'ajaxStateChange'));
        if(!empty($component_slug)) {
            // We assign it :
            $this->current_component = $component_slug;

	        $this->loadIncludeFolder( $component_slug );

	        $this->loadMetaboxes( $component_slug );

	        // Component CSS :
            add_action('wp_enqueue_scripts', array($this, 'loadCSSFront'));
            add_action('admin_enqueue_scripts', array($this, 'loadCSSAdmin'));
            // Settings link :
            add_filter( 'plugin_action_links_eonet-'.$this->current_component.'/init.php', array($this,'addSetting'));
        }
    }

	/**
	 * @param $component_slug
	 */
	protected function loadIncludeFolder( $component_slug ) {
		//TODO We should create a function to get this path everywhere
		if(file_exists(WP_PLUGIN_DIR . '/eonet/init.php'))
			$path = WP_PLUGIN_DIR . '/eonet/component-'.$component_slug.'/include/*.php';
		else
			$path = WP_PLUGIN_DIR . '/eonet-' . $component_slug . '/component-' . $component_slug . '/include/*.php';
		
		foreach ( glob( $path ) as $file ) {
			include_once $file;
		}
	}

	final private function __clone()
	{
		throw new \Exception('Cloning is not allowed.');
	}

	/**
	 * Returns the main instance, saved statically
	 *
	 * @return EonetComponents
	 */
	final public static function instance() {

		//Get the name of the called class
		$calledClass = get_called_class();

		//If the class has not been instanced already, then instance a new one
		if (!isset(static::$instances[$calledClass]))
		{
			static::$instances[$calledClass] = new $calledClass();
		}

		//Otherwise return the previous instance
		return static::$instances[$calledClass];
	}

    /**
     * Add setting link to the plugin page for each component
     * @param $links array Plugin Action links
     * @return	array
     */
    public function addSetting($links) {

        // We don't go through if there is no component
        if(empty($this->current_component))
            return;

        $link = '<a href="' . admin_url( 'plugins.php?page=eonet&eo_settings='.$this->current_component ) . '">'. esc_html__('Settings','eonet-live-notifications') .'</a>';

        if(in_array($link, $links))
            return $links;

        $mylinks = array(
            $link,
        );

        return array_merge( $links, $mylinks );


    }

    /**
     * We load the CSS for the extension, if it exists :
     */
    public function loadCSSFront() {

        // We don't go through if there is no component
        if(empty($this->current_component))
            return;

        // We get the path :
        $component_stylesheet_path = $this->getPath($this->current_component).'/assets/css/eonet-'.$this->current_component.'-style.min.css';
        $component_stylesheet_url = $this->getUrl($this->current_component).'/assets/css/eonet-'.$this->current_component.'-style.min.css';

        if(file_exists($component_stylesheet_path)) {
            // Component stylesheet :
            wp_enqueue_style('eonet-'.$this->current_component.'-css', $component_stylesheet_url);
        }

    }

	/**
	 * We load the CSS for the admin dashboard
	 */
	public function loadCSSAdmin() {

		// We don't go through if there is no component
		if(empty($this->current_component))
			return;

		// We get the path :
		$component_stylesheet_path = $this->getPath($this->current_component).'/assets/css/eonet-'.$this->current_component.'-admin.min.css';
		$component_stylesheet_url = $this->getUrl($this->current_component).'/assets/css/eonet-'.$this->current_component.'-admin.min.css';

		if(file_exists($component_stylesheet_path)) {
			// Component stylesheet :
			wp_enqueue_style('eonet-'.$this->current_component.'-admin-css', $component_stylesheet_url);
		}

	}

    /**
     * We returns a path to the component' main root
     * It's depends whether we're on production OR development
     * In the first case the main root is wp-content/plugins/
     * and in the second case it'll be wp-content/plugins/eonet/
     * @param $slug : slug of the component
     * @return string : path to the component's index
     */
    public function getPath($slug){
        $camelized = eonet_camel($slug);
        // We check if it's setup as the dev repository :
        $gulp_file = EONET_DIR.'/gulpfile.js';
        if(file_exists($gulp_file)) {
            return WP_PLUGIN_DIR.'/eonet/component-'.$slug;
        } else {
            return WP_PLUGIN_DIR.'/eonet-'.$slug.'/component-'.$slug;
        }
    }

    /**
     * We returns an url to the component' main root
     * @param $slug : slug of the component
     * @return string : path to the component's index
     */
    public function getUrl($slug){
        $camelized = eonet_camel($slug);
        // We check if it's setup as the dev repository :
        $gulp_file = EONET_DIR.'/gulpfile.js';
        if(file_exists($gulp_file)) {
            return plugins_url(). '/eonet/component-'.$slug;
        } else {
            return plugins_url().'/eonet-'.$slug.'/component-'.$slug;
        }
    }

    /**
     * Activate a component for Eonet :
     * @param $slug : its slug
     * @return bool
     */
    public function activate($slug){

        /**
         * ! IMPORTANT :
         * The 'bp-favorite-groups' is for dev purpose
         * It'll be replace with EONET will BE in the air (WP.ORG)
         */

        // Wordpress installable plugin zip :
        $kickstart_file = 'eonet-'.$slug.'/init.php';
        //$slug = "bp-favorite-groups";
        $zip_url = "https://downloads.wordpress.org/plugin/eonet-".$slug.".zip";
        //$kickstart_file = 'bp-favorite-groups/sp-favorite-groups.php';

        // We install it :
        $upgrader = new Plugin_Upgrader();
        $installed = $upgrader->install( $zip_url );

        if($installed == true) {
            activate_plugin( $kickstart_file );
            return true;
        } else {
            return false;
        }

    }

    /**
     * Deactivate an Eonet component :
     * @param $slug : its slug
     */
    public function deactivate($slug){

        /**
         * ! IMPORTANT :
         * The 'bp-favorite-groups' is for dev purpose
         * It'll be replace with EONET will BE in the air (WP.ORG)
         */

        $folder = 'eonet-'.$slug;
        $kickstart_file = $folder.'/init.php';
        //$kickstart_file = 'bp-favorite-groups/sp-favorite-groups.php';
        //$folder = 'bp-favorite-groups';
        // We deactivate the plugin :
        deactivate_plugins( $kickstart_file );
        // We remove its files
        unlink(plugin_dir_path($folder));

    }

    /**
     * Handles the activation / deactivation of any component :
     * @return mixed
     */
    public function ajaxStateChange() {

        // We check whether it's from our page or not.
        check_ajax_referer( 'eonet_admin_state_component_nonce', 'security' );

        // We get our data :
        $method = (isset($_POST['method'])) ? esc_html($_POST['method']) : '';
        $component = (isset($_POST['component'])) ? esc_html($_POST['component']) : '';

        $response = array();
        if(!empty($method) && !empty($component)) {

            if($method == "deactivate") {
                $this->deactivate($component);
                $response['status'] = 'success';
                $response['title'] = esc_html__('Extension has been deleted !', 'eonet-live-notifications');
                $response['content'] = esc_html__('The page will be refreshed in a few seconds...', 'eonet-live-notifications');
            }
            if($method == "activate") {
                $installed = $this->activate($component);
                if($installed == false){
                    $response['status'] = 'error';
                    $response['title'] = esc_html__('We couldn\'t install it...', 'eonet-live-notifications');
                    $response['content'] = esc_html__('Please try from your plugin page.', 'eonet-live-notifications');
                }
                /*$response['status'] = 'success';
                $response['title'] = esc_html__('We\'re installing the extension !', 'eonet-live-notifications');
                $response['content'] = esc_html__('The page will be refreshed in a few seconds...', 'eonet-live-notifications');*/
            }
        } else {
            $response['status'] = 'error';
            $response['title'] = esc_html__('Something went wrong...', 'eonet-live-notifications');
            $response['content'] = __('We\'ve not been able to reset your settings, please try again later.', 'eonet-live-notifications');
        }

        if(is_array($response)) {
            echo json_encode($response);
        }

        // We stop it :
        wp_die();

    }

    /**
     * List all the available components :
     * @param $slug string : specify a component to return
     * @return array
     */
    static public function getComponents($slug = '') {

        $components = array();

        $components['live-search'] = array(
            'name' => esc_html__('Live Search', 'eonet-live-notifications'),
            'version' => '1.0.0',
            'icon' => 'ion-ios-search',
            'premium' => false,
            'description' => esc_html__('Enhance your Wordpress search bar with customizable live search through all your post types and members.', 'eonet-live-notifications'),
        );

        $components['frontend-publisher'] = array(
            'name' => esc_html__('Frontend Publisher', 'eonet-live-notifications'),
            'version' => '1.0.0',
            'icon' => 'ion-ios-compose-outline',
            'premium' => false,
            'description' => esc_html__('Allow yourself, your team or your user to edit and publish any Wordpress post or page.', 'eonet-live-notifications'),
        );

	    $components['manual-user-approve'] = array(
		    'name' => esc_html__('Manual User Approve', 'eonet-live-notifications'),
		    'version' => '1.0.0',
		    'icon' => 'ion-person-add',
		    'premium' => false,
		    'description' => esc_html__('Allows the admin of the site to approve or deny users manually, after they have registered.', 'eonet-live-notifications'),
	    );

        $components['live-notifications'] = array(
            'name' => esc_html__('Live Notifications', 'eonet-live-notifications'),
            'version' => '1.0.0',
            'icon' => 'ion-ios-loop',
            'premium' => false,
            'description' => esc_html__('Enables live notifications for all your users to get better interactions within your site.', 'eonet-live-notifications'),
        );

        $components['project-manager'] = array(
            'name' => esc_html__('Project Manager', 'eonet-live-notifications'),
            'version' => '1.0.0',
            'icon' => 'ion-briefcase',
            'premium' => false,
            'description' => esc_html__('Enables a complete project management tool for your site.', 'eonet-live-notifications'),
        );

        $components['social-connect'] = array(
            'name' => esc_html__('Social Connect', 'eonet-live-notifications'),
            'version' => '0.0.0',
            'icon' => 'ion-ios-cloud-download-outline',
            'premium' => false,
            'description' => esc_html__('Allow your visitor to login or register using Facebook, Twitter, Slack or Linkedin.', 'eonet-live-notifications'),
        );

        if($slug != '') {
            return $components[$slug];
        } else {
            return $components;
        }

    }

    /**
     * Retrieve the config of the extension (name, version, icon, premium, description)
     * @param $slug string
     * @return array
     */
    static function getConfig($slug)
    {
        return self::getComponents($slug);
    }

    /**
     * Retrieve the settings of an extension
     * @param $slug string
     * @return array
     */
    static function getSettings($slug)
    {

        if(file_exists(WP_PLUGIN_DIR . '/eonet/init.php')) {
            $path = WP_PLUGIN_DIR . '/eonet/component-'.$slug.'/settings.php';
        } else {
            $path = WP_PLUGIN_DIR . '/eonet-'.$slug.'/component-'.$slug.'/settings.php';
        }

        if(file_exists($path)) {
            include($path);
        }

        if(isset($settings)) {
            return $settings;
        } else {
            return array();
        }
    }

    /**
     * Returns the settings HTML markup
     * @param $slug string
     * @return string
     */
    static function getSettingsMarkup($slug)
    {
        $settings = self::getSettings($slug);
        if($settings) {
            return EonetOptions::renderForm($settings);
        }
    }

	/**
	 * Load the metaboxes fields for each component
	 *
	 * @param $slug
	 */
	protected function loadMetaboxes($slug)
	{

		if(file_exists(WP_PLUGIN_DIR . '/eonet/init.php')) {
			$path = WP_PLUGIN_DIR . '/eonet/component-'.$slug.'/metaboxes.php';
		} else {
			$path = WP_PLUGIN_DIR . '/eonet-'.$slug.'/component-'.$slug.'/metaboxes.php';
		}

		if(file_exists($path)) {
			include($path);
		}

		if(isset($settings)) {
			new EonetMetaboxes($settings);
		}

	}

    /**
     * Get all active components
     * @return array
     */
    static public function getActiveComponents() {

        $components = self::getComponents();

        $active_components = array();

        foreach ($components as $slug=>$component)
        {
            $camelized = eonet_camel($slug);
            $className = eonet_camel('eonet-'.$slug);
            $classPath = eonet_camel('component-'.$slug);
            if(class_exists($classPath.'\\'.$className)) {
                $active_components[$slug] = $slug;
            }
        }

        return $active_components;

    }

	/**
	 * Load the languages files
	 */
	public function load_textdomain() {

		load_plugin_textdomain( 'eonet-live-notifications');

	}
}