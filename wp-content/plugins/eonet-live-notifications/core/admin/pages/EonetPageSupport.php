<?php
namespace Eonet\Core\Admin\Pages;

use Eonet\Core\Admin\EonetAdminPages;

if ( ! defined('ABSPATH') ) die('Forbidden');

class EonetPageSupport extends EonetAdminPages
{

    function getPageName()
    {
        return esc_html__('Support', 'eonet-live-notifications');
    }

    function getPageSlug()
    {
        return 'support';
    }

    function getPageIcon()
    {
        return 'fa fa-question-circle';
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