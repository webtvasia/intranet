<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

echo $title;
?>
	<!-- WIDGET -->
	<ul class="list-styled list-projects">
		<?php

		$project_query_args = array(
			'post_type' => 'project',
			'posts_per_page' => '-1'
		);

		$projects = new WP_query($project_query_args);
		$excluded = array();
		while ( $projects->have_posts() ) : $projects->the_post();
			if(!woffice_is_user_allowed_projects()) {
				array_push($excluded, get_the_ID());
			}
		endwhile;

			//QUERY $tax
			$query_args = array(
				'post_type' => 'project',
				'post__not_in' => $excluded,
				'posts_per_page' => -1,
			);
			if (!empty($category) && $category != "all") {
				$the_tax = array(array(
					'taxonomy' => 'project-category',
					'terms' => array($category),
					'field' => 'slug',
				));
				$query_args['tax_query'] = $the_tax;
			}
			// GET PROJECTS POSTS

      /**
       * Filter the query args for the filter "(Woffice) Recent Projects"
       *
       * @param array $query_args
       */
	    $query_args = apply_filters('woffice_widget_recent_projects_query_args', $query_args);
			$widget_projects_query = new WP_Query( $query_args );

      /**
       * Filter the maximum number of projects to display in the widget "(Woffice) Recent Projects"
       *
       * @param int
       */
			$widget_projects_max = apply_filters('woffice_widget_recent_projects_max', 8);

			$numberprojects = 0;

			$user_id = ((bool)$current_user) ? get_current_user_id() : false;

			while($widget_projects_query->have_posts()) : $widget_projects_query->the_post();

				if( $numberprojects == $widget_projects_max)
					break;

				if( $user_id ) {
					$project_members = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option(get_the_ID(), 'project_members') : '';
					if( !empty($project_members) && !in_array($user_id, $project_members)) {
						continue;
					}
				}

				echo'<li>';
					echo '<a href="'. get_the_permalink() .'" rel="bookmark">'. get_the_title() .'</a>';
					echo woffice_project_progressbar();
				echo '</li>';
				$numberprojects++;

			endwhile;

			if ($numberprojects == 0)
				esc_html_e("Sorry you don't have any project yet.","woffice");

			wp_reset_postdata();
		?>
	</ul>
<?php echo $after_widget ?>