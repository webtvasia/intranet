<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Time Tracking', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to add a time tracking widget for your staff.', 'woffice' );
$manifest['version'] = '1.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = fw_get_template_customizations_directory_uri().'/extensions/woffice-time-tracking/static/img/thumbnails/time-tracking.jpg';
