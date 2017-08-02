/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* PROJECTS 
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
(function($) {
    "use strict";

    /**
     * Handle the autocomplete members for every input field in the page with the right HTML layout and attributes
     */
    $.wofficeMembersAutoompleteWatcher = function () {
        var $body = $( 'body' ),
            inst = this;

        inst.init = function() {
            inst.keyDownWatcher();

            inst.removeMemberWatcher();
        };

        /**
         * Listen the keydown of the fields and fetch the suggested members
         */
        inst.keyDownWatcher = function() {

            $body.on('keydown.autocomplete', '.woffice-users-suggest_input', function() {
                
                //console.log('Triggered autocomplete members fetcher');
                $(this).autocomplete({
                    source: ajaxurl + '?action=woffice_members_suggestion_autocomplete',
                    delay: 500,
                    minLength: 2,
                    position: ( 'undefined' !== typeof isRtl && isRtl ) ? {
                        my: 'right top',
                        at: 'right bottom'
                    } : {
                        my: 'left top',
                        at: 'left bottom'
                    },
                    open: function () {
                        $(this).addClass('open');
                    },
                    close: function () {
                        $(this).removeClass('open');
                        $(this).val('');
                    },
                    select: function (event, ui) {
                        inst.add_member_to_list(event, ui);
                    }
                });
            });
        };

        /**
         *  Remove a member on 'x' click
         */
        inst.removeMemberWatcher = function() {
            $body.on( 'click', '.woffice-users-suggest_members-list .woffice-users-suggest_remove-member', function( e ) {
                e.preventDefault();

                var $wrapper = $(e.target).closest('.woffice-users-suggest'),
                    id_removed = $(e.target).closest('li').data('id'),
                    members_excluded = $wrapper.data( 'members-excluded' );

                // members_excluded = (!members_excluded.trim()) ? [] : members_excluded.split(',');

                //Remove the item
                $( $(e.target).closest('li') ).remove();

                // if( $.inArray( id_removed, members_excluded)) {
                //     //members_excluded.splice($.inArray(id_removed, members_excluded), 1);
                //     members_excluded = jQuery.grep(members_excluded, function( n, i ) {
                //         return ( parseInt(n) !== id_removed  );
                //     });
                //     $wrapper.attr( 'data-members-excluded', members_excluded);
                // }

                //Update the hidden field containing all ids
                var users_to_add = [];
                $wrapper.find('.woffice-users-suggest_members-list li').each( function() {
                    users_to_add.push( $(this).data('id' ) );
                } );
                $wrapper.find('.woffice-users-suggest_members-ids').first().val('').val(users_to_add);

            } );
        };

        /**
         * Add the id of the member to an hidden input field
         *
         * @param e
         * @param ui
         */
        inst.add_member_to_list = function( e, ui ) {
            //Add the user to the visible list
            var $wrapper = $(e.target).closest('div');

            $wrapper.find('.woffice-users-suggest_members-list').first().append('<li data-id="' + ui.item.value + '"><a href="javascript:void(0)" class="woffice-users-suggest_remove-member"><i class="fa fa-times"></i></a> ' + ui.item.label + '</li>');

            //Add the id of the member to an hidden input
            var members_added = $wrapper.find('.woffice-users-suggest_members-ids').val(),
                members_excluded = $wrapper.data( 'members-excluded' );

            members_added = (!members_added.trim()) ? [] : members_added.split(',');
            //members_excluded = (!members_excluded.trim()) ? [] : members_excluded.split(',');

            members_added.push(parseInt(ui.item.value));
            //members_excluded.push(parseInt(ui.item.value));

            $wrapper.find('.woffice-users-suggest_members-ids').first().val( members_added );
            //$wrapper.attr( 'data-members-excluded', members_excluded);
        };

        inst.init();
    };

    /* 
     * The to-do JS here
     */
    // THE CHECKBOX ACTIONS
    $('.woffice-task header label input').each(function(){
    	var Checkbox = $(this);
	    if (Checkbox.is(':checked')) {
		    Checkbox.closest('.woffice-task').addClass('is-done');
	    }
	    
	});
	
	// THE NOTE TOGGLE
	$(".woffice-task .todo-note").hide();
	$("#woffice-project-todo").on('click', '.woffice-task header i.fa.fa-file-text-o', function(){
		var Task = $(this).closest('.woffice-task');
	    Task.find('.todo-note').slideToggle();
	    Task.toggleClass('unfolded');
	});
	
    // NAVAIGATION ACTIVE CLASS
    $('#project-nav ul').on('click', 'li', function(){
	    $('#project-nav ul li').removeClass('active');
	    $(this).addClass('active');
	    
		$("#right-sidebar").mCustomScrollbar("update");
	    
	});

	//DATEPICKER :
	$('.row .datepicker').datepicker({
	    format: 'dd-mm-yyyy',
	    todayHighlight: true,
        clearBtn: true,
        orientation: 'bottom'
	});
	
	/* 
     * The layout JS here
     */
    // INITIATE
	$("#project-content-edit, #project-content-comments, #project-content-todo, #project-content-files, #project-loader").hide();
    
    // REDIRECT ON PAGE RELOAD
    var hash = location.hash.replace('#', '');
    var search = location.search.replace('?', '');
    if(search === 'mv_add_file=1'){
    	$("#project-tab-files").addClass("active");
		$('#project-content-files').show();
		$('#project-tab-view').removeClass('active');
		$("#project-content-view").hide();
    }
    else {
	    if (hash !== '') {
	    	$('#'+hash).show();
	    	if (hash !== 'project-content-view') {
	    		$('#project-tab-view').removeClass('active');
				$("#project-content-view").hide();
		    	if (hash === 'project-content-edit') {
				    $("#project-tab-edit").addClass("active");
				} else if (hash === 'project-content-todo') {
				    $("#project-tab-todo").addClass("active");
				} else if (hash === 'project-content-files') {
				    $("#project-tab-files").addClass("active");
				} else {
				    $("#project-tab-comments").addClass("active");
					$("#project-content-comments").show();
				}
			}
	    } 
	}
    
    // VIEW 
    $("#project-tab-view a").on('click', function(){
    	var loader = new Woffice.loader($('.project-tabs-wrapper'));
		$("#project-content-edit, #project-content-comments, #project-content-files, #project-content-todo").hide();
    	function show_project_view() {
	    	$("#project-content-view").show();
			loader.remove();
		}
		setTimeout(show_project_view, 1000);
	});


    // EDIT
    $("#project-tab-edit a").on('click', function(){
    	var loader = new Woffice.loader($('.project-tabs-wrapper'));
	    $("#project-content-view, #project-content-comments, #project-content-files, #project-content-todo").hide();
	    function show_project_edit() {
	    	$("#project-content-edit").show();
			loader.remove();
		}
		setTimeout(show_project_edit, 1000);
	});

    // TO-DO
    $("#project-tab-todo a").on('click', function(){
    	var loader = new Woffice.loader($('.project-tabs-wrapper'));
	    $("#project-content-view, #project-content-edit, #project-content-comments, #project-content-files").hide();
	    function show_project_todo() {
	    	$("#project-content-todo").show();
			loader.remove();
		}
		setTimeout(show_project_todo, 1000);
	});

    // FILES
    $("#project-tab-files a").on('click', function(){
    	var loader = new Woffice.loader($('.project-tabs-wrapper'));
	    $("#project-content-view, #project-content-edit, #project-content-comments, #project-content-todo").hide();
	    function show_project_files() {
	    	$("#project-content-files").show();
			loader.remove();
		}
		setTimeout(show_project_files, 1000);
	});

    // COMMENTS
    $("#project-tab-comments a").on('click', function(){
    	var loader = new Woffice.loader($('.project-tabs-wrapper'));
	    $("#project-content-view, #project-content-edit, #project-content-files, #project-content-todo").hide();
	    function show_project_comments() {
	    	$("#project-content-comments").show();
			loader.remove();
		}
		setTimeout(show_project_comments, 1000);
	});

	// CHANGE BUDDYPRESS LINK EFFECT
	$( "#project-nav .item-list-tabs #project-tab-delete a").unbind( "click" );

	// CREATE NEW PROJECT
    $("#project-create").hide();
	$("#show-project-create").on('click', function(){
    	var loader = new Woffice.loader($('.frontend-wrapper'));
	    $("#projects-list, #projects-bottom, .blog-next-page").hide();
	    function show_create_project() {
	    	$("#project-create").show();
			loader.remove();
		}
		setTimeout(show_create_project, 1000);
	});
	$("#hide-project-create").on('click', function(){
    	var loader = new Woffice.loader($('.frontend-wrapper'));
	    $("#project-create").hide();
	    function hide_create_project() {
	    	$("#projects-list,#projects-bottom, .blog-next-page").show();
			loader.remove();
		}
		setTimeout(hide_create_project, 1000);	    
	});


	/*
	 * Fire on events
	 */
    $(document).ready( function() {

        $.wofficeMembersAutoompleteWatcher();

    });

})(jQuery);