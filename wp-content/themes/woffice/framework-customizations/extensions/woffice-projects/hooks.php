<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

if(!function_exists('woffice_todos_fetch')) {
    /**
     * We fetch the To-Dos using AJAX
     */
    function woffice_todos_fetch()
    {

        if(!check_ajax_referer('woffice_todos') || !isset($_POST['id'])) {
            echo json_encode(array('status' => 'fail'));
            die();
        }

        // We get the ID from the current Project post
        $the_ID = $_POST['id'];

        // We get the todos
        $project_todo_lists = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($the_ID, 'project_todo_lists') : '';

        // More check to add some extra data
        $post = get_post($the_ID);
        $allowed_modify = woffice_current_user_can_edit_project($post->ID);

        // We format our data
        foreach ($project_todo_lists as $key=>$todo) {
            $project_todo_lists[$key]['_can_check'] = woffice_current_user_can_check_task( $todo, $post, $allowed_modify);
            $project_todo_lists[$key]['_has_user_domain'] = function_exists('bp_core_get_user_domain');
            $project_todo_lists[$key]['_display_note'] = false;
            $project_todo_lists[$key]['_display_edit'] = false;
            if(isset($todo['date'])) {
                $project_todo_lists[$key]['_timestamp_date'] = strtotime($todo['date']);
                $project_todo_lists[$key]['_formatted_date'] = date_i18n(get_option('date_format'), strtotime($todo['date']));
            }
            if(!empty($todo['assigned']) && $todo['assigned'] != array('nope')) {
                $todo['assigned'] = (is_array($todo['assigned'])) ? $todo['assigned'] : explode(',',$todo['assigned']);
                $new_assigned = array();
                foreach($todo['assigned'] as $key2=>$assigned) {
                    $new_assigned[$key2]['_id'] = $assigned;
                    $new_assigned[$key2]['_avatar'] = get_avatar($assigned);
                    if (function_exists('bp_core_get_user_domain')) {
                        $new_assigned[$key2]['_profile_url'] = bp_core_get_user_domain($assigned);
                    }
                }
                $project_todo_lists[$key]['assigned'] = $new_assigned;
            }
        }

        // We return them through AJAX
        echo json_encode(array(
            'status' => 'success',
            'todos' => $project_todo_lists
        ));

        die();

    }
}
add_action('wp_ajax_nopriv_woffice_todos_fetch', 'woffice_todos_fetch');
add_action('wp_ajax_woffice_todos_fetch', 'woffice_todos_fetch');

if(!function_exists('woffice_todos_update')) {
    /**
     * We update the To-Dos using AJAX
     */
    function woffice_todos_update()
    {

        if(!check_ajax_referer('woffice_todos') || !isset($_POST['id']) || !isset($_POST['type'])) {
            echo json_encode(array('status' => 'fail'));
            die();
        }

        // We get the ID from the current Project post
        $id = intval($_POST['id']);

        // We get the type of update : add / delete / check / order / edit
        $type = $_POST['type'];

        // We get the todos
        $todos = (!isset($_POST['todos']) || empty($_POST['todos'])) ? array() : $_POST['todos'];

        $excluded_users_keys = array('-1', -1, 'NaN', 'No One');

        // We sanitize our data
        foreach ($todos as $key=>$todo) {
            foreach ($todo as $key2=>&$val) {

                // We re-format our assigned array
                $val = ( $val == 'false' ) ? false : $val;
                $val = ( $val == 'true' ) ? true : $val;

                if($key2 == 'assigned' && is_array($todo['assigned'])) {

                    $new_assigned = array();

                    foreach ($todo['assigned'] as $assigned) {

                        /*
                         * Each assigned is either an array if that's an old task:
                         * [6] => Array
                         *   (
                         *       [_id] => ...
                         *       [_avatar] => ....
                         *       [_profile_url] => ...
                         *   )
                         * OR if it's a new task OR an edit
                         * [6] => 7 // and integer sent by the select form
                         */
                        if(is_array($assigned) && !in_array($assigned['_id'], $excluded_users_keys)) {
                            $new_assigned[] = $assigned['_id'];
                        } elseif(!in_array($assigned,$excluded_users_keys)) {
                            $new_assigned[] = $assigned;
                        }

                    }

                    if(isset($todo['_is_new'])) {
                        woffice_projects_new_task_actions($id, $todo);
                    }

                    // We assign the users to the saved to-do
                    $todo['assigned'] = $new_assigned;

                }

                // We remove all the information related to the view, starting by "_"
                if(substr($key2, 0, 1) == '_')
                    unset($todo[$key2]);

            }
            $cleaned_todos[$key] = $todo;
        }

        // We get our extension instance
        $ext_instance = fw()->extensions->get('woffice-projects');

        // We update the meta
        $projects_assigned_email = woffice_get_settings_option('projects_assigned_email');
        if ($type == 'add' && $projects_assigned_email == "yep") {
            // We send email if needed
            $new_todos_email_checked = $ext_instance->woffice_projects_assigned_email($id, $cleaned_todos);
            // We update the meta finally
            $updated = $ext_instance->woffice_projects_update_postmeta($id, $new_todos_email_checked);
        } else {
            // Otherwise we just update the meta
            $updated = $ext_instance->woffice_projects_update_postmeta($id, $cleaned_todos);
        }

        // In case of an issue
        if($updated == false) {
            error_log(2);
            error_log(serialize($cleaned_todos));

            echo json_encode(array('status' => 'fail'));
            die();
        }

        /**
         * Whenever the todos are updated
         *
         * @param string $type
         * @param array $cleaned_todos
         * @param int $id
         */
        do_action('woffice_todo_update', $type, $cleaned_todos, $id);


        // We save the project
        global $post;
        do_action('save_post', $id, $post, true);

        // We return a success to let our user know
        echo json_encode(array(
            'status' => 'success'
        ));

        die();

    }
}
add_action('wp_ajax_nopriv_woffice_todos_update', 'woffice_todos_update');
add_action('wp_ajax_woffice_todos_update', 'woffice_todos_update');

