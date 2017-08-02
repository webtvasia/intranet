<?php
/**
 * TGM Plugin Activation file
 */
if ( !is_multisite() ) {

    require_once dirname( __FILE__ ) . '/plugins/TGM_Plugin_Activation.php';

    /*
     *  INSTALL PLUGINS WITH TGM PLUGIN ACTIVATION
    */
    function _action_theme_register_required_plugins() {
        tgmpa( array(
            array(
                'name'      => 'Unyson',
                'slug'      => 'unyson',
                'force_activation'  => false,
                'required'  => true,
            ),
            /*buddypress*/
            array(
                'name'      => 'Buddypress',
                'slug'      => 'buddypress',
                'force_activation'  => false,
                'required'  => false,
            ),
            /*wisechat*/
            array(
                'name'      => 'Wise chat',
                'slug'      => 'wise-chat',
                'force_activation'  => false,
                'required'  => false,
            ),
            /*RevSlider*/
            array(
                'name'                  => 'Revolution Slider', // The plugin name
                'slug'                  => 'revslider', // The plugin slug (typically the folder name)
                'source'                => get_template_directory_uri() . '/inc/plugins/revslider.zip',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
                'version'              => '5.4.3.1',
            ),
            /*dpProEventCalendar*/
            array(
                'name'                  => 'Pro Event Calendar (New)', // The plugin name
                'slug'                  => 'dpProEventCalendar', // The plugin slug (typically the folder name)
                'source'                => get_template_directory_uri() . '/inc/plugins/dpProEventCalendar.zip',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
                'version'              => '2.9.5',
            ),
            /*EonetLiveNotifications*/
            array(
                'name'                  => 'Eonet Live Notifications', // The plugin name
                'slug'                  => 'eonet-live-notifications', // The plugin slug (typically the folder name)
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
            ),
            /*FileAway*/
            array(
                'name'      => 'File Away',
                'slug'      => 'file-away',
                'force_activation'  => false,
                'required'  => false,
            ),
            /*contact-form-7
            array(
                'name'      => 'Contact Form 7',
                'slug'      => 'contact-form-7',
                'force_activation'  => false,
                'required'  => false,
            ),*/
            /*buddypress-xprofile-custom-fields-type
            array(
                'name'      => 'Buddypress Profile Custom Fields Type',
                'slug'      => 'buddypress-xprofile-custom-fields-type',
                'force_activation'  => false,
                'required'  => false,
            ),*/
            /*visualizer*/
            /*array(
                'name'      => 'Graph visualizer',
                'slug'      => 'visualizer',
                'force_activation'  => false,
                'required'  => false,
            ),*/
            /*EventOn*/
            /*array(
                'name'                  => 'EventOn Calendar (Depreciated, try Pro Event above)', // The plugin name
                'slug'                  => 'eventON', // The plugin slug (typically the folder name)
                'source'                => get_template_directory_uri() . '/inc/plugins/eventON.zip',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
                'version'              => '2.5.4,
            ),*/
            /*EventOn Asset*/
            /*array(
                'name'                  => 'EventOn Asset (Full Calendar ADDON)', // The plugin name
                'slug'                  => 'eventon-full-cal', // The plugin slug (typically the folder name)
                'source'                => get_template_directory_uri() . '/inc/plugins/eventon-full-cal.zip',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
                'version'              => '1.1.3',
            ),*/
            /* Removed since 1.7.2
             * array(
                'name'                  => 'EventOn Asset (Action User ADDON)', // The plugin name
                'slug'                  => 'eventon-action-user', // The plugin slug (typically the folder name)
                'source'                => get_template_directory_uri() . '/inc/plugins/eventon-action-user.zip',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
                'version'              => '1.9.2',
            ),
            array(
                'name'                  => 'EventOn Asset (Single Event ADDON)', // The plugin name
                'slug'                  => 'eventon-single-event', // The plugin slug (typically the folder name)
                'source'                => get_template_directory_uri() . '/inc/plugins/eventon-single-event.zip',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
                'version'              => '1.1',
            ),*/
            /*File Manager*/
            /* REMOVED SINCE 1.4.4
                array(
                'name'                  => 'Multiverso file manager', // The plugin name
                'slug'                  => 'multiverso', // The plugin slug (typically the folder name)
                'source'                => get_template_directory_uri() . '/inc/plugins/multiverso.zip',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'required'              => false,
            ),*/
        ) );

    }
    add_action( 'tgmpa_register', '_action_theme_register_required_plugins' );

}