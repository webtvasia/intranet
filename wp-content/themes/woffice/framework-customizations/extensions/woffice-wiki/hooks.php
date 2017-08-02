<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/*---------------------------------------------------------
** 
** LIKE BUTTON JQUERY
**
----------------------------------------------------------*/	
function woffice_wiki_buttons_js(){
	
	if (is_singular("wiki")){
		/*Ajax URL*/
		$ajax_url = admin_url('admin-ajax.php');
		/*Ajax Nonce*/
		$ajax_nonce = wp_create_nonce('ajax-nonce');
		
		echo'<script type="text/javascript">
			jQuery(function () {
			
				jQuery(".wiki-like a").click(function(){
	     
			        like = jQuery(this);
			        post_id = like.data("post_id");
			         
			        // Ajax call
			        jQuery.ajax({
			            type: "post",
			            url: "'.$ajax_url.'",
			            data: "action=post-like&nonce='.$ajax_nonce.'&post_like=&post_id="+post_id,
			            success: function(count){
			                if(count != "already")
			                {
			                    like.closest(".wiki-like").addClass("voted");
			                    like.siblings(".count").text(count);
			                }
			            }
			        });
			         
			        return false;
			        
			    });
			
			});
		</script>';
	}
	
}
add_action('wp_footer', 'woffice_wiki_buttons_js');

/*---------------------------------------------------------
** 
** LIKE BUTTON FUNCTION
**
----------------------------------------------------------*/	
function post_like(){
	
	$ext_instance = fw()->extensions->get( 'woffice-wiki' );
	
    // Check for nonce security
    $nonce = $_POST['nonce'];
  
    if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
        die ( 'Busted!');
     
    if(isset($_POST['post_like']))
    {
        // Retrieve user IP address
        $ip = $_SERVER['REMOTE_ADDR'];
        $post_id = intval($_POST['post_id']);

        // We get the Wiki Likes "Engine" :
        $like_engine = woffice_get_settings_option('like_engine');
        if($like_engine == 'members') {



        } else {

            // Get voters'IPs for the current post
            $meta_IP = get_post_meta($post_id, "voted_IP");
            $voted_IP = (empty($meta_IP)) ? 0 : $meta_IP[0];
            if(!is_array($voted_IP))
                $voted_IP = array();

        }

        // Get votes count for the current post
        $meta_count = get_post_meta($post_id, "votes_count", true);

        // Use has already voted ?
        if(!$ext_instance->woffice_user_has_already_voted($post_id))
        {

            if($like_engine == 'members') {

                $voted_IDs = get_post_meta($post_id, "voted_IDs", true);
                $user_id = (is_user_logged_in()) ? get_current_user_id() : 0;
                if(empty($voted_IDs))
                    $voted_IDs = array();
                $voted_IDs[$user_id] = $user_id;
                // Save it :
                update_post_meta($post_id, "voted_IDs", $voted_IDs);

            } else {
                $voted_IP[$ip] = time();
                // Save IP and increase votes count
                update_post_meta($post_id, "voted_IP", $voted_IP);
            }

            update_post_meta($post_id, "votes_count", ++$meta_count);
             
            // Display count (ie jQuery return value)
            echo $meta_count;

	        //Add notification
	        if(Woffice_Notification_Handler::is_notification_enabled('wiki-like')) {
		        $post_object = get_post($post_id);

		        bp_notifications_add_notification( array(
			        'user_id'           => $post_object->post_author,
			        'item_id'           => $post_id,
			        'secondary_item_id' => get_current_user_id(),
			        'component_name'    => 'woffice_wiki',
			        'component_action'  => 'woffice_wiki_like',
			        'date_notified'     => bp_core_current_time(),
			        'is_new'            => 1,
		        ) );
	        }

	        $current_user_id = get_current_user_id();

	        if($current_user_id != 0 && Woffice_Activity_Handler::is_activity_enabled('wiki-like')) {
		        $activity_args = array(
			        'action' => '<a href="'.bp_loggedin_user_domain().'">'.woffice_get_name_to_display($current_user_id).'</a> '.__('liked the wiki post ','woffice').' <a href="'.get_the_permalink($post_id).'">'.get_the_title($post_id).'</a>',
			        //'content' => $post->post_title,
			        'component' => 'wiki',
			        'type' => 'wiki-like',
			        'item_id' => $post_id,
			        'user_id' => $current_user_id,
			        //'hide_sitewide' => true
		        );
		        bp_activity_add( $activity_args );
	        }


        }
        else
            echo "already";
    }
    exit;
}
add_action('wp_ajax_nopriv_post-like', 'post_like');
add_action('wp_ajax_post-like', 'post_like');


