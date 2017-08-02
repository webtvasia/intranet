<?php

$active_extensions = Eonet\Core\EonetComponents::getActiveComponents();

//The default page to display
$default_page = ( !empty($active_extensions) ) ? 'settings' : 'extensions';
$default_page = apply_filters( 'eonet_admins_pages_default_page', $default_page);

//The page to display if it is reached by url
$current_page = (isset($_GET['eo_tab'])) ? $_GET['eo_tab'] : $default_page;
$current_page_class = 'Eonet\Core\Admin\Pages\EonetPage'.ucwords($current_page);
$current_page_class = apply_filters('eonet_admins_pages_get_class_from_slug', $current_page_class, $current_page);

$page_instance = new $current_page_class();

//The pages to display in the eonet dashboard
$admins_pages = array();

// Components settings
if( !empty($active_extensions) )
	$admins_pages[] = new Eonet\Core\Admin\Pages\EonetPageSettings();

// Components cards
$admins_pages[] = new Eonet\Core\Admin\Pages\EonetPageExtensions();

// Promo Themes
if( !eo_is_current_theme_by_alkaweb())
	$admins_pages[] = new Eonet\Core\Admin\Pages\EonetPageThemes();

// Support page
$admins_pages[] = new Eonet\Core\Admin\Pages\EonetPageSupport();

$admins_pages = apply_filters('eonet_admins_page_nav', $admins_pages);
?>

<div id="eo_admin">

    <?php do_action('eonet_admin_before'); ?>

    <div id="eo_admin_wrapper">

        <div id="eo_admin_header" class="wp-clearfix">

            <div id="eo_admin_logo">
                <img src="<?php echo EONET_ASSETS_URL.'/images/logo_colored.png'; ?>"/>
                <div class="eo_admin_right">
                </div>
            </div>

            <?php do_action('eonet_admin_header'); ?>

            <nav id="eo_admin_nav">
                <ul>
                    <?php foreach ($admins_pages as $page) :  ?>
                        <?php $active = ($page->getPageSlug() == $page_instance->getPageSlug()) ? 'is-active' : '';  ?>
                        <li class="eo_admin_nav_item <?php echo $active; ?>">
                            <a href="javascript:void(0);" id="eo_admin_nav_trigger_<?php echo $page->getPageSlug(); ?>" data-eo-slug="<?php echo $page->getPageSlug(); ?>">
                                <i class="<?php echo $page->getPageIcon(); ?>"></i>
                                <h4><?php echo $page->getPageName(); ?></h4>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

        </div>

        <div id="eo_admin_content">

            <div id="eo_admin_main_tabs">
                <?php echo $page_instance->getPageContent(); ?>
            </div>

        </div>

        <script type="text/javascript">

            <?php //Security :
            $action = 'eonet_admin_get_page';
            $nonce = wp_create_nonce( $action . '_nonce' );
            ?>

            (function ($) {

                // Helper variables :
                var eoCurrentPage = $.eonetGetUrlParam('page');
                var eoWrapper = $('#eo_admin_wrapper');
                var eoPageNav = eoWrapper.find('#eo_admin_nav');
                var eoPageContainer = eoWrapper.find('#eo_admin_main_tabs');

                eoPageNav.find('a').on('click', function (e) {

                    // Loader :
                    eoPageContainer.addClass('has_loader_colored');
                    $('.has_loader_colored').eonetLoader({'colored': true});

                    // Class toggling :
                    eoPageNav.find('li').each(function () {
                        $(this).removeClass('is-active');
                    });
                    $(this).parent().toggleClass('is-active');

                    var slug = $(this).data('eo-slug');

                    var data = {
                        'action' : '<?php echo $action; ?>',
                        'slug' : slug,
                        'security' : '<?php echo $nonce; ?>'
                    };

                    jQuery.post(ajaxurl, data, function(response) {

                        var oldTab = eoPageContainer.find('.eo_admin_tab');
                        if(oldTab.length > 0){
                            oldTab.fadeOut('slow');
                            setTimeout(function () {
                                oldTab.remove();
                            }, 500);
                        }
                        setTimeout(function () {
                            eoPageContainer.prepend(response);
                            if (history.pushState) {
                                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=eonet&eo_tab=' + slug;
                                window.history.pushState({path:newurl},'',newurl);
                            }

	                        if(slug == 'extensions') {
		                        set_height_of_the_componenet_cards();
	                        }
                        }, 500);

                        setTimeout(function () {
                            eoPageContainer.removeClass('has_loader_colored');
                            eoPageContainer.find('.eonet_loader').remove();
                        }, 500);

                        $.eonetForms();

                    });

                    // Stop it here :
                    return false;

                });

	            /**
	             * Set the same height for all componenets cards
	             * (in order to avoid mess up with float left)
	             */
	            function set_height_of_the_componenet_cards() {
		            var $cards = $('.eo_single_box');

		            if($cards.length > 0) {
			            var maxHeight = Math.max.apply(null, $cards.map(function ()
			            {
				            return $(this).height();
			            }).get());

			            $cards.height(maxHeight);
		            }
	            }

            })(jQuery);

        </script>

    </div>

    <?php do_action('eonet_admin_after'); ?>

</div>