if(!function_exists('woffice_project_assigned_user')) {
    /**
     * Send email to an user if he's assigned to an user
     *
     * @param $post_id int
     * @param $post WP_Post
     */
    function woffice_project_assigned_user($post_id, $post)
    {

        if (!empty($post)) {
            $ext_instance = fw()->extensions->get('woffice-projects');

            /* We only process if it's a project : */
            $slug = "project";
            if ($post->post_type != $slug) {
                return;
            }

            /* If this is just a revision, don't send the email. */
            if (wp_is_post_revision($post_id)) {
                return;
            }

            /* Only if the option is turned on */
            $projects_assigned_email = woffice_get_settings_option('projects_assigned_email');
            if ($projects_assigned_email == "yep") {

                /* We get all the todos */
                $project_todo_lists = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_todo_lists') : '';
                if (!empty($project_todo_lists)) {

                    /* We send email if needed */
                    $new_todos = $ext_instance->woffice_projects_assigned_email($post_id, $project_todo_lists);

                    /* We save the data in the postmeta*/
                    $ext_instance->woffice_projects_update_postmeta($post_id, $new_todos);

                }

            }
        }

    }
}
add_action('save_post','woffice_project_assigned_user', 100, 3 );

if(!function_exists('woffice_project_sync_events')) {
    /**
     * ADD Project to the calendar
     *
     * @param $post_id int
     * @param $post WP_Post
     */
    function woffice_project_sync_events($post_id, $post)
    {

        if (defined('DOING_AJAX') && DOING_AJAX) return;

        // We only process if it's a project :
        $slug = "project";
        if ($post->post_type != $slug)
            return;

        // If this is just a revision, don't go further.
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // We check if the option is turned on
        $project_calendar = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_calendar') : '';

        /*
         * Event Creation
         */
        if ($project_calendar && (class_exists('EventON') || defined( 'DP_PRO_EVENT_CALENDAR_VER' ))) {

            // We get the dates first
            $project_date_start = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_date_start') : '';
            $project_date_end = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_date_end') : '';

            // If not two date set we exit now
            if (empty($project_date_end) || empty($project_date_start))
                return;

            // Unix times
            $begin = strtotime($project_date_start);
            $end = strtotime($project_date_end);
            // We get the title
            $title = get_the_title($post_id);
            // We don't set the content for now
            //$content = get_the_excerpt($post_id);
            // We get the project's URL
            $url = get_permalink($post_id);
            // We get the project's color from the Theme Settings
            $color_colored = woffice_get_settings_option('color_colored');
            // We get the project's members
            $project_members = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post_id, 'project_members') : '';

            // We check if the event already exists
            global $wpdb;
            if(class_exists('EventON')) {
                $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'ajde_events'", $title);
            } else {
                $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'pec-events'", $title);
            }
            $check_exists = $wpdb->get_results($query);

            // If it does exist we exit
            if($check_exists)
                return;

            // We create the calendar event post array
            $post_information = array(
                'post_title' => wp_strip_all_tags($title),
                //'post_content' => $content,
                'post_status' => 'publish',
                'post_type' => (class_exists('EventON')) ? 'ajde_events' : 'pec-events',
            );

            // If the project has members (private) and it's not EventON we don't create it as we won't guarantee privacy
            if(!empty($project_members) && defined( 'DP_PRO_EVENT_CALENDAR_VER' )) {
                $calendar_event_id = 0;
            } else {
                $calendar_event_id = wp_insert_post($post_information);
            }
            // If it doesn't fail, we add some meta
            if ($calendar_event_id != 0) {

                if(class_exists('EventON')) {

                    /*We add the post meta - http://www.myeventon.com/documentation/event-post-meta-variables/*/
                    add_post_meta($calendar_event_id, 'evcal_srow', $begin);
                    add_post_meta($calendar_event_id, 'evcal_erow', $end);
                    add_post_meta($calendar_event_id, 'evcal_event_color', $color_colored);
                    add_post_meta($calendar_event_id, 'evcal_allday', 'yes');
                    add_post_meta($calendar_event_id, 'evcal_lmlink', $url);

                    /*We add the taxonomy*/
                    $eventON_catgeory_object = get_term_by('slug', 'Projects', 'event_type');
                    if ($eventON_catgeory_object != false) {
                        $value_set = wp_set_post_terms($calendar_event_id, array($eventON_catgeory_object->term_id), 'event_type');
                    }

                    /* We add the users */
                    if (!empty($project_members)) {
                        $tagged = wp_set_object_terms($calendar_event_id, $project_members, 'event_users');
                    }

                } else {

                    add_post_meta($calendar_event_id, 'pec_date', date('Y-m-d',$begin));
                    add_post_meta($calendar_event_id, 'pec_end_date', date('Y-m-d',$end));

                }

            }

        }
        /*
         * If not activated we check for an event with the same title and we delete it
         * Only EventON for now, can be improved with DP Pro Event support later
         */
        else if (!$project_calendar) {

            /* We check if the calendar event already exists */
            $title = get_the_title($post_id);
            global $wpdb;
            $query = $wpdb->prepare(
                'SELECT ID FROM ' . $wpdb->posts . '
				WHERE post_title = %s
				AND post_type = \'ajde_events\'',
                $title
            );
            $check_exists = $wpdb->get_results($query);

            /*If exist delete it*/
            if ($check_exists) {
                wp_delete_post($check_exists[0]->ID);
            }

        }

    }
}
add_action('save_post','woffice_project_sync_events', 100, 2 );

