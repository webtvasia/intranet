/**
 * Main Woffice object
 *
 * @since 2.5.0
 * @type {{}}
 */
var Woffice = {

    /**
	 * Initialize the Woffice's JS
	 *
	 * @param {jQuery} $
     */
    init: function ($) {

        "use strict";

    	var self = this;

    	self.$ = (typeof $ === 'undefined') ? jQuery : $;
    	self.data = (typeof WOFFICE !== 'undefined') ? WOFFICE : {};

        /*
         * Markup attributes
         */
        self.$body           = self.$('body');
        self.$main           = self.$("#main-content");
        self.$navigation     = self.$("#navigation");
        self.$navbar         = self.$('#navbar');
        self.$adminBar       = self.$('#wpadminbar');
        self.$searchTrigger  = self.$("#search-trigger");
        self.$mainSearch     = self.$("#main-search");
        self.$navWrapper     = self.$("#nav-languages").find("a");
        self.$navTrigger     = self.$("a#nav-trigger");
        self.$scrollHelper   = self.$('#can-scroll');
        self.$rightSidebar   = self.$("#right-sidebar");
        self.$sidebarTrigger = self.$("a#nav-sidebar-trigger");

        /*
		 * When the page is starting
         */
        self.$(window).load(function(){

            self.userSidebar.start();
            self.alerts.start();
            self.customScrollbars.start();
            self.wooCommerce.start();
            self.tooltips.start();
            self.frontend.start();
            self.sliders.start();
            self.animatedNumbers.start();
            self.footer.start();
            self.menu.start();
            self.masonryLayout.start();
            self.navigation.start();
            self.sidebar.start();

		});

        /*
         * When the page is re sized
         */
        self.$(window).resize(function () {

        	self.wooCommerce.resize();
        	self.menu.submenus();
            self.masonryLayout.refresh();
            self.navigation.resize();
            self.sidebar.resize();

        });

        /*
         * Watchers, binding events after page load
         */
        {
            self.userSidebar.watch();
            self.alerts.watch();
            self.searchBar.watch();
            self.wooCommerce.watch();
            self.languageSwitcher.watch();
            self.sliders.watch();
            self.buddyPress.watchOldActivities();
            self.scrollTop.watch();
            self.linkEffect.watch();
            self.masonryLayout.watch();
            self.navigation.watch();
            self.sidebar.watch();
            self.buddyPressNotifications.watch();
        }

    },

    /**
     * Creates a new loader
     *
     * @param {jQuery} $el
     * @param {object} newOptions
     * @return {Spinner}
     */
    loader: function ($el, newOptions) {

        var loader,
            self = this;

        self.isLoading = false;

        self.options = {
            color: '#757575',
            top: '50%',
            speed: 1.6
        };

        // Set default versions
        if(typeof newOptions !== 'undefined')
            Woffice.$.extend( self.options, newOptions );

        /**
         * Creates the loader
         */
        self.create = function () {

            // Loader object
            self.loader = new Spinner(self.options);
            self.loader.spin($el[0]);
            self.loader.element = $el;

            // Element change
            self.loader.element.addClass('has-loader');

            // Watcher variable
            self.isLoading = true;

        };

        /**
         * Removes a loader
         */
        self.remove = function () {

            // Element change
            self.loader.element.removeClass('has-loader');

            // Stop the spinner
            self.loader.stop();

            // Remove the loader
            self.loader = null;

            // Watcher variable
            self.isLoading = false;

        };

        self.create();

        return self;

    },

    /**
     * Main alerts
     */
    alerts: {

        timeout: 4000,

        start: function() {

            var $ = Woffice.$,
                self = this;

            var alerts = $('.woffice-main-alert');

            if(alerts.length === 0) {
                return;
            }

            alerts.each(function () {
                var closeTrigger = $(this).find('.woffice-alert-close');
                var alert = $(this);
                // We either close it teh button is clicked
                closeTrigger.on('click', function () {
                    alert.slideUp();
                });
                // Or after x sec
                setTimeout(function () {
                    alert.slideUp();
                }, self.timeout);
            });

        },

        watch: function () {

            var $ = Woffice.$;

            $('.woffice-alert-close ').on('click', function () {

                $(this).closest('.woffice-main-alert').slideUp();

            });

        }

    },

    /**
	 * User sidebar functions
     */
    userSidebar: {

        /**
		 * Set the global state on the page load
         */
    	start: function () {

    		var $ = Woffice.$,
			$userSidebar = $("#user-sidebar");

    		// Layout
            if($userSidebar.length >0 ){
                var topbarHeight = $("#topbar").height(),
                    menuHeight = $("#navbar").height(),
                    sidebarTop = 0;
                if($("topbar").hasClass("topbar-closed")){
                    sidebarTop = menuHeight + topbarHeight;
                }
                else{
                    sidebarTop = menuHeight;
                }
                $userSidebar.css("padding-top",sidebarTop);
            }

            // Binding events
            $("#user-sidebar nav ul li.menu-parent a, #user-sidebar #dropdown-user-menu li.menu-item-has-children a").bind('click', false);
            $("#user-sidebar nav ul li.menu-child a, #user-sidebar #dropdown-user-menu li.menu-item-has-children ul a").unbind('click', false);

        },

        /**
		 * Watch any toggling action
         */
        watch: function () {

            var $ = Woffice.$;

            // This is for the main layout: display the sidebar or not
            $(".bp_is_active #user-thumb, #user-close").on("click",function(){
                $("#nav-user, #user-sidebar").toggleClass("active");
            });

            // Submenus within the sidebar
            $("#user-sidebar nav ul li.menu-parent a, #user-sidebar #dropdown-user-menu li.menu-item-has-children a").on("click",function(){
                $(this).toggleClass("dropdownOn");
                $(this).parent("li").toggleClass("dropdownOn");
                $(this).parent("li").find('ul').slideToggle();
            });

        }

    },

    /**
	 * Custom scroll bars
     */
    customScrollbars: {

        /**
		 * Declares the scroll bars using mCustomScrollbar
         */
		start:  function () {

            var $ = Woffice.$;

            /*
             * Navigation scroll bars
             */
            $("body.menu-is-vertical #navigation").mCustomScrollbar({
                axis: "y",
                theme: "minimal"
            });
            if (window.matchMedia('(max-width: ' + Woffice.data.menu_threshold + 'px)').matches || Woffice.$body.hasClass('force-responsive')) {
                $("body.menu-is-horizontal #navigation").mCustomScrollbar({
                    axis: "y",
                    theme: "minimal"
                });
            }

            /*
             * User menu scroll bar
             */
            $("#user-sidebar").mCustomScrollbar({
                axis: "y",
                theme: "minimal-dark"
            });

            /*
             * WooCommerce box scroll bar
             */
            var $wooCommerceCart = $("#woffice-minicart-top");
            if ($wooCommerceCart.length) {
                $wooCommerceCart.mCustomScrollbar({
                    axis: "y",
                    theme: "minimal-dark"
                });
            }

            /*
             * Right sidebar's custom scroll bar
             * See self.sidebar() for more details
             */
            $("#right-sidebar").mCustomScrollbar({
                axis:"y",
                theme:"minimal-dark",
                mouseWheel:{ deltaFactor: 100 },
                callbacks:{
                    onInit:function(){
                        $('#can-scroll').on("click",function(){
                            if(!$('#main-content').hasClass('sidebar-hidden')){
                                $('#can-scroll').show();
                            }
                            if($(this).hasClass('clicked')){
                                $('#right-sidebar').mCustomScrollbar("scrollTo","top");
                                $(this).removeClass('clicked');
                            }
                            else{
                                $('#right-sidebar').mCustomScrollbar("scrollTo","bottom");
                                $(this).addClass('clicked');
                            }
                        });
                    },

                    onUpdate:function(){
                        if ($('#main-content').height() >= $('#right-sidebar')[0].scrollHeight){
                            $('#can-scroll').addClass('clicked');
                        } else {
                            $('#can-scroll').removeClass('clicked');
                        }
                    },
                    onScroll:function(){
                        if ($('#main-content').height() >= $('#right-sidebar')[0].scrollHeight){
                            $('#can-scroll').addClass('clicked');
                        } else {
                            $('#can-scroll').removeClass('clicked');
                        }
                    }
                }
            });

        }

    },

    /**
	 * Search bar
     */
    searchBar: {

        /**
		 * Whenever someone click on the icon
         */
    	watch: function() {

    		var $ = Woffice.$;

            Woffice.$searchTrigger.on("click",function(){

                Woffice.$mainSearch.slideToggle();
                Woffice.$mainSearch.toggleClass('opened');

                $('html,body').animate({ scrollTop: 0 }, 'fast');
                document.getElementById("s").focus();
                return false;

            });

		}

	},

    /**
	 * WooCommerce
     */
    wooCommerce : {

        flexsliderWoo: null,

        /**
		 * Watch for the cart's toggling
         */
    	watch: function () {

    		var $ = Woffice.$;

            $("#nav-buttons").on("click","#nav-cart-trigger.active", function(){
                $(this).toggleClass("clicked");
                $("#woffice-minicart-top").slideToggle();
            });

        },

        /**
		 * Get the number of items according to the width
         */
    	getGridSize: function () {

            return 	(window.innerWidth < 400) ? 1 :
                	(window.innerWidth < 600) ? 2 :
                    (window.innerWidth < 910) ? 3 : 4;

        },

        /**
		 * Starts the product carousels
         */
    	start: function () {

    		var $ = Woffice.$,
                self = this;

            $('.flexslider > .woocommerce').flexslider({
                animation: "slide",
                animationLoop: false,
                selector: ".products > li.product",
                itemWidth: 210,
                itemMargin: 0,
                controlNav: false,
                move: 0,
                slideshow: false,
                minItems: self.getGridSize(), // use function to pull in initial value
                maxItems: self.getGridSize(), // use function to pull in initial value
                start: function (slider) {
                    self.flexsliderWoo = slider; //Initializing flexslider here.
                }
            });

        },

        /**
		 * Changes the number of items according to the width
         */
        resize: function () {

        	var self = this,
				$ = Woffice.$;

            if ($('.flexslider > .woocommerce').length > 0) {
                var gridSize = self.getGridSize();
                if (self.flexsliderWoo !== null) {
                    self.flexsliderWoo.vars.minItems = gridSize;
                    self.flexsliderWoo.vars.maxItems = gridSize;
                }
            }

        }

	},

    /**
	 * Language switcher
     */
    languageSwitcher: {

    	watch: function () {

            Woffice.$navWrapper.find('a').on("click",function(){
                Woffice.$navWrapper.find('ul').slideToggle();
            });

        }

	},

	/**
	 * Bootstrap tooltips
	 */
	tooltips: {

    	start: function () {

    		var $ = Woffice.$;

            $('[data-toggle="tooltip"]').tooltip();

        }

	},

    /**
	 * Theme form actions and frontend actions
     */
	frontend: {

		checkboxes: function () {

            var $ = Woffice.$;

            $('#page-wrapper .wpcf7-checkbox input:checkbox,#page-wrapper .wpcf7-radio input:radio').change(function(){
                if($(this).is(":checked")) {
                    $(this).parent("label").addClass("checked");
                } else {
                    $(this).parent("label").removeClass("checked");
                }
            });

        },

		// Todo must be re-written using Spinner.js
		toggleEdit: function () {

            var $ = Woffice.$;

            if ($('#blog-create').length === 0)
            	return;

			$("#blog-create, #blog-loader").hide();
			$("#show-blog-create").on('click', function(){
				var loader = new Woffice.loader($('.frontend-wrapper'));
				$("#show-blog-create").hide();
				function show_create_blog() {
					$("#blog-create").show();
					loader.remove();
				}
				setTimeout(show_create_blog, 1000);
			});
			$("#hide-blog-create").on('click', function(){
				var loader = new Woffice.loader($('.frontend-wrapper'));
				$("#blog-create").hide();
				function hide_create_blog() {
					$("#show-blog-create").show();
					loader.remove();
				}
				setTimeout(hide_create_blog, 1000);
			});

        },

		toggleCreation: function () {

            var $ = Woffice.$;

            if ($('#blog-edit').length === 0)
                return;

			$("#blog-edit, #blog-loader").hide();
			$("#show-blog-edit").on('click', function(){
				var loader = new Woffice.loader($('.frontend-wrapper'));
				$("#show-blog-edit").hide();
				function show_edit_blog() {
					$("#blog-edit").show();
					loader.remove();
				}
				setTimeout(show_edit_blog, 1000);
			});
			$("#hide-blog-edit").on('click', function(){
				var loader = new Woffice.loader($('.frontend-wrapper'));
				$("#blog-edit").hide();
				function hide_edit_blog() {
					$("#show-blog-edit").show();
					loader.remove();
				}
				setTimeout(hide_edit_blog, 1000);
			});

        },

        initAutocompleteMap : function() {
            // Create the autocomplete object using Google Maps API, restricting the search to geographical location
            var input = document.getElementById(Woffice.data.input_location_bb);
            //we check that this input exist on the dom to avoid any error
            if(input === null) return;
            autocomplete = new google.maps.places.Autocomplete(
                (input),
                {types: ['geocode']}
            );

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        },

		start: function () {

			var self = this;

			self.checkboxes();
            self.toggleEdit();
            self.toggleCreation();
            self.initAutocompleteMap();

        }

	},

    /**
	 * Sliders
     */
    sliders: {

    	watch: function () {

    		var $ = Woffice.$,
                self = this;

            $("#dashboard .widget a, #nav-trigger, #nav-sidebar-trigger").on('click',function(){
                setTimeout(function () {
                    // Flexslider
                    $('#dashboard').find('.widget_woffice_funfacts .flexslider').flexslider();
                    // Rev slider
                    self.refreshRevSlider();
                }, 2000);
            });

        },

        /**
         * Refreshes Revolution slider on layout changes
         */
        refreshRevSlider: function () {

            var $ = Woffice.$,
                $slider = $(".rev_slider"),
                sliderOnPage = (typeof $slider.revredraw === 'function');

            if(sliderOnPage) {
                setTimeout(function () {
                    $slider.revredraw();
                }, 1000);
            }

        },

        /**
		 * Creates the fun fact and "familiers" sliders
		 * Using the Flexslider plugin
         * We also bind the revolution slider
         */
		start: function () {

            var $ = Woffice.$,
                self = this;

            $('.widget_woffice_funfacts .flexslider').flexslider({
                animation: "slide",
                animationLoop: true,
                slideshow: true,
                directionNav: false,
                selector: ".slides > li",
                smoothHeight: false,
                start: function(){
                    $('.widget_woffice_funfacts .flexslider').resize();
                }
            });

            $('#familiers').find('.flexslider').flexslider({
                animation: "slide",
                animationLoop: true,
                selector: ".slides > li",
                itemWidth: 80,
                itemMargin: 0,
                controlNav: false,
                directionNav: false,
                minItems: 0,
                move: 0,
                slideshow: false
            });

            self.refreshRevSlider();

        }

	},

    /**
	 * Animated numbers animation
     */
	animatedNumbers: {

    	start: function () {

    		var $ = Woffice.$;

            $('.animated-number h1').countTo({
                speed: 4000
            });

        }

	},

    /**
	 * BuddyPress customizations
     */
	buddyPress: {

        /**
         * It allows to display old activity comments, it fixes the compatibility issue between BuddyPress and Bootstrap
         */
		watchOldActivities: function() {

			var $ = Woffice.$;

            $('.activity-comments .show-all').on("click", function () {

                $(this).closest('.activity-comments').find('li').removeClass('hidden');

            });

        }

	},

    /**
	 * Scroll to the top action
     */
	scrollTop: {

		watch: function () {

            var $ = Woffice.$;

            $("#scroll-top").on("click",function(){
                //SCROLL TO TOP
                $('html, body').animate({
                    scrollTop: $( $.attr(this, 'href') ).offset().top
                }, 500);
                return false;
            });

        }

	},

    /**
	 * Touch devices helpers
     */
	touch: {

        /**
		 * Check whether the device is touchable or not
		 *
         * @return {boolean}
         */
        isTouchDevice: function() {
			return (('ontouchstart' in window)
			|| (navigator.MaxTouchPoints > 0)
			|| (navigator.msMaxTouchPoints > 0));
		},

        /**
		 * Check if Woffice must be reactive
		 * If so we apply a class to the body
         */
		checkForceResponsive: function() {

			if(
			    parseInt(Woffice.data.use_force_responsive) !== 1 &&
                this.isTouchDevice() &&
                window.matchMedia("(min-width: 993px)").matches
            )
				Woffice.$body.addClass("force-responsive");

		}


	},

    /**
	 * Material design inspired effect on links click
     */
    linkEffect: {

    	elements: [
			'#navbar a',
			'.main-menu li > a',
			'a.btn.btn-default',
			'#content-container #buddypress button',
			'#buddypress .button-nav li a,#main-content button[type="submit"]',
			'input[type="submit"]',
			'#user-sidebar nav ul li a',
			'#buddypress #item-nav div.item-list-tabs ul li a',
			'#woffice-login .login-submit input[type="submit"]',
			'#main-content input[type="button"],#learndash_next_prev_link a',
			'#content-container .ssfa_fileup_wrapper span'
		],

    	watch: function () {

    		var self = this,
				selector = self.elements.join(", "),
				$ = Woffice.$;

            var ink, d, x, y;

            $(selector).on("click",function(e){
				if($(this).find(".material").length === 0){
					$(this).prepend("<span class='material'></span>");
				}

				ink = $(this).find(".material");
				ink.removeClass("animate");

				if(!ink.height() && !ink.width()){
					d = Math.max($(this).outerWidth(), $(this).outerHeight());
					ink.css({height: d, width: d});
				}

				x = e.pageX - $(this).offset().left - ink.width()/2;
				y = e.pageY - $(this).offset().top - ink.height()/2;

				ink.css({top: y+'px', left: x+'px'}).addClass("animate");
			});

        }

	},

    /**
	 * Footer functions
     */
    footer: {

        /**
		 * Loads the extra footer once the page ready
         */
        loadExtafooterAvatars : function () {

            var $ = Woffice.$,
				$extrafooter = $('#extrafooter[data-woffice-ajax-load=true]');

            if ($extrafooter.length === 0)
            	return;

            var loader = new Woffice.loader($extrafooter.find('#familiers'), { left: '45%'});

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'load_extrafooter_avatars'
				},
				success: function (data) {

					$extrafooter.find('#familiers').html(data);

                    loader.remove();

				}
			});

        },

        /**
		 * Build the footer layout (widgets)
         */
 		footerWidgetsLayout : function() {

            var $ = Woffice.$;

            if (window.matchMedia("(min-width: 992px)").matches) {

                var $widgets_wrapper = $("#main-footer").find("#widgets"),
                    layout = $widgets_wrapper.attr('data-widgets-layout');

                if ($widgets_wrapper.length > 0) {

                    layout = layout.split("-");

                    //For each column assign the width depending on the selected layout
                    $widgets_wrapper.find(".widget").each(function (i) {
                        var layout_length = layout.length;

                        $(this).removeClass("col-md-3");
                        $(this).addClass("col-md-" + layout[i % layout_length]);
                    });

                }

            }

        },

        start: function () {

 			var self = this;

            self.loadExtafooterAvatars();
            self.footerWidgetsLayout();

        }

	},

    /**
	 * Menu
	 * including sub menus and mega menus
     */
    menu: {

    	megaMenuTimer: null,

        megaMenuCol: 200,

        edgeLimit: 0,

        /**
		 * Mega menu
         */
    	megaMenu: function () {

    		var self = this,
				$ = Woffice.$;

            if(!Woffice.$body.hasClass('menu-is-horizontal')) {

                setTimeout(function(){
                    $("#main-menu").find("li").each(function() {
                        if ($(this).hasClass("menu-item-has-mega-menu")){
                            var liheight = $(this).innerHeight();
                            var megamenu = $(this).find('div.mega-menu');
                            $(megamenu).css('margin-top', '-'+(liheight)+'px');
                        }
                    });
                }, 2500);

                self.megaMenuCol = 180;

            }

            $('.main-menu > li.menu-item-has-mega-menu').on({
                mouseenter: function(){

                    // COUNT THE NUMBER OF COLUMN
                    var megamenucontainer = $(this).find("div.mega-menu");
                    var numberofrows = megamenucontainer.find("li.mega-menu-col").length;

                    // SIZE -> 180 per rows
                    megamenucontainer.width(numberofrows*(self.megaMenuCol) + 20);
                    // SHOW IT

                    self.megaMenuTimer = setTimeout(function(){
                        megamenucontainer.addClass('open animated');
                    }, 0);
                    $(self).data('timeoutId', self.megaMenuTimer);

                },
                mouseleave: function(){

                    var megamenucontainer = $(this).find("div.mega-menu");

                    clearTimeout($(self).data('timeoutId'));

                    setTimeout(function(){
                        megamenucontainer.removeClass('open');
                    }, 0);

                }
            });

        },

        /**
         * Checks if the submenu elements is placed over the window limit
         * @param who
         */
        calculateEdge: function(who) {

			var $ = Woffice.$,
				self = this,
				elm = $(who).find('ul').first();

			if(elm.length) {

				var off = elm .offset(),
					l = off.left,
					w = elm.width(),
					isEntirelyVisible = (l+ w <= self.edgeLimit);

				if ( ! isEntirelyVisible ) {
					$(who).addClass('edge');
				} else {
					$(who).removeClass('edge');
				}

			}

		},

        /**
		 * Set the submenus
         */
		setSubMenus: function () {

			var $ = Woffice.$;

            $("#main-menu").find("li").each(function() {

                if ($(this).hasClass("menu-item-has-children")){

                    var lineheight = $(this).height();
                    var submenu = $(this).find('.sub-menu');
                    $(submenu).css('margin-top', '-'+(lineheight)+'px');

                }

            });

        },

        /**
		 * This function is run on the page load and resize
         */
		submenus: function () {

            var self = this,
                $ = Woffice.$;

            self.edgeLimit = $('#page-wrapper').width();

            if($(window).width() <= 992 || Woffice.$body.hasClass('force-responsive')) {

                $('.main-menu > li.menu-item-has-children > a:first-child').not(".binded").addClass("binded").on("click",function(){
                    $(this).toggleClass("mobile-menu-displayed");
                    var parentContainer = $(this).parent("li");
                    if(parentContainer.hasClass('menu-item-has-mega-menu')) {
                        parentContainer.find('> .mega-menu').slideToggle();
                    } else {
                        parentContainer.find('> .sub-menu').slideToggle();
                    }
                    return false;
                });

            }
            else {

                var timer;

                $('.main-menu li.menu-item-has-children:not(.menu-item-has-mega-menu)').on({

                    mouseenter: function(){
                        var self = this,
                            submenu = $(self).find('.sub-menu').first();

                        submenu.addClass("display-submenu");
                        submenu.attr('style', 'margin-top: ' + '-' + $(this).height() + 'px');

                        var scrollTop     = $(window).scrollTop(),
                            elementOffset = submenu.offset().top,
                            distanceFromTop      = (elementOffset - scrollTop),
                            megamenu_height = 0;

                        var edgeOffset = (distanceFromTop  + submenu.height() - $(window).height());

                        if( $(self).hasClass('mega-menu-col')) {

                            megamenu_height = Math.max.apply(null,$(self).closest('.mega-menu').find('.mega-menu-col').map(function () {
                                return $(this).height();
                            }).get());

                            edgeOffset = (distanceFromTop  + megamenu_height - $(window).height());
                        }

                        var isEntirelyVisible = (edgeOffset < 0);

                        if(!isEntirelyVisible) {
                            if( $(self).hasClass('mega-menu-col')) {

                                //$(self).closest('.mega-menu')[0].style.removeProperty('margin-top');
                                $(self).closest('.mega-menu')[0].style.setProperty( 'margin-top', '-' + parseInt( megamenu_height ) + 'px', 'important' );
                            } else {

                                submenu.attr('style', 'margin-top: ' + '-' +parseInt($(self).height() + edgeOffset) + 'px !important');
                            }
                        }

                        $(self).data('timeoutId', timer);
                        Woffice.menu.calculateEdge(this);

                    },

                    mouseleave: function(){

                        var self = this;
                        var submenu = $(self).find('> .sub-menu');

                        clearTimeout($(self).data('timeoutId'));
                        submenu.removeClass("display-submenu");
                        $(self).removeClass('edge');
                    }

                });

            }

        },

		start: function () {

		    var self = this,
                $ = Woffice.$;

            self.edgeLimit = $('#page-wrapper').width();
            self.megaMenu();
            self.setSubMenus();
            self.submenus();

        }

	},

    /**
     * Masonry layouts used across the theme
     */
    masonryLayout: {

        /**
         * Build helper the Masonry layout
         */
        build: function () {

            var $ = Woffice.$,
                $dashboard = $('#dashboard');

            $('.masonry-layout').isotope({
                // options
                itemSelector: '.box',
                layoutMode: 'masonry'
            });

            if( $dashboard.length > 0 ) {
                if(!$dashboard.hasClass('is-draggie')) {
                    $dashboard.isotope();
                }
                /*
                 * Commented from WOF-92.
                 * It's causing the jQuery memory issue.
                 *
                 * $dashboard.find( '.widget_woffice_funfacts .flexslider' ).resize();
                 */
            }

        },

        /**
         * Refresh layout
         */
        refresh: function() {

            var $ = Woffice.$;

            Woffice.masonryLayout.build();

            // fix ratios for resizing the calendar size
            $('.eventon_fullcal').each(function(){
                var cal_width = $(this).width();
                var strip = $(this).find('.evofc_months_strip');
                var multiplier = strip.attr('data-multiplier');

                if(multiplier<0){
                    strip.width(cal_width*3).css({'margin-left':(multiplier*cal_width)+'px'});
                }
                $(this).find('.evofc_month').width(cal_width);
            });

        },

        /**
         * We watch several events
         */
        watch: function () {

            var self = this,
                $ = Woffice.$;

            $("#dashboard").on('click', 'a.evcal_list_a, .widget a, p.evo_fc_day', function(){
                self.refresh();
            });

            $("#nav-trigger, #nav-sidebar-trigger").on('click', function(){
                setTimeout(self.refresh, 600);
            });

        },

        /**
         * Starts the layouts
         */
        start: function () {

            var $ = Woffice.$,
                $list = null;

            setTimeout(function () {
                $list = $('#groups-dir-list #groups-list, #members-dir-list #members-list').isotope({
                    // options
                    itemSelector: 'li',
                    layoutMode: 'fitRows'
                });
            }, 1000);

            $("#nav-trigger, #nav-sidebar-trigger").on('click',function(){
                setTimeout(function () {
                    $list.isotope();
                }, 1000);
            });

            setTimeout(function () {
                Woffice.masonryLayout.build();
            }, 200);

            setInterval(function(){
                Woffice.masonryLayout.build();
                Woffice.masonryLayout.refresh();
            }, 2000);

        }

    },

    /**
     * Handles the navigation actions
     */
    navigation: {

        watch: function () {

            var $ = Woffice.$,
                self = this;

            Woffice.$navTrigger.on("click",function() {
                if ($("#main-content").hasClass("navigation-hidden")){
                    self.showVerticalMenu(true);
                }
                else {
                    self.hideVerticalMenu(true);
                }
                Woffice.sidebar.setSidebarWidth();
            });

        },

        hideVerticalMenu: function(handle_cookie) {

            var $ = Woffice.$;

            // Icon class switching
            Woffice.$navTrigger.find("i").addClass("fa-bars");
            Woffice.$navTrigger.find("i").removeClass("fa-long-arrow-left");

            $("body, #navigation, #main-content, #main-header, #main-footer").addClass("navigation-hidden");

            if(handle_cookie) {
                // Create cookies to save user choice :
                Cookies.set('Woffice_nav_position', 'navigation-hidden', { expires: 7, path: '/' });
                Cookies.set('Woffice_nav_position_icon', 'fa-bars', { expires: 7, path: '/' });
            }

            // Rebuild the sliders
            Woffice.sliders.start();

        },

        showVerticalMenu: function(handle_cookie) {

            var $ = Woffice.$;

            // Icon class switching
            Woffice.$navTrigger.find("i").removeClass("fa-bars");
            Woffice.$navTrigger.find("i").addClass("fa-long-arrow-left");

            if($(window).width() <= 450 && !Woffice.$navbar.hasClass('has_fixed_navbar') && Woffice.$body.hasClass("menu-is-vertical")) {
                Woffice.$navigation.css('top',  Woffice.$adminBar.height() + Woffice.$navbar.height() - $(window).scrollTop());
            }

            $("body,#navigation, #main-content, #main-header, #main-footer").removeClass("navigation-hidden");

            if(handle_cookie) {
                // ERASE COOKIES
                Cookies.remove('Woffice_nav_position', {expires: 7, path: '/'});
                Cookies.remove('Woffice_nav_position_icon', {expires: 7, path: '/'});
            }

        },

        responsiveMenu: function(onready) {

            var self = this,
                $ = Woffice.$,
                $mainMenu = $('.main-menu');

            if (window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches || Woffice.$body.hasClass('force-responsive')) {

                if(onready)
                    self.hideVerticalMenu();

                // We create a duplicate of the link
                if (!$mainMenu.hasClass("menu-loop-happened")){
                    $('.main-menu > li.menu-item-has-children').each(function(){
                        var linkElement = $(this).find("> a");
                        var submenuContainer = $(this).find("> ul.sub-menu");
                        var linkElement_href = linkElement.attr('href');

                        if(linkElement_href !== '#' && linkElement_href !== 'javascript:void(0)') {
                            if($(this).hasClass('menu-item-has-mega-menu')) {
                                submenuContainer = $(this).find(".mega-menu .sub-menu:first-child");
                            }
                            var subElement = '<li class="menu-item mobile-submenu-link"><a href="'+linkElement_href+'" class="center"><i class="fa fa-arrow-right"></i></a></li>';
                            submenuContainer.prepend(subElement);
                        }
                    });
                }

                $mainMenu.addClass("menu-loop-happened");

            } else {

                $('.main-menu > li.menu-item-has-children').each(function(){
                    $(this).find(".mobile-submenu-link").remove();
                });
                if (!$("#page-wrapper").hasClass("menu-is-closed")) {
                    if(!Woffice.$body.hasClass('menu-is-horizontal')) {
                        self.showVerticalMenu();
                    }
                }
            }

            if(Woffice.$body.hasClass('menu-is-horizontal')) {
                /**
                 * Unused variables
                 *
                 * @deprecated 2.5.0
                 * @type {number}
                 */
                // var nav_height = (Woffice.$navbar.length > 0) ? Woffice.$navbar.height() : 0;
                // var adminbar_height = (Woffice.$adminBar.length > 0) ? Woffice.$adminBar.height() : 0;
            }

        },

        /**
         * Set the menu layout
         */
        setNavLayout: function () {

            var $ = Woffice.$;

            if (!window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches && !Woffice.$body.hasClass('force-responsive'))
                return;

            var buttonsWidth = $('#navbar #nav-buttons').width();

            $('#navbar #nav-left').css('margin-right', buttonsWidth + 'px');

        },

        start: function () {

            var self = this;

            self.responsiveMenu(true);
            self.setNavLayout();

            //Fix the menu display on load
            setTimeout(function(){
                Woffice.$navigation.removeClass("mobile-hidden");
            }, 600)

        },

        resize: function () {

            var self = this;

            self.responsiveMenu(false);
            self.setNavLayout();

        }

    },

    /**
     * Sidebar actions
     */
    sidebar: {

        start: function () {

            var self = this,
                $ = Woffice.$;

            // Setting up the layout correctly
            self.setSidebarWidth();
            self.responsiveSidebar(true);
            Woffice.touch.checkForceResponsive();
            self.horizontalMenuAuto();

            if(Woffice.$rightSidebar.length === 0 || Woffice.$main.hasClass('sidebar-hidden'))
                Woffice.$body.addClass('sidebar-hidden');

            Woffice.$scrollHelper.hide();

            if(Woffice.$rightSidebar.length > 0){
                if(Woffice.$main.hasClass('sidebar-hidden')){
                    Woffice.$scrollHelper.fadeOut();
                }
                else{
                    if (Woffice.$main.height() < Woffice.$rightSidebar[0].scrollHeight){
                        Woffice.$scrollHelper.fadeIn('slow');
                    }
                }
            }

            // If the Cookies already exist
            if(Cookies.get('Woffice_sidebar_position')){

                Woffice.$sidebarTrigger.addClass("sidebar-hidden");

                // Main Layout changes
                $("#main-content, #main-header, body").addClass("sidebar-hidden");

                // Icon Class
                Woffice.$sidebarTrigger.find('i').addClass("fa-long-arrow-left");
                Woffice.$sidebarTrigger.find('i').removeClass("fa-long-arrow-right");

                Woffice.$scrollHelper.fadeOut();

                // Rebuild the sliders
                Woffice.sliders.start();

            }
            // For the default position
            if (!self.isOpen()){
                Woffice.$sidebarTrigger.find('i').addClass("fa-long-arrow-left");
                Woffice.$sidebarTrigger.find('i').removeClass("fa-long-arrow-right");
            }

        },

        resize: function () {

            var self = this;

            self.setSidebarWidth();
            self.responsiveSidebar(false);

            Woffice.touch.checkForceResponsive();

        },

        watch: function () {

            var self = this,
                $ = Woffice.$;

            $("#nav-sidebar-trigger").on("click",function() {

                self.sidebarToggling();

            });

        },

        /**
         * Calculates the sidebar's width
         */
        setSidebarWidth: function() {

            var $ = Woffice.$,
                $rightSidebar = $("#right-sidebar"),
                SidebarWidth;

            if($rightSidebar.length === 0 )
                return;

            // Right sidebar width
            var windowWidth = $(window).width();

            if ($("#main-content").hasClass("navigation-hidden") || Woffice.$body.hasClass("menu-is-horizontal")){
                SidebarWidth = (.25 * windowWidth);
            }
            else{
                SidebarWidth = (.25 * windowWidth) - 23;
            }

            $rightSidebar.width(SidebarWidth);
            $('#can-scroll').width(SidebarWidth);

        },

        /**
         * Makes the sidebar responsive (and the menu)
         *
         * // Todo @antonio I'm lost in those lines, any comments would be appreciated :)
         * // It was in the sidebar block but it's related to the menu right?
         *
         * @param {boolean} on_load if this is during the page loading or the resize event
         */
        responsiveSidebar: function (on_load) {

            var $ = Woffice.$,
                width = $(window).width(),
                height = $(window).height();

            var $horizontalMenuWrapper = $("#horizontal-menu-trigger-container");

            var switcher = $("#horizontal-menu-trigger").length;

            if (window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches || Woffice.$body.hasClass('force-responsive')) {
                // Horizontal menu responsive
                if (Woffice.$body.hasClass("menu-is-horizontal")) {

                    Woffice.$navigation.addClass("menu-responsive-horizontal");

                    if (!switcher && $('#horizontal-menu-trigger-container').length === 0) {
                        Woffice.$navigation.find("ul.main-menu").prepend('<li id="horizontal-menu-trigger-container"><a href="#" id="horizontal-menu-trigger"><i class="fa fa-bars"></i></a></li>');
                    }

                }
            }
            else {
                if ($horizontalMenuWrapper.length > 0) {
                    $horizontalMenuWrapper.remove();
                }
            }

            // Don't fire on mobile :
            if($(window).width() !== width && $(window).height() !== height || on_load) {
                if (window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches || Woffice.$body.hasClass('force-responsive')) {

                    // Icon class switching
                    Woffice.$sidebarTrigger.find("i").addClass("fa-long-arrow-left");
                    Woffice.$sidebarTrigger.find("i").removeClass("fa-long-arrow-right");

                    // Navigation bar Class
                    Woffice.$sidebarTrigger.addClass("sidebar-hidden");

                    // Main Layout changes
                    $("#main-content, body").addClass("sidebar-hidden");
                    Woffice.$scrollHelper.fadeOut('fast');

                    // Horizontal menu responsiveness
                    if (Woffice.$body.hasClass("menu-is-horizontal")) {

                        Woffice.$navigation.addClass("menu-responsive-horizontal");

                        if (!switcher && $('#horizontal-menu-trigger-container').length == 0) {
                            Woffice.$navigation.find("ul.main-menu").prepend('<li id="horizontal-menu-trigger-container"><a href="#" id="horizontal-menu-trigger"><i class="fa fa-bars"></i></a></li>');
                        }

                    }

                }
                else {

                    if ($horizontalMenuWrapper.length > 0) {
                        $horizontalMenuWrapper.remove();
                    }

                }
            }

        },

        /**
         * Opens or closes the sidebar and handle the cookies
         */
        sidebarToggling: function () {

            var self = this,
                $ = Woffice.$;

            // Open the sidebar
            if (!self.isOpen()){

                self.openSidebar();

                // Erase the cookies
                Cookies.remove('Woffice_sidebar_position',{ expires: 7, path: '/' });
                Cookies.remove('Woffice_sidebar_position_icon',{ expires: 7, path: '/' });

            }
            // Close the sidebar
            else{

                // If is a mobile device
                if(window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches) {
                    var $search_trigger = $('#search-trigger'),
                        $notification_trigger = $('#nav-notification-trigger');

                    //if the search bar is opened, then close it
                    if($search_trigger.length > 0 && $('#main-search').hasClass('opened'))
                        $($search_trigger).click();

                    //if the notifications bar is opened, then close it
                    if($notification_trigger.length > 0 && $notification_trigger.hasClass('clicked'))
                        $($notification_trigger).click();
                }

                self.closeSidebar();

                // Create cookies to save user choice :
                Cookies.set('Woffice_sidebar_position', 'navigation-hidden', { expires: 7, path: '/' });
                Cookies.set('Woffice_sidebar_position_icon', 'fa-bars', { expires: 7, path: '/' });

            }

        },

        /**
         * Sets the sidebar offset and animate if required
         */
        setSidebarTopOffset : function () {

            var $ = Woffice.$;

            if(window.matchMedia('(min-width: 993px)').matches)
                return;

            var $sidebar = $('#right-sidebar'),
                height = 0;

            //If there is no sidebar rendered, then exit from this function
            if($sidebar.length <= 0)
                return;

            if(window.matchMedia('(max-width: 600px)').matches)
                if($('#navbar').length > 0)
                    height += $('#navbar').height();

            Woffice.$rightSidebar.animate({
                top: height
            });

        },

        /**
         * Checks whether the sidebar is open or not
         *
         * @return {boolean}
         */
        isOpen : function() {

            return !(Woffice.$main.hasClass("sidebar-hidden"));

        },

        /**
         * Opens the sidebar
         */
        openSidebar: function() {

            var self = this,
                $ = Woffice.$;

            // Icon class switching
            Woffice.$sidebarTrigger.find("i").removeClass("fa-long-arrow-left");
            Woffice.$sidebarTrigger.find("i").addClass("fa-long-arrow-right");

            // Navbar Class
            Woffice.$sidebarTrigger.removeClass("sidebar-hidden");

            // Main Layout changes
            $("#main-content, #main-header, body").removeClass("sidebar-hidden");

            //Avoid overlap with header
            self.setSidebarTopOffset();

            Woffice.$scrollHelper.fadeIn('fast');

            if(
                Woffice.$body.hasClass('menu-is-vertical') &&
                !Woffice.$body.hasClass('navigation-hidden') &&
                window.matchMedia('(max-width: 450px)').matches
            ) {
                $('#nav-trigger').click();
            }

        },

        /**
         * Closes the sidebar
         */
        closeSidebar: function() {

            var $ = Woffice.$;

            // Icon class switching
            Woffice.$sidebarTrigger.find("i").addClass("fa-long-arrow-left");
            Woffice.$sidebarTrigger.find("i").removeClass("fa-long-arrow-right");

            // Navigation bar class
            Woffice.$sidebarTrigger.addClass("sidebar-hidden");

            // Main Layout changes
            $("#main-content, #main-header, body").addClass("sidebar-hidden");

            Woffice.$scrollHelper.fadeOut();

        },

        /**
         * Responsive menu class toggling
         * This is here because we need the right scope to close the sidebar automatically
         */
        horizontalMenuAuto: function () {

            var self = this,
                $ = Woffice.$;

            $('.main-menu').on("click", "#horizontal-menu-trigger", function(){

                Woffice.$navigation.toggleClass("menu-responsive-horizontal-show");
                Woffice.$body.toggleClass("navigation-hidden");

                if(
                    !Woffice.$body.hasClass('navigation-hidden') &&
                    window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches
                )
                    self.closeSidebar();

            });

        }

    },

    buddyPressNotifications: {

        /**
         * Close the notification box
         */
        close: function () {

            var $ = Woffice.$;

            $('#woffice-notifications-menu').fadeOut();

        },

        watch: function () {

            var $ = Woffice.$;

            $('a.mark-notification-read').on('click', function(){
                Woffice.buddyPressNotifications.markRead(this);
            });

            $('#nav-notification-trigger').click(function(){
                Woffice.buddyPressNotifications.fetch();
            });

        },

        /**
         * Mark a message as read
         * @param el
         */
        markRead: function(el) {

            var $ = Woffice.$;

            var readLink = $(el),
                component_action = readLink.data('component-action'),
                component_name = readLink.data('component-name'),
                item_id = readLink.data('item-id');

            $.ajax({
                url: Woffice.data.ajax_url.toString(),
                type: 'POST',
                data: {
                    'action': 'wofficeNoticationsMarked',
                    'component_action': component_action,
                    'component_name': component_name,
                    'item_id': item_id
                },
                success: function() {

                    readLink.parent().closest('div').remove();
                    if ($('#woffice-notifications-content').children().length === 0 ){
                        $('#nav-notification-trigger').removeClass('active');
                        Woffice.buddyPressNotifications.close();
                    }

                },
                error:function(){
                    console.log('Ajax marked failed');
                }
            });

        },

        /**
         * Fetch the notifications
         */
        fetch: function () {

            var $ = Woffice.$;

            if (!$(this).hasClass('clicked')) {

                var $wrapper = $('#woffice-notifications-menu');

                $wrapper.slideDown();
                $('#woffice-notifications-content').empty();

                var loader = new Woffice.loader($wrapper);

                $.ajax({
                    url: Woffice.data.ajax_url.toString(),
                    type: 'POST',
                    data: { 'action': 'wofficeNoticationsGet', 'user': Woffice.data.user_id.toString() },
                    success: function(notifications){
                        $('#woffice-notifications-content').append(notifications);
                        loader.remove();
                        Woffice.buddyPressNotifications.watch();
                    },
                    error:function(){
                        console.log('Ajax notifications failed');
                    }
                });
                $(this).addClass('clicked');

            } else {
                Woffice.buddyPressNotifications.close();
                $(this).removeClass('clicked');
            }

        }



    }

};

/**
 * Start it!
 *
 * We give it a jQuery object to play with
 */
Woffice.init(jQuery);