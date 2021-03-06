<?php
/**
* Template Name: Projects
*/

$process_result = array();

if (function_exists( 'woffice_projects_extension_on' )){

	$projects_create = woffice_get_settings_option('projects_create'); 				
	if (Woffice_Frontend::role_allowed($projects_create)):
	
		$process_result = Woffice_Frontend::frontend_process('project');
		
	endif;
	
}

get_header(); 
?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">

			<?php  //GET THEME HEADER CONTENT

			woffice_title(get_the_title()); ?> 	

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">

				<!-- START CONTENT -->
				<div id="content">
					<?php if (woffice_is_user_allowed()) { ?>
						<?php 
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('box','content');


                        // CHECK IF USER CAN CREATE PROJECT POST
                       $projects_create = woffice_get_settings_option('projects_create');
                        if (Woffice_Frontend::role_allowed($projects_create)): ?>

                        <div class="frontend-wrapper box intern-padding"
                             style="padding: 0px 40px;">

                            <div class="content" id="projects-bottom">
                                <?php
                                /**
                                 * Filter the text of the button "Create a project"
                                 *
                                 * @param string
                                 */
                                $new_project_button_text = apply_filters('woffice_new_project_button_text', __("Create a project", "woffice")); ?>
                                <a href="javascript:void(0)" class="btn btn-default" id="show-project-create"><i class="fa fa-plus-square"></i> <?php echo $new_project_button_text; ?></a>
                            </div>

                            <?php Woffice_Frontend::frontend_render('project', $process_result); ?>

                        </div>

                        <?php endif; ?>
                        

						<!-- LOOP ALL THE PROJECTS-->
						<?php // GET POSTS
						if (function_exists( 'woffice_projects_extension_on' )){

							$project_query_args = array(
								'post_type' => 'project',
								'posts_per_page' => '-1'
							);

							$projects = new WP_query($project_query_args);
							$excluded = array();
							while ( $projects->have_posts() ) : $projects->the_post();

								$hide_projects_completed = woffice_get_settings_option( 'hide_projects_completed', true );
								$hide = false;
								if( $hide_projects_completed ) {
									$hide = woffice_get_post_option( get_the_ID(), 'project_completed', false);
								}
								if(!woffice_is_user_allowed_projects() || $hide) {
									array_push($excluded, get_the_ID());
								}
							endwhile;

                            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

							$project_query_args = array(
								'post_type' => 'project',
								'paged' => $paged,
								'post__not_in' => $excluded,
							);

              /**
               * Filter the args of the query for project items loop
               *
               * @param array
               */
							$project_query_args = apply_filters('woffice_projects_loop_args', $project_query_args);

							$project_query = new WP_Query($project_query_args);
							if ( $project_query->have_posts() ) :

								// We check for the layout
								$projects_layout = woffice_get_settings_option('projects_layout');
								$projects_layout_class = '';
								if($projects_layout == "masonry") {
									$projects_layout_class = 'masonry-layout';
									$masonry_columns = woffice_get_settings_option('projects_masonry_columns');

									$projects_layout_class .= ' masonry-layout--'.$masonry_columns.'-columns';
								}

								echo'<ul id="projects-list" class="'. $projects_layout_class .'">';
								// LOOP
								while($project_query->have_posts()) : $project_query->the_post();

									get_template_part('template-parts/content', 'project');
								
								endwhile;
								echo '</ul>';
                                woffice_paging_nav($project_query);
							else :
								get_template_part( 'content', 'none' );
							endif;
							wp_reset_postdata();




						 }?>

					<?php
					} else { 
						get_template_part( 'content', 'private' );
					}
					?>
					</div>
				</div>
					
			</div><!-- END #content-container -->
		
			<?php woffice_scroll_top(); ?>

		</div><!-- END #left-content -->

	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();



