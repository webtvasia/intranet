<div id="eo_admin_tab_<?php echo esc_attr($slug); ?>" class="eo_admin_tab">

    <div id="eo_admin_tab_title_<?php echo esc_attr($slug); ?>" class="eo_admin_tab_title">

        <h1><?php echo esc_html($name); ?></h1>

    </div>

    <div id="eo_admin_content_<?php echo esc_attr($slug); ?>" class="eo_admin_tab_content">

        <p>
            <strong><?php esc_html_e('Having any question or issue ?', 'eonet-live-notifications'); ?></strong>
            <?php esc_html_e('Our team is here to help you out at anytime.', 'eonet-live-notifications'); ?>
            <?php esc_html_e('If you have any idea about how we could improve.', 'eonet-live-notifications'); ?>
            <?php esc_html_e('You can share access to your site on our helpdesk if it can help getting faster.', 'eonet-live-notifications'); ?>
            <?php esc_html_e('There are two ways to in touch with us : ', 'eonet-live-notifications'); ?>
        </p>

        <ul id="eo_support_list" class="eo_boxes_list wp-clearfix">

            <li class="eo_single_box wp-clearfix">
                <div class="eo_single_left">
                    <div class="eo_single_icon_wrapper">
                        <i class="fa fa-tags"></i>
                    </div>
                </div>
                <div class="eo_single_right">
                    <div class="eo_single_inner">
                        <h4><?php esc_html_e('Tickets Support', 'eonet-live-notifications'); ?></h4>
                        <p><?php esc_html_e('Open a ticket on our helpdesk, we don\'t guarantee a fast response but within a week. Except if you\'ve purchased one of our product we\'ll reply within 24 hours.', 'eonet-live-notifications'); ?></p>
                        <div class="text-right">
                            <a href="https://alkaweb.ticksy.com/submit/" class="eo_btn eo_btn_default" target="_blank">
                                <i class="fa fa-plus-square"></i>
                                <?php esc_html_e('Open a ticket', 'eonet-live-notifications'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="eo_single_box wp-clearfix">
                <div class="eo_single_left">
                    <div class="eo_single_icon_wrapper">
                        <i class="fa fa-comments"></i>
                    </div>
                </div>
                <div class="eo_single_right">
                    <div class="eo_single_inner">
                        <h4><?php esc_html_e('Forum Support', 'eonet-live-notifications'); ?></h4>
                        <p><?php esc_html_e("Create a new thread on our plugin page, participation is open to anyone from all around the world. We'll be there to help as well but can't guarantee any delay.", 'eonet-live-notifications'); ?></p>
                        <div class="text-right">
                            <a href="http://wordpress.org/plugins/" class="eo_btn eo_btn_default" target="_blank">
                                <i class="fa fa-plus-square"></i>
                                <?php esc_html_e('Open a thread', 'eonet-live-notifications'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </li>

        </ul>


    </div>

</div>