<?php
namespace Eonet\Core\Admin\Pages;

use Eonet\Core\Admin\EonetAdminPages;

if ( ! defined('ABSPATH') ) die('Forbidden');

class EonetPageExtensions extends EonetAdminPages
{

    function getPageName()
    {
        return esc_html__('Components', 'eonet-live-notifications');
    }

    function getPageSlug()
    {
        return 'extensions';
    }

    function getPageIcon()
    {
        return 'fa fa-th';
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