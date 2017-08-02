(function ($) {

    /**
     * Mark a notification as read
     */
    function eoHandleRead() {
        var eoDataHandler = $('.eonet_alert .eonet_alert_content p').find('a'),
            eoData = {
                'action' : EONET_NOTIFICATIONS.notifications_action_mark,
                'security' : EONET_NOTIFICATIONS.notifications_nonce,
                'component_action' : eoDataHandler.data('eo-component-action'),
                'component_name' : eoDataHandler.data('eo-component-name'),
                'item_id' : eoDataHandler.data('eo-item-id')
            };
        jQuery.post(EONET_NOTIFICATIONS.ajax_url, eoData);
    }

    /**
     * Play a notification sound
     * Helper function
     */
    function eoNotificationSound() {

        var self = this;

        self.sound = '';
        self.soundEl = '';

        self.init = function() {

            if (EONET_NOTIFICATIONS.notifications_sound.length > 0 && EONET_NOTIFICATIONS.notifications_sound == 'true') {

                var uniqueNbr = Math.floor((Math.random() * 10000) + 1),
                    ID = 'eo-sound-' + uniqueNbr,
                    eoContainer = $('body'),
                    audioTag = '<audio id="'+ ID +'" preload="auto" >' +
                        '<source src="' + EONET_NOTIFICATIONS.sound_path + '.wav" type="audio/x-wav" />' +
                        '<source src="' + EONET_NOTIFICATIONS.sound_path + '.ogg" type="audio/ogg" />' +
                        '<source src="' + EONET_NOTIFICATIONS.sound_path + '.mp3" type="audio/mpeg" />' +
                        '</audio>';

                eoContainer.append(audioTag);

                self.soundEl = $( '#' + ID );
                self.sound = self.soundEl[0];

                return self.sound;

            }

        };

        self.soundUp = function() {

            if(self.sound == '') {
                console.error("Something wrong happened with the sound:");
                console.error(self.soundEl);
                return;
            }

            self.sound.play();

            setTimeout(function () {
                self.sound.remove();
            }, 5000);

        }

    }

    /**
     * Fetch Notifications from the server
     */
    function eoFetchNotifications() {
        var data = {
            'action' : EONET_NOTIFICATIONS.notifications_action,
            'security' : EONET_NOTIFICATIONS.notifications_nonce,
        };
        jQuery.post(EONET_NOTIFICATIONS.ajax_url, data, function(response) {

            if(response.length != 0) {
                // Create the alert :
                var object = JSON.parse(response);
                // Create notification
                if(object.icon != undefined && object.title != undefined && object.content != undefined) {
                    $.eonetNotification(object.icon, object.title, object.content, EONET_NOTIFICATIONS.notifications_fadeout);
                    // Sound
                    var eoSound = new eoNotificationSound();
                    eoSound.init();
                    eoSound.soundUp();
                    // Position
                    $('.eonet_alert').addClass('eo_alert_' + EONET_NOTIFICATIONS.notifications_position);
                    // Mark it as read, if needed
                    eoHandleRead();
                }
            }
            setTimeout(eoFetchNotifications, EONET_NOTIFICATIONS.notifications_refresh);

        });
    }
    eoFetchNotifications();


})(jQuery);