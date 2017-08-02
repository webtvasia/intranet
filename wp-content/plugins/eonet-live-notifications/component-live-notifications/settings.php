<?php
/**
 * Component settings, used in the Eonet admin pages
 */

if(function_exists('bp_is_active') && !bp_is_active('notifications')) {
    $html = '<div class="eo_alert eo_alert_info">';
    $html .= '<h4>'.__('Notificatons are not enabled...', 'eonet-live-notifications').'</h4>';
    $html .= '<p>'.__('The BuddyPress notifications component must be enabled in order to use this plugin. Enable it on', 'eonet-live-notifications');
    $html .= ' <a href="'.get_admin_url().'options-general.php?page=bp-components">'.__('the BuddyPress settings page here', 'eonet-live-notifications').'</a>.</p>';
    $html .= '</div>';
    $settings = array(
        array(
            'name' => 'notitications_requirement',
            'type' => 'html',
            'label' => __('Important', 'eonet-live-notifications'),
            'content' => $html
        ),
    );
} else {
    $active_components = ComponentLiveNotifications\EonetLiveNotifications::getBuddypressComponents();
    $settings = array(
        array(
            'name' => 'notifications_refresh',
            'type' => 'text',
            'label' => __('Refresh time', 'eonet-live-notifications'),
            'desc' => __('In milliseconds, if the delay is too short, you might encounter server performance issues.', 'eonet-live-notifications'),
            'val' => '2000'
        ),
        array(
            'name'      => 'notifications_history',
            'type'      => 'switch',
            'label'     => __('Load history', 'eonet-live-notifications'),
            'desc'      => __('Fetch old unread notifications on page load or only new ones.', 'eonet-live-notifications'),
            'val'       => true
        ),
        array(
            'name' => 'notifications_components',
            'type' => 'select',
            'label' => __('Enable for', 'eonet-live-notifications'),
            'desc' => __('We\'ll only fetch notifications from the following selected components.', 'eonet-live-notifications'),
            'multiple' => true,
            'choices' => $active_components
        ),
        array(
            'name' => 'notifications_position',
            'type' => 'select',
            'label' => __('Notification\'s position', 'eonet-live-notifications'),
            'desc' => __('The notifications can either appear on the left side or the right one.', 'eonet-live-notifications'),
            'multiple' => false,
            'val' => 'right',
            'choices' => array(
                'left' => __('Left Bottom Corner', 'woffice'),
                'right' => __('Right Bottom Corner', 'woffice')
            )
        ),
        array(
            'name' => 'notifications_fadeout',
            'type' => 'text',
            'label' => __('Alert box stays', 'eonet-live-notifications'),
            'desc' => __('In milliseconds, time before the notification fade out.', 'eonet-live-notifications'),
            'val' => '5000'
        ),
        array(
            'name'      => 'notifications_sound',
            'type'      => 'switch',
            'label'     => __('Sound Notification', 'eonet-live-notifications'),
            'val'       => false
        ),
    );
}