if(!function_exists('woffice_groups_create_new_categories')) {
    /**
     * BuddyPress create a new category for each Group
     *
     * @param $group_id int
     */
    function woffice_groups_create_new_categories($group_id)
    {

        // We fetch the option :
        $projects_groups = woffice_get_settings_option('projects_groups');

        if ($projects_groups == "yep") {
            // We get all the groups :
            if (function_exists('bp_is_active') && bp_is_active('groups')) {

                // Get all groups
                $groups = groups_get_groups(array('show_hidden' => true));

                foreach ($groups['groups'] as $group) {
                    // we check if there is already a ctageory with the group's name
                    $term = term_exists($group->name, 'project-category');
                    // If it doesn't exist then create it
                    if ($term == 0 || $term == null) {
                        wp_insert_term($group->name, 'project-category');
                    }
                }

            }
        }

    }
}
add_action('groups_group_create_complete','woffice_groups_create_new_categories');
add_action('fw_settings_form_saved','woffice_groups_create_new_categories');

if(!function_exists('woffice_groups_sync_members')) {
    /**
     * BuddyPress add all members to the project whenever a post is saved
     *
     * @param $post_id
     * @param  $post
     */
    function woffice_groups_sync_members($post_id, $post) {

        // We check if it's a project being saved
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        if ($post->post_type != "project") return;
        if (wp_is_post_revision($post_id)) return;
        if (!function_exists('bp_is_active')) return;
        if (!bp_is_active('groups')) return;

        // We fetch the option :
        $projects_groups = woffice_get_settings_option('projects_groups');
        if ($projects_groups == "yep") {

            // we check for each group if it's a term name :
            $groups = groups_get_groups(array('show_hidden' => true));
            foreach ($groups['groups'] as $group) {
                // If it has the term and it's a buddypress group name
                if (has_term($group->name, 'project-category', $post_id)) {

                    // We create an array :
                    $array_members = array();
                    // we get the members
                    $group_members = groups_get_group_members(array('group_id' => $group->id));
                    if (!empty($group_members)) {
                        foreach ($group_members['members'] as $member) {
                            $array_members[] = $member->ID;
                        }
                    }
                    // we get the admins
                    $group_admins = groups_get_group_admins($group->id);
                    if (!empty($group_admins)) {
                        foreach ($group_admins as $admins) {
                            $array_members[] = $admins->user_id;
                        }
                    }
                    // we get the mods
                    $group_mods = groups_get_group_mods($group->id);
                    if (!empty($group_mods)) {
                        foreach ($group_mods as $mods) {
                            $array_members[] = $mods->user_id;
                        }
                    }

                    // We update the option :
                    if (!empty($array_members)) {

                        // Get the metas :
                        $project_data = get_post_meta($post_id, 'fw_options', true);
                        $new_project_data = $project_data;
                        $new_project_data['project_members'] = $array_members;
                        update_post_meta($post_id, 'fw_options', $new_project_data);
                        //fw_set_db_post_option($post_id, 'project_members', $array_members);

                    }

                    // We exit the loop
                    break;
                }
            }

        }
    }
}
add_action('save_post','woffice_groups_sync_members', 11, 3 );
add_action('woffice_after_frontend_process','woffice_groups_sync_members', 10, 2 );

