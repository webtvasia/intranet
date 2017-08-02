<?php

global $process_result;

$project_date_start = woffice_get_post_option( $post->ID, 'project_date_start');
$project_date_end = woffice_get_post_option( $post->ID, 'project_date_end');

// GET THE TERMS
$project_terms = get_the_term_list( $post->ID, 'project-category', '', ', ' );

// GET MEMBERS
$project_members = woffice_get_project_members( $post->ID );
// GET THE LINKS
$project_links = woffice_get_post_option( $post->ID, 'project_links');

$is_completed = (bool)woffice_get_post_option( $post->ID, 'project_completed', false);

$project_edit = woffice_get_post_option( $post->ID, 'project_edit');

$post_classes = array('box','content');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
	<?php if ( has_post_thumbnail()) : ?>
		<!-- THUMBNAIL IMAGE -->
		<?php Woffice_Frontend::render_featured_image_single_post($post->ID) ?>

	<?php endif; ?>
	<div id="project-nav" class="intern-box">
		<div class="item-list-tabs-project">
			<?php
			if (function_exists('woffice_get_project_menu')){
				echo woffice_get_project_menu($post);
			}
			?>
		</div>
	</div>


	<!-- DISPLAY ALL THE CONTENT OF THE project ARTICLE-->
	<div id="project-content-view">

		<header id="project-meta">

			<?php //GET THE PROGRESS BAR




			if (!empty($project_date_start) && !empty($project_date_end))
				woffice_project_progressbar();

            ?>

			<div class="row">

				<?php // GET THE PROJECT DATES
				// CLASS FOR THE COLUMN
				/*if( ( empty($project_date_start) && empty($project_terms) ) || ( empty($project_members) || empty($project_links ) ) ):
					$column_class = 6;
				elseif( ( empty($project_date_start) && empty($project_terms) && empty($project_members) ) || 
				( empty($project_date_start) && empty($project_terms) && empty($project_links) )):
					$column_class = 12;
				else : 
					$column_class = 4;
				endif;*/
				?>

				<?php
        /**
         * Before all the project meta columns, in the single project page
         */
        do_action('woffice_before_project_meta'); ?>

				<?php if (!empty($project_terms) || !empty($project_date_start) || !empty($project_date_end) || $is_completed): ?>

					<div class="col-md-4">
						<ul class="project-meta-list">

							<?php
							if( $is_completed ) {
								echo '<li class="project-meta-completed">';
								echo esc_html__( 'Completed', 'woffice' );
								echo '</li>';
							}
							?>

							<?php if(!empty($project_date_start) || !empty($project_date_end)): ?>
								<li class="project-meta-date">
									<?php
									if( !empty($project_date_start) )
										echo date_i18n(get_option('date_format'),strtotime(esc_html($project_date_start)));

									if( !empty($project_date_start) && !empty($project_date_end))
										echo ' - ';

									if( !empty($project_date_end) )
										echo date_i18n(get_option('date_format'),strtotime(esc_html($project_date_end))); ?>
								</li>
							<?php endif; ?>

							<?php if (!empty($project_terms)): ?>
								<li class="project-meta-category">
									<?php echo get_the_term_list( $post->ID, 'project-category', '', ', ' ); ?>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif;

				if (!empty($project_members)): ?>
					<div class="col-md-4">
						<ul class="project-meta-list">
							<li class="project-meta-users"><?php _e("Project's Members","woffice"); ?></li>
						</ul>
						<div class="project-members">
							<?php
                            /**
                             * In the single project page, this is performed before the list of project members
                             */
							do_action('woffice_before_project_meta_members');

							foreach($project_members as $project_member) {
								if (function_exists('bp_is_active')):
									$user_info = get_userdata($project_member);
									if (!empty($user_info->display_name)){
										$name = woffice_get_name_to_display($user_info);
										echo'<a href="'. esc_url(bp_core_get_user_domain($project_member)) .'" title="'. $name .'" data-toggle="tooltip" data-placement="top">';
										echo get_avatar($project_member);
										echo'</a>';
									}
									else {
										echo'<a href="'. esc_url(bp_core_get_user_domain($project_member)) .'">';
										echo get_avatar($project_member);
										echo'</a>';
									}
								else :
									echo get_avatar($project_member);
								endif;
							}

                          /**
                           * In the single project page, this is performed after the list of project members
                           */
							do_action('woffice_after_project_meta_members');
							?>
						</div>
					</div>
				<?php endif; ?>





				<?php if (!empty($project_links)): ?>
					<div class="col-md-4">
						<ul class="project-meta-list">
							<li  class="project-meta-links"><?php _e("Project's Links","woffice"); ?></li>
						</ul>

						<?php
                        /**
                         * In the single project page, this is performed before the list of project links
                         */
                        do_action('woffice_before_project_meta_links'); ?>

						<ul id="project-links">
							<?php
							foreach($project_links as $project_link){
								echo'<li><a href="'.esc_url($project_link['href']).'" target="_blank">';
								echo'<i class="'.esc_attr($project_link['icon']).'"></i> '.esc_html($project_link['title']);
								echo'</a></li>';
							}
							?>
						</ul>

						<?php
                        /**
                         * In the single project page, this is performed after the list of project links
                         */
                        do_action('woffice_after_project_meta_links'); ?>

					</div>
				<?php endif; ?>

				<?php
                /**
                 * After all the project meta columns, in the single project page
                 */
                do_action('woffice_after_project_meta'); ?>

			</div>
		</header>

		<div class="intern-padding">
			<?php // THE CONTENT


                    /* Delete Link */
                    $user = wp_get_current_user();
            	    $user_can_delete = ($post->post_author == $user->ID || $current_user_is_admin);

                    /**
                     * Filter if the user can delete a project
                     *
                     * @param bool $user_can_delete If the user can delete or not the project
                     * @param WP_Post $post The project post
                     * @param WP_user $user The user object
                     *
                     */
            	    $user_can_delete = apply_filters( 'woffice_user_can_delete_project', $user_can_delete, $post, $user);
            $html .= '<ul style="list-style-type: none;display: inline; ">';
                    if ( $user_can_delete ) :
                        $html .= '<li id="project-tab-delete" style="padding: 1px 2px 3px;float: right;">
            				<a onclick="return confirm(\'' . __('Are you sure you wish to delete project :', 'woffice') . ' ' . get_the_title() . ' ?\')" href="' . get_delete_post_link(get_the_ID(), '') . '" class="">
            					' . __("Delete", "woffice") . '
            				</a>
            			</li>';
                    endif;
            /* Edit Link */
            $project_edit = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option(get_the_ID(), 'project_edit') : '';
            if ($project_edit != 'no-edit' && is_user_logged_in()) :
                $user_can_edit = woffice_current_user_can_edit_project(get_the_ID());

                if($user_can_edit) {
                    $html .= '<li id="project-tab-edit" style="padding: 1px 2px 3px;float: right;">';
                    if ($project_edit == 'frontend-edit'):
                        $html .= '<a href="#project-content-edit" class="">' . __("Edit", "woffice") . '</a>';
                    else :
                        $html .= '<a href="' . get_edit_post_link($post->ID) . '" class="fa-pencil-square-o">' . __("Quick Edit", "woffice") . '</a>';
                    endif;
                    $html .= '</li>';


                }
            endif;



            $html .= '</ul>';
            echo $html;

			the_content(); ?>
		</div>
	</div>

	<div class="project-tabs-wrapper intern-padding">

