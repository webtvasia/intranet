/**
 * EONET UI
 * @made by Alkaweb
 * @version 1.0.0
 */
(function ($) {
    /**
     * Form Upload Field using WordPress Uploader library.
     * It's used on both frontend and backend with Eonet
     * We have both the trigger and the function that creates the bridge
     * @link https://code.tutsplus.com/tutorials/getting-started-with-the-wordpress-media-uploader--cms-22011
     */
    $.eonetMedia = function(){
        var inst = this;
        // We get the wrapper
        var eoMediaWrapper = $('.eo_field_upload_wrapper');
        // We handle the upload
        inst.eoMediaUploader = function(input_name) {
            'use strict';
            if(typeof inst.currentUploader == 'undefined'){
                return;
            }
            var file_frame, image_data;
            if ( undefined !== file_frame ) {
                file_frame.open();
                return;
            }
            file_frame = wp.media.frames.file_frame = wp.media({
                frame:    'post',
                state:    'insert',
                multiple: false
            });
            file_frame.on( 'insert', function() {

                // Get media attachment details from the frame state
                var attachment = file_frame.state().get('selection').first().toJSON();

                // If there is already an image, then we remove it :
                if(inst.currentUploader.find('.eo_field_upload_preview').length != 0){
                    inst.currentUploader.find('.eo_field_upload_preview').remove();
                }

                // Send the attachment URL to our custom image input field.
                var markup = '<div class="eo_field_upload_preview">\
                    <div class="eo_field_upload_single_file">\
                        <a href="javascript:void(0);" class="eo_field_upload_single_delete"><i class="fa fa-times"></i></a>\
                        <img src="'+attachment.url+'">\
                    </div>\
                </div>';

                // The val :
                inst.currentUploader.find('#'+input_name).val(attachment.id);


                // Append the Markup
                inst.currentUploader.prepend(markup);

                // Handle delete cases :
                inst.eoMediaDelete();

            });
            file_frame.open();
        };
        // we handle the image delete
        inst.eoMediaDelete = function () {
            if(typeof inst.currentUploader == 'undefined'){
                return;
            }
            inst.currentUploader.find( '.eo_field_upload_single_delete' ).on( 'click', function( event ) {
                // We delete the live preview :
                event.preventDefault();
                inst.currentUploader.find('.eo_field_upload_preview').fadeOut();
                setTimeout(function () {
                    inst.currentUploader.find('.eo_field_upload_preview').remove();
                }, 500);
                // We reset the val() of the input :
                inst.currentUploader.find('input[type="hidden"]').val('');
            });


        };

        inst.init = function() {

            // We start the delete method :
            inst.eoMediaDelete();

            // We listen for the trigger :
            eoMediaWrapper.find( '.eo_btn_upload_wp' ).on( 'click', function( event ) {
                event.preventDefault();
                inst.eoMediaUploader($(this).data('eo-name'));
                inst.currentUploader = $(this).closest('.eo_field_upload_wrapper');
            });
        };

        inst.init();


    };
    $(document).ready(function() {
        $.eonetMedia();
    });

    /**
     * Form tag option
     * We create an input where we can tag elemens according
     * to a certain population
     */
    $.eonetTags = function(){

        var inst = this;

        // We get the wrapper
        var eoTagsWrapper = $('.eo_field_tags_wrapper'),
            eoTagsInputName = eoTagsWrapper.data('eo-input-name'),
            eoTagsData = eoTagsWrapper.data('eo-data'),
            eoTagsTyper = eoTagsWrapper.find('input.eo_field_tags_typer');

        inst.eoTags = [];

        // We add a new tag to the list
        inst.eoTagsAdd = function(val){
            var eoNewTagMarkup = '<li class="eo_single_tag">\ ' +
                '<input type="hidden" name="eo_field_'+eoTagsInputName+'[]" value="'+val+'">'
                +val+'' +
                '<span class="eo_single_tag_close"><i class="fa fa-times"></i></span>' +
                '</li>';
            $(eoNewTagMarkup).insertBefore(eoTagsTyper);
            inst.eoTags.push(val);
            eoTagsTyper.val('');
        };

        // We delete the tag from the list
        inst.eoTagsDelete = function (eoTagEl) {
            // We remove it from the array :
            var val = eoTagEl.find('input').val();
            var vaIndex = inst.eoTags.indexOf(val);
            if (vaIndex > -1) {
                inst.eoTags.splice(vaIndex, 1);
            }
            // We remove from the HTML :
            eoTagEl.fadeOut();
            setTimeout(function () {
                eoTagEl.remove();
            }, 400);
        };

        // We validate whether a tag can be added or not :
        inst.eoTagsValidate = function (val) {

            // If empty :
            if(val == ''){
                return false;
            }
            // If it's not defined :
            else if(typeof val == "undefined"){
                return false;
            }
            // If it already exists :
            else if(inst.eoTags.indexOf(val) != -1){
                return false;
            }
            else {
                return true;
            }

        };

        // We search to match the val from our population of terms
        inst.eoTagsFetcher = function (val) {
            // We make sure to have some data and it's not empty :
            if(typeof eoTagsData == 'undefined' || eoTagsData.length == 0 || val == '') {
                if(eoTagsWrapper.find('.eo_autocomplete_list')) {
                    eoTagsWrapper.find('.eo_autocomplete_list').remove();
                }
                return;
            }
            // We go for it :
            var matches = [];
            eoTagsData.forEach(function (term) {
                if(term.toLowerCase().search(val.toLowerCase()) != -1){
                    matches.push(term);
                }
            });
            // If Match :
            if(matches.length != 0) {
                if(eoTagsWrapper.find('.eo_autocomplete_list')) {
                    eoTagsWrapper.find('.eo_autocomplete_list').remove();
                }
                // We add the markup :
                var eoSuggestions = '';
                matches.forEach(function (match) {
                    if(inst.eoTagsValidate(match)) {
                        eoSuggestions += '<li><a href="#">' + match + '</a></li>';
                    }
                });
                var eoSuggestionsMarkup = '<ul class="eo_autocomplete_list">'+eoSuggestions+'</ul>';
                eoTagsTyper.parent().addClass('has-suggestions');
                eoTagsTyper.parent().append(eoSuggestionsMarkup);
                var eoSuggestionsEl = eoTagsWrapper.find('.eo_autocomplete_list')
                eoSuggestionsEl.hide();
                setTimeout(function () {
                    eoSuggestionsEl.fadeIn();
                }, 500);
                // We handle the click :
                eoSuggestionsEl.find('a').on('click', function (e) {
                    e.preventDefault();
                    inst.eoTagsAdd($(this).text());
                    eoSuggestionsEl.fadeOut();
                    setTimeout(function () {
                        eoSuggestionsEl.remove();
                    }, 400);
                });
            }
        };

        // Let's start it :
        inst.init = function() {

            // On enter space :
            $(document).keypress(function(e) {
                if(e.which == 13 && inst.eoTagsValidate(eoTagsTyper.val())) {
                    inst.eoTagsAdd(eoTagsTyper.val());
                }
            });

            // Population fetcher :
            eoTagsTyper.on('input', function () {
                inst.eoTagsFetcher(eoTagsTyper.val());
            });

            // On delete click :
            eoTagsWrapper.on('click', '.eo_single_tag_close', function () {
                var tagEl = $(this).closest('li.eo_single_tag');
                inst.eoTagsDelete(tagEl);
            });

        };

        this.init();

    };
    $.eonetTags();

    // Links :
    $.fn.eonetLink = function (options) {

        // Establish our default settings
        var settings = $.extend({
            color: '#FFF',
        }, options);

        var ink, d, x, y;
        $(this).on("click", function (e) {
            var $this = $(this);

            $(this).addClass('eonet_btn_material');
            setTimeout(function(){
                $this.removeClass('eonet_btn_material')
            },650);
            
            if ($(this).find(".eonet_material").length === 0) {
                $(this).prepend('<span class="eonet_material" style="background: ' + settings.color + ';"></span>');
            }
            ink = $(this).find(".eonet_material");
            ink.removeClass("is-animated");
            if (!ink.height() && !ink.width()) {
                d = Math.max($(this).outerWidth(), $(this).outerHeight());
                ink.css({height: d, width: d});
            }
            x = e.pageX - $(this).offset().left - ink.width() / 2;
            y = e.pageY - $(this).offset().top - ink.height() / 2;
            ink.css({top: y + 'px', left: x + 'px'}).addClass("is-animated");
        });
    }
    // http://stackoverflow.com/questions/19491336/get-url-parameter-jquery-or-how-to-get-query-string-values-in-js :
    $.eonetGetUrlParam = function (sParam){
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    }
    // Forms Frontend :
    $.eonetForms = function () {
        /**
         * Select form, class switcher :
         */
        $('.eo_field_select_wrapper').on('click', function () {
            $(this).toggleClass('is-active');
            $('.eo_field_select_wrapper select').on('change', function () {
                $(this).closest('.eo_field_select_wrapper').toggleClass('is-active');
            });
        });
    };
    $.eonetForms();
    /**
     * Eonet Notifications function
     * @param icon string: an Ionicon class
     * @param title string
     * @param content string
     * @param fadeoout int : optional
     */
    $.eonetNotification = function (icon, title, content, fadeoout) {

        var wrapper = $('body');
        var inst = this;

        fadeoout = fadeoout || 5000;

        inst.add = function () {
            // If already existing :
            if (wrapper.find(".eonet_alert")) wrapper.find(".eonet_alert").remove();
            // We build the HTML :
            var html = '<div class="eonet_alert">\
                <div class="eonet_alert_wrapper">\
                    <div class="eonet_alert_icon"><i class="fa ' + icon + '"></i></div>\
                    <div class="eonet_alert_content">\
                        <h4>' + title + '</h4>\
                        <p>' + content + '</p>\
                    </div>\
                    <a href="javascript:void(0);" class="eonet_alert_close"><i class="fa fa-times"></i></a>\
                </div>\
            </div>';
            // We add it :
            wrapper.append(html);
            // We animate it :
            $('.eonet_alert').addClass('eonet_alert_on');
        };

        // We remove it :
        inst.removeIt = function () {
            $('.eonet_alert').removeClass('eonet_alert_on');
            setTimeout(function () {
                $('.eonet_alert').remove();
            }, 400);
        };

        inst.init = function () {

            // We create it :
            this.add();

            // On remove click
            $('.eonet_alert_close').on('click', function () {
                inst.removeIt();
            });

            // We wait X secs :
            setTimeout(function () {
                inst.removeIt();
            }, fadeoout);

        };

        inst.init();

    };
    // Modal :
    $.eonetModal = function() {
        var wrapper = $('body');
        var modalObj = this;
        // We create it :
        modalObj.create = function() {
            var modalHtml = '<div class="eo_modal" id="eonet_modal" tabindex="-1"><div class="eo_modal_dialog" role="document"></div></div>';
            wrapper.append(modalHtml);
            var modalBg = '<div class="eo_modal_backdrop"></div>';
            wrapper.append(modalBg);
        };
        // Must be called after the init :
        modalObj.show = function () {
            if (wrapper.find("#eonet_modal")) {
                wrapper.find("#eonet_modal").addClass('is-in');
                wrapper.find(".eo_modal_backdrop").addClass('is-in');
                wrapper.addClass('eo_modal_open');
            }
        };
        // We remove the modal
        modalObj.kill = function () {
            wrapper.find("#eonet_modal").removeClass('is-fired');
            setTimeout(function () {
                wrapper.find("#eonet_modal").remove();
                wrapper.removeClass('eo_modal_open');
                wrapper.find('.eo_modal_backdrop').remove();
            }, 300);
        };
        // We populate the content :
        modalObj.feed = function(html) {
            var modal = wrapper.find("#eonet_modal");
            if(modal.length != 0) {
                modal.find('.eo_modal_dialog').append(html);
                modal.addClass('is-fired');
                $('.eo_close, .eo_modal_backdrop').on('click', function () {
                    setTimeout(function () {
                        modalObj.kill();
                    }, 300);
                });
            }
        };
        return this;
    };
    // Loader :
    $.fn.eonetLoader = function (options) {
        // Establish our default settings
        var settings = $.extend({
            colored: false, // bool
        }, options);
        if (settings.colored) {
            var classColor = 'is-colored';
            if (!$(this).hasClass('has_loader_colored')) $(this).addClass('has_loader_colored');
        } else {
            var classColor = 'not-colored';
            if (!$(this).hasClass('has_loader')) $(this).addClass('has_loader');
        }
        var html = '<div class="eonet_loader ' + classColor + '"><div class="loader-inner ball-pulse-sync"><div></div><div></div><div></div></div></div>';
        $(this).append(html);
    };
})(jQuery);