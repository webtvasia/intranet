<?php
$is_completed = (bool)woffice_get_post_option( get_the_ID(), 'project_completed', false);

$completed_class = ($is_completed) ? 'project-completed' : '';

?>

<li class="box content <?php echo $completed_class; ?>">
	<div class="intern-padding">

		<?php
		// Completed
		if( $is_completed) {
			echo '<div class="project-completed-label">';
			echo '<div class="project-completed-label--background"></div>';
			echo '<a href="'.get_the_permalink().'" data-toggle="tooltip" data-placement="right" title="'. esc_html__( 'Completed', 'woffice') .'" ><i class="fa fa-check"></i></a>';
			echo '</div>';
		}
		?>

		<a href="<?php the_permalink(); ?>" rel="bookmark" class="project-head">

			<h2 class="project-title"><i class="fa fa-cubes"></i><?php the_title() ?></h2>

			<?php
			if (get_comment_count(get_the_ID()) > 0):
				echo '<span class="project-comments"><i class="fa fa-comments-o"></i> '.get_comments_number( '0', '1', '%' ).'</span>';
			endif;
			?>

			<?php
			// CATEGORY
			if( has_term('', 'project-category')):
				echo '<span class="project-category"><i class="fa fa-tag"></i>';
				echo wp_strip_all_tags(get_the_term_list( $post->ID, 'project-category', '', ', ' ));
				echo '</span>';
			endif;
			?>

			<?php
			// MEMBERS
			$project_members = woffice_get_project_members();
			echo '<span class="project-members"><i class="fa fa-users"></i> '.count($project_members).'</span>';
			?>

            <?php
            // DATE
            $project_date_start = (!empty($post->ID) && function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post->ID, 'project_date_start') : date('d-m-Y');
            $project_date_end = (!empty($post->ID) && function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($post->ID, 'project_date_end') : date('d-m-Y');
            echo '<span class="project-category"><i class="fa fa-calendar-o"></i>';
            $dateTimestampStart = strtotime($project_date_start);
            $dateTimestampEnd = strtotime($project_date_end);
            $date_now = strtotime(date(get_option('date_format')));
            if ($dateTimestampStart > $date_now) :
                echo __('Starts on : ', 'woffice').$project_date_start;
            elseif ($dateTimestampEnd < $date_now):
                echo __('Ended on : ', 'woffice').$project_date_end;
            else :
                echo __('Ends on : ', 'woffice').$project_date_end;
            endif;
            echo '</span>';
            ?>

		</a>

		<?php
		// THE PROGRESS BAR
		woffice_project_progressbar();
		?>

		<p class="project-excerpt"><?php the_excerpt() ?></p>

		<div class="text-right">
			<a href="<?php the_permalink(); ?>" class="btn btn-default"><?php esc_html_e("See Project","woffice")?> <i class="fa fa-arrow-right"></i></a>
		</div>
	</div> <!-- .intern-padding -->
</li>