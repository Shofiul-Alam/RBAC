<?php
/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */

namespace XLite\Module\Shofi\PermissionsManager\Model;



class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator {

    /**
     * Check - specified permission is allowed or not
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isBusinessPermissionAllowed($code)
    {


        $result = false;

        if (0 < count($this->getRoles())) {
            foreach ($this->getRoles() as $role) {

                if ($role->isBusinessPermissionAllowed($code)) {
                    $result = true;

                    break;
                }
            }

        } elseif (0 === \XLite\Core\Database::getRepo('XLite\Model\Role')->count()) {
            $result = true;
        }

        return $result;
    }

    public function hasBusinessRole() {
        $result = false;

        if (0 < count($this->getRoles())) {
            foreach ($this->getRoles() as $role) {

                if ($role->isBusinessRole()) {
                    $result = true;

                    break;
                }
            }
        }

        return $result;


    }

    public function hasAccessPermission($code) {


        $result = false;

        if (0 < count($this->getRoles())) {
            foreach ($this->getRoles() as $role) {

                if ($role->hasAccessPermission($code)) {
                    $result = true;

                    break;
                }
            }

        } elseif (0 === \XLite\Core\Database::getRepo('XLite\Model\Role')->count()) {
            $result = true;
        }

        return $result;
    }
}