<?php
namespace Eonet\Core\Admin\Pages;

use Eonet\Core\Admin\EonetAdminPages;

if ( ! defined('ABSPATH') ) die('Forbidden');

class EonetPageSettings extends EonetAdminPages
{

    function getPageName()
    {
	    return apply_filters( 'eonet_components_settings_page_name', esc_html__('Settings', 'eonet-live-notifications') );
    }

    function getPageSlug()
    {
        return 'settings';
    }

    function getPageIcon()
    {
        return 'fa fa-sliders ';
    }

    function getPageContent()
    {
        $args = array(
            'slug' => $this->getPageSlug(),
            'name' => $this->getPageName(),
        );
        return eonet_render_view($this->getPath().'views/'.$this->getPageSlug().'.php', $args);
    }

}