<?php
/**
 * Class Woffice_Activity_Handler
 *
 * Handle BuddyPress activity streams and how Woffice deal with the BuddyPress activity items
 *
 * @since 2.1.3
 * @author Alkaweb
 */
if( ! class_exists( 'Woffice_Activity_Handler' ) ) {
	class Woffice_Activity_Handler {

		public function __construct() {

			add_action( 'comment_post', array($this, 'on_comment_publishing') , 10, 2 );
			add_action('transition_comment_status', array( $this, 'on_comment_approve') ,10,3);
		}

		/**
		 * Triggered when a comment is published directly, without waiting for approval
		 *
		 * @param $comment_ID
		 * @param $comment_approved
		 */
		public function on_comment_publishing( $comment_ID, $comment_approved ) {
			if( 1 === $comment_approved ){
				$comment_object = get_comment($comment_ID);
				$this->publish_comment_activity($comment_object);


			}
		}

		/**
		 * Triggered when a comment was pending and now is approved
		 *
		 * @param $new_status
		 * @param $old_status
		 * @param $comment_object
		 */
		public function on_comment_approve( $new_status, $old_status, $comment_object ) {
			if( $old_status != $new_status && $new_status == 'approved' ){
				$this->publish_comment_activity($comment_object);
			}
		}

		public function publish_comment_activity($comment_object){

			if(!woffice_is_buddypres_component_active( 'activity' ))
				return false;

			$post_type = get_post_type($comment_object->comment_post_ID);
			$comment_author = $comment_object->user_id;
			$post_object = get_post($comment_object->comment_post_ID);

			switch($post_type) {
				case 'project':
					$action = '<a href="' . bp_loggedin_user_domain() . '">' . woffice_get_name_to_display( $comment_author ) . '</a> ' . __( 'added a comment to the project ', 'woffice' ) . ' <a href="' . get_the_permalink( $post_object->ID ) . '">' . get_the_title( $post_object->ID ) . '</a>';
					$label = 'project';
					break;
				case 'wiki':
					$action = '<a href="' . bp_loggedin_user_domain() . '">' . woffice_get_name_to_display( $comment_author ) . '</a> ' . __( 'commented the wiki post ', 'woffice' ) . ' <a href="' . get_the_permalink( $post_object->ID ) . '">' . get_the_title( $post_object->ID ) . '</a>';
					$label = 'wiki';
					break;
				case 'post':
					$action = '<a href="' . bp_loggedin_user_domain() . '">' . woffice_get_name_to_display( $comment_author ) . '</a> ' . __( 'commented the post ', 'woffice' ) . ' <a href="' . get_the_permalink( $post_object->ID ) . '">' . get_the_title( $post_object->ID ) . '</a>';
					$label = 'blog';
					break;
				default:
					return false;
			}

			if(!self::is_activity_enabled($label . '-comment') || $comment_author == 0)
				return false;

			$activity_args = array(
				'action'    => $action,
				'content' => $comment_object->comment_content ,
				'component' => $label,
				'type'      => $label . '-comment',
				'item_id' => $post_object->ID,
				'user_id'   => $comment_author,
				//'hide_sitewide' => true
			);
			bp_activity_add( $activity_args );

		}

		/**
		 * Check if a given activity stream is active
		 *
		 * @param $activity
		 * @return bool
		 * @throws Exception
		 */
		public static function is_activity_enabled($activity) {

			if(! woffice_is_buddypres_component_active( 'activity' ) )
				return false;

			switch($activity) {
				case 'wiki-like':
					return (! ( defined( 'WOFFICE_DISABLE_ACTIVITY_STREAM_WIKI_LIKE' ) && ( true == WOFFICE_DISABLE_ACTIVITY_STREAM_WIKI_LIKE ) ) );
				case 'wiki-comment':
					return (! ( defined( 'WOFFICE_DISABLE_ACTIVITY_STREAM_WIKI_COMMENT' ) && ( true == WOFFICE_DISABLE_ACTIVITY_STREAM_WIKI_COMMENT ) ) );
				case 'project-comment':
					return (! ( defined( 'WOFFICE_DISABLE_ACTIVITY_STREAM_PROJECT_COMMENT' ) && ( true == WOFFICE_DISABLE_ACTIVITY_STREAM_PROJECT_COMMENT ) ) );
				case 'project-creation':
					return (! ( defined( 'WOFFICE_DISABLE_ACTIVITY_STREAM_PROJECT_CREATION' ) && ( true == WOFFICE_DISABLE_ACTIVITY_STREAM_PROJECT_CREATION ) ) );
				case 'blog-like':
					return (! ( defined( 'WOFFICE_DISABLE_ACTIVITY_STREAM_BLOG_LIKE' ) && ( true == WOFFICE_DISABLE_ACTIVITY_STREAM_BLOG_LIKE ) ) );
				case 'blog-comment':
					return (! ( defined( 'WOFFICE_DISABLE_ACTIVITY_STREAM_BLOG_COMMENT' ) && ( true == WOFFICE_DISABLE_ACTIVITY_STREAM_BLOG_COMMENT ) ) );
				default:
					throw new Exception('Notification type unknown');
			}

		}

	}

	new Woffice_Activity_Handler();
}