/*---------------------------------------------------------
**
** LIKE BUTTON FUNCTION
**
----------------------------------------------------------*/


add_action( 'bp_setup_globals', 'woffice_register_wiki_notification' );

if(!function_exists('woffice_register_wiki_notification')) {
	function woffice_register_wiki_notification() {
		// Register component manually into buddypress() singleton
		buddypress()->woffice_wiki = new stdClass;
		// Add notification callback function
		buddypress()->woffice_wiki->notification_callback = 'woffice_wiki_format_notifications';

		// Now register components into active components array
		buddypress()->active_components['woffice_wiki'] = 1;
	}
};

if(!function_exists('woffice_wiki_format_notifications')) {
	function woffice_wiki_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

		if ( ! ('woffice_wiki_like' === $action || 'woffice_wiki_comment' === $action)) {
			return $action;
		}

		$post_title = get_the_title( $item_id );

		if ('woffice_wiki_like' === $action) {
			$custom_title = sprintf( esc_html__( 'New like received', 'woffice' ), $post_title );
			$custom_link  = get_permalink( $item_id );
			if ( (int) $total_items > 1 ) {
				$custom_text  = sprintf( esc_html__( 'You received "%1$s" new likes on wiki articles', 'woffice' ), $total_items );
				$custom_link = bp_get_notifications_permalink();
			} else {
				$custom_text  = sprintf( esc_html__( 'Your post "%1$s" received a like', 'woffice' ), $post_title );
			}

		}

		if ('woffice_wiki_comment' === $action) {
			$custom_title = sprintf( esc_html__( 'New comment received', 'woffice' ), $post_title );
			$custom_link  = get_permalink( $item_id );
			if ( (int) $total_items > 1 ) {
				$custom_text  = sprintf( esc_html__( 'You received "%1$s" new comments on wiki articles', 'woffice' ), $total_items );
				$custom_link = bp_get_notifications_permalink();
			} else {
				$custom_text  = sprintf( esc_html__( 'Your wiki "%1$s" received a new comment', 'woffice' ), $post_title );
			}

		}

		

		// WordPress Toolbar
		if ( 'string' === $format ) {
			$message = (!empty($custom_link)) ? '<a href="' . esc_url( $custom_link ) . '" title="' . esc_attr( $custom_title ) . '">' . esc_html( $custom_text ) . '</a>' : $custom_text;
			$return = apply_filters( 'woffice_wiki_like_format', $message, $custom_text, $custom_link );

			// Deprecated BuddyBar
		} else {
			$return = apply_filters( 'woffice_wiki_like_format', array(
				'text' => $custom_text,
				'link' => $custom_link
			), $custom_link, (int) $total_items, $custom_text, $custom_title );
		}

		return $return;

	}
}

if(!function_exists('woffice_clear_wiki_like_notification')) {
	function woffice_clear_wiki_like_notification() {
		if ( is_singular( 'wiki' ) && is_user_logged_in() && function_exists('bp_is_active') && bp_is_active( 'notifications' )  ) {
			global $post;
			$current_user_id = get_current_user_id();
			if ( $post->post_author == $current_user_id ) {
				bp_notifications_mark_notifications_by_item_id( $current_user_id, $post->ID, 'woffice_wiki', 'Woffice_wiki_like', false, 0 );
				bp_notifications_mark_notifications_by_item_id( $current_user_id, $post->ID, 'woffice_wiki', 'Woffice_wiki_comment', false, 0 );
			}
		}

	}
}
add_action('wp', 'woffice_clear_wiki_like_notification');

/**
 * Remove all the restricted wiki from the wiki directory page
 *
 * @param WP_Query $query
 *
 * @return mixed
 */
function remove_restricted_wiki_from_the_list($query)
{

	if (!(isset($query->query_vars['woffice_check_wiki_permission']) && $query->query_vars['woffice_check_wiki_permission']))
		return $query;

	//var_dump(!(isset($query->query_vars['woffice_check_wiki_permission']) && $query->query_vars['woffice_check_wiki_permission']));
	//var_dump($query);

	$new_args = $query->query_vars;
	unset($new_args['woffice_check_wiki_permission']);
	$my_query = new WP_Query($new_args);

	$excluded_posts = array();

	while ( $my_query->have_posts() ) : $my_query->the_post();
		if(!woffice_is_user_allowed_wiki(get_the_ID())) {
			array_push( $excluded_posts, get_the_ID() );
		}
	endwhile;


	wp_reset_postdata();

	//If not exclude it from the real query call
	$query->set('post__not_in', $excluded_posts);


	return $query;

}
add_filter('pre_get_posts', 'remove_restricted_wiki_from_the_list');