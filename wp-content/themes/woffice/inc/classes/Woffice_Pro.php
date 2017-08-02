<?php
/**
 * Class Woffice_Pro
 *
 * Load everything related to the Woffice Pro service
 * Also includes filters and actions related to Unyson
 *
 * @since 2.4.5
 * @author Alkaweb
 */
if( ! class_exists( 'Woffice_Pro' ) ) {
    class Woffice_Pro
    {

        /**
         * An unique product key (Woffice key)
         *
         * @var string
         */
        static $product_key =  '';

        /**
         * Woffice_Pro constructor
         */
        public function __construct()
        {

            // Set the product key
            $this->set_product_key();
            add_action( 'wp_ajax_nopriv_hub_get_data', array($this, 'hub_get_data' ));
            add_action( 'wp_ajax_hub_get_data', array($this, 'hub_get_data' ));

        }

        /**
         * Check whether the website is part of a pro plan from the database
         * We save it in an option: alka_pro_last_checked
         * If it's -1 it's not a pro.
         * Otherwise we check the date, if it was more than 2 weeks, we re-check and re-save
         *
         * @return boolean
         */
        static function is_pro() {

            $is_pro_last_checked = (int) get_option('alka_pro_last_checked', 0);

            // If not a pro
            if($is_pro_last_checked === -1)
                return false;

            // Timestamps
            $now = time();
            $two_weeks = 14 * 24 * 60 * 60;
            $two_weeks_ago = $now - $two_weeks;

            // If default or value more than 2 weeks ago, we check again
            if($is_pro_last_checked === 0 || $is_pro_last_checked < $two_weeks_ago )
                self::api_check_pro_account();

            // Last check is to make sure there is a pro key
            $pro_key = get_option('alka_pro_key', false);
            if($pro_key == false)
                return false;

            // It's a pro!
            return true;


        }

        /**
         * Check whether the website is part of a pro plan from the API
         *
         * @return bool
         */
        static function api_check_pro_account() {

            // We build the request object
            $site_url = get_site_url();
            $email = get_option('admin_email');
            $request_string = array(
                'body' => array(
                    'product_sku' => '11671924',
                    'email' => $email,
                    'site_url' => $site_url,
                    'product_key' => self::$product_key
                )
            );

            // We call the API
            $raw_response = wp_remote_post('https://hub.alka-web.com/api/pro/check/', $request_string);

            // We check the response
            $response = null;
            if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
                $response = json_decode($raw_response['body']);
            if( empty($response) )
                error_log('Problem from the Alka Pro API: '.$raw_response['body']['message']);

            // If it's a pro
            if(isset($response->pro_key)) {
                update_option('alka_pro_last_checked', time());
                update_option('alka_pro_key', $response->pro_key);
                return true;
            }
            // If it's NOT a pro
            else {
                update_option('alka_pro_last_checked', -1);
                return false;
            }

        }

        /**
         * Set the product key
         *
         * It's either a string or false if not set yet
         */
        static function set_product_key() {

            $product_key = get_option('woffice_key');

            self::$product_key = $product_key;

        }

        /**
         * Provides site data to our API
         * Safely checked
         */
        public function hub_get_data() {

            $key = !empty($_GET['pro_key']) ? $_GET['pro_key'] : null;
            $email = !empty($_GET['email']) ? $_GET['email'] : null ;
            //we check that the parameters are provided in the request
            if (!($email && $key))
                wp_die();

            // we check if the key and email match our records
            if ($key == self::$product_key && $email == get_option('admin_email')) {
                //we get the number of posts
                $number_blog_posts = wp_count_posts();
                $number_wiki = wp_count_posts('wiki');
                $number_members_directory = wp_count_posts('directory');
                $number_projects = wp_count_posts('project');
                $number_users = count_users();
                $theme = wp_get_theme();
                //we only keep relevant information related to the theme : name (child or no)
                $theme_info = array(
                    'name' => $theme->get( 'Name' ),
                    //we test if it's the child/parent theme and display relevant version
                    'version' => $theme->get( 'Version' ) ? $theme->get( 'Version' ) : wp_get_theme(get_template())->get('Version')
                );
                $data = array(
                            'blog' => $number_blog_posts,
                            'wiki' => $number_wiki,
                            'directory' => $number_members_directory,
                            'project' => $number_projects,
                            'users' => $number_users,
                            'theme' => $theme_info
                        );
            }

            echo json_encode(array('success' => true, 'data' => $data));

            wp_die();
        }
    }
}

/**
 * Let's fire it :
 */
new Woffice_Pro();