<!--		--><?php //if ( $project_edit == 'frontend-edit' && woffice_current_user_can_edit_project(get_the_ID()) ) : ?>
			<!-- EDIT THE CONTENT IN FRONTEND VIEW-->
			<div id="project-content-edit">
				<?php Woffice_Frontend::frontend_render('project', $process_result, get_the_ID()); ?>
			</div>
<!--		--><?php //endif; ?>

		<?php $project_todo = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_todo') : '';
		if($project_todo): ?>
			<!-- SEE THE TO-DO LIST-->
			<div id="project-content-todo">
				<?php // IF THERE IS A WUNDERLIST LINK
				$project_wunderlist = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_wunderlist') : '';
				if(!empty($project_wunderlist)): ?>

					<iframe src="https://www.wunderlist.com/lists/<?php echo $project_wunderlist; ?>"; width="100%" height="600"></iframe>

				<?php else: ?>

					<?php woffice_projects_todo($post); ?>

				<?php endif; ?>

			</div>
		<?php endif; ?>

		<!-- SEE THE FILES-->
		<div id="project-content-files">

            <a href="http://localhost:8888/google-drive-sdk-api-php-insert-file-parent-example/" target="ugdl" >Connect Intranet to Google Drive.</a>



            <form enctype="multipart/form-data" action="/google-drive-sdk-api-php-insert-file-parent-example/formAction.php" method="POST">

                    <input type="file" name="file" required>

                    <input type="hidden"  name="folderName" value="<?php echo get_the_title(get_the_ID()); ?>">

                    <input type="hidden" name="folderDesc" placeholder="Pictures of my cat">

                    <input style="float: right" type="submit" name="submit" value="Upload to Drive">
                </form>
