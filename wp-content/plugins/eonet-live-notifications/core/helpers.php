<?php
if ( ! defined('ABSPATH') ) die('Forbidden') ;

/**
 * Helper to print anything clearly in the dashboard
 * Inspired by the great plugin unyson/framework/helpers/general.php#L180
 * @param $value : message written
 */
function eonet_print($value)
{
    if(class_exists('Eonet\Core\External\EonetDumper')) {
        static $first_time = true;
        if ($first_time) {
            ob_start();
            echo str_replace(array('  ', "\n"), '', ob_get_clean());
            $first_time = false;
        }
        if (func_num_args() == 1) {
            echo '<div class="eonet_print_r"><pre>';
            echo Eonet\Core\External\EonetDumper::dump($value);
            echo '</pre></div>';
        } else {
            echo '<div class="enet_print_r_group">';
            foreach (func_get_args() as $param) {
                eonet_print($param);
            }
            echo '</div>';
        }
    }
}

/**
 * Autoload our classes :
 */
function eonet_autoload($classname){
    // We get the full pattern :
    $classPattern = explode('\\', $classname);
    // We get the size :
    $patterSize = sizeof($classPattern);
    // We rebuild it :
    for($i = 0; $i < ($patterSize - 1); $i++){
        $lower = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $classPattern[$i]));
        $classPattern[$i] = $lower;
    }
    $classPath = implode('/', $classPattern);


    // If it's a component file :
    if(substr($classPattern[0], 0, 10) == 'component-'){
        $component_slug = substr($classPattern[0], 10);
        if(file_exists(WP_PLUGIN_DIR . '/eonet/init.php')) {
            $plugin_path = WP_PLUGIN_DIR . '/eonet/';
        } else {
            $plugin_path = WP_PLUGIN_DIR . '/eonet-'.$component_slug.'/';
        }
    }
    // Core file :
    else {
        $plugin_path = EONET_DIR;
    }

    // create the actual filepath
    $filePath = $plugin_path . $classPath . '.php';

    // check if the file exists
    if(file_exists($filePath))
    {
        // require once on the file
        require_once $filePath;
    }
}

/**
 * Render a view and pass it variables
 * @param $file_path : direct path to the view
 * @param $view_variables: array of variables passed to the view
 * @return mixed
 */
function eonet_render_view($file_path, $view_variables = array(), $return = true) {
    extract($view_variables, EXTR_REFS);
    unset($view_variables);
    if ($return) {
        ob_start();
        require $file_path;
        return ob_get_clean();
    } else {
        require $file_path;
    }
}

/**
 * Helper to add admin alert
 * @param $type string : category of the error (success, warning, error, info)
 * @param $content string : content of the alert box
 * @return string
 */
function eonet_new_admin_alert($type = 'warning', $content = 'No Content') {
    $html = '<div class="notice notice-'. $type .' is-dismissible">';
    $html .= '<p>'. $content .'</p>';
    $html .= '</div>';
    return $html;
}

/**
 * Helper to turn strings into camel case
 * @param $string string
 * @return string
 */
function eonet_camel($string)
{
    $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    return $str;
}

/**
 * Helper to turn slugs into strings
 * @param $string slug
 * @return string
 */
function eonet_stringify($slug)
{
    $string = str_replace('-', ' ', $slug);

    return ucwords($string);
}

//This function cannot be overridden by a child themes
/**
 * Check if the current theme is a theme by Alkaweb
 *
 * @return bool
 */
function eo_is_current_theme_by_alkaweb() {

	$theme = wp_get_theme();

	$name = $theme;

	return ( $name == 'Eonet' || $name == 'Eonet Child' || $name == 'Woffice' || $name == 'Woffice Child' );

}

/**
 * Helper to return an option val from the Eonet Settings
 * @param $name string
 * @param $default string
 * @return string
 */
function eonet_get_option($name = '', $default = '')
{
    if(class_exists('Eonet\Core\Admin\EonetAdmin')) {
        return Eonet\Core\Admin\EonetAdmin::getSetting($name, $default);
    }

    return $default;
}

/**
 * @param int $post_id Post ID.
 * @param string $name The meta key to retrieve (without the suffix eo_)
 * @param bool $single Optional. Whether to return a single value. Default true.
 * @param null|mixed $default A default value to return
 *
 * @return mixed|null
 */
function eonet_get_meta_option($post_id, $name, $single = true, $default = null) {

	$meta_value = get_post_meta( $post_id, 'eo_' . $name, $single );

	if($meta_value == '' && !is_null($default))
		return $default;

	return $meta_value;

}

/**
 * Return the icons and groups of all FontAwesome icons available
 *
 * @return array
 */
