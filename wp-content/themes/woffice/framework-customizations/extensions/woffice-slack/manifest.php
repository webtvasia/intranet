<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Slack', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to synchronize your site notifications with any Slack channel.', 'woffice' );
$manifest['version'] = '1.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = fw_get_template_customizations_directory_uri().'/extensions/woffice-slack/static/img/thumbnails/slack.jpg';