<!---->
<!--            <iframe id="ugdl" src="http://localhost:8888/google-drive-sdk-api-php-insert-file-parent-example/" width="700px" height="50px" frameborder="0"></iframe>-->

            <a onclick="reloadgdl();" style="float: right"><img src="<?php echo get_template_directory_uri(); ?>/images/reload.png" style="cursor: pointer;height: 20px;width: 20px"></a>

<!--            <button onclick="reloadgdl();">RELOAD</button>-->
            <iframe id="gdl" src="https://drive.google.com/embeddedfolderview?id=0B1pkGI56XtXYTTFIM1NoU1FKR1E#list" width="700" height="500" frameborder="0"></iframe>
<script type="text/javascript">
function reloadgdl() {
    $( '#gdl' ).attr( 'src', function ( i, val ) { return val; });
}

</script>


            <!--			--><?php
//			//Subdir fix
//			if(isset($_GET['drawer']) && strpos('projects_', $_GET['drawer']) === FALSE ) { ?>
<!--				<script>-->
<!--					(function($){-->
<!--						if(!window.location.hash) {-->
<!--							location.href = window.location.href + '#project-content-files';-->
<!--						}-->
<!--					})(jQuery);-->
<!--				</script>-->
<!--				--><?php
//			}
//			?>
<!--			--><?php //// IF THERE IS FILES
//			$project_files = ( function_exists( 'fw_get_db_post_option' ) && defined('fileaway')) ? fw_get_db_post_option(get_the_ID(), 'project_files') : '';
//			if(!empty($project_files)):
//
//				if (defined('fileaway')):
//					$post_slug = $post->post_name;
//					woffice_projects_fileway_manager($post_slug);
//				else :
//					$post_slug = $post->post_name;
//					$the_terms = get_term_by( 'slug', $post_slug, 'multiverso-categories');
//					$first = true;
//					foreach ($the_terms as $term):
//						if (!empty($term) && $first):
//							echo do_shortcode('[mv_single_category id='.$term.']');
//							woffice_mv_managefiles_projects($term);
//							$first = false;
//						endif;
//					endforeach;
//				endif;
//			endif; ?>
		</div>

		<!-- SEE THE COMMENTS-->
		<div id="project-content-comments">





<div id="chatdiv" style="overflow:scroll; height:300px;">
<!--            <ul id="messages"></ul>-->

 <div id="messages"></div>
</div>
            <form action="">
                <input id="m" autocomplete="off"  style="width: 100%;" placeholder="Enter message here"/>
<!--                <button>Send</button>-->
            </form>

            <script src="http://localhost:3000/socket.io/socket.io.js"></script>
            <script src="https://code.jquery.com/jquery-1.11.1.js"></script>


            <?php $user = get_current_user_id();?>
            <?php $userData = get_userdata($user);
                  $usern = $userData->user_nicename;
            ?>
            <script>



                $(function () {
                    var socket = io("http://localhost:3000/");
                    var username= <?php echo json_encode($user); ?>;
//                    socket.emit('roomname', <?php //echo the_ID(); ?>//, username);
//                    socket.on('roomname', function(msg){
//                        $('#messages').append($('<li>').text(msg));
//                    });


                        $('form').submit(function () {
                            if ($('#m').val() != '' ) {
                                // Calling server
                                socket.emit('chat message', $('#m').val(), username,<?php echo the_ID(); ?>, '<?php echo $usern; ?>');
                                $('#m').val('');
                                return false;
                            }
                        });


                    // getting reponse back
                    socket.on('chat message', function(msg,username,roomname,usern){
                        var ava= '';
                        if(username != <?php echo $user; ?>){
                            ava= '<span style="margin-top: 40px;color:#1f5b98;font-weight: bold">'+ usern +':</span><div class="talk-bubble tri-right border round left-top" style="margin-left: 24px"><div class="talktext" ><p>' + msg + '</p></div></div>';
                        }else
                            ava= '<div class="talk-bubble tri-right round border right-top" style="margin-top: 15px;margin-left: 40%;background-color: #cae3f3"><div class="talktext" ><p>' + msg + '</p></div></div><div></div>';
                        if (roomname==<?php echo the_ID(); ?>) {
                            $('#messages').append(ava);
                        }

                        var objDiv = document.getElementById("chatdiv");
                        objDiv.scrollTop = objDiv.scrollHeight;
                        });


                });
            </script>
<!--			--><?php
//			// If comments are open or we have at least one comment, load up the comment template.
//			if ( (comments_open() || get_comments_number()) && woffice_projects_have_comments()) {
//				comments_template();
//			}
//			else {
//				_e("Comments are closed...","woffice");
//			}
//			?>
		</div>

	</div>
</article>
