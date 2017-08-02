/**
 * Woffice Time Tracking JS
 *
 * @type {{wrapper: *, init: WofficeTimeTracking.init}}
 */
var WofficeTimeTracking = {

    wrapper: null,
    isClockActive: false,

    /**
     * Live time clock, increases every minute
     */
    liveClock: function() {

        var self = this,
            $clock = self.wrapper.find('.woffice-time-tracking-view > .woffice-time-tracking_time-displayed');
        this.liveTime = setInterval(function () {

            if(!self.isClockActive) {
                return;
            }

            var fullTime = $clock.html(),
                index = fullTime.indexOf(':'),
                hours = parseInt(fullTime.substr(0, index)),
                minutes = parseInt(fullTime.substr(index + 1)),
                newMinutes = minutes,
                newHours = hours;

            newMinutes = newMinutes + 1;

            if(newMinutes == 60) {
                newMinutes = 0;
                newHours = newHours + 1;
            }

            newMinutes = newMinutes.toString();
            newHours = newHours.toString();

            if(newMinutes.length == 1) {
                newMinutes = '0' + newMinutes;
            }

            if(newHours.length == 1) {
                newHours = '0'+newHours;
            }

            $clock.html(newHours + ':' + newMinutes);

        }, 60*1000);

    },

    /**
     * Switch between the timer and the history tab
     */
    switchTab: function () {

        var self = this,
            $toggle = self.wrapper.find('.woffice-time-tracking-history-toggle'),
            $tabs = self.wrapper.find('.woffice-time-tracking-view'),
            $icon = self.wrapper.find('.woffice-time-tracking-history-toggle i')

        $toggle.toggleClass('is-history');

        if($toggle.hasClass('is-history')) {
            $tabs.first().fadeOut();
            $tabs.last().fadeIn();
            $icon.removeClass('fa-history');
            $icon.addClass('fa-times');
        } else {
            $tabs.first().fadeIn();
            $tabs.last().fadeOut();
            $icon.addClass('fa-history');
            $icon.removeClass('fa-times');
        }

    },

    /**
     * Change the state: stop || start
     */
    stateChange: function () {

        var self = this,
            $btn = self.wrapper.find('.woffice-time-tracking-state-toggle');

        if(self.wrapper.hasClass('is-tracking')) {
            // We change the button
            $btn.removeClass('btn-danger');
            $btn.addClass('btn-info');
            $btn.find('i.fa').removeClass('fa-stop');
            $btn.find('i.fa').addClass('fa-play');
            $btn.find('span').html(WOFFICE_TIME_TRACKING.text_start);
            // Action
            var action = 'stop';
        } else {
            // We change the button
            $btn.addClass('btn-danger');
            $btn.removeClass('btn-info');
            $btn.find('i.fa').addClass('fa-stop');
            $btn.find('i.fa').removeClass('fa-play');
            $btn.find('span').html(WOFFICE_TIME_TRACKING.text_stop);
            // Action
            var action = 'start';
        }

        self.isClockActive = !self.isClockActive;
        self.wrapper.toggleClass('is-tracking');
        self.wrapper.addClass('is-loading');

        jQuery.post( WOFFICE_TIME_TRACKING.ajax_url, {
            action: 'woffice_time_tracking',
            _wpnonce: WOFFICE_TIME_TRACKING.time_tracking_nonce,
            tracking_action: action,
            tracking_meta: ''
        })
            .done(function( data ) {
                self.wrapper.removeClass('is-loading');
            });

    },

    /**
     * Init the widget
     */
    init: function () {

        var self = this;

        self.wrapper = jQuery('.woffice-time-tracking');

        // If we are currently tracking
        if(self.wrapper.hasClass('is-tracking')) {
            self.isClockActive = true;
        }

        // Clock:
        self.liveClock();

        // Tracking state
        self.wrapper.find('.woffice-time-tracking-state-toggle').on('click', function (e) {
            e.preventDefault();
            self.stateChange();
        });

        // Show history
        self.wrapper.find('.woffice-time-tracking-history-toggle').on('click', function (e) {
            e.preventDefault();
            self.switchTab();
        });

    }

};

// Starts it!
jQuery( document ).ready(function() {
    WofficeTimeTracking.init();
});