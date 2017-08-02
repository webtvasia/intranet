<?php
$active_extensions = Eonet\Core\EonetComponents::getActiveComponents();
$extensions_array = array();
foreach ($active_extensions as $key=>$extension) {
    $extensions_array[$key] = Eonet\Core\EonetComponents::getConfig($key);
}
$current_settings = (isset($_GET['eo_settings'])) ? $_GET['eo_settings'] : '';
?>

<div id="eo_admin_tab_<?php echo esc_attr($slug); ?>" class="eo_admin_tab">


    <div id="eo_admin_tab_title_<?php echo esc_attr($slug); ?>" class="eo_admin_tab_title">

        <h1><?php echo esc_html($name); ?></h1>

    </div>

    <div id="eo_admin_content_<?php echo esc_attr($slug); ?>" class="eo_admin_tab_content eo_no_padding">

        <?php
        if(empty($extensions_array)) :
	        echo "<div class='eo_admin_tab_content'><p>".__('You haven\'t any component enabled which\'d require a Settings Panel', 'eonet-live-notifications')."</p></div>";
        else:
        ?>

            <form id="eo_admin_settings_form" method="post" action="<?php echo get_admin_url(). 'admin.php?page=eonet&eo_tab=settings' ?>" class="eo_form">

                <?php wp_nonce_field( 'eonet_admin_save_settings' ); ?>

                <input type="hidden" name="action" value="eonet_admin_save_settings">

                <div class="eo_admin_innner_tabs wp-clearfix">

                    <ul class="eo_nav_tabs" role="tablist">

                        <?php // We build the tab navigations
                        $count = 0;
                        foreach ($extensions_array as $extension_slug=>$extension) : ?>
                            <?php // Active or not ?
                            if((!empty($current_settings) && $current_settings == $extension_slug) || (empty($current_settings) && $count == 0)) {
                                $active = 'is-active';
                            } else {
                               $active = '';
                            }
                            ?>
                            <li class="<?php echo esc_attr($active); ?>" >
                                <a href="#eo_tab_<?php echo esc_attr($extension_slug); ?>" data-eo-slug="<?php echo esc_attr($extension_slug); ?>"><?php echo esc_html($extension['name']); ?></a>
                            </li>
                            <?php $count++; ?>
                        <?php endforeach; ?>

                    </ul>

                    <div class="eo_tab_content">

                        <?php // We build the tab content
                        $count = 0;
                        foreach ($extensions_array as $extension_slug=>$extension) : ?>
                            <?php // Active or not ?
                            if((!empty($current_settings) && $current_settings == $extension_slug) || (empty($current_settings) && $count == 0)) {
                                $active = 'is-active';
                            } else {
                                $active = '';
                            } ?>
                            <?php // empty or not ?
                            $component_settings = Eonet\Core\EonetComponents::getSettings($extension_slug);
                            if(!empty($component_settings)) : ?>
                                <div class="eo_tab_pane <?php esc_attr($active); ?>" id="eo_tab_<?php echo esc_attr($extension_slug); ?>">
                                    <div class="eo_tab_inner_title">
                                        <h4><?php echo esc_html($extension['name']); ?></h4>
                                        <div class="eo_admin_settings_saving">
                                            <?php echo Eonet\Core\EonetOptions::getSubmit('save', 'Settings'); ?>
                                        </div>
                                    </div>
                                    <div class="eo_tab_inner_content">
                                        <?php // Render the HTML :
                                        echo Eonet\Core\EonetComponents::getSettingsMarkup($extension_slug);
                                        ?>
                                    </div>
                                    <div class="eo_tab_inner_footer">
                                        <div class="eo_admin_settings_saving">
                                            <?php echo Eonet\Core\EonetOptions::getSubmit('save', 'Settings'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="eo_tab_pane <?php echo esc_attr($active); ?>" id="eo_tab_<?php echo esc_attr($extension_slug); ?>">
                                    <div class="eo_tab_inner_title">
                                        <h4><?php echo esc_html($extension['name']); ?></h4>
                                    </div>
                                    <div class="eo_tab_inner_content">
                                        <div class="eonet_padding">
                                            <div class="eo_alert eo_alert_info">
                                                <h4><?php esc_html_e('No option available at this moment... ','eonet-live-notifications'); ?></h4>
                                                <p><?php esc_html_e("We're still working on this component options. Feel free to get in touch with us for any option idea. Options will be coming in the premium version.", 'eonet-live-notifications'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="eo_tab_inner_footer">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    </div>

                </div>

                <a href="javascript:void(0);" id="eo_admin_settings_reset_trigger" class="eo_btn eo_btn_default">
                    <?php esc_html_e('Reset Settings', 'eonet-live-notifications'); ?>
                </a>

            </form>

	        <?php //Security :
	        $reset_action = 'eonet_admin_reset_settings';
	        $reset_nonce = wp_create_nonce( $reset_action . '_nonce' );
	        ?>

            <script type="text/javascript">

                (function ($) {

	                setTimeout(function(){
	                    var data = {
	                        'action' : '<?php echo $reset_action; ?>',
	                        'reset' : 'true',
	                        'security' : '<?php echo $reset_nonce; ?>'
	                    },
		                    message = "<?php esc_html_e("Are you sure? This can't be undone. All your Eonet settings will be erased.",'eonet-live-notifications') ?>";;

		                jQuery.eoSettingsResetWatcher(data, message);
	                }, 1000);


                })(jQuery);

            </script>

        <?php endif; ?>

    </div>

</div>