function eonet_get_font_awesome_icons() {

	return array (
		'groups' => array(
			'web-application' => 'Web Application Icons',
			'accessibility' => 'Accessibility Icons',
			'hand' => 'Hand Icons',
			'transportation' => 'Transportation Icons',
			'gender' => 'Gender Icons',
			'file-type' => 'File Type Icons',
			'spinner' => 'Spinner Icons',
			'form-control' => 'Form Control Icons',
			'payment' => 'Payment Icons',
			'chart' => 'Chart Icons',
			'currency' => 'Currency Icons',
			'text-editor' => 'Text Editor Icons',
			'directional' => 'Directional Icons',
			'video-player' => 'Video Player Icons',
			'brand' => 'Brand Icons',
			'medical' => 'Medical Icons',
		),
		'icons' => array(
			'web-application' =>
				array (
					'fa fa-address-book',
					'fa fa-address-book-o',
					'fa fa-address-card',
					'fa fa-address-card-o',
					'fa fa-adjust',
					'fa fa-american-sign-language-interpreting',
					'fa fa-anchor',
					'fa fa-archive',
					'fa fa-area-chart',
					'fa fa-arrows',
					'fa fa-arrows-h',
					'fa fa-arrows-v',
					'fa fa-asl-interpreting',
					'fa fa-assistive-listening-systems',
					'fa fa-asterisk',
					'fa fa-at',
					'fa fa-audio-description',
					'fa fa-automobile',
					'fa fa-balance-scale',
					'fa fa-ban',
					'fa fa-bank',
					'fa fa-bar-chart',
					'fa fa-bar-chart-o',
					'fa fa-barcode',
					'fa fa-bars',
					'fa fa-bath',
					'fa fa-bathtub',
					'fa fa-battery',
					'fa fa-battery-0',
					'fa fa-battery-1',
					'fa fa-battery-2',
					'fa fa-battery-3',
					'fa fa-battery-4',
					'fa fa-battery-empty',
					'fa fa-battery-full',
					'fa fa-battery-half',
					'fa fa-battery-quarter',
					'fa fa-battery-three-quarters',
					'fa fa-bed',
					'fa fa-beer',
					'fa fa-bell',
					'fa fa-bell-o',
					'fa fa-bell-slash',
					'fa fa-bell-slash-o',
					'fa fa-bicycle',
					'fa fa-binoculars',
					'fa fa-birthday-cake',
					'fa fa-blind',
					'fa fa-bluetooth',
					'fa fa-bluetooth-b',
					'fa fa-bolt',
					'fa fa-bomb',
					'fa fa-book',
					'fa fa-bookmark',
					'fa fa-bookmark-o',
					'fa fa-braille',
					'fa fa-briefcase',
					'fa fa-bug',
					'fa fa-building',
					'fa fa-building-o',
					'fa fa-bullhorn',
					'fa fa-bullseye',
					'fa fa-bus',
					'fa fa-cab',
					'fa fa-calculator',
					'fa fa-calendar',
					'fa fa-calendar-check-o',
					'fa fa-calendar-minus-o',
					'fa fa-calendar-o',
					'fa fa-calendar-plus-o',
					'fa fa-calendar-times-o',
					'fa fa-camera',
					'fa fa-camera-retro',
					'fa fa-car',
					'fa fa-caret-square-o-down',
					'fa fa-caret-square-o-left',
					'fa fa-caret-square-o-right',
					'fa fa-caret-square-o-up',
					'fa fa-cart-arrow-down',
					'fa fa-cart-plus',
					'fa fa-cc',
					'fa fa-certificate',
					'fa fa-check',
					'fa fa-check-circle',
					'fa fa-check-circle-o',
					'fa fa-check-square',
					'fa fa-check-square-o',
					'fa fa-child',
					'fa fa-circle',
					'fa fa-circle-o',
					'fa fa-circle-o-notch',
					'fa fa-circle-thin',
					'fa fa-clock-o',
					'fa fa-clone',
					'fa fa-close',
					'fa fa-cloud',
					'fa fa-cloud-download',
					'fa fa-cloud-upload',
					'fa fa-code',
					'fa fa-code-fork',
					'fa fa-coffee',
					'fa fa-cog',
					'fa fa-cogs',
					'fa fa-comment',
					'fa fa-comment-o',
					'fa fa-commenting',
					'fa fa-commenting-o',
					'fa fa-comments',
					'fa fa-comments-o',
					'fa fa-compass',
					'fa fa-copyright',
					'fa fa-creative-commons',
					'fa fa-credit-card',
					'fa fa-credit-card-alt',
					'fa fa-crop',
					'fa fa-crosshairs',
					'fa fa-cube',
					'fa fa-cubes',
					'fa fa-cutlery',
					'fa fa-dashboard',
					'fa fa-database',
					'fa fa-deaf',
					'fa fa-deafness',
					'fa fa-desktop',
					'fa fa-diamond',
					'fa fa-dot-circle-o',
					'fa fa-download',
					'fa fa-drivers-license',
					'fa fa-drivers-license-o',
					'fa fa-edit',
					'fa fa-ellipsis-h',
					'fa fa-ellipsis-v',
					'fa fa-envelope',
					'fa fa-envelope-o',
					'fa fa-envelope-open',
					'fa fa-envelope-open-o',
					'fa fa-envelope-square',
					'fa fa-eraser',
					'fa fa-exchange',
					'fa fa-exclamation',
					'fa fa-exclamation-circle',
					'fa fa-exclamation-triangle',
					'fa fa-external-link',
					'fa fa-external-link-square',
					'fa fa-eye',
					'fa fa-eye-slash',
					'fa fa-eyedropper',
					'fa fa-fax',
					'fa fa-feed',
					'fa fa-female',
					'fa fa-fighter-jet',
					'fa fa-file-archive-o',
					'fa fa-file-audio-o',
					'fa fa-file-code-o',
					'fa fa-file-excel-o',
					'fa fa-file-image-o',
					'fa fa-file-movie-o',
					'fa fa-file-pdf-o',
					'fa fa-file-photo-o',
					'fa fa-file-picture-o',
					'fa fa-file-powerpoint-o',
					'fa fa-file-sound-o',
					'fa fa-file-video-o',
					'fa fa-file-word-o',
					'fa fa-file-zip-o',
					'fa fa-film',
					'fa fa-filter',
					'fa fa-fire',
					'fa fa-fire-extinguisher',
					'fa fa-flag',
					'fa fa-flag-checkered',
					'fa fa-flag-o',
					'fa fa-flash',
					'fa fa-flask',
					'fa fa-folder',
					'fa fa-folder-o',
					'fa fa-folder-open',
					'fa fa-folder-open-o',
					'fa fa-frown-o',
					'fa fa-futbol-o',
					'fa fa-gamepad',
					'fa fa-gavel',
					'fa fa-gear',
					'fa fa-gears',
					'fa fa-gift',
					'fa fa-glass',
					'fa fa-globe',
					'fa fa-graduation-cap',
					'fa fa-group',
					'fa fa-hand-grab-o',
					'fa fa-hand-lizard-o',
					'fa fa-hand-paper-o',
					'fa fa-hand-peace-o',
					'fa fa-hand-pointer-o',
					'fa fa-hand-rock-o',
					'fa fa-hand-scissors-o',
					'fa fa-hand-spock-o',
					'fa fa-hand-stop-o',
					'fa fa-handshake-o',
					'fa fa-hard-of-hearing',
					'fa fa-hashtag',
					'fa fa-hdd-o',
					'fa fa-headphones',
					'fa fa-heart',
					'fa fa-heart-o',
					'fa fa-heartbeat',
					'fa fa-history',
					'fa fa-home',
					'fa fa-hotel',
					'fa fa-hourglass',
					'fa fa-hourglass-1',
					'fa fa-hourglass-2',
					'fa fa-hourglass-3',
					'fa fa-hourglass-end',
					'fa fa-hourglass-half',
					'fa fa-hourglass-o',
					'fa fa-hourglass-start',
					'fa fa-i-cursor',
					'fa fa-id-badge',
					'fa fa-id-card',
					'fa fa-id-card-o',
					'fa fa-image',
					'fa fa-inbox',
					'fa fa-industry',
					'fa fa-info',
					'fa fa-info-circle',
					'fa fa-institution',
					'fa fa-key',
					'fa fa-keyboard-o',
					'fa fa-language',
					'fa fa-laptop',
					'fa fa-leaf',
					'fa fa-legal',
					'fa fa-lemon-o',
					'fa fa-level-down',
					'fa fa-level-up',
					'fa fa-life-bouy',
					'fa fa-life-buoy',
					'fa fa-life-ring',
					'fa fa-life-saver',
					'fa fa-lightbulb-o',
					'fa fa-line-chart',
					'fa fa-location-arrow',
					'fa fa-lock',
					'fa fa-low-vision',
					'fa fa-magic',
					'fa fa-magnet',
					'fa fa-mail-forward',
					'fa fa-mail-reply',
					'fa fa-mail-reply-all',
					'fa fa-male',
					'fa fa-map',
					'fa fa-map-marker',
					'fa fa-map-o',
					'fa fa-map-pin',
					'fa fa-map-signs',
					'fa fa-meh-o',
					'fa fa-microchip',
					'fa fa-microphone',
					'fa fa-microphone-slash',
					'fa fa-minus',
					'fa fa-minus-circle',
					'fa fa-minus-square',
					'fa fa-minus-square-o',
					'fa fa-mobile',
					'fa fa-mobile-phone',
					'fa fa-money',
					'fa fa-moon-o',
					'fa fa-mortar-board',
					'fa fa-motorcycle',
					'fa fa-mouse-pointer',
					'fa fa-music',
					'fa fa-navicon',
					'fa fa-newspaper-o',
					'fa fa-object-group',
					'fa fa-object-ungroup',
					'fa fa-paint-brush',
					'fa fa-paper-plane',
					'fa fa-paper-plane-o',
					'fa fa-paw',
					'fa fa-pencil',
					'fa fa-pencil-square',
					'fa fa-pencil-square-o',
					'fa fa-percent',
					'fa fa-phone',
					'fa fa-phone-square',
					'fa fa-photo',
					'fa fa-picture-o',
					'fa fa-pie-chart',
					'fa fa-plane',
					'fa fa-plug',
					'fa fa-plus',
					'fa fa-plus-circle',
					'fa fa-plus-square',
					'fa fa-plus-square-o',
					'fa fa-podcast',
					'fa fa-power-off',
					'fa fa-print',
					'fa fa-puzzle-piece',
					'fa fa-qrcode',
					'fa fa-question',
					'fa fa-question-circle',
					'fa fa-question-circle-o',
					'fa fa-quote-left',
					'fa fa-quote-right',
					'fa fa-random',
					'fa fa-recycle',
					'fa fa-refresh',
					'fa fa-registered',
					'fa fa-remove',
					'fa fa-reorder',
					'fa fa-reply',
					'fa fa-reply-all',
					'fa fa-retweet',
					'fa fa-road',
					'fa fa-rocket',
					'fa fa-rss',
					'fa fa-rss-square',
					'fa fa-s15',
					'fa fa-search',
					'fa fa-search-minus',
					'fa fa-search-plus',
					'fa fa-send',
					'fa fa-send-o',
					'fa fa-server',
					'fa fa-share',
					'fa fa-share-alt',
					'fa fa-share-alt-square',
					'fa fa-share-square',
					'fa fa-share-square-o',
					'fa fa-shield',
					'fa fa-ship',
					'fa fa-shopping-bag',
					'fa fa-shopping-basket',
					'fa fa-shopping-cart',
					'fa fa-shower',
					'fa fa-sign-in',
					'fa fa-sign-language',
					'fa fa-sign-out',
					'fa fa-signal',
					'fa fa-signing',
					'fa fa-sitemap',
					'fa fa-sliders',
					'fa fa-smile-o',
					'fa fa-snowflake-o',
					'fa fa-soccer-ball-o',
					'fa fa-sort',
					'fa fa-sort-alpha-asc',
					'fa fa-sort-alpha-desc',
					'fa fa-sort-amount-asc',
					'fa fa-sort-amount-desc',
					'fa fa-sort-asc',
					'fa fa-sort-desc',
					'fa fa-sort-down',
					'fa fa-sort-numeric-asc',
					'fa fa-sort-numeric-desc',
					'fa fa-sort-up',
					'fa fa-space-shuttle',
					'fa fa-spinner',
					'fa fa-spoon',
					'fa fa-square',
					'fa fa-square-o',
					'fa fa-star',
					'fa fa-star-half',
					'fa fa-star-half-empty',
					'fa fa-star-half-full',
					'fa fa-star-half-o',
					'fa fa-star-o',
					'fa fa-sticky-note',
					'fa fa-sticky-note-o',
					'fa fa-street-view',
					'fa fa-suitcase',
					'fa fa-sun-o',
					'fa fa-support',
					'fa fa-tablet',
					'fa fa-tachometer',
					'fa fa-tag',
					'fa fa-tags',
					'fa fa-tasks',
					'fa fa-taxi',
					'fa fa-television',
					'fa fa-terminal',
					'fa fa-thermometer',
					'fa fa-thermometer-0',
					'fa fa-thermometer-1',
					'fa fa-thermometer-2',
					'fa fa-thermometer-3',
					'fa fa-thermometer-4',
					'fa fa-thermometer-empty',
					'fa fa-thermometer-full',
					'fa fa-thermometer-half',
					'fa fa-thermometer-quarter',
					'fa fa-thermometer-three-quarters',
					'fa fa-thumb-tack',
					'fa fa-thumbs-down',
					'fa fa-thumbs-o-down',
					'fa fa-thumbs-o-up',
					'fa fa-thumbs-up',
					'fa fa-ticket',
					'fa fa-times',
					'fa fa-times-circle',
					'fa fa-times-circle-o',
					'fa fa-times-rectangle',
					'fa fa-times-rectangle-o',
					'fa fa-tint',
					'fa fa-toggle-down',
					'fa fa-toggle-left',
					'fa fa-toggle-off',
					'fa fa-toggle-on',
					'fa fa-toggle-right',
					'fa fa-toggle-up',
					'fa fa-trademark',
					'fa fa-trash',
					'fa fa-trash-o',
					'fa fa-tree',
					'fa fa-trophy',
					'fa fa-truck',
					'fa fa-tty',
					'fa fa-tv',
					'fa fa-umbrella',
					'fa fa-universal-access',
					'fa fa-university',
					'fa fa-unlock',
					'fa fa-unlock-alt',
					'fa fa-unsorted',
					'fa fa-upload',
					'fa fa-user',
					'fa fa-user-circle',
					'fa fa-user-circle-o',
					'fa fa-user-o',
					'fa fa-user-plus',
					'fa fa-user-secret',
					'fa fa-user-times',
					'fa fa-users',
					'fa fa-vcard',
					'fa fa-vcard-o',
					'fa fa-video-camera',
					'fa fa-volume-control-phone',
					'fa fa-volume-down',
					'fa fa-volume-off',
					'fa fa-volume-up',
					'fa fa-warning',
					'fa fa-wheelchair',
					'fa fa-wheelchair-alt',
					'fa fa-wifi',
					'fa fa-window-close',
					'fa fa-window-close-o',
					'fa fa-window-maximize',
					'fa fa-window-minimize',
					'fa fa-window-restore',
					'fa fa-wrench',
				),
			'accessibility' =>
				array (
					'fa fa-american-sign-language-interpreting',
					'fa fa-asl-interpreting',
					'fa fa-assistive-listening-systems',
					'fa fa-audio-description',
					'fa fa-blind',
					'fa fa-braille',
					'fa fa-cc',
					'fa fa-deaf',
					'fa fa-deafness',
					'fa fa-hard-of-hearing',
					'fa fa-low-vision',
					'fa fa-question-circle-o',
					'fa fa-sign-language',
					'fa fa-signing',
					'fa fa-tty',
					'fa fa-universal-access',
					'fa fa-volume-control-phone',
					'fa fa-wheelchair',
					'fa fa-wheelchair-alt',
				),
			'hand' =>
				array (
					'fa fa-hand-grab-o',
					'fa fa-hand-lizard-o',
					'fa fa-hand-o-down',
					'fa fa-hand-o-left',
					'fa fa-hand-o-right',
					'fa fa-hand-o-up',
					'fa fa-hand-paper-o',
					'fa fa-hand-peace-o',
					'fa fa-hand-pointer-o',
					'fa fa-hand-rock-o',
					'fa fa-hand-scissors-o',
					'fa fa-hand-spock-o',
					'fa fa-hand-stop-o',
					'fa fa-thumbs-down',
					'fa fa-thumbs-o-down',
					'fa fa-thumbs-o-up',
					'fa fa-thumbs-up',
				),
			'transportation' =>
				array (
					'fa fa-ambulance',
					'fa fa-automobile',
					'fa fa-bicycle',
					'fa fa-bus',
					'fa fa-cab',
					'fa fa-car',
					'fa fa-fighter-jet',
					'fa fa-motorcycle',
					'fa fa-plane',
					'fa fa-rocket',
					'fa fa-ship',
					'fa fa-space-shuttle',
					'fa fa-subway',
					'fa fa-taxi',
					'fa fa-train',
					'fa fa-truck',
					'fa fa-wheelchair',
					'fa fa-wheelchair-alt',
				),
			'gender' =>
				array (
					'fa fa-genderless',
					'fa fa-intersex',
					'fa fa-mars',
					'fa fa-mars-double',
					'fa fa-mars-stroke',
					'fa fa-mars-stroke-h',
					'fa fa-mars-stroke-v',
					'fa fa-mercury',
					'fa fa-neuter',
					'fa fa-transgender',
					'fa fa-transgender-alt',
					'fa fa-venus',
					'fa fa-venus-double',
					'fa fa-venus-mars',
				),
			'file-type' =>
				array (
					'fa fa-file',
					'fa fa-file-archive-o',
					'fa fa-file-audio-o',
					'fa fa-file-code-o',
					'fa fa-file-excel-o',
					'fa fa-file-image-o',
					'fa fa-file-movie-o',
					'fa fa-file-o',
					'fa fa-file-pdf-o',
					'fa fa-file-photo-o',
					'fa fa-file-picture-o',
					'fa fa-file-powerpoint-o',
					'fa fa-file-sound-o',
					'fa fa-file-text',
					'fa fa-file-text-o',
					'fa fa-file-video-o',
					'fa fa-file-word-o',
					'fa fa-file-zip-o',
				),
			'spinner' =>
				array (
					'fa fa-circle-o-notch',
					'fa fa-cog',
					'fa fa-gear',
					'fa fa-refresh',
					'fa fa-spinner',
				),
			'form-control' =>
				array (
					'fa fa-check-square',
					'fa fa-check-square-o',
					'fa fa-circle',
					'fa fa-circle-o',
					'fa fa-dot-circle-o',
					'fa fa-minus-square',
					'fa fa-minus-square-o',
					'fa fa-plus-square',
					'fa fa-plus-square-o',
					'fa fa-square',
					'fa fa-square-o',
				),
			'payment' =>
				array (
					'fa fa-cc-amex',
					'fa fa-cc-diners-club',
					'fa fa-cc-discover',
					'fa fa-cc-jcb',
					'fa fa-cc-mastercard',
					'fa fa-cc-paypal',
					'fa fa-cc-stripe',
					'fa fa-cc-visa',
					'fa fa-credit-card',
					'fa fa-credit-card-alt',
					'fa fa-google-wallet',
					'fa fa-paypal',
				),
			'chart' =>
				array (
					'fa fa-area-chart',
					'fa fa-bar-chart',
					'fa fa-bar-chart-o',
					'fa fa-line-chart',
					'fa fa-pie-chart',
				),
			'currency' =>
				array (
					'fa fa-bitcoin',
					'fa fa-btc',
					'fa fa-cny',
					'fa fa-dollar',
					'fa fa-eur',
					'fa fa-euro',
					'fa fa-gbp',
					'fa fa-gg',
					'fa fa-gg-circle',
					'fa fa-ils',
					'fa fa-inr',
					'fa fa-jpy',
					'fa fa-krw',
					'fa fa-money',
					'fa fa-rmb',
					'fa fa-rouble',
					'fa fa-rub',
					'fa fa-ruble',
					'fa fa-rupee',
					'fa fa-shekel',
					'fa fa-sheqel',
					'fa fa-try',
					'fa fa-turkish-lira',
					'fa fa-usd',
					'fa fa-won',
					'fa fa-yen',
				),
			'text-editor' =>
				array (
					'fa fa-align-center',
					'fa fa-align-justify',
					'fa fa-align-left',
					'fa fa-align-right',
					'fa fa-bold',
					'fa fa-chain',
					'fa fa-chain-broken',
					'fa fa-clipboard',
					'fa fa-columns',
					'fa fa-copy',
					'fa fa-cut',
					'fa fa-dedent',
					'fa fa-eraser',
					'fa fa-file',
					'fa fa-file-o',
					'fa fa-file-text',
					'fa fa-file-text-o',
					'fa fa-files-o',
					'fa fa-floppy-o',
					'fa fa-font',
					'fa fa-header',
					'fa fa-indent',
					'fa fa-italic',
					'fa fa-link',
					'fa fa-list',
					'fa fa-list-alt',
					'fa fa-list-ol',
					'fa fa-list-ul',
					'fa fa-outdent',
					'fa fa-paperclip',
					'fa fa-paragraph',
					'fa fa-paste',
					'fa fa-repeat',
					'fa fa-rotate-left',
					'fa fa-rotate-right',
					'fa fa-save',
					'fa fa-scissors',
					'fa fa-strikethrough',
					'fa fa-subscript',
					'fa fa-superscript',
					'fa fa-table',
					'fa fa-text-height',
					'fa fa-text-width',
					'fa fa-th',
					'fa fa-th-large',
					'fa fa-th-list',
					'fa fa-underline',
					'fa fa-undo',
					'fa fa-unlink',
				),
			'directional' =>
				array (
					'fa fa-angle-double-down',
					'fa fa-angle-double-left',
					'fa fa-angle-double-right',
					'fa fa-angle-double-up',
					'fa fa-angle-down',
					'fa fa-angle-left',
					'fa fa-angle-right',
					'fa fa-angle-up',
					'fa fa-arrow-circle-down',
					'fa fa-arrow-circle-left',
					'fa fa-arrow-circle-o-down',
					'fa fa-arrow-circle-o-left',
					'fa fa-arrow-circle-o-right',
					'fa fa-arrow-circle-o-up',
					'fa fa-arrow-circle-right',
					'fa fa-arrow-circle-up',
					'fa fa-arrow-down',
					'fa fa-arrow-left',
					'fa fa-arrow-right',
					'fa fa-arrow-up',
					'fa fa-arrows',
					'fa fa-arrows-alt',
					'fa fa-arrows-h',
					'fa fa-arrows-v',
					'fa fa-caret-down',
					'fa fa-caret-left',
					'fa fa-caret-right',
					'fa fa-caret-square-o-down',
					'fa fa-caret-square-o-left',
					'fa fa-caret-square-o-right',
					'fa fa-caret-square-o-up',
					'fa fa-caret-up',
					'fa fa-chevron-circle-down',
					'fa fa-chevron-circle-left',
					'fa fa-chevron-circle-right',
					'fa fa-chevron-circle-up',
					'fa fa-chevron-down',
					'fa fa-chevron-left',
					'fa fa-chevron-right',
					'fa fa-chevron-up',
					'fa fa-exchange',
					'fa fa-hand-o-down',
					'fa fa-hand-o-left',
					'fa fa-hand-o-right',
					'fa fa-hand-o-up',
					'fa fa-long-arrow-down',
					'fa fa-long-arrow-left',
					'fa fa-long-arrow-right',
					'fa fa-long-arrow-up',
					'fa fa-toggle-down',
					'fa fa-toggle-left',
					'fa fa-toggle-right',
					'fa fa-toggle-up',
				),
			'video-player' =>
				array (
					'fa fa-arrows-alt',
					'fa fa-backward',
					'fa fa-compress',
					'fa fa-eject',
					'fa fa-expand',
					'fa fa-fast-backward',
					'fa fa-fast-forward',
					'fa fa-forward',
					'fa fa-pause',
					'fa fa-pause-circle',
					'fa fa-pause-circle-o',
					'fa fa-play',
					'fa fa-play-circle',
					'fa fa-play-circle-o',
					'fa fa-random',
					'fa fa-step-backward',
					'fa fa-step-forward',
					'fa fa-stop',
					'fa fa-stop-circle',
					'fa fa-stop-circle-o',
					'fa fa-youtube-play',
				),
			'brand' =>
				array (
					'fa fa-500px',
					'fa fa-adn',
					'fa fa-amazon',
					'fa fa-android',
					'fa fa-angellist',
					'fa fa-apple',
					'fa fa-bandcamp',
					'fa fa-behance',
					'fa fa-behance-square',
					'fa fa-bitbucket',
					'fa fa-bitbucket-square',
					'fa fa-bitcoin',
					'fa fa-black-tie',
					'fa fa-bluetooth',
					'fa fa-bluetooth-b',
					'fa fa-btc',
					'fa fa-buysellads',
					'fa fa-cc-amex',
					'fa fa-cc-diners-club',
					'fa fa-cc-discover',
					'fa fa-cc-jcb',
					'fa fa-cc-mastercard',
					'fa fa-cc-paypal',
					'fa fa-cc-stripe',
					'fa fa-cc-visa',
					'fa fa-chrome',
					'fa fa-codepen',
					'fa fa-codiepie',
					'fa fa-connectdevelop',
					'fa fa-contao',
					'fa fa-css3',
					'fa fa-dashcube',
					'fa fa-delicious',
					'fa fa-deviantart',
					'fa fa-digg',
					'fa fa-dribbble',
					'fa fa-dropbox',
					'fa fa-drupal',
					'fa fa-edge',
					'fa fa-eercast',
					'fa fa-empire',
					'fa fa-envira',
					'fa fa-etsy',
					'fa fa-expeditedssl',
					'fa fa-fa',
					'fa fa-facebook',
					'fa fa-facebook-f',
					'fa fa-facebook-official',
					'fa fa-facebook-square',
					'fa fa-firefox',
					'fa fa-first-order',
					'fa fa-flickr',
					'fa fa-font-awesome',
					'fa fa-fonticons',
					'fa fa-fort-awesome',
					'fa fa-forumbee',
					'fa fa-foursquare',
					'fa fa-free-code-camp',
					'fa fa-ge',
					'fa fa-get-pocket',
					'fa fa-gg',
					'fa fa-gg-circle',
					'fa fa-git',
					'fa fa-git-square',
					'fa fa-github',
					'fa fa-github-alt',
					'fa fa-github-square',
					'fa fa-gitlab',
					'fa fa-gittip',
					'fa fa-glide',
					'fa fa-glide-g',
					'fa fa-google',
					'fa fa-google-plus',
					'fa fa-google-plus-circle',
					'fa fa-google-plus-official',
					'fa fa-google-plus-square',
					'fa fa-google-wallet',
					'fa fa-gratipay',
					'fa fa-grav',
					'fa fa-hacker-news',
					'fa fa-houzz',
					'fa fa-html5',
					'fa fa-imdb',
					'fa fa-instagram',
					'fa fa-internet-explorer',
					'fa fa-ioxhost',
					'fa fa-joomla',
					'fa fa-jsfiddle',
					'fa fa-lastfm',
					'fa fa-lastfm-square',
					'fa fa-leanpub',
					'fa fa-linkedin',
					'fa fa-linkedin-square',
					'fa fa-linode',
					'fa fa-linux',
					'fa fa-maxcdn',
					'fa fa-meanpath',
					'fa fa-medium',
					'fa fa-meetup',
					'fa fa-mixcloud',
					'fa fa-modx',
					'fa fa-odnoklassniki',
					'fa fa-odnoklassniki-square',
					'fa fa-opencart',
					'fa fa-openid',
					'fa fa-opera',
					'fa fa-optin-monster',
					'fa fa-pagelines',
					'fa fa-paypal',
					'fa fa-pied-piper',
					'fa fa-pied-piper-alt',
					'fa fa-pied-piper-pp',
					'fa fa-pinterest',
					'fa fa-pinterest-p',
					'fa fa-pinterest-square',
					'fa fa-product-hunt',
					'fa fa-qq',
					'fa fa-quora',
					'fa fa-ra',
					'fa fa-ravelry',
					'fa fa-rebel',
					'fa fa-reddit',
					'fa fa-reddit-alien',
					'fa fa-reddit-square',
					'fa fa-renren',
					'fa fa-resistance',
					'fa fa-safari',
					'fa fa-scribd',
					'fa fa-sellsy',
					'fa fa-share-alt',
					'fa fa-share-alt-square',
					'fa fa-shirtsinbulk',
					'fa fa-simplybuilt',
					'fa fa-skyatlas',
					'fa fa-skype',
					'fa fa-slack',
					'fa fa-slideshare',
					'fa fa-snapchat',
					'fa fa-snapchat-ghost',
					'fa fa-snapchat-square',
					'fa fa-soundcloud',
					'fa fa-spotify',
					'fa fa-stack-exchange',
					'fa fa-stack-overflow',
					'fa fa-steam',
					'fa fa-steam-square',
					'fa fa-stumbleupon',
					'fa fa-stumbleupon-circle',
					'fa fa-superpowers',
					'fa fa-telegram',
					'fa fa-tencent-weibo',
					'fa fa-themeisle',
					'fa fa-trello',
					'fa fa-tripadvisor',
					'fa fa-tumblr',
					'fa fa-tumblr-square',
					'fa fa-twitch',
					'fa fa-twitter',
					'fa fa-twitter-square',
					'fa fa-usb',
					'fa fa-viacoin',
					'fa fa-viadeo',
					'fa fa-viadeo-square',
					'fa fa-vimeo',
					'fa fa-vimeo-square',
					'fa fa-vine',
					'fa fa-vk',
					'fa fa-wechat',
					'fa fa-weibo',
					'fa fa-weixin',
					'fa fa-whatsapp',
					'fa fa-wikipedia-w',
					'fa fa-windows',
					'fa fa-wordpress',
					'fa fa-wpbeginner',
					'fa fa-wpexplorer',
					'fa fa-wpforms',
					'fa fa-xing',
					'fa fa-xing-square',
					'fa fa-y-combinator',
					'fa fa-y-combinator-square',
					'fa fa-yahoo',
					'fa fa-yc',
					'fa fa-yc-square',
					'fa fa-yelp',
					'fa fa-yoast',
					'fa fa-youtube',
					'fa fa-youtube-play',
					'fa fa-youtube-square',
				),
			'medical' =>
				array (
					'fa fa-ambulance',
					'fa fa-h-square',
					'fa fa-heart',
					'fa fa-heart-o',
					'fa fa-heartbeat',
					'fa fa-hospital-o',
					'fa fa-medkit',
					'fa fa-plus-square',
					'fa fa-stethoscope',
					'fa fa-user-md',
					'fa fa-wheelchair',
					'fa fa-wheelchair-alt',
				),
		)
	);

}

