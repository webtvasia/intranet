<?php
/**
 * Class Eonet Live Notifications
 */
namespace ComponentLiveNotifications;

if ( ! defined('ABSPATH') ) die('Forbidden');

use Eonet\Core\EonetComponents;

if(!class_exists('ComponentLiveNotifications\EonetLiveNotifications')) {

    class EonetLiveNotifications extends EonetComponents
    {

        /**
         * Slug of the component so we can get its details
         * @var string
         */
        public $slug = "live-notifications";

        /**
         * Ajax action name
         * @var string
         */
        public $ajax_action = 'eonet_fetch_notifications';

        /**
         * Construct the component :
         */
        public function __construct()
        {
            // If the notification component is disabled :
            if (!function_exists('bp_is_active') || !bp_is_active('notifications')) {
                return null;
            }

            // Ajax to handle the notifications
            add_action('wp_ajax_'.$this->ajax_action, array($this, 'ajaxFetchNotifications'));
            add_action('wp_ajax_nopriv_'.$this->ajax_action, array($this, 'ajaxFetchNotifications'));
            add_action('wp_ajax_'.$this->ajax_action.'_mark', array($this, 'ajaxMarkRead'));
            add_action('wp_ajax_nopriv_'.$this->ajax_action.'_mark', array($this, 'ajaxMarkRead'));
            // JS scripts
            add_action('wp_enqueue_scripts', array($this, 'loadScripts'));
            // Parent Instance :
            parent::__construct($this->slug);
            // Action :
            do_action('eonet_live_notifications_construct');

        }

        /**
         * We'll fetch notification for a specific user ID
         * By default, we'll take the current logged in user.
         * @return int
         */
        public function getUserID()
        {
            return apply_filters('eonet_notifications_user', get_current_user_id());
        }


        /**
         * Returns an array of the active Buddypress components
         * But not the notifications as it MUST be included in the first place
         * @param $active boolean whether we only return active components or not
         * @return array
         */
        static function getBuddypressComponents($active = true)
        {

            $array = array();

            if(!function_exists('bp_is_active'))
                return $array;

            if ($active && bp_is_active('messages'))
                $array['messages'] = __('Private Messages', 'eonet-live-notifications');
            if ($active && bp_is_active('friends'))
                $array['friends'] = __('Friend Connections', 'eonet-live-notifications');
            if ($active && bp_is_active('groups'))
                $array['groups'] = __('User Groups', 'eonet-live-notifications');
            if ($active && bp_is_active('activity'))
                $array['activity'] = __('Activity Streams', 'eonet-live-notifications');

            return $array;

        }

        /**
         * Return an ionicon class depending on the Buddypress component
         * @param $component_name
         * @return string
         */
        static function getBuddypressComponentClass($component_name)
        {

            switch ($component_name) :
                case 'messages' :
                    $icon_class = 'fa-comments';
                    break;
                case 'friends' :
                    $icon_class = 'fa-thumbs-o-up';
                    break;
                case 'groups' :
                    $icon_class = 'fa-users';
                    break;
                case 'activity' :
                    $icon_class = 'fa-newspaper-o';
                    break;
                default :
                    $icon_class = 'fa-bolt';
            endswitch;

            return apply_filters('eonet_notifications_icon_class', $icon_class, $component_name);

        }

        /**
         * Add the scripts used by the extension :
         */
        public function loadScripts()
        {

            $sounds_base = $this->getUrl($this->slug) . '/assets/sounds/01';

            $data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'notifications_nonce' => wp_create_nonce($this->ajax_action . '_nonce'),
                'notifications_action' => $this->ajax_action,
                'notifications_action_mark' => $this->ajax_action.'_mark',
                'notifications_refresh' => eonet_get_option('notifications_refresh', '2000'),
                'notifications_position' => eonet_get_option('notifications_position', 'right'),
                'notifications_fadeout' => eonet_get_option('notifications_fadeout', '5000'),
                'notifications_sound' => eonet_get_option('notifications_sound', 'false'),
                /**
                 * `eonet_notifications_sounds_base` filter
                 * change the songs files locations, it must be
                 * path/to/files/filename
                 * And must contain: .wav, .ogg, .mp3
                 * @param $sounds_base
                 */
                'sound_path' => apply_filters('eonet_notifications_sounds_base', $sounds_base),
            );

            wp_enqueue_script($this->slug . '-script', $this->getUrl($this->slug) . '/assets/js/eonet_live_notifications.js', array('jquery'), 1.0, true);
            wp_localize_script($this->slug . '-script', 'EONET_NOTIFICATIONS', $data);

        }

        /**
         * This notification fetch any new notification
         * And return an array for the notification
         * At this moment, we only return one notification at a time
         */
        public function ajaxFetchNotifications()
        {
            // We check whether it's from our page or not.
            check_ajax_referer($this->ajax_action . '_nonce', 'security');

            $user_id = $this->getUserID();

            if ($user_id > 0) {

                /**
                 * We make sure the user actually have a notification
                 * That doesn't mean it'll be displayed because
                 * even if the notification is unread, if it already has been
                 * displayed, we won't show it again
                 */
                if ($this->hasNotifications($user_id)) {
                    $notification = $this->renderNotifications($user_id);
                    if (!empty($notification)) {
                        echo json_encode($notification);
                    }
                }

            }

            // We stop it :
            wp_die();

        }

        /**
         * This function marks a BuddyPress notification as read
         * when the close icon is being clicked
         */
        public function ajaxMarkRead()
        {
            // We check whether it's from our page or not.
            check_ajax_referer($this->ajax_action . '_nonce', 'security');

            if(isset($_POST['component_action']) && isset($_POST['component_name']) && isset($_POST['item_id'])) {

                $user_id = $this->getUserID();
                $component_action = sanitize_text_field($_POST['component_action']);
                $component_name = sanitize_text_field($_POST['component_name']);
                $item_id = intval($_POST['item_id']);
                bp_notifications_mark_notifications_by_item_id($user_id, $item_id, $component_name, $component_action, false, 0);

            }

            // We stop it :
            wp_die();
        }

        /**
         * We take the unread notifications an render the one that must be displayed
         * if there is any of them.
         * We return an array ready to be displayed as a notification
         * @param $user_id
         * @return array
         */
        public function renderNotifications($user_id)
        {

            // We get those notifications
            $notifications = bp_notifications_get_notifications_for_user($user_id, "object");
            $notifications = array_reverse($notifications);

            if (empty($notifications))
                return array();

            $displayed_notifications = get_user_meta($user_id, 'eonet_notifications_displayed', true);
            $displayed_notifications = (empty($displayed_notifications)) ? array() : $displayed_notifications;
            $ready_notifications = array();


            /**
             * Whether we load the old unread notifications or only the new ones
             */
            $history = eonet_get_option('notifications_history', true);
            foreach ($notifications as $notification) {
                /**
                 * If it's new and not displayed yet
                 * Then we can display it
                 *
                 */
                if (
                    $notification->is_new != 1
                    ||
                    in_array($notification->id, $displayed_notifications)
                )
                {
                    $checked = false;
                } else {
                    /**
                     * If we load the history, then we show it anyway :
                     */
                    if ($history == true) {
                        $checked = true;
                    }
                    /**
                     * If we don't load the history then we check if it wasn't displayed yet
                     * and otherwise, we compare the date to 5 minutes before the ajax call
                     * The date format is : [date_notified] => '2015-11-08 14:50:08'
                     */
                    else {
                        if (
                            $notification->is_new == 1
                            &&
                            !in_array($notification->id, $displayed_notifications)
                            &&
                            strtotime($notification->date_notified) > (strtotime("now") - (5*60))
                        ) {
                            $checked = true;
                        } else {
                            $checked = false;
                        }
                    }
                }

                if($checked) {
                    $ready_notifications[] = $notification;
                }
            }

            if (empty($ready_notifications))
                return array();

            /**
             * All notifications
             * which are not read yet and were not displayed yet.
             * We only take the first one for now
             * we also update the displayed notifications array
             * attached to the user
             */
            $live_notification = $ready_notifications[0];

            array_push($displayed_notifications, $live_notification->id);
            update_user_meta($user_id, 'eonet_notifications_displayed', $displayed_notifications);

            $data = 'data-eo-component-action="{$live_notification->component_action}" data-eo-component-name="{$live_notification->component_name}" data-eo-item-id="{$live_notification->item_id}"';

            $response = array(
                'icon' => self::getBuddypressComponentClass($live_notification->component_name),
                'title' => __('You\'ve a new notification!', 'eonet-live-notifications'),
                'content' => '<a href="' . $live_notification->href . '" '.$data.'>' . $live_notification->content . '</a>',
            );

            return $response;

        }

        /**
         * Make sure we have a notification to return for a specific user
         * @param $user_id
         * @return boolean
         */
        public function hasNotifications($user_id)
        {

            if (bp_notifications_get_unread_notification_count($user_id) > 0) {
                return true;
            } else {
                return false;
            }
        }


    }

    // We start it :
    new EonetLiveNotifications();

}