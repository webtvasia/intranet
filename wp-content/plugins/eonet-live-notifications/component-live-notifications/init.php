<?php
/**
 * Let's init the component properly :
 * We load the classes :
 */
if(defined('Eonet')) {

    add_action('plugins_loaded', 'eonet_component_init_live_notifications');

    function eonet_component_init_live_notifications()
    {

        // Classes to load :
        $component_classes = array(
            'ComponentLiveNotifications\EonetLiveNotifications',
        );
        // Load them :
        foreach ($component_classes as $class) {
            eonet_autoload($class);
        }
        // Fire it !
        eonet_live_notifications();

        // Hook it
        do_action('eonet_component_after_init_live_notifications');

    }

	if(!function_exists('eonet_live_notifications')) {
		/**
		 * Return the static instance of the class, in this way the class is instanced only one time and ae avoided actions doubled
		 *
		 * @return ComponentLiveNotifications\EonetLiveNotifications
		 */
		function eonet_live_notifications() {
			return \ComponentLiveNotifications\EonetLiveNotifications::instance();
		}
	}

}
