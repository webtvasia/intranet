<?php
/**
 * Bootstrap the Eonet main framework :
 */
if ( ! defined('ABSPATH') ) die('Forbidden') ;

if (!defined('Eonet')) {

    define('Eonet', true);

    add_action('plugins_loaded', 'eonet_init_framework');

    function eonet_init_framework()
    {

        do_action('eonet_before_init');

        require EONET_DIR . '/core/helpers.php';

        $classes = array(
            // Main :
            'Core\Eonet',
            'Core\EonetComponents',
            'Core\EonetOptions',
            'Core\EonetMetaboxes',
            // Assets :
            'Core\External\EonetDumper',
            // Pages :
            'Core\Admin\EonetAdmin',
            'Core\Admin\EonetAdminPages',
            'Core\Admin\Pages\EonetPageExtensions',
            'Core\Admin\Pages\EonetPageSettings',
            'Core\Admin\Pages\EonetPageSupport',
            'Core\Admin\Pages\EonetPageThemes',
	        //Others
	        'Core\EonetGoogleFontLoader',
        );

        foreach ($classes as $class) {
            eonet_autoload($class);
        }

        new Eonet\Core\Eonet();

        if(is_admin()){
            new Eonet\Core\Admin\EonetAdmin();
	        \Eonet\Core\EonetComponents::instance();
        }

        do_action('eonet_init');

    }

}