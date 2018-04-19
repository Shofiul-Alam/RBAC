<?php

/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */


namespace XLite\Module\Shofi\PermissionsManager\Marketplace;


class Marketplace extends \XLite\Upgrade\Entry\Module\Marketplace implements \XLite\Base\IDecorator {

    /**
     * Return entry new major version
     *
     * @return string
     */
    public function getMajorVersionNew()
    {
        
        if($this->getModuleForUpgrade()) {
            return $this->getModuleForUpgrade()->getMajorVersion();
        } else {
            return '5.3';
        }

    }

    /**
     * Return entry new minor version
     *
     * @return string
     */
    public function getMinorVersionNew()
    {
        if($this->getModuleForUpgrade()) {
            return $this->getModuleForUpgrade()->getFullMinorVersion();
        } else {
            return '5';
        }

    }

}