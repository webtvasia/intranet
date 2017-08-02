<?php
/**
 * Class EonetAdminPages
 * Build the page class for the admin side :
 */

namespace Eonet\Core\Admin;

if ( ! defined('ABSPATH') ) die('Forbidden');

abstract class EonetAdminPages
{

    /**
     * Get the current path to make it easier
     * @return string
     */
    public function getPath()
    {
        return EONET_DIR.'/core/admin/pages/';
    }

    /**
     * Get page name
     * @return string
     */
    abstract function getPageName();

    /**
     * Get page slug
     * @return string
     */
    abstract function getPageSlug();

    /**
     * Get page icon
     * @return string
     */
    abstract function getPageIcon();

    /**
     * Get page content
     * @return mixed
     */
    abstract function getPageContent();

}