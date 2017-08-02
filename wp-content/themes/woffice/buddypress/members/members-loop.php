<?php

/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_members_loop' ); ?>

<?php 
$members_loop_query = woffice_get_members_loop_query();

/**
 * You can hide the role of users displayed in the members loop page
 *
 * @param bool
 */
$members_role_enabled = apply_filters('woffice_enable_member_role_on_members_page', true);

/**
 * You can hide the last activity of users displayed in the members loop page
 *
 * @param bool
 */
$last_activity_enabled = apply_filters('woffice_enable_member_last_activity_on_members_page', true);

if ( bp_has_members( $members_loop_query )) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<?php
	$buddy_members_layout = woffice_get_settings_option('buddy_members_layout');
	if ($buddy_members_layout == "cards") :
	?>

		<ul id="members-list" class="item-list" role="main">

		<?php while ( bp_members() ) : bp_the_member(); ?>

			<li>
				<?php
				$user_ID = bp_get_member_user_id();
				$the_cover = woffice_get_cover_image($user_ID);
				if (!empty($the_cover)):
					echo'<div class="item-avatar has-cover" style="background-image: url('.esc_url($the_cover).')">';
				else :
					echo'<div class="item-avatar">';
				endif;
				?>
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar('type=full&width=100&height=100'); ?></a>

					<?php
					if( $members_role_enabled ) {
						// TAG WITH THE USER ROLE
						$user = get_userdata($user_ID);
						/* WE NEED TO REMOVE BBP ROLES */
						$roles = array();
						global $wp_roles;
						foreach ($user->roles as $key => $role) {
							if (substr($role, 0, 4) != 'bbp_') {
								array_push($roles, translate_user_role($wp_roles->roles[$role]['name']));
							}
						}
						if(!empty($roles))
						    echo'<span class="member-role">'.implode(', ',$roles).'</span>';
					}
					?>
				</div>

				<div class="item">
					<div class="item-title">

						<?php
						// USERNAME OR NAME DISPLAYED
						$ready_display = woffice_get_name_to_display($user_ID);
						?>

						<a href="<?php bp_member_permalink(); ?>" class="heading"><h3><?php echo $ready_display; ?></h3></a>

						<?php if ( bp_get_member_latest_update() ) : ?>

							<span class="update"> <?php bp_member_latest_update(); ?></span>

						<?php endif; ?>

					</div>

					<?php if( $last_activity_enabled ): ?>
					<div class="item-meta"><span class="activity"><?php bp_member_last_active(); ?></span></div>
					<?php endif; ?>

					<?php do_action( 'bp_directory_members_item' ); ?>

					<?php
					 /***
					  * If you want to show specific profile fields here you can,
					  * but it'll add an extra query for each member in the loop
					  * (only one regardless of the number of fields you show):
					  *
					  * bp_member_profile_data( 'field=the field name' );
					  */
					?>

					<?php
                    /**
                     * Before the list of custom member fields, in the members page (card layout)
                     */
					do_action('woffice_before_list_xprofile_fields');

					woffice_list_xprofile_fields(bp_get_member_user_id());

                    /**
                     * After the list of custom member fields, in the members page (card layout)
                     */
                    do_action('woffice_after_list_xprofile_fields'); ?>
				</div>

				<div class="action">

					<?php do_action( 'bp_directory_members_actions' ); ?>

				</div>

				<div class="clear"></div>
			</li>

		<?php endwhile; ?>

		</ul>

	<?php else : ?>

		<?php
		if (bp_is_active( 'xprofile' )) {
			// We fetch all the Buddypress fields :
			global $wpdb;
			$table_name = woffice_get_xprofile_table('fields');
			$sqlStr = "SELECT name, type, field_order, group_id FROM " . $table_name . ' ORDER BY group_id, field_order';
			$fields = $wpdb->get_results($sqlStr);

            //Add wordpress email to the fields
            $wordpress_email_value = new stdClass();
            $wordpress_email_value->name = 'wordpress_email';
            $wordpress_email_value->type = 'email';
            array_unshift($fields, $wordpress_email_value);
		}
		?>
        <div class="table-responsive">
		    <table id="members-list-table" class="members table table-hover table-striped">
			<thead>
				<th><?php _e('Name', 'woffice'); ?></th>
				<?php if( $members_role_enabled ): ?>
				<th><?php _e('Role', 'woffice'); ?></th>
				<?php endif; ?>
				<?php if( $last_activity_enabled ): ?>
				<th><?php _e('Activity', 'woffice'); ?></th>
				<?php endif; ?>
				<?php
				if (isset($fields) && count($fields) > 0) {
					foreach ($fields as $field) {
						$field_name = $field->name;
						$field_type = $field->type;
						$field_show = woffice_get_settings_option('buddypress_' . $field_name . '_display');
						$field_icon = woffice_get_settings_option('buddypress_' . $field_name . '_icon');
						if (!empty($field_show)) {
							if ($field_show == true) {

                                if($field_name != 'wordpress_email')
								    echo '<th><i class="fa '.$field_icon.'"></i> '.$field_name.'</th>';
                                else
                                    echo '<th><i class="fa '.$field_icon.'"></i> '. __('Email', 'woffice') .'</th>';
							}
						}
					}
				}
				?>
				<?php if (bp_is_active('friends')) { ?>
					<th><?php _e('Friendship', 'woffice'); ?></th>
				<?php } ?>
			</thead>
			<tbody>
				<?php while ( bp_members() ) : bp_the_member(); ?>
				<tr>
					<td>
						<a href="<?php bp_member_permalink(); ?>" class="clearfix">
							<?php bp_member_avatar('type=full&width=100&height=100'); ?>
							<?php
							// USERNAME OR NAME DISPLAYED
                            $user_ID = bp_get_member_user_id();
                            $ready_display = woffice_get_name_to_display($user_ID);
							echo '<span>'.$ready_display.'</span>';
							?>
						</a>
					</td>
					<?php if( $members_role_enabled ): ?>
					<td>
						<?php // TAG WITH THE USER ROLE
						$user = get_userdata($user_ID);
						/* WE NEED TO REMOVE BBP ROLES */
						$roles = array();
						global $wp_roles;
						foreach ($user->roles as $key => $role) {
							if (substr($role, 0, 4) != 'bbp_') {
								array_push($roles, translate_user_role($wp_roles->roles[$role]['name']));
							}
						} ?>
						<span class="member-role label"><?php echo implode(', ',$roles); ?></span>
					</td>
					<?php endif; ?>
					<?php if( $last_activity_enabled ): ?>
					<td>
						<span class="activity"><?php bp_member_last_active(); ?></span>
					</td>
					<?php endif; ?>
					<?php
					if (isset($fields) && count($fields) > 0) {
						foreach ($fields as $field) {

                            $field_name = $field->name;
                            $field_type = $field->type;
                            $field_show = woffice_get_settings_option('buddypress_' . $field_name . '_display');
                            $field_icon = woffice_get_settings_option('buddypress_' . $field_name . '_icon');
                            if (!empty($field_show)) {

                                // We check for display :
                                if ($field_show == true) {

                                    if($field->name != 'wordpress_email') {
                                        $field_value = bp_get_profile_field_data( 'field='.$field_name.'&user_id='.$user_ID);
                                    } else {
                                        $user_info = get_userdata($user_ID);
                                        $field_value = "<a href='mailto:".$user_info->user_email."' rel='nofollow'>$user_info->user_email</a>";
                                    }

                                    $containe_url = (filter_var($field_value, FILTER_VALIDATE_URL) !== false);
                                    if (empty($field_icon)) {
                                        $field_icon = 'fa-arrow-right';
                                        if ($field_type == 'datebox') {
                                            $field_icon = 'fa-calendar';
                                        } elseif ($field_type == 'email') {
                                            $field_icon = 'fa-envelope';
                                        } elseif (!($field_type == 'url' || $field_type == 'web' || $field_type == 'email') && $containe_url) {
                                            $field_name = strtolower($field_name);
                                            if (strpos($field_name, 'facebook') !== false) {
                                                $field_icon = 'fa-facebook-square';
                                            } elseif (strpos($field_name, 'twitter') !== false) {
                                                $field_icon = 'fa-twitter-square';
                                            } elseif (strpos($field_name, 'instagram') !== false) {
                                                $field_icon = 'fa-instagram-square';
                                            } elseif (strpos($field_name, 'github') !== false) {
                                                $field_icon = 'fa-github-square';
                                            } elseif (strpos($field_name, 'google') !== false) {
                                                $field_icon = 'fa-google-plus-square';
                                            } elseif (strpos($field_name, 'slack') !== false) {
                                                $field_icon = 'fa-slack';
                                            } elseif (strpos($field_name, 'linkedin') !== false) {
                                                $field_icon = 'fa-linkedin-square';
                                            }
                                        }
                                    }


                                    // we check if it's an URL
                                    if ($field_type == 'url' || $field_type == 'web' || $field_type == 'email') {
                                        echo '<td>';
                                        echo $field_value;
                                        echo '</td>';
                                    } elseif ($containe_url) {
                                        if (strpos($field_value, 'href') !== false) {
                                            $link_content = new SimpleXMLElement($field_value);
                                            $field_value = (string)$link_content['href'];
                                        }
                                        if (!empty($field_value)) {
                                            echo '<td class="field-icon">';
                                            echo '<a href="' . $field_value . '" target="_blank">';
                                            echo '<i class="fa ' . $field_icon . '"></i>';
                                            echo '</a>';
                                            echo '</td>';
                                        }

                                    } elseif (is_array($field_value)) {
                                        echo '<td>';
                                        echo implode(", ", $field_value);
                                        echo '</td>';
                                    } else {
                                        echo '<td>';
                                        echo $field_value;
                                        echo '</td>';
                                    }


                                }

                            }
                        }
					}
					?>
					<?php if (bp_is_active('friends')) { ?>
						<td>
							<?php do_action( 'bp_directory_members_actions' ); ?>
						</td>
					<?php } ?>
				</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
        </div>

	<?php endif; ?>

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