if(!function_exists('woffice_register_project_notification')) {
    /**
     * Register project notifications
     */
    function woffice_register_project_notification() {

        // Register component manually into buddypress() singleton
        buddypress()->woffice_project = new stdClass;
        // Add notification callback function
        buddypress()->woffice_project->notification_callback = 'woffice_project_format_notifications';

        // Now register components into active components array
        buddypress()->active_components['woffice_project'] = 1;

    }
}
add_action( 'bp_setup_globals', 'woffice_register_project_notification' );

if(!function_exists('woffice_clear_project_notifications')) {
    /**
     * Clear project notifications
     */
    function woffice_clear_project_notifications() {

        // One check for speed optimization
        if ( is_singular( 'project' ) ) {

            if (is_user_logged_in() && function_exists('bp_is_active') && bp_is_active('notifications')) {

                global $post;
                $current_user_id = get_current_user_id();

                if ($post->post_author == $current_user_id) {
                    bp_notifications_mark_notifications_by_item_id($current_user_id, $post->ID, 'woffice_project', 'Woffice_project_comment', false, 0);
                }

                bp_notifications_mark_notifications_by_item_id($current_user_id, $post->ID, 'woffice_project', 'woffice_project_assigned_todo', false, 0);

                bp_notifications_mark_notifications_by_item_id($current_user_id, $post->ID, 'woffice_project', 'woffice_project_assigned_member', false, 0);

            }

        }

    }
}
add_action('wp', 'woffice_clear_project_notifications');

if(!function_exists('woffice_project_notification_members_added')) {
    /**
     * Add BuddyPress notification for the Project, whenever a member is added
     *
     * @param $post_id int
     * @param $post WP_Post
     */
    function woffice_project_notification_members_added($post_id, $post) {

        if ( $post->post_type != 'project' || ! Woffice_Notification_Handler::is_notification_enabled('project-member-assigned')) {
            return;
        }

        // Assigned members
        $members_assigned = fw_get_db_post_option($post_id, 'project_members');

        foreach ($members_assigned as $member_id) {
            bp_notifications_add_notification( array(
                'user_id'           => $member_id,
                'item_id'           => $post_id,
                'secondary_item_id' => get_current_user_id(),
                'component_name'    => 'woffice_project',
                'component_action'  => 'woffice_project_assigned_member',
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
            ) );
        }

    }
}
add_action('woffice_frontend_process_completed_success', 'woffice_project_notification_members_added', 10, 2);

if(!function_exists('woffice_add_activity_stream_for_project_creation')) {
    /**
     * Add BuddyPress activity for the Project
     *
     * @param $post_id int
     * @param $post WP_Post
     */
    function woffice_add_activity_stream_for_project_creation($post_id, $post) {

        if ( $post->post_type != 'project' || !Woffice_Activity_Handler::is_activity_enabled('project-creation')) {
            return;
        }

        // Current user ID
        $current_user_id = get_current_user_id();

        if ($current_user_id != 0) {
            $activity_args = array(
                'action' => '<a href="'.bp_loggedin_user_domain().'">'.woffice_get_name_to_display($current_user_id).'</a> '.__('created the project ','woffice').' <a href="'.get_the_permalink($post_id).'">'.get_the_title($post_id).'</a>',
                //'content' => $post->post_title,
                'component' => 'project',
                'type' => 'project-creation',
                'item_id' => $post_id,
                'user_id' => $current_user_id,
                //'hide_sitewide' => true
            );
            bp_activity_add( $activity_args );
        }

    }
}
add_action('woffice_frontend_process_completed_success', 'woffice_add_activity_stream_for_project_creation', 10, 2);

if(!function_exists('woffice_add_activity_stream_for_project_editing')) {
    /**
     * New notification whenever a project is edited
     *
     * @param $post WP_Post
     */
    function woffice_add_activity_stream_for_project_editing($post) {

        if ( !Woffice_Activity_Handler::is_activity_enabled('project-creation') && !function_exists('bp_activity_add')) {
            return;
        }

        // Current user ID
        $current_user_id = get_current_user_id();

        if ($current_user_id != 0) {
            $activity_args = array(
                'action' => '<a href="'.bp_loggedin_user_domain().'">'.woffice_get_name_to_display($current_user_id).'</a> '.__('edited the project ','woffice').' <a href="'.get_the_permalink($post->ID).'">'.get_the_title($post->ID).'</a>',
                //'content' => $post->post_title,
                'component' => 'project',
                'type' => 'project-editing',
                'item_id' => $post->ID,
                'user_id' => $current_user_id,
                //'hide_sitewide' => true
            );
            bp_activity_add( $activity_args );
        }

    }
}
add_action('woffice_after_project_editing', 'woffice_add_activity_stream_for_project_editing');

if(!function_exists('woffice_add_title_as_mv_category')) {
    /**
     * Set Multiverso categories for the project
     *
     * @param $postid
     */
    function woffice_add_title_as_mv_category($postid)
    {
        if (!class_exists('multiverso_mv_category_files') || defined('fileaway')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        $post = get_post($postid);
        if ($post->post_type == 'project') {
            $term = get_term_by('slug', $post->post_name, 'multiverso-categories');
            if (empty($term)) {
                $add = wp_insert_term($post->post_title, 'multiverso-categories', array('slug' => $post->post_name));
                if (is_array($add) && isset($add['term_id'])) {
                    wp_set_object_terms($postid, $add['term_id'], 'multiverso-categories', true);
                }
            }
        }
    }
}
add_action('save_post', 'woffice_add_title_as_mv_category');

if(!function_exists('woffice_members_suggestion_autocomplete')) {
    /**
     * AJAX handler for project member autocomplete requests.
     *
     */
    function woffice_members_suggestion_autocomplete() {

        // Fail it's a large network.
        if ( is_multisite() && wp_is_large_network( 'users' ) ) {
            wp_die( - 1 );
        }

        $term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';

        /**
         * Filter the members ids included in the members suggestion (in project assignation)
         *
         * @param array
         */
        $include = apply_filters( 'woffice_members_suggestion_include', array());

        /**
         * Filter the members ids excluded from the members suggestion (in project assignation)
         *
         * @param array
         */
        $exclude = apply_filters( 'woffice_members_suggestion_exclude', array());

        if ( ! $term ) {
            wp_die( - 1 );
        }

        $user_fields = array( 'ID' );

        //TODO remove users already added?
        $users       = new \WP_User_Query( array(
            'fields' => $user_fields,
            'search'         => "*{$term}*",
            'search_columns' => array(
                'user_login',
                'user_nicename',
                'user_email',
            ),
            'include' => $include,
            'exclude' => $exclude
        ) );

        $users_found_1 = $users->get_results();

        $users       = new \WP_User_Query( array(
            'fields' => $user_fields,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'first_name',
                    'value'   => esc_attr( $term ),
                    'compare' => 'LIKE'
                ),
                array(
                    'key'     => 'last_name',
                    'value'   => esc_attr( $term ),
                    'compare' => 'LIKE'
                ),
            ),
            'include' => $include,
            'exclude' => $exclude

        ) );
        $users_found_2 = $users->get_results();

        $users_found = array_unique( array_merge($users_found_1, $users_found_2), SORT_REGULAR );

        $matches = array();

        if ( $users_found && ! is_wp_error( $users_found ) ) {
            foreach ( $users_found as $user ) {

                if( function_exists( 'bp_is_user_active' ) && !bp_is_user_active($user->ID) )
                    continue;

                $matches[] = array(
                    'label' => woffice_get_name_to_display($user->ID),
                    'value' => $user->ID,
                );
            }
        }

        wp_die( json_encode( $matches ) );
    }
}
add_action( 'wp_ajax_woffice_members_suggestion_autocomplete', 'woffice_members_suggestion_autocomplete'  );
