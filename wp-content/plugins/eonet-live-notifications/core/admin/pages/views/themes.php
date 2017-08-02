<?php
$themes = \Eonet\Core\Admin\EonetAdmin::getThemes();
?>
<div id="eo_admin_tab_<?php echo esc_attr($slug); ?>" class="eo_admin_tab">

    <div id="eo_admin_tab_title_<?php echo esc_attr($slug); ?>" class="eo_admin_tab_title">

        <h1><?php echo esc_html($name); ?></h1>

    </div>

    <div id="eo_admin_content_<?php echo esc_attr($slug); ?>" class="eo_admin_tab_content">

        <p>
            <strong><?php esc_html_e('Do you want your site to looks nice ?', 'eonet-live-notifications'); ?></strong>
            <?php esc_html_e('Our team is here to help you out at anytime.', 'eonet-live-notifications'); ?>
            <?php esc_html_e('If you have any idea about how we could improve.', 'eonet-live-notifications'); ?>
        </p>

        <ul id="eo_themes_list" class="eo_boxes_list wp-clearfix">

            <?php foreach ($themes as $theme) : ?>

                <li id="eo_theme_<?php echo eonet_camel($theme['name']); ?>" class="eo_single_box wp-clearfix">
                    <div class="eo_single_left">
                        <img src="<?php echo esc_url($theme['thumb']); ?>">
                    </div>
                    <div class="eo_single_right">
                        <div class="eo_single_inner">
                            <h4><?php echo esc_html($theme['name']); ?></h4>
                            <ul class="eo_styled_list eo_colored_list eo_features_list">
                                <?php foreach($theme['features'] as $feature) : ?>
                                    <li><?php echo esc_html($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="text-right">
                                <a href="<?php echo esc_url($theme['link_features']); ?>" class="eo_btn eo_btn_default">
                                    <?php esc_html_e('All features', 'eonet-live-notifications'); ?>
                                </a>
                                <a href="<?php echo esc_url($theme['link_demo']); ?>" class="eo_btn eo_btn_default">
                                    <?php esc_html_e('Demo', 'eonet-live-notifications'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

</div>