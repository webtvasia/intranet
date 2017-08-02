/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * MAIN JAVASCRIPT
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
(function($) {
    "use strict";

    /**
     * Handle the behaviors of the iconpicker fields.
     * A single instance handle all the iconpicker fields on the page, also that ones created dinamically
     */
    function eoIconPicker( ) {

        var inst = this;

        var $body = $('body'),
            iconpicker_container_selector = '.eo_field_iconpicker_wrapper';

        /**
         * Show only the list icons of the group selected
         */
        inst.groupSelectWatcher = function() {

            $body.on('change', iconpicker_container_selector + ' .eo_field_iconpicker_group_select', function(){

                var $iconpicker_container = $(this).closest(iconpicker_container_selector),
                    $iconpicker_panels = $iconpicker_container.find('.eo_field_iconpicker_icons_grid_panel');

                var group_selected = $(this).val();

                if( group_selected != 'all') {
                    $iconpicker_panels.addClass('hidden');
                    $iconpicker_container.find('.eo_field_iconpicker_icons_grid_panel[data-eo-icons-group="' + group_selected + '"]').removeClass('hidden');
                } else {
                    $iconpicker_panels.removeClass('hidden');
                }

                inst.addEmptyIcon( $iconpicker_container );
            });

        };

        /**
         * Trigger the icon selected by the grid
         */
        inst.iconSelectWatcher = function() {

            $body.on('click', iconpicker_container_selector + ' .eo_field_iconpicker_icons_grid_panel .eo_field_iconpicker_icons_grid_element', function(){

                var $iconpicker_container = $(this).closest(iconpicker_container_selector),
                    $iconpicker_panels = $iconpicker_container.find('.eo_field_iconpicker_icons_grid_panel'),
                    $icons_select = $iconpicker_container.find('.eo_field_iconpicker_select');

                $iconpicker_panels.find('.eo_field_iconpicker_icons_grid_element').removeClass('selected');
                $(this).addClass('selected');

                var icon_selected = $(this).find('i').attr('class');

                $icons_select.val(icon_selected);
                $iconpicker_container.find('.eo_field_iconpicker_icon_selected i').attr('class', '').addClass(icon_selected);
            });

        };

        /**
         * Triggered when the details of the field are opened/closed
         */
        inst.showGridWatcher = function() {

            $body.on( 'click', iconpicker_container_selector + ' .eo_field_iconpicker_details_trigger', function(){
                var $iconpicker_container = $(this).closest(iconpicker_container_selector);

                inst.markSelectedIcon($iconpicker_container);

                $iconpicker_container.find('.eo_field_iconpicker_icons_details').slideToggle();
            });

        };

        /**
         * Highlight the selected icon on the page load
         */
        inst.markSelectedIcon = function( $iconpicker_container ) {
            var $icon = null,
                $iconpicker_panels = $iconpicker_container.find('.eo_field_iconpicker_icons_grid_panel'),
                $icons_select = $iconpicker_container.find('.eo_field_iconpicker_select'),
                icon_selected = $icons_select.val();

            if(icon_selected) {
                icon_selected = icon_selected.replace('fa ', '');
                $icon = $iconpicker_panels.find('.' + icon_selected).closest('.eo_field_iconpicker_icons_grid_element');
            } else {
                $icon = $iconpicker_panels.find('.eo_empty_icon');
            }

            $icon.addClass('selected');

        };

        /**
         * Append an empty icon, which allow to deselect any icon
         */
        inst.addEmptyIcon = function( $iconpicker_container ) {

            var $iconpicker_panels = $iconpicker_container.find('.eo_field_iconpicker_icons_grid_panel'),
                $visble_panels = $iconpicker_container.find('.eo_field_iconpicker_icons_grid_panel:not(.hidden)');

            var $empty_icon = $iconpicker_container.find('.eo_empty_icon'),
                empty_icon_selected = ($empty_icon.length > 0 && $empty_icon.hasClass('selected'));

            $iconpicker_panels.find('.eo_empty_icon').remove();

            $visble_panels.first().find('.eo_field_iconpicker_icons_grid_panel_icons').prepend('<a href="javascript:void(0)" class="eo_field_iconpicker_icons_grid_element eo_empty_icon"></a>');

            if( empty_icon_selected )
                $visble_panels.find('.eo_empty_icon').addClass('selected');
        };

        inst.init = function() {

            inst.groupSelectWatcher();

            inst.iconSelectWatcher();

            inst.showGridWatcher();

        };

        inst.init();
    }

    /**
     * Handle the behaviors of the image_select fields, there is an instance for each icnpicker field in the page
     *
     * @param field The iconpicker wrapper of the instance
     */
    function eoImageSelect( field ) {

        var inst = this;

        var $imageselect_container = $('#' + field),
            $images = $imageselect_container.find('.eo_image_select_element'),
            $hidden_input = $imageselect_container.find('.eo_field_image_select_selected_image');
            
        /**
         * Trigger the image selected by the list
         */
        inst.imageSelectWatcher = function() {

            $images.on('click', function(){
                
                $images.removeClass('selected');
                $(this).addClass('selected');

                $hidden_input.val( $(this).attr('data-eo-value') );
                
            });

        };
    
        inst.init = function() {

            inst.imageSelectWatcher();

        };

        inst.init();
    }

    function eoMultiSelect( field ) {

        var inst = this;

        var $field_container = $('#' + field),
            $field = $field_container.find('.eo_field'),
            choices = JSON.parse($field_container.attr('data-eo-available-choices')),
            available_choices = [];

        /**
         * Trigger something added
         */
        inst.inputWatcher = function() {

            $images.on('click', function(){

                $images.removeClass('selected');
                $(this).addClass('selected');

                $hidden_input.val( $(this).attr('data-eo-value') );

            });

        };

        inst.init = function() {

            //inst.inputWatcher();

            $.each(choices, function(id, element){
                available_choices.push(element);
            });

            $field.tagit({
                'availableTags' : available_choices,
                //'fieldName': 'blog_tags',
                'singleField': true,
                'singleFieldNode': $field
            });

        };

        inst.init();

    }

    $.eoSettingsManager = function(){

        var inst = this,
            $parent = $('#eo_admin_content');

        /**
         * Set the settings panel active on the page load
         */
        inst.defaultPanelWatcher = function() {
            var current_tab = window.location.hash,
                current_slug = current_tab.substring(8),
                $nav_tabs = $('.eo_nav_tabs');

            if( $nav_tabs.length <= 0)
                return;

            // Set the default opened panel
            if( current_tab.substring(0, 8) == '#eo_tab_') {
                // Nav
                $nav_tabs.find('li').removeClass( 'is-active' );
                $nav_tabs.find('a[data-eo-slug="' + current_slug + '"]').closest('li').addClass( 'is-active' );

                // Content
                $( '.eo_tab_pane').removeClass('is-active');
                $( current_tab ).addClass('is-active');
            } else {
                $nav_tabs.find('li').first().addClass( 'is-active' );
            }
        };

        /**
         * Allow to navigate settings sections using the nav menu on the left
         */
        inst.navigationWatcher = function() {

            $parent.on('click', '.eo_admin_innner_tabs ul.eo_nav_tabs a', function (e) {
                e.preventDefault();

                var eoInnertabs = $parent.find('.eo_admin_innner_tabs'),
                    eoInnertabsNav = eoInnertabs.find('ul.eo_nav_tabs'),
                    eoInnertabsWrapper = eoInnertabs.find('div.eo_tab_content');

                // Class toggeling in the nav :
                eoInnertabsNav.find('li').each(function () {
                    $(this).removeClass('is-active');
                });
                var $parent_li = $(this).parent();
                $parent_li.toggleClass('is-active');

                var has_child = $(this).attr('data-eo-child');
                var parent = $(this).attr('data-eo-parent');

                // If the link has children, then highlight the first child
                if( has_child == 'yes' )
                    $parent_li.find('ul.eo-tab-subsection li:first-child').addClass('is-active');

                // If the link has parent, then keep the parent active
                if( typeof parent !== undefined ) {
                    $parent.find('a[data-eo-slug="' + parent + '"]').parent().addClass('is-active');
                }

                eoInnertabsWrapper.find('.eo_tab_pane').each(function () {
                    $(this).removeClass('is-active');
                });
                // If the link has children, then show the content of the first child, otherwise show own content
                var slug;
                if(  has_child == 'yes' ) {
                    slug = $parent_li.find('ul.eo-tab-subsection li:first-child a').attr('href');
                } else {
                    slug = $(this).attr('href');
                }
                $(slug).toggleClass('is-active');

                // Save the new current panel in the url
                var pos = $(window).scrollTop();
                window.location.hash = slug;
                $(window).scrollTop(pos);


            });

        };

        /**
         * Watch the save settings button
         */
        inst.saveWatcher = function(){

            // Settings Saving :
            $parent.on('click', '.eo_admin_settings_saving button', function (e) {

                e.preventDefault();

                // Helpers:
                var eoCurrentPane = $(this).closest('.eo_tab_pane');
                var eoOptionsContrainer = eoCurrentPane.find('.eo_tab_inner_content');

                // Loader :
                eoOptionsContrainer.addClass('has_loader_colored');
                $('.has_loader_colored').eonetLoader({'colored': true});

                // WP Editor
                var editorsContent = '';
                if( typeof tinyMCE !== 'undefined' ) {
                    tinyMCE.editors.forEach(function (editor) {
                        editorsContent += '&' + editor.id + '=' + editor.getContent();
                    });
                }

                // Getting the data ready
                var data = $('#eo_admin_settings_form').serialize() + editorsContent;

                // Ajax Request
                jQuery.post(ajaxurl, data, function(response) {
                    var object = JSON.parse(response);
                    if(object.length != 0) {
                        // Alert :
                        var alertClass = (object.status == 'success') ? 'ion-ios-checkmark' : 'ion-ios-close';
                        $.eonetNotification(alertClass, object.title, object.content);
                    }
                    eoOptionsContrainer.removeClass('has_loader_colored');
                    return false;

                });

            });
        };

        /**
         * Init all watcher
         */
        inst.init = function() {

            inst.defaultPanelWatcher();

            inst.navigationWatcher();

            inst.saveWatcher();
        };

        inst.init();
    };

    $.eoSettingsResetWatcher = function( data, message ) {
        
        var $parent = $('#eo_admin_content');
        
        $parent.on('click', '#eo_admin_settings_reset_trigger', function (e) {
            e.preventDefault();
            var eoIsConfirmed = confirm( message );
            if(eoIsConfirmed){
                // Loader :
                $parent.find('.eo_tab_content').addClass('has_loader_colored');
                $('.has_loader_colored').eonetLoader({'colored': true});
                // We make the request :
                jQuery.post(ajaxurl, data, function(response) {
                    var object = JSON.parse(response);
                    if(object.length != 0) {
                        // Alert :
                        var alertClass = (object.status == 'success') ? 'ion-ios-checkmark' : 'ion-ios-close';
                        $.eonetNotification(alertClass, object.title, object.content);
                        // Page refresh
                        setTimeout(function () {
                            window.location.reload(true);
                        }, 1500);
                    }
                    $parent.find('.eo_tab_content').removeClass('has_loader_colored');
                    return false;
                });

            }
        });

    };


    $(document).ready( function() {

        // Instance the iconpicker class, which handle all the iconpicker fields
        new eoIconPicker( );

        // Instance the image_select class for each field
        $('.eo_field_image_select_wrapper').each(function(){
            new eoImageSelect( $(this).attr('id') );
        });

        // Handle all the colorpicker fields
        $('.eo_field_colorpicker_wrapper').each(function(){
            var args = {
                component : '.eo_colorpicker_input',
                //format: 'hex'
            };

            if(!$(this).data('eo-transparent')) {
                args.format = 'hex';
            }
            $(this).colorpicker(args);
        });

        // Handle all the slider fields
        $(".eo_field_slider").on("slide", function(slideEvt) {
            $(this).closest(".eo_field_slider_wrapper").find('.eo_field_slider_value').val(slideEvt.value);
        });

        //Init datepicker in settings fields
        $('.eo_field_datepicker').datepicker({
            //TODO This have to be filler by the WordPress dateformat + a php filter
            dateFormat: 'yy-mm-dd'
        });

        $.eoSettingsManager();

    });

    // Tooltips :
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // Links :
    $('.eo_admin_nav_item').eonetLink({ color: '#57a8b1'});

    // Loader :
    $('.has_loader_colored').eonetLoader({'colored': true});
    $('.has_loader').eonetLoader();
})(jQuery);