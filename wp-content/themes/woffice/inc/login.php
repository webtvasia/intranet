<?php
/**
 * Helpers to clean the login page template
 * woffice/page-templates/login.php
 *
 * @since 2.3.5
 */

if (! function_exists('woffice_login_render_errors')) {
    /**
     * Renders all the errors according to the requests parameters
     */
    function woffice_login_render_errors()
    {

        $color_notifications_green = woffice_get_settings_option('color_notifications_green');
        $color_notifications = woffice_get_settings_option('color_notifications');

        $login  = (isset($_GET['login']) ) ? $_GET['login'] : 0;

        /*
         * New User Approve
         */
        if((is_string($login) && $login == 'pending_approval')) {
            $message = \ComponentManualUserApprove\classes\Eonet_MUA_Message::get_message_content('authentication_pending');
            woffice_login_error('error', $message, $color_notifications);
        }

        if((is_string($login) && $login == 'denied_approval')) {
            $message = \ComponentManualUserApprove\classes\Eonet_MUA_Message::get_message_content('authentication_denied');
            woffice_login_error('error', $message, $color_notifications);
        }

        /*
         * Login actions
         */
        if ( $login === "failed" ) {
            $message = __('Invalid username and/or password.','woffice');
            woffice_login_error('error', $message, $color_notifications);
        }
        elseif ( $login === "empty" ) {
            $message = __('Username and/or Password is empty.','woffice');
            woffice_login_error('error', $message, $color_notifications);
        }
        elseif ( $login === "false" ) {
            $message = __('You are logged out.','woffice');
            woffice_login_error('success', $message, $color_notifications_green);
        }

        /*
         * Lost Password & Password reset
         */
        if(isset($_GET['type']) && ($_GET['type'] == 'lost-password' || $_GET['type'] == 'reset-password')) {
            if (isset($_REQUEST['errors'])) {
                $error_codes = explode(',', $_REQUEST['errors']);
                foreach ($error_codes as $error_code) {
                    switch ($error_code) {
                        case 'empty_username':
                            $message = __('You need to enter your email address to continue.', 'woffice');
                            break;
                        case 'invalid_email':
                            $message = __('Your email is not valid..', 'woffice');
                            break;
                        case 'invalidcombo':
                            $message = __('There are no users registered with this email address.', 'woffice');
                            break;
                        case 'expiredkey':
                            $message = __('Key is expired.', 'woffice');
                            break;
                        case 'invalidkey':
                            $message = __('The password reset link you used is not valid anymore.', 'woffice');
                            break;
                        case 'password_reset_mismatch':
                            $message = __("The two passwords you entered don't match.", 'woffice');
                            break;
                        case 'password_reset_empty':
                            $message = __("Sorry, we don't accept empty passwords.", 'woffice');
                            break;
                        default:
                            $message = __("Something happened, please try again.", 'woffice');
                            break;
                    }
                    woffice_login_error('error', $message, $color_notifications);
                }
            }  else if ($_GET['type'] == 'lost-password') {
                $message = __('Enter your email address and we\'ll send you a link you can use to pick a new password.', 'woffice');
                woffice_login_error('success', $message, $color_notifications_green, __('Forgot Your Password ? ', 'woffice'));
            } else if ($_GET['type'] == 'reset-password') {
                $message = __('Reset key is valid, you can pick a new password.', 'woffice');
                woffice_login_error('success', $message, $color_notifications_green, __('Pick a New Password', 'woffice'));
            }
        }
        if (isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm') {
            $message = __('Check your email for a link to reset your password.', 'woffice');
            woffice_login_error('success', $message, $color_notifications_green);
        }
        if (isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed') {
            $message = __('Your password has been changed. You can sign in now.', 'woffice');
            woffice_login_error('success', $message, $color_notifications_green);
        }

    }
}

if (! function_exists('woffice_login_render_reset_password')) {
    /**
     * Renders the reset password form
     */
    function woffice_login_render_reset_password()
    {

        ?>

        <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">

            <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />
            <input type="hidden" name="rp_key" value="<?php echo esc_attr( $_GET['key'] ); ?>" />

            <p>
                <label for="pass1"><?php _e( 'New password', 'woffice' ) ?></label>
                <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
            </p>
            <p>
                <label for="pass2"><?php _e( 'Repeat new password', 'woffice' ) ?></label>
                <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
            </p>

            <p class="description"><?php echo wp_get_password_hint(); ?></p>

            <p class="resetpass-submit">
                <input type="submit" name="submit" id="resetpass-button" class="btn btn-default" value="<?php _e( 'Reset Password', 'woffice' ); ?>" />
            </p>
        </form>

        <div id="go-back-to-login" class="text-center">
            <?php // Login URL:
            $login_page_slug = woffice_get_login_page_name();
            $login_page = home_url( '/' . $login_page_slug . '/' ); ?>
            <a href="<?php echo $login_page ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php _e('Login', 'woffice'); ?></a>
        </div>

        <?php

    }
}

if (! function_exists('woffice_login_error')) {
    /**
     * Renders a single error
     *
     * @param $type string - 'error' or 'success'
     * @param $content string
     * @param $color string
     * @param $custom_label string
     */
    function woffice_login_error($type, $content, $color, $custom_label = '')
    {

        $class = ($type == 'error') ? 'fa-exclamation-triangle' : 'fa-check-circle-o';
        if(empty($custom_label)) {
            $label = ($type == 'error') ? __('We have an error:', 'woffice') : __('Success:', 'woffice');
        } else {
            $label = $custom_label;
        }
        ?>
        <div class="infobox <?php echo $class; ?>" style="background-color: <?php echo $color; ?>;">
            <span class="infobox-head">
                <i class="fa <?php echo $class; ?>"></i> <?php echo $label; ?>
            </span>
            <p><?php echo $content; ?></p>
        </div>
        <?php

    }
}

if (! function_exists('woffice_login_render_register')) {
    /**
     * Renders the register form within the login page
     */
    function woffice_login_render_register()
    {

        $register_option = get_option('users_can_register');

        /**
         * Filters if user can register or not
         *
         * @param $register_option bool - set in the WordPress Settings page
         */
        $user_can_register = apply_filters('woffice_users_can_register', $register_option);

        if ($user_can_register == 0)
            return;

        $register_message = woffice_get_settings_option('register_message');
        $register_pmp = woffice_get_settings_option('register_pmp');

        ?>

        <div id="register-wrapper">

            <p><?php echo $register_message; ?></p>

            <?php
            // For Paid Membership Pro
            if ($register_pmp == "yep" && function_exists("pmpro_getOption")) : ?>

                <a href="<?php echo get_permalink(pmpro_getOption("levels_page_id")); ?>" class="btn btn-default"
                   id="register-trigger">
                    <i class="fa fa-sign-in"></i> <?php _e('Sign Up', 'woffice'); ?>
                </a>

            <?php else: ?>

                <a href="#register-form" class="btn btn-default" id="register-trigger">
                    <i class="fa fa-sign-in"></i> <?php _e('Sign Up', 'woffice'); ?>
                </a>

            <?php endif; ?>

        </div>

        <?php
        // We render the register form
        echo do_shortcode('[woffice_registration_form]'); ?>

        <div id="goback-trigger">
            <a href="#loginform" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php _e('Go back', 'woffice'); ?></a>
        </div>

        <?php

    }
}

if (! function_exists('woffice_login_render_form')) {
    /**
     * Renders the login form
     */
    function woffice_login_render_form()
    {

        $redirect_url = woffice_get_redirect_page_after_login();
        $login_rest_password = woffice_get_settings_option('login_rest_password');

        // The login form
        wp_login_form( array(
            'redirect' => $redirect_url,
            'id_username' => 'user',
            'id_password' => 'pass',
        ) );

        // Password reset link
        if ($login_rest_password == "yep") : ?>

             <a href="<?php echo wp_lostpassword_url(); ?>" class="password-lost"><?php _e('Lost Password','woffice'); ?></a>

        <?php endif;

    }
}

if (! function_exists('woffice_login_render_lost_password')) {
    /**
     * Renders the lost password form
     */
    function woffice_login_render_lost_password()
    {

        ?>

        <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" class="clearfix" method="post">

            <p class="form-row">
                <label for="user_login"><?php _e( 'Email', 'woffice' ); ?>
                <input type="text" name="user_login" id="user_login">
            </p>

            <p class="lostpassword-submit">
                <input type="submit" name="submit" class="lostpassword-button" value="<?php _e( 'Reset Password', 'woffice' ); ?>"/>
            </p>

        </form>

        <div id="go-back-to-login" class="text-center">
            <?php // Login URL:
            $login_page_slug = woffice_get_login_page_name();
            $login_page = home_url( '/' . $login_page_slug . '/' ); ?>
            <a href="<?php echo $login_page ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php _e('Login', 'woffice'); ?></a>
        </div>

        <?php

    }
}

if (! function_exists('woffice_login_render_footer')) {
    /**
     * Renders the footer
     */
    function woffice_login_render_footer()
    {

        $login_wordpress = woffice_get_settings_option('login_wordpress');
        if ($login_wordpress == "yep") {
            ?>
            <footer>
                <p>
                    <?php _e("Proudly powered by", "woffice"); ?>
                    <a href="https://webtvasia.com/" target="_blank">
                        <img src="<?php echo home_url(); ?>/wp-admin/images/wtva.jpeg" alt="WTV Asia logo">
                    </a>
                </p>
            </footer>
            <?php
        }

    }
}

if (! function_exists('woffice_login_render_script')) {
    /**
     * Renders the login's JS
     */
    function woffice_login_render_script() {

        ?>

        <!-- JAVSCRIPTS BELOW AND FILES LOADED BY WORDPRESS -->
        <script type="text/javascript">
            if (jQuery('#success-register').length > 0) {
                var loader = new Woffice.loader(jQuery('.login-tabs-wrapper'));
                jQuery("#register-form, #goback-trigger").hide();
                jQuery("#loginform, #register-wrapper,a.password-lost").hide();
                function show_login() {
                    jQuery("#loginform, #register-wrapper,a.password-lost").show();
                    loader.remove();
                }
                setTimeout(show_login, 2000);
                <?php $register_autoredirect = woffice_get_settings_option('register_autoredirect');
                if($register_autoredirect == "yep") : ?>
                var NewUser = jQuery('#success-register').data('user');
                if (NewUser) {
                    jQuery.ajax({
                        type:"POST",
                        url: "<?php echo get_site_url() ?>/wp-admin/admin-ajax.php",
                        data: {"action" : "WofficeRegisterRedirect", "user_id" : NewUser, "security" : "<?php echo wp_create_nonce( "WofficeRegisterRedirectNonce" ); ?>"},
                        success:function(returnval){
                            jQuery(returnval).appendTo("body");
                            jQuery(".woffice-ajax-main").show();
                            function RedirectAfterLogin() {
                                window.location.replace("<?php echo get_site_url() ?>");
                            }
                            setTimeout(RedirectAfterLogin, 2000);
                        },
                    });
                }
                <?php endif; ?>
            }
            jQuery("#register-loader, #register-form, #goback-trigger").hide();
            jQuery("#register-trigger").on('click', function(){
                var loader = new Woffice.loader(jQuery('.login-tabs-wrapper'));
                jQuery("#loginform, #register-wrapper,a.password-lost").hide();
                function show_register() {
                    jQuery("#register-form, #goback-trigger").show();
                    loader.remove();
                }
                setTimeout(show_register, 1000);
            });
            jQuery("#goback-trigger a").on('click', function(){
                var loader = new Woffice.loader(jQuery('.login-tabs-wrapper'));
                jQuery("#register-form, #goback-trigger").hide();
                function show_login() {
                    jQuery("#loginform, #register-wrapper,a.password-lost").show();
                    loader.remove();
                }
                setTimeout(show_login, 1000);
            });
            var hash = location.hash.replace('#', '').toString();
            if (hash === 'register-form') {
                if (jQuery('#success-register').length  == 0) {
                    var loader = new Woffice.loader(jQuery('.login-tabs-wrapper'));
                    jQuery("#loginform, #register-wrapper,a.password-lost").hide();
                    function show_register() {
                        jQuery("#register-form").show();
                        loader.remove();
                    }
                    setTimeout(show_register, 1000);
                }
            }
        </script>

        <?php

    }
}