function eonet_get_google_fonts() {

	return array (
		'ABeeZee' =>
			array (
				'family' => 'ABeeZee',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Abel' =>
			array (
				'family' => 'Abel',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Abhaya Libre' =>
			array (
				'family' => 'Abhaya Libre',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
						4 => '800',
					),
			),
		'Abril Fatface' =>
			array (
				'family' => 'Abril Fatface',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Aclonica' =>
			array (
				'family' => 'Aclonica',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Acme' =>
			array (
				'family' => 'Acme',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Actor' =>
			array (
				'family' => 'Actor',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Adamina' =>
			array (
				'family' => 'Adamina',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Advent Pro' =>
			array (
				'family' => 'Advent Pro',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '200',
						2 => '300',
						3 => 'regular',
						4 => '500',
						5 => '600',
						6 => '700',
					),
			),
		'Aguafina Script' =>
			array (
				'family' => 'Aguafina Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Akronim' =>
			array (
				'family' => 'Akronim',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Aladin' =>
			array (
				'family' => 'Aladin',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Aldrich' =>
			array (
				'family' => 'Aldrich',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Alef' =>
			array (
				'family' => 'Alef',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Alegreya' =>
			array (
				'family' => 'Alegreya',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
						4 => '900',
						5 => '900italic',
					),
			),
		'Alegreya SC' =>
			array (
				'family' => 'Alegreya SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
						4 => '900',
						5 => '900italic',
					),
			),
		'Alegreya Sans' =>
			array (
				'family' => 'Alegreya Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '500',
						7 => '500italic',
						8 => '700',
						9 => '700italic',
						10 => '800',
						11 => '800italic',
						12 => '900',
						13 => '900italic',
					),
			),
		'Alegreya Sans SC' =>
			array (
				'family' => 'Alegreya Sans SC',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '500',
						7 => '500italic',
						8 => '700',
						9 => '700italic',
						10 => '800',
						11 => '800italic',
						12 => '900',
						13 => '900italic',
					),
			),
		'Alex Brush' =>
			array (
				'family' => 'Alex Brush',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Alfa Slab One' =>
			array (
				'family' => 'Alfa Slab One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Alice' =>
			array (
				'family' => 'Alice',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Alike' =>
			array (
				'family' => 'Alike',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Alike Angular' =>
			array (
				'family' => 'Alike Angular',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Allan' =>
			array (
				'family' => 'Allan',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Allerta' =>
			array (
				'family' => 'Allerta',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Allerta Stencil' =>
			array (
				'family' => 'Allerta Stencil',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Allura' =>
			array (
				'family' => 'Allura',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Almendra' =>
			array (
				'family' => 'Almendra',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Almendra Display' =>
			array (
				'family' => 'Almendra Display',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Almendra SC' =>
			array (
				'family' => 'Almendra SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Amarante' =>
			array (
				'family' => 'Amarante',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Amaranth' =>
			array (
				'family' => 'Amaranth',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Amatic SC' =>
			array (
				'family' => 'Amatic SC',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Amatica SC' =>
			array (
				'family' => 'Amatica SC',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Amethysta' =>
			array (
				'family' => 'Amethysta',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Amiko' =>
			array (
				'family' => 'Amiko',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '600',
						2 => '700',
					),
			),
		'Amiri' =>
			array (
				'family' => 'Amiri',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Amita' =>
			array (
				'family' => 'Amita',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Anaheim' =>
			array (
				'family' => 'Anaheim',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Andada' =>
			array (
				'family' => 'Andada',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Andika' =>
			array (
				'family' => 'Andika',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Angkor' =>
			array (
				'family' => 'Angkor',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Annie Use Your Telescope' =>
			array (
				'family' => 'Annie Use Your Telescope',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Anonymous Pro' =>
			array (
				'family' => 'Anonymous Pro',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Antic' =>
			array (
				'family' => 'Antic',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Antic Didone' =>
			array (
				'family' => 'Antic Didone',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Antic Slab' =>
			array (
				'family' => 'Antic Slab',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Anton' =>
			array (
				'family' => 'Anton',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Arapey' =>
			array (
				'family' => 'Arapey',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Arbutus' =>
			array (
				'family' => 'Arbutus',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Arbutus Slab' =>
			array (
				'family' => 'Arbutus Slab',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Architects Daughter' =>
			array (
				'family' => 'Architects Daughter',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Archivo Black' =>
			array (
				'family' => 'Archivo Black',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Archivo Narrow' =>
			array (
				'family' => 'Archivo Narrow',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Aref Ruqaa' =>
			array (
				'family' => 'Aref Ruqaa',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Arima Madurai' =>
			array (
				'family' => 'Arima Madurai',
				'category' => 'display',
				'variants' =>
					array (
						0 => '100',
						1 => '200',
						2 => '300',
						3 => 'regular',
						4 => '500',
						5 => '700',
						6 => '800',
						7 => '900',
					),
			),
		'Arimo' =>
			array (
				'family' => 'Arimo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Arizonia' =>
			array (
				'family' => 'Arizonia',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Armata' =>
			array (
				'family' => 'Armata',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Artifika' =>
			array (
				'family' => 'Artifika',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Arvo' =>
			array (
				'family' => 'Arvo',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Arya' =>
			array (
				'family' => 'Arya',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Asap' =>
			array (
				'family' => 'Asap',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '500',
						3 => '500italic',
						4 => '700',
						5 => '700italic',
					),
			),
		'Asar' =>
			array (
				'family' => 'Asar',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Asset' =>
			array (
				'family' => 'Asset',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Assistant' =>
			array (
				'family' => 'Assistant',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '600',
						4 => '700',
						5 => '800',
					),
			),
		'Astloch' =>
			array (
				'family' => 'Astloch',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Asul' =>
			array (
				'family' => 'Asul',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Athiti' =>
			array (
				'family' => 'Athiti',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
					),
			),
		'Atma' =>
			array (
				'family' => 'Atma',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Atomic Age' =>
			array (
				'family' => 'Atomic Age',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Aubrey' =>
			array (
				'family' => 'Aubrey',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Audiowide' =>
			array (
				'family' => 'Audiowide',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Autour One' =>
			array (
				'family' => 'Autour One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Average' =>
			array (
				'family' => 'Average',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Average Sans' =>
			array (
				'family' => 'Average Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Averia Gruesa Libre' =>
			array (
				'family' => 'Averia Gruesa Libre',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Averia Libre' =>
			array (
				'family' => 'Averia Libre',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '700',
						5 => '700italic',
					),
			),
		'Averia Sans Libre' =>
			array (
				'family' => 'Averia Sans Libre',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '700',
						5 => '700italic',
					),
			),
		'Averia Serif Libre' =>
			array (
				'family' => 'Averia Serif Libre',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '700',
						5 => '700italic',
					),
			),
		'Bad Script' =>
			array (
				'family' => 'Bad Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo' =>
			array (
				'family' => 'Baloo',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo Bhai' =>
			array (
				'family' => 'Baloo Bhai',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo Bhaina' =>
			array (
				'family' => 'Baloo Bhaina',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo Chettan' =>
			array (
				'family' => 'Baloo Chettan',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo Da' =>
			array (
				'family' => 'Baloo Da',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo Paaji' =>
			array (
				'family' => 'Baloo Paaji',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo Tamma' =>
			array (
				'family' => 'Baloo Tamma',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Baloo Thambi' =>
			array (
				'family' => 'Baloo Thambi',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Balthazar' =>
			array (
				'family' => 'Balthazar',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bangers' =>
			array (
				'family' => 'Bangers',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Basic' =>
			array (
				'family' => 'Basic',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Battambang' =>
			array (
				'family' => 'Battambang',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Baumans' =>
			array (
				'family' => 'Baumans',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bayon' =>
			array (
				'family' => 'Bayon',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Belgrano' =>
			array (
				'family' => 'Belgrano',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Belleza' =>
			array (
				'family' => 'Belleza',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'BenchNine' =>
			array (
				'family' => 'BenchNine',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '700',
					),
			),
		'Bentham' =>
			array (
				'family' => 'Bentham',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Berkshire Swash' =>
			array (
				'family' => 'Berkshire Swash',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bevan' =>
			array (
				'family' => 'Bevan',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bigelow Rules' =>
			array (
				'family' => 'Bigelow Rules',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bigshot One' =>
			array (
				'family' => 'Bigshot One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bilbo' =>
			array (
				'family' => 'Bilbo',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bilbo Swash Caps' =>
			array (
				'family' => 'Bilbo Swash Caps',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'BioRhyme' =>
			array (
				'family' => 'BioRhyme',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '700',
						4 => '800',
					),
			),
		'BioRhyme Expanded' =>
			array (
				'family' => 'BioRhyme Expanded',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '700',
						4 => '800',
					),
			),
		'Biryani' =>
			array (
				'family' => 'Biryani',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '600',
						4 => '700',
						5 => '800',
						6 => '900',
					),
			),
		'Bitter' =>
			array (
				'family' => 'Bitter',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Black Ops One' =>
			array (
				'family' => 'Black Ops One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bokor' =>
			array (
				'family' => 'Bokor',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bonbon' =>
			array (
				'family' => 'Bonbon',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Boogaloo' =>
			array (
				'family' => 'Boogaloo',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bowlby One' =>
			array (
				'family' => 'Bowlby One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bowlby One SC' =>
			array (
				'family' => 'Bowlby One SC',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Brawler' =>
			array (
				'family' => 'Brawler',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bree Serif' =>
			array (
				'family' => 'Bree Serif',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bubblegum Sans' =>
			array (
				'family' => 'Bubblegum Sans',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bubbler One' =>
			array (
				'family' => 'Bubbler One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Buda' =>
			array (
				'family' => 'Buda',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
					),
			),
		'Buenard' =>
			array (
				'family' => 'Buenard',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Bungee' =>
			array (
				'family' => 'Bungee',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bungee Hairline' =>
			array (
				'family' => 'Bungee Hairline',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bungee Inline' =>
			array (
				'family' => 'Bungee Inline',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bungee Outline' =>
			array (
				'family' => 'Bungee Outline',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Bungee Shade' =>
			array (
				'family' => 'Bungee Shade',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Butcherman' =>
			array (
				'family' => 'Butcherman',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Butterfly Kids' =>
			array (
				'family' => 'Butterfly Kids',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cabin' =>
			array (
				'family' => 'Cabin',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '500',
						3 => '500italic',
						4 => '600',
						5 => '600italic',
						6 => '700',
						7 => '700italic',
					),
			),
		'Cabin Condensed' =>
			array (
				'family' => 'Cabin Condensed',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
					),
			),
		'Cabin Sketch' =>
			array (
				'family' => 'Cabin Sketch',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Caesar Dressing' =>
			array (
				'family' => 'Caesar Dressing',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cagliostro' =>
			array (
				'family' => 'Cagliostro',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cairo' =>
			array (
				'family' => 'Cairo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '600',
						4 => '700',
						5 => '900',
					),
			),
		'Calligraffitti' =>
			array (
				'family' => 'Calligraffitti',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cambay' =>
			array (
				'family' => 'Cambay',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Cambo' =>
			array (
				'family' => 'Cambo',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Candal' =>
			array (
				'family' => 'Candal',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cantarell' =>
			array (
				'family' => 'Cantarell',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Cantata One' =>
			array (
				'family' => 'Cantata One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cantora One' =>
			array (
				'family' => 'Cantora One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Capriola' =>
			array (
				'family' => 'Capriola',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cardo' =>
			array (
				'family' => 'Cardo',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Carme' =>
			array (
				'family' => 'Carme',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Carrois Gothic' =>
			array (
				'family' => 'Carrois Gothic',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Carrois Gothic SC' =>
			array (
				'family' => 'Carrois Gothic SC',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Carter One' =>
			array (
				'family' => 'Carter One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Catamaran' =>
			array (
				'family' => 'Catamaran',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '200',
						2 => '300',
						3 => 'regular',
						4 => '500',
						5 => '600',
						6 => '700',
						7 => '800',
						8 => '900',
					),
			),
		'Caudex' =>
			array (
				'family' => 'Caudex',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Caveat' =>
			array (
				'family' => 'Caveat',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Caveat Brush' =>
			array (
				'family' => 'Caveat Brush',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cedarville Cursive' =>
			array (
				'family' => 'Cedarville Cursive',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ceviche One' =>
			array (
				'family' => 'Ceviche One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Changa' =>
			array (
				'family' => 'Changa',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
						6 => '800',
					),
			),
		'Changa One' =>
			array (
				'family' => 'Changa One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Chango' =>
			array (
				'family' => 'Chango',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Chathura' =>
			array (
				'family' => 'Chathura',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '300',
						2 => 'regular',
						3 => '700',
						4 => '800',
					),
			),
		'Chau Philomene One' =>
			array (
				'family' => 'Chau Philomene One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Chela One' =>
			array (
				'family' => 'Chela One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Chelsea Market' =>
			array (
				'family' => 'Chelsea Market',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Chenla' =>
			array (
				'family' => 'Chenla',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cherry Cream Soda' =>
			array (
				'family' => 'Cherry Cream Soda',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cherry Swash' =>
			array (
				'family' => 'Cherry Swash',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Chewy' =>
			array (
				'family' => 'Chewy',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Chicle' =>
			array (
				'family' => 'Chicle',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Chivo' =>
			array (
				'family' => 'Chivo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '900',
						3 => '900italic',
					),
			),
		'Chonburi' =>
			array (
				'family' => 'Chonburi',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cinzel' =>
			array (
				'family' => 'Cinzel',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
						2 => '900',
					),
			),
		'Cinzel Decorative' =>
			array (
				'family' => 'Cinzel Decorative',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
						2 => '900',
					),
			),
		'Clicker Script' =>
			array (
				'family' => 'Clicker Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Coda' =>
			array (
				'family' => 'Coda',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '800',
					),
			),
		'Coda Caption' =>
			array (
				'family' => 'Coda Caption',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '800',
					),
			),
		'Codystar' =>
			array (
				'family' => 'Codystar',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
					),
			),
		'Coiny' =>
			array (
				'family' => 'Coiny',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Combo' =>
			array (
				'family' => 'Combo',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Comfortaa' =>
			array (
				'family' => 'Comfortaa',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '700',
					),
			),
		'Coming Soon' =>
			array (
				'family' => 'Coming Soon',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Concert One' =>
			array (
				'family' => 'Concert One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Condiment' =>
			array (
				'family' => 'Condiment',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Content' =>
			array (
				'family' => 'Content',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Contrail One' =>
			array (
				'family' => 'Contrail One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Convergence' =>
			array (
				'family' => 'Convergence',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cookie' =>
			array (
				'family' => 'Cookie',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Copse' =>
			array (
				'family' => 'Copse',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Corben' =>
			array (
				'family' => 'Corben',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Cormorant' =>
			array (
				'family' => 'Cormorant',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '500',
						5 => '500italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
					),
			),
		'Cormorant Garamond' =>
			array (
				'family' => 'Cormorant Garamond',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '500',
						5 => '500italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
					),
			),
		'Cormorant Infant' =>
			array (
				'family' => 'Cormorant Infant',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '500',
						5 => '500italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
					),
			),
		'Cormorant SC' =>
			array (
				'family' => 'Cormorant SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Cormorant Unicase' =>
			array (
				'family' => 'Cormorant Unicase',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Cormorant Upright' =>
			array (
				'family' => 'Cormorant Upright',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Courgette' =>
			array (
				'family' => 'Courgette',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cousine' =>
			array (
				'family' => 'Cousine',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Coustard' =>
			array (
				'family' => 'Coustard',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '900',
					),
			),
		'Covered By Your Grace' =>
			array (
				'family' => 'Covered By Your Grace',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Crafty Girls' =>
			array (
				'family' => 'Crafty Girls',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Creepster' =>
			array (
				'family' => 'Creepster',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Crete Round' =>
			array (
				'family' => 'Crete Round',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Crimson Text' =>
			array (
				'family' => 'Crimson Text',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '600',
						3 => '600italic',
						4 => '700',
						5 => '700italic',
					),
			),
		'Croissant One' =>
			array (
				'family' => 'Croissant One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Crushed' =>
			array (
				'family' => 'Crushed',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cuprum' =>
			array (
				'family' => 'Cuprum',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Cutive' =>
			array (
				'family' => 'Cutive',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Cutive Mono' =>
			array (
				'family' => 'Cutive Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Damion' =>
			array (
				'family' => 'Damion',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Dancing Script' =>
			array (
				'family' => 'Dancing Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Dangrek' =>
			array (
				'family' => 'Dangrek',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'David Libre' =>
			array (
				'family' => 'David Libre',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '700',
					),
			),
		'Dawning of a New Day' =>
			array (
				'family' => 'Dawning of a New Day',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Days One' =>
			array (
				'family' => 'Days One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Dekko' =>
			array (
				'family' => 'Dekko',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Delius' =>
			array (
				'family' => 'Delius',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Delius Swash Caps' =>
			array (
				'family' => 'Delius Swash Caps',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Delius Unicase' =>
			array (
				'family' => 'Delius Unicase',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Della Respira' =>
			array (
				'family' => 'Della Respira',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Denk One' =>
			array (
				'family' => 'Denk One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Devonshire' =>
			array (
				'family' => 'Devonshire',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Dhurjati' =>
			array (
				'family' => 'Dhurjati',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Didact Gothic' =>
			array (
				'family' => 'Didact Gothic',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Diplomata' =>
			array (
				'family' => 'Diplomata',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Diplomata SC' =>
			array (
				'family' => 'Diplomata SC',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Domine' =>
			array (
				'family' => 'Domine',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Donegal One' =>
			array (
				'family' => 'Donegal One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Doppio One' =>
			array (
				'family' => 'Doppio One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Dorsa' =>
			array (
				'family' => 'Dorsa',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Dosis' =>
			array (
				'family' => 'Dosis',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
						6 => '800',
					),
			),
		'Dr Sugiyama' =>
			array (
				'family' => 'Dr Sugiyama',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Droid Sans' =>
			array (
				'family' => 'Droid Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Droid Sans Mono' =>
			array (
				'family' => 'Droid Sans Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Droid Serif' =>
			array (
				'family' => 'Droid Serif',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Duru Sans' =>
			array (
				'family' => 'Duru Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Dynalight' =>
			array (
				'family' => 'Dynalight',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'EB Garamond' =>
			array (
				'family' => 'EB Garamond',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Eagle Lake' =>
			array (
				'family' => 'Eagle Lake',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Eater' =>
			array (
				'family' => 'Eater',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Economica' =>
			array (
				'family' => 'Economica',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Eczar' =>
			array (
				'family' => 'Eczar',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
						4 => '800',
					),
			),
		'Ek Mukta' =>
			array (
				'family' => 'Ek Mukta',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
						6 => '800',
					),
			),
		'El Messiri' =>
			array (
				'family' => 'El Messiri',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
					),
			),
		'Electrolize' =>
			array (
				'family' => 'Electrolize',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Elsie' =>
			array (
				'family' => 'Elsie',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '900',
					),
			),
		'Elsie Swash Caps' =>
			array (
				'family' => 'Elsie Swash Caps',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '900',
					),
			),
		'Emblema One' =>
			array (
				'family' => 'Emblema One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Emilys Candy' =>
			array (
				'family' => 'Emilys Candy',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Engagement' =>
			array (
				'family' => 'Engagement',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Englebert' =>
			array (
				'family' => 'Englebert',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Enriqueta' =>
			array (
				'family' => 'Enriqueta',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Erica One' =>
			array (
				'family' => 'Erica One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Esteban' =>
			array (
				'family' => 'Esteban',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Euphoria Script' =>
			array (
				'family' => 'Euphoria Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ewert' =>
			array (
				'family' => 'Ewert',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Exo' =>
			array (
				'family' => 'Exo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Exo 2' =>
			array (
				'family' => 'Exo 2',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Expletus Sans' =>
			array (
				'family' => 'Expletus Sans',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '500',
						3 => '500italic',
						4 => '600',
						5 => '600italic',
						6 => '700',
						7 => '700italic',
					),
			),
		'Fanwood Text' =>
			array (
				'family' => 'Fanwood Text',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Farsan' =>
			array (
				'family' => 'Farsan',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fascinate' =>
			array (
				'family' => 'Fascinate',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fascinate Inline' =>
			array (
				'family' => 'Fascinate Inline',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Faster One' =>
			array (
				'family' => 'Faster One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fasthand' =>
			array (
				'family' => 'Fasthand',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fauna One' =>
			array (
				'family' => 'Fauna One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Federant' =>
			array (
				'family' => 'Federant',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Federo' =>
			array (
				'family' => 'Federo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Felipa' =>
			array (
				'family' => 'Felipa',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fenix' =>
			array (
				'family' => 'Fenix',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Finger Paint' =>
			array (
				'family' => 'Finger Paint',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fira Mono' =>
			array (
				'family' => 'Fira Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Fira Sans' =>
			array (
				'family' => 'Fira Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '500',
						5 => '500italic',
						6 => '700',
						7 => '700italic',
					),
			),
		'Fjalla One' =>
			array (
				'family' => 'Fjalla One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fjord One' =>
			array (
				'family' => 'Fjord One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Flamenco' =>
			array (
				'family' => 'Flamenco',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
					),
			),
		'Flavors' =>
			array (
				'family' => 'Flavors',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fondamento' =>
			array (
				'family' => 'Fondamento',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Fontdiner Swanky' =>
			array (
				'family' => 'Fontdiner Swanky',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Forum' =>
			array (
				'family' => 'Forum',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Francois One' =>
			array (
				'family' => 'Francois One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Frank Ruhl Libre' =>
			array (
				'family' => 'Frank Ruhl Libre',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '700',
						4 => '900',
					),
			),
		'Freckle Face' =>
			array (
				'family' => 'Freckle Face',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fredericka the Great' =>
			array (
				'family' => 'Fredericka the Great',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fredoka One' =>
			array (
				'family' => 'Fredoka One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Freehand' =>
			array (
				'family' => 'Freehand',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fresca' =>
			array (
				'family' => 'Fresca',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Frijole' =>
			array (
				'family' => 'Frijole',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fruktur' =>
			array (
				'family' => 'Fruktur',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Fugaz One' =>
			array (
				'family' => 'Fugaz One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'GFS Didot' =>
			array (
				'family' => 'GFS Didot',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'GFS Neohellenic' =>
			array (
				'family' => 'GFS Neohellenic',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Gabriela' =>
			array (
				'family' => 'Gabriela',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gafata' =>
			array (
				'family' => 'Gafata',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Galada' =>
			array (
				'family' => 'Galada',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Galdeano' =>
			array (
				'family' => 'Galdeano',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Galindo' =>
			array (
				'family' => 'Galindo',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gentium Basic' =>
			array (
				'family' => 'Gentium Basic',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Gentium Book Basic' =>
			array (
				'family' => 'Gentium Book Basic',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Geo' =>
			array (
				'family' => 'Geo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Geostar' =>
			array (
				'family' => 'Geostar',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Geostar Fill' =>
			array (
				'family' => 'Geostar Fill',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Germania One' =>
			array (
				'family' => 'Germania One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gidugu' =>
			array (
				'family' => 'Gidugu',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gilda Display' =>
			array (
				'family' => 'Gilda Display',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Give You Glory' =>
			array (
				'family' => 'Give You Glory',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Glass Antiqua' =>
			array (
				'family' => 'Glass Antiqua',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Glegoo' =>
			array (
				'family' => 'Glegoo',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Gloria Hallelujah' =>
			array (
				'family' => 'Gloria Hallelujah',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Goblin One' =>
			array (
				'family' => 'Goblin One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gochi Hand' =>
			array (
				'family' => 'Gochi Hand',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gorditas' =>
			array (
				'family' => 'Gorditas',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Goudy Bookletter 1911' =>
			array (
				'family' => 'Goudy Bookletter 1911',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Graduate' =>
			array (
				'family' => 'Graduate',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Grand Hotel' =>
			array (
				'family' => 'Grand Hotel',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gravitas One' =>
			array (
				'family' => 'Gravitas One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Great Vibes' =>
			array (
				'family' => 'Great Vibes',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Griffy' =>
			array (
				'family' => 'Griffy',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gruppo' =>
			array (
				'family' => 'Gruppo',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Gudea' =>
			array (
				'family' => 'Gudea',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Gurajada' =>
			array (
				'family' => 'Gurajada',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Habibi' =>
			array (
				'family' => 'Habibi',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Halant' =>
			array (
				'family' => 'Halant',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Hammersmith One' =>
			array (
				'family' => 'Hammersmith One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Hanalei' =>
			array (
				'family' => 'Hanalei',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Hanalei Fill' =>
			array (
				'family' => 'Hanalei Fill',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Handlee' =>
			array (
				'family' => 'Handlee',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Hanuman' =>
			array (
				'family' => 'Hanuman',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Happy Monkey' =>
			array (
				'family' => 'Happy Monkey',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Harmattan' =>
			array (
				'family' => 'Harmattan',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Headland One' =>
			array (
				'family' => 'Headland One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Heebo' =>
			array (
				'family' => 'Heebo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '700',
						5 => '800',
						6 => '900',
					),
			),
		'Henny Penny' =>
			array (
				'family' => 'Henny Penny',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Herr Von Muellerhoff' =>
			array (
				'family' => 'Herr Von Muellerhoff',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Hind' =>
			array (
				'family' => 'Hind',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Hind Guntur' =>
			array (
				'family' => 'Hind Guntur',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Hind Madurai' =>
			array (
				'family' => 'Hind Madurai',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Hind Siliguri' =>
			array (
				'family' => 'Hind Siliguri',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Hind Vadodara' =>
			array (
				'family' => 'Hind Vadodara',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Holtwood One SC' =>
			array (
				'family' => 'Holtwood One SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Homemade Apple' =>
			array (
				'family' => 'Homemade Apple',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Homenaje' =>
			array (
				'family' => 'Homenaje',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'IM Fell DW Pica' =>
			array (
				'family' => 'IM Fell DW Pica',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'IM Fell DW Pica SC' =>
			array (
				'family' => 'IM Fell DW Pica SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'IM Fell Double Pica' =>
			array (
				'family' => 'IM Fell Double Pica',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'IM Fell Double Pica SC' =>
			array (
				'family' => 'IM Fell Double Pica SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'IM Fell English' =>
			array (
				'family' => 'IM Fell English',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'IM Fell English SC' =>
			array (
				'family' => 'IM Fell English SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'IM Fell French Canon' =>
			array (
				'family' => 'IM Fell French Canon',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'IM Fell French Canon SC' =>
			array (
				'family' => 'IM Fell French Canon SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'IM Fell Great Primer' =>
			array (
				'family' => 'IM Fell Great Primer',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'IM Fell Great Primer SC' =>
			array (
				'family' => 'IM Fell Great Primer SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Iceberg' =>
			array (
				'family' => 'Iceberg',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Iceland' =>
			array (
				'family' => 'Iceland',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Imprima' =>
			array (
				'family' => 'Imprima',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Inconsolata' =>
			array (
				'family' => 'Inconsolata',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Inder' =>
			array (
				'family' => 'Inder',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Indie Flower' =>
			array (
				'family' => 'Indie Flower',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Inika' =>
			array (
				'family' => 'Inika',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Inknut Antiqua' =>
			array (
				'family' => 'Inknut Antiqua',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
						5 => '800',
						6 => '900',
					),
			),
		'Irish Grover' =>
			array (
				'family' => 'Irish Grover',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Istok Web' =>
			array (
				'family' => 'Istok Web',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Italiana' =>
			array (
				'family' => 'Italiana',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Italianno' =>
			array (
				'family' => 'Italianno',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Itim' =>
			array (
				'family' => 'Itim',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Jacques Francois' =>
			array (
				'family' => 'Jacques Francois',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Jacques Francois Shadow' =>
			array (
				'family' => 'Jacques Francois Shadow',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Jaldi' =>
			array (
				'family' => 'Jaldi',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Jim Nightshade' =>
			array (
				'family' => 'Jim Nightshade',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Jockey One' =>
			array (
				'family' => 'Jockey One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Jolly Lodger' =>
			array (
				'family' => 'Jolly Lodger',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Jomhuria' =>
			array (
				'family' => 'Jomhuria',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Josefin Sans' =>
			array (
				'family' => 'Josefin Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
					),
			),
		'Josefin Slab' =>
			array (
				'family' => 'Josefin Slab',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
					),
			),
		'Joti One' =>
			array (
				'family' => 'Joti One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Judson' =>
			array (
				'family' => 'Judson',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Julee' =>
			array (
				'family' => 'Julee',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Julius Sans One' =>
			array (
				'family' => 'Julius Sans One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Junge' =>
			array (
				'family' => 'Junge',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Jura' =>
			array (
				'family' => 'Jura',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
					),
			),
		'Just Another Hand' =>
			array (
				'family' => 'Just Another Hand',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Just Me Again Down Here' =>
			array (
				'family' => 'Just Me Again Down Here',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kadwa' =>
			array (
				'family' => 'Kadwa',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Kalam' =>
			array (
				'family' => 'Kalam',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '700',
					),
			),
		'Kameron' =>
			array (
				'family' => 'Kameron',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Kanit' =>
			array (
				'family' => 'Kanit',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Kantumruy' =>
			array (
				'family' => 'Kantumruy',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '700',
					),
			),
		'Karla' =>
			array (
				'family' => 'Karla',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Karma' =>
			array (
				'family' => 'Karma',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Katibeh' =>
			array (
				'family' => 'Katibeh',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kaushan Script' =>
			array (
				'family' => 'Kaushan Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kavivanar' =>
			array (
				'family' => 'Kavivanar',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kavoon' =>
			array (
				'family' => 'Kavoon',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kdam Thmor' =>
			array (
				'family' => 'Kdam Thmor',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Keania One' =>
			array (
				'family' => 'Keania One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kelly Slab' =>
			array (
				'family' => 'Kelly Slab',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kenia' =>
			array (
				'family' => 'Kenia',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Khand' =>
			array (
				'family' => 'Khand',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Khmer' =>
			array (
				'family' => 'Khmer',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Khula' =>
			array (
				'family' => 'Khula',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '600',
						3 => '700',
						4 => '800',
					),
			),
		'Kite One' =>
			array (
				'family' => 'Kite One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Knewave' =>
			array (
				'family' => 'Knewave',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kotta One' =>
			array (
				'family' => 'Kotta One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Koulen' =>
			array (
				'family' => 'Koulen',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kranky' =>
			array (
				'family' => 'Kranky',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kreon' =>
			array (
				'family' => 'Kreon',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '700',
					),
			),
		'Kristi' =>
			array (
				'family' => 'Kristi',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Krona One' =>
			array (
				'family' => 'Krona One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kumar One' =>
			array (
				'family' => 'Kumar One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kumar One Outline' =>
			array (
				'family' => 'Kumar One Outline',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Kurale' =>
			array (
				'family' => 'Kurale',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'La Belle Aurore' =>
			array (
				'family' => 'La Belle Aurore',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Laila' =>
			array (
				'family' => 'Laila',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Lakki Reddy' =>
			array (
				'family' => 'Lakki Reddy',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lalezar' =>
			array (
				'family' => 'Lalezar',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lancelot' =>
			array (
				'family' => 'Lancelot',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lateef' =>
			array (
				'family' => 'Lateef',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lato' =>
			array (
				'family' => 'Lato',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '700',
						7 => '700italic',
						8 => '900',
						9 => '900italic',
					),
			),
		'League Script' =>
			array (
				'family' => 'League Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Leckerli One' =>
			array (
				'family' => 'Leckerli One',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ledger' =>
			array (
				'family' => 'Ledger',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lekton' =>
			array (
				'family' => 'Lekton',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Lemon' =>
			array (
				'family' => 'Lemon',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lemonada' =>
			array (
				'family' => 'Lemonada',
				'category' => 'display',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '600',
						3 => '700',
					),
			),
		'Libre Baskerville' =>
			array (
				'family' => 'Libre Baskerville',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Libre Franklin' =>
			array (
				'family' => 'Libre Franklin',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Life Savers' =>
			array (
				'family' => 'Life Savers',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Lilita One' =>
			array (
				'family' => 'Lilita One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lily Script One' =>
			array (
				'family' => 'Lily Script One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Limelight' =>
			array (
				'family' => 'Limelight',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Linden Hill' =>
			array (
				'family' => 'Linden Hill',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Lobster' =>
			array (
				'family' => 'Lobster',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lobster Two' =>
			array (
				'family' => 'Lobster Two',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Londrina Outline' =>
			array (
				'family' => 'Londrina Outline',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Londrina Shadow' =>
			array (
				'family' => 'Londrina Shadow',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Londrina Sketch' =>
			array (
				'family' => 'Londrina Sketch',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Londrina Solid' =>
			array (
				'family' => 'Londrina Solid',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lora' =>
			array (
				'family' => 'Lora',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Love Ya Like A Sister' =>
			array (
				'family' => 'Love Ya Like A Sister',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Loved by the King' =>
			array (
				'family' => 'Loved by the King',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lovers Quarrel' =>
			array (
				'family' => 'Lovers Quarrel',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Luckiest Guy' =>
			array (
				'family' => 'Luckiest Guy',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Lusitana' =>
			array (
				'family' => 'Lusitana',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Lustria' =>
			array (
				'family' => 'Lustria',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Macondo' =>
			array (
				'family' => 'Macondo',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Macondo Swash Caps' =>
			array (
				'family' => 'Macondo Swash Caps',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mada' =>
			array (
				'family' => 'Mada',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '900',
					),
			),
		'Magra' =>
			array (
				'family' => 'Magra',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Maiden Orange' =>
			array (
				'family' => 'Maiden Orange',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Maitree' =>
			array (
				'family' => 'Maitree',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
					),
			),
		'Mako' =>
			array (
				'family' => 'Mako',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mallanna' =>
			array (
				'family' => 'Mallanna',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mandali' =>
			array (
				'family' => 'Mandali',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Marcellus' =>
			array (
				'family' => 'Marcellus',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Marcellus SC' =>
			array (
				'family' => 'Marcellus SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Marck Script' =>
			array (
				'family' => 'Marck Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Margarine' =>
			array (
				'family' => 'Margarine',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Marko One' =>
			array (
				'family' => 'Marko One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Marmelad' =>
			array (
				'family' => 'Marmelad',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Martel' =>
			array (
				'family' => 'Martel',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '600',
						4 => '700',
						5 => '800',
						6 => '900',
					),
			),
		'Martel Sans' =>
			array (
				'family' => 'Martel Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '600',
						4 => '700',
						5 => '800',
						6 => '900',
					),
			),
		'Marvel' =>
			array (
				'family' => 'Marvel',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Mate' =>
			array (
				'family' => 'Mate',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Mate SC' =>
			array (
				'family' => 'Mate SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Maven Pro' =>
			array (
				'family' => 'Maven Pro',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '700',
						3 => '900',
					),
			),
		'McLaren' =>
			array (
				'family' => 'McLaren',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Meddon' =>
			array (
				'family' => 'Meddon',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'MediSomething is wrongSharp' =>
			array (
				'family' => 'MediSomething is wrongSharp',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Medula One' =>
			array (
				'family' => 'Medula One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Meera Inimai' =>
			array (
				'family' => 'Meera Inimai',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Megrim' =>
			array (
				'family' => 'Megrim',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Meie Script' =>
			array (
				'family' => 'Meie Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Merienda' =>
			array (
				'family' => 'Merienda',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Merienda One' =>
			array (
				'family' => 'Merienda One',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Merriweather' =>
			array (
				'family' => 'Merriweather',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '700',
						5 => '700italic',
						6 => '900',
						7 => '900italic',
					),
			),
		'Merriweather Sans' =>
			array (
				'family' => 'Merriweather Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '700',
						5 => '700italic',
						6 => '800',
						7 => '800italic',
					),
			),
		'Metal' =>
			array (
				'family' => 'Metal',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Metal Mania' =>
			array (
				'family' => 'Metal Mania',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Metamorphous' =>
			array (
				'family' => 'Metamorphous',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Metrophobic' =>
			array (
				'family' => 'Metrophobic',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Michroma' =>
			array (
				'family' => 'Michroma',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Milonga' =>
			array (
				'family' => 'Milonga',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Miltonian' =>
			array (
				'family' => 'Miltonian',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Miltonian Tattoo' =>
			array (
				'family' => 'Miltonian Tattoo',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Miniver' =>
			array (
				'family' => 'Miniver',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Miriam Libre' =>
			array (
				'family' => 'Miriam Libre',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Mirza' =>
			array (
				'family' => 'Mirza',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
					),
			),
		'Miss Fajardose' =>
			array (
				'family' => 'Miss Fajardose',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mitr' =>
			array (
				'family' => 'Mitr',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
					),
			),
		'Modak' =>
			array (
				'family' => 'Modak',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Modern Antiqua' =>
			array (
				'family' => 'Modern Antiqua',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mogra' =>
			array (
				'family' => 'Mogra',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Molengo' =>
			array (
				'family' => 'Molengo',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Molle' =>
			array (
				'family' => 'Molle',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'italic',
					),
			),
		'Monda' =>
			array (
				'family' => 'Monda',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Monofett' =>
			array (
				'family' => 'Monofett',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Monoton' =>
			array (
				'family' => 'Monoton',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Monsieur La Doulaise' =>
			array (
				'family' => 'Monsieur La Doulaise',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Montaga' =>
			array (
				'family' => 'Montaga',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Montez' =>
			array (
				'family' => 'Montez',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Montserrat' =>
			array (
				'family' => 'Montserrat',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Montserrat Alternates' =>
			array (
				'family' => 'Montserrat Alternates',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Montserrat Subrayada' =>
			array (
				'family' => 'Montserrat Subrayada',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Moul' =>
			array (
				'family' => 'Moul',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Moulpali' =>
			array (
				'family' => 'Moulpali',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mountains of Christmas' =>
			array (
				'family' => 'Mountains of Christmas',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Mouse Memoirs' =>
			array (
				'family' => 'Mouse Memoirs',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mr Bedfort' =>
			array (
				'family' => 'Mr Bedfort',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mr Dafoe' =>
			array (
				'family' => 'Mr Dafoe',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mr De Haviland' =>
			array (
				'family' => 'Mr De Haviland',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mrs Saint Delafield' =>
			array (
				'family' => 'Mrs Saint Delafield',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mrs Sheppards' =>
			array (
				'family' => 'Mrs Sheppards',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Mukta Vaani' =>
			array (
				'family' => 'Mukta Vaani',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
						6 => '800',
					),
			),
		'Muli' =>
			array (
				'family' => 'Muli',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '200italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
						10 => '800',
						11 => '800italic',
						12 => '900',
						13 => '900italic',
					),
			),
		'Mystery Quest' =>
			array (
				'family' => 'Mystery Quest',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'NTR' =>
			array (
				'family' => 'NTR',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Neucha' =>
			array (
				'family' => 'Neucha',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Neuton' =>
			array (
				'family' => 'Neuton',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => 'italic',
						4 => '700',
						5 => '800',
					),
			),
		'New Rocker' =>
			array (
				'family' => 'New Rocker',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'News Cycle' =>
			array (
				'family' => 'News Cycle',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Niconne' =>
			array (
				'family' => 'Niconne',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nixie One' =>
			array (
				'family' => 'Nixie One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nobile' =>
			array (
				'family' => 'Nobile',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Nokora' =>
			array (
				'family' => 'Nokora',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Norican' =>
			array (
				'family' => 'Norican',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nosifer' =>
			array (
				'family' => 'Nosifer',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nothing You Could Do' =>
			array (
				'family' => 'Nothing You Could Do',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Noticia Text' =>
			array (
				'family' => 'Noticia Text',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Noto Sans' =>
			array (
				'family' => 'Noto Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Noto Serif' =>
			array (
				'family' => 'Noto Serif',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Nova Cut' =>
			array (
				'family' => 'Nova Cut',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nova Flat' =>
			array (
				'family' => 'Nova Flat',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nova Mono' =>
			array (
				'family' => 'Nova Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nova Oval' =>
			array (
				'family' => 'Nova Oval',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nova Round' =>
			array (
				'family' => 'Nova Round',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nova Script' =>
			array (
				'family' => 'Nova Script',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nova Slim' =>
			array (
				'family' => 'Nova Slim',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nova Square' =>
			array (
				'family' => 'Nova Square',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Numans' =>
			array (
				'family' => 'Numans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Nunito' =>
			array (
				'family' => 'Nunito',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '200italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
						10 => '800',
						11 => '800italic',
						12 => '900',
						13 => '900italic',
					),
			),
		'Nunito Sans' =>
			array (
				'family' => 'Nunito Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '200italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
						10 => '800',
						11 => '800italic',
						12 => '900',
						13 => '900italic',
					),
			),
		'Odor Mean Chey' =>
			array (
				'family' => 'Odor Mean Chey',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Offside' =>
			array (
				'family' => 'Offside',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Old Standard TT' =>
			array (
				'family' => 'Old Standard TT',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Oldenburg' =>
			array (
				'family' => 'Oldenburg',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Oleo Script' =>
			array (
				'family' => 'Oleo Script',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Oleo Script Swash Caps' =>
			array (
				'family' => 'Oleo Script Swash Caps',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Open Sans' =>
			array (
				'family' => 'Open Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '600',
						5 => '600italic',
						6 => '700',
						7 => '700italic',
						8 => '800',
						9 => '800italic',
					),
			),
		'Open Sans Condensed' =>
			array (
				'family' => 'Open Sans Condensed',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => '700',
					),
			),
		'Oranienbaum' =>
			array (
				'family' => 'Oranienbaum',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Orbitron' =>
			array (
				'family' => 'Orbitron',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '700',
						3 => '900',
					),
			),
		'Oregano' =>
			array (
				'family' => 'Oregano',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Orienta' =>
			array (
				'family' => 'Orienta',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Original Surfer' =>
			array (
				'family' => 'Original Surfer',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Oswald' =>
			array (
				'family' => 'Oswald',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '700',
					),
			),
		'Over the Rainbow' =>
			array (
				'family' => 'Over the Rainbow',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Overlock' =>
			array (
				'family' => 'Overlock',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
						4 => '900',
						5 => '900italic',
					),
			),
		'Overlock SC' =>
			array (
				'family' => 'Overlock SC',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ovo' =>
			array (
				'family' => 'Ovo',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Oxygen' =>
			array (
				'family' => 'Oxygen',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '700',
					),
			),
		'Oxygen Mono' =>
			array (
				'family' => 'Oxygen Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'PT Mono' =>
			array (
				'family' => 'PT Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'PT Sans' =>
			array (
				'family' => 'PT Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'PT Sans Caption' =>
			array (
				'family' => 'PT Sans Caption',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'PT Sans Narrow' =>
			array (
				'family' => 'PT Sans Narrow',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'PT Serif' =>
			array (
				'family' => 'PT Serif',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'PT Serif Caption' =>
			array (
				'family' => 'PT Serif Caption',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Pacifico' =>
			array (
				'family' => 'Pacifico',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Palanquin' =>
			array (
				'family' => 'Palanquin',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '200',
						2 => '300',
						3 => 'regular',
						4 => '500',
						5 => '600',
						6 => '700',
					),
			),
		'Palanquin Dark' =>
			array (
				'family' => 'Palanquin Dark',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
					),
			),
		'Paprika' =>
			array (
				'family' => 'Paprika',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Parisienne' =>
			array (
				'family' => 'Parisienne',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Passero One' =>
			array (
				'family' => 'Passero One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Passion One' =>
			array (
				'family' => 'Passion One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
						2 => '900',
					),
			),
		'Pathway Gothic One' =>
			array (
				'family' => 'Pathway Gothic One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Patrick Hand' =>
			array (
				'family' => 'Patrick Hand',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Patrick Hand SC' =>
			array (
				'family' => 'Patrick Hand SC',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Pattaya' =>
			array (
				'family' => 'Pattaya',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Patua One' =>
			array (
				'family' => 'Patua One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Pavanam' =>
			array (
				'family' => 'Pavanam',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Paytone One' =>
			array (
				'family' => 'Paytone One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Peddana' =>
			array (
				'family' => 'Peddana',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Peralta' =>
			array (
				'family' => 'Peralta',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Permanent Marker' =>
			array (
				'family' => 'Permanent Marker',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Petit Formal Script' =>
			array (
				'family' => 'Petit Formal Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Petrona' =>
			array (
				'family' => 'Petrona',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Philosopher' =>
			array (
				'family' => 'Philosopher',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Piedra' =>
			array (
				'family' => 'Piedra',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Pinyon Script' =>
			array (
				'family' => 'Pinyon Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Pirata One' =>
			array (
				'family' => 'Pirata One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Plaster' =>
			array (
				'family' => 'Plaster',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Play' =>
			array (
				'family' => 'Play',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Playball' =>
			array (
				'family' => 'Playball',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Playfair Display' =>
			array (
				'family' => 'Playfair Display',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
						4 => '900',
						5 => '900italic',
					),
			),
		'Playfair Display SC' =>
			array (
				'family' => 'Playfair Display SC',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
						4 => '900',
						5 => '900italic',
					),
			),
		'Podkova' =>
			array (
				'family' => 'Podkova',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Poiret One' =>
			array (
				'family' => 'Poiret One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Poller One' =>
			array (
				'family' => 'Poller One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Poly' =>
			array (
				'family' => 'Poly',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Pompiere' =>
			array (
				'family' => 'Pompiere',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Pontano Sans' =>
			array (
				'family' => 'Pontano Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Poppins' =>
			array (
				'family' => 'Poppins',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Port Lligat Sans' =>
			array (
				'family' => 'Port Lligat Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Port Lligat Slab' =>
			array (
				'family' => 'Port Lligat Slab',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Pragati Narrow' =>
			array (
				'family' => 'Pragati Narrow',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Prata' =>
			array (
				'family' => 'Prata',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Preahvihear' =>
			array (
				'family' => 'Preahvihear',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Press Start 2P' =>
			array (
				'family' => 'Press Start 2P',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Pridi' =>
			array (
				'family' => 'Pridi',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
					),
			),
		'Princess Sofia' =>
			array (
				'family' => 'Princess Sofia',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Prociono' =>
			array (
				'family' => 'Prociono',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Prompt' =>
			array (
				'family' => 'Prompt',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Prosto One' =>
			array (
				'family' => 'Prosto One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Proza Libre' =>
			array (
				'family' => 'Proza Libre',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '500',
						3 => '500italic',
						4 => '600',
						5 => '600italic',
						6 => '700',
						7 => '700italic',
						8 => '800',
						9 => '800italic',
					),
			),
		'Puritan' =>
			array (
				'family' => 'Puritan',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Purple Purse' =>
			array (
				'family' => 'Purple Purse',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Quando' =>
			array (
				'family' => 'Quando',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Quantico' =>
			array (
				'family' => 'Quantico',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Quattrocento' =>
			array (
				'family' => 'Quattrocento',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Quattrocento Sans' =>
			array (
				'family' => 'Quattrocento Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Questrial' =>
			array (
				'family' => 'Questrial',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Quicksand' =>
			array (
				'family' => 'Quicksand',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '700',
					),
			),
		'Quintessential' =>
			array (
				'family' => 'Quintessential',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Qwigley' =>
			array (
				'family' => 'Qwigley',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Racing Sans One' =>
			array (
				'family' => 'Racing Sans One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Radley' =>
			array (
				'family' => 'Radley',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Rajdhani' =>
			array (
				'family' => 'Rajdhani',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Rakkas' =>
			array (
				'family' => 'Rakkas',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Raleway' =>
			array (
				'family' => 'Raleway',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Raleway Dots' =>
			array (
				'family' => 'Raleway Dots',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ramabhadra' =>
			array (
				'family' => 'Ramabhadra',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ramaraja' =>
			array (
				'family' => 'Ramaraja',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rambla' =>
			array (
				'family' => 'Rambla',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Rammetto One' =>
			array (
				'family' => 'Rammetto One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ranchers' =>
			array (
				'family' => 'Ranchers',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rancho' =>
			array (
				'family' => 'Rancho',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ranga' =>
			array (
				'family' => 'Ranga',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Rasa' =>
			array (
				'family' => 'Rasa',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Rationale' =>
			array (
				'family' => 'Rationale',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ravi Prakash' =>
			array (
				'family' => 'Ravi Prakash',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Redressed' =>
			array (
				'family' => 'Redressed',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Reem Kufi' =>
			array (
				'family' => 'Reem Kufi',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Reenie Beanie' =>
			array (
				'family' => 'Reenie Beanie',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'RSomething is wrongia' =>
			array (
				'family' => 'RSomething is wrongia',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rhodium Libre' =>
			array (
				'family' => 'Rhodium Libre',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ribeye' =>
			array (
				'family' => 'Ribeye',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ribeye Marrow' =>
			array (
				'family' => 'Ribeye Marrow',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Righteous' =>
			array (
				'family' => 'Righteous',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Risque' =>
			array (
				'family' => 'Risque',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Roboto' =>
			array (
				'family' => 'Roboto',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '500',
						7 => '500italic',
						8 => '700',
						9 => '700italic',
						10 => '900',
						11 => '900italic',
					),
			),
		'Roboto Condensed' =>
			array (
				'family' => 'Roboto Condensed',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '700',
						5 => '700italic',
					),
			),
		'Roboto Mono' =>
			array (
				'family' => 'Roboto Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '500',
						7 => '500italic',
						8 => '700',
						9 => '700italic',
					),
			),
		'Roboto Slab' =>
			array (
				'family' => 'Roboto Slab',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '100',
						1 => '300',
						2 => 'regular',
						3 => '700',
					),
			),
		'Rochester' =>
			array (
				'family' => 'Rochester',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rock Salt' =>
			array (
				'family' => 'Rock Salt',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rokkitt' =>
			array (
				'family' => 'Rokkitt',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Romanesco' =>
			array (
				'family' => 'Romanesco',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ropa Sans' =>
			array (
				'family' => 'Ropa Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Rosario' =>
			array (
				'family' => 'Rosario',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Rosarivo' =>
			array (
				'family' => 'Rosarivo',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Rouge Script' =>
			array (
				'family' => 'Rouge Script',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rozha One' =>
			array (
				'family' => 'Rozha One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rubik' =>
			array (
				'family' => 'Rubik',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '500',
						5 => '500italic',
						6 => '700',
						7 => '700italic',
						8 => '900',
						9 => '900italic',
					),
			),
		'Rubik Mono One' =>
			array (
				'family' => 'Rubik Mono One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rubik One' =>
			array (
				'family' => 'Rubik One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ruda' =>
			array (
				'family' => 'Ruda',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
						2 => '900',
					),
			),
		'Rufina' =>
			array (
				'family' => 'Rufina',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Ruge Boogie' =>
			array (
				'family' => 'Ruge Boogie',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ruluko' =>
			array (
				'family' => 'Ruluko',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rum Raisin' =>
			array (
				'family' => 'Rum Raisin',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ruslan Display' =>
			array (
				'family' => 'Ruslan Display',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Russo One' =>
			array (
				'family' => 'Russo One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ruthie' =>
			array (
				'family' => 'Ruthie',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Rye' =>
			array (
				'family' => 'Rye',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sacramento' =>
			array (
				'family' => 'Sacramento',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sahitya' =>
			array (
				'family' => 'Sahitya',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Sail' =>
			array (
				'family' => 'Sail',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Salsa' =>
			array (
				'family' => 'Salsa',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sanchez' =>
			array (
				'family' => 'Sanchez',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Sancreek' =>
			array (
				'family' => 'Sancreek',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sansita One' =>
			array (
				'family' => 'Sansita One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sarala' =>
			array (
				'family' => 'Sarala',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Sarina' =>
			array (
				'family' => 'Sarina',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sarpanch' =>
			array (
				'family' => 'Sarpanch',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
						4 => '800',
						5 => '900',
					),
			),
		'Satisfy' =>
			array (
				'family' => 'Satisfy',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Scada' =>
			array (
				'family' => 'Scada',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Scheherazade' =>
			array (
				'family' => 'Scheherazade',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Schoolbell' =>
			array (
				'family' => 'Schoolbell',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Scope One' =>
			array (
				'family' => 'Scope One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Seaweed Script' =>
			array (
				'family' => 'Seaweed Script',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Secular One' =>
			array (
				'family' => 'Secular One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sevillana' =>
			array (
				'family' => 'Sevillana',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Seymour One' =>
			array (
				'family' => 'Seymour One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Shadows Into Light' =>
			array (
				'family' => 'Shadows Into Light',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Shadows Into Light Two' =>
			array (
				'family' => 'Shadows Into Light Two',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Shanti' =>
			array (
				'family' => 'Shanti',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Share' =>
			array (
				'family' => 'Share',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Share Tech' =>
			array (
				'family' => 'Share Tech',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Share Tech Mono' =>
			array (
				'family' => 'Share Tech Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Shojumaru' =>
			array (
				'family' => 'Shojumaru',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Short Stack' =>
			array (
				'family' => 'Short Stack',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Shrikhand' =>
			array (
				'family' => 'Shrikhand',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Siemreap' =>
			array (
				'family' => 'Siemreap',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sigmar One' =>
			array (
				'family' => 'Sigmar One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Signika' =>
			array (
				'family' => 'Signika',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '600',
						3 => '700',
					),
			),
		'Signika Negative' =>
			array (
				'family' => 'Signika Negative',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '600',
						3 => '700',
					),
			),
		'Simonetta' =>
			array (
				'family' => 'Simonetta',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '900',
						3 => '900italic',
					),
			),
		'Sintony' =>
			array (
				'family' => 'Sintony',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Sirin Stencil' =>
			array (
				'family' => 'Sirin Stencil',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Six Caps' =>
			array (
				'family' => 'Six Caps',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Skranji' =>
			array (
				'family' => 'Skranji',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Slabo 13px' =>
			array (
				'family' => 'Slabo 13px',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Slabo 27px' =>
			array (
				'family' => 'Slabo 27px',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Slackey' =>
			array (
				'family' => 'Slackey',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Smokum' =>
			array (
				'family' => 'Smokum',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Smythe' =>
			array (
				'family' => 'Smythe',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sniglet' =>
			array (
				'family' => 'Sniglet',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '800',
					),
			),
		'Snippet' =>
			array (
				'family' => 'Snippet',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Snowburst One' =>
			array (
				'family' => 'Snowburst One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sofadi One' =>
			array (
				'family' => 'Sofadi One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sofia' =>
			array (
				'family' => 'Sofia',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sonsie One' =>
			array (
				'family' => 'Sonsie One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sorts Mill Goudy' =>
			array (
				'family' => 'Sorts Mill Goudy',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
					),
			),
		'Source Code Pro' =>
			array (
				'family' => 'Source Code Pro',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '600',
						5 => '700',
						6 => '900',
					),
			),
		'Source Sans Pro' =>
			array (
				'family' => 'Source Sans Pro',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '200italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
						10 => '900',
						11 => '900italic',
					),
			),
		'Source Serif Pro' =>
			array (
				'family' => 'Source Serif Pro',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '600',
						2 => '700',
					),
			),
		'Space Mono' =>
			array (
				'family' => 'Space Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Special Elite' =>
			array (
				'family' => 'Special Elite',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Spicy Rice' =>
			array (
				'family' => 'Spicy Rice',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Spinnaker' =>
			array (
				'family' => 'Spinnaker',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Spirax' =>
			array (
				'family' => 'Spirax',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Squada One' =>
			array (
				'family' => 'Squada One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sree Krushnadevaraya' =>
			array (
				'family' => 'Sree Krushnadevaraya',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sriracha' =>
			array (
				'family' => 'Sriracha',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Stalemate' =>
			array (
				'family' => 'Stalemate',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Stalinist One' =>
			array (
				'family' => 'Stalinist One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Stardos Stencil' =>
			array (
				'family' => 'Stardos Stencil',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Stint Ultra Condensed' =>
			array (
				'family' => 'Stint Ultra Condensed',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Stint Ultra Expanded' =>
			array (
				'family' => 'Stint Ultra Expanded',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Stoke' =>
			array (
				'family' => 'Stoke',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
					),
			),
		'Strait' =>
			array (
				'family' => 'Strait',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sue Ellen Francisco' =>
			array (
				'family' => 'Sue Ellen Francisco',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Suez One' =>
			array (
				'family' => 'Suez One',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sumana' =>
			array (
				'family' => 'Sumana',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Sunshiney' =>
			array (
				'family' => 'Sunshiney',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Supermercado One' =>
			array (
				'family' => 'Supermercado One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Sura' =>
			array (
				'family' => 'Sura',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Suranna' =>
			array (
				'family' => 'Suranna',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Suravaram' =>
			array (
				'family' => 'Suravaram',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Suwannaphum' =>
			array (
				'family' => 'Suwannaphum',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Swanky and Moo Moo' =>
			array (
				'family' => 'Swanky and Moo Moo',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Syncopate' =>
			array (
				'family' => 'Syncopate',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Tangerine' =>
			array (
				'family' => 'Tangerine',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Taprom' =>
			array (
				'family' => 'Taprom',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Tauri' =>
			array (
				'family' => 'Tauri',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Taviraj' =>
			array (
				'family' => 'Taviraj',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Teko' =>
			array (
				'family' => 'Teko',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Telex' =>
			array (
				'family' => 'Telex',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Tenali Ramakrishna' =>
			array (
				'family' => 'Tenali Ramakrishna',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Tenor Sans' =>
			array (
				'family' => 'Tenor Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Text Me One' =>
			array (
				'family' => 'Text Me One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'The Girl Next Door' =>
			array (
				'family' => 'The Girl Next Door',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Tienne' =>
			array (
				'family' => 'Tienne',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
						2 => '900',
					),
			),
		'Tillana' =>
			array (
				'family' => 'Tillana',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '600',
						3 => '700',
						4 => '800',
					),
			),
		'Timmana' =>
			array (
				'family' => 'Timmana',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Tinos' =>
			array (
				'family' => 'Tinos',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Titan One' =>
			array (
				'family' => 'Titan One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Titillium Web' =>
			array (
				'family' => 'Titillium Web',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '200italic',
						2 => '300',
						3 => '300italic',
						4 => 'regular',
						5 => 'italic',
						6 => '600',
						7 => '600italic',
						8 => '700',
						9 => '700italic',
						10 => '900',
					),
			),
		'Trade Winds' =>
			array (
				'family' => 'Trade Winds',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Trirong' =>
			array (
				'family' => 'Trirong',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '100',
						1 => '100italic',
						2 => '200',
						3 => '200italic',
						4 => '300',
						5 => '300italic',
						6 => 'regular',
						7 => 'italic',
						8 => '500',
						9 => '500italic',
						10 => '600',
						11 => '600italic',
						12 => '700',
						13 => '700italic',
						14 => '800',
						15 => '800italic',
						16 => '900',
						17 => '900italic',
					),
			),
		'Trocchi' =>
			array (
				'family' => 'Trocchi',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Trochut' =>
			array (
				'family' => 'Trochut',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
					),
			),
		'Trykker' =>
			array (
				'family' => 'Trykker',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Tulpen One' =>
			array (
				'family' => 'Tulpen One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ubuntu' =>
			array (
				'family' => 'Ubuntu',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '300',
						1 => '300italic',
						2 => 'regular',
						3 => 'italic',
						4 => '500',
						5 => '500italic',
						6 => '700',
						7 => '700italic',
					),
			),
		'Ubuntu Condensed' =>
			array (
				'family' => 'Ubuntu Condensed',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Ubuntu Mono' =>
			array (
				'family' => 'Ubuntu Mono',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Ultra' =>
			array (
				'family' => 'Ultra',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Uncial Antiqua' =>
			array (
				'family' => 'Uncial Antiqua',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Underdog' =>
			array (
				'family' => 'Underdog',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Unica One' =>
			array (
				'family' => 'Unica One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'UnifrakturCook' =>
			array (
				'family' => 'UnifrakturCook',
				'category' => 'display',
				'variants' =>
					array (
						0 => '700',
					),
			),
		'UnifrakturMaguntia' =>
			array (
				'family' => 'UnifrakturMaguntia',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Unkempt' =>
			array (
				'family' => 'Unkempt',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
						1 => '700',
					),
			),
		'Unlock' =>
			array (
				'family' => 'Unlock',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Unna' =>
			array (
				'family' => 'Unna',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'VT323' =>
			array (
				'family' => 'VT323',
				'category' => 'monospace',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Vampiro One' =>
			array (
				'family' => 'Vampiro One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Varela' =>
			array (
				'family' => 'Varela',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Varela Round' =>
			array (
				'family' => 'Varela Round',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Vast Shadow' =>
			array (
				'family' => 'Vast Shadow',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Vesper Libre' =>
			array (
				'family' => 'Vesper Libre',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => '500',
						2 => '700',
						3 => '900',
					),
			),
		'Vibur' =>
			array (
				'family' => 'Vibur',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Vidaloka' =>
			array (
				'family' => 'Vidaloka',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Viga' =>
			array (
				'family' => 'Viga',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Voces' =>
			array (
				'family' => 'Voces',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Volkhov' =>
			array (
				'family' => 'Volkhov',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Vollkorn' =>
			array (
				'family' => 'Vollkorn',
				'category' => 'serif',
				'variants' =>
					array (
						0 => 'regular',
						1 => 'italic',
						2 => '700',
						3 => '700italic',
					),
			),
		'Voltaire' =>
			array (
				'family' => 'Voltaire',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Waiting for the Sunrise' =>
			array (
				'family' => 'Waiting for the Sunrise',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Wallpoet' =>
			array (
				'family' => 'Wallpoet',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Walter Turncoat' =>
			array (
				'family' => 'Walter Turncoat',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Warnes' =>
			array (
				'family' => 'Warnes',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Wellfleet' =>
			array (
				'family' => 'Wellfleet',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Wendy One' =>
			array (
				'family' => 'Wendy One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Wire One' =>
			array (
				'family' => 'Wire One',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Work Sans' =>
			array (
				'family' => 'Work Sans',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '200',
						2 => '300',
						3 => 'regular',
						4 => '500',
						5 => '600',
						6 => '700',
						7 => '800',
						8 => '900',
					),
			),
		'Yanone Kaffeesatz' =>
			array (
				'family' => 'Yanone Kaffeesatz',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '200',
						1 => '300',
						2 => 'regular',
						3 => '700',
					),
			),
		'Yantramanav' =>
			array (
				'family' => 'Yantramanav',
				'category' => 'sans-serif',
				'variants' =>
					array (
						0 => '100',
						1 => '300',
						2 => 'regular',
						3 => '500',
						4 => '700',
						5 => '900',
					),
			),
		'Yatra One' =>
			array (
				'family' => 'Yatra One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Yellowtail' =>
			array (
				'family' => 'Yellowtail',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Yeseva One' =>
			array (
				'family' => 'Yeseva One',
				'category' => 'display',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Yesteryear' =>
			array (
				'family' => 'Yesteryear',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
		'Yrsa' =>
			array (
				'family' => 'Yrsa',
				'category' => 'serif',
				'variants' =>
					array (
						0 => '300',
						1 => 'regular',
						2 => '500',
						3 => '600',
						4 => '700',
					),
			),
		'Zeyada' =>
			array (
				'family' => 'Zeyada',
				'category' => 'handwriting',
				'variants' =>
					array (
						0 => 'regular',
					),
			),
	);


}