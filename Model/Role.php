<?php
/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */

namespace XLite\Module\Shofi\PermissionsManager\Model;


class Role extends \XLite\Model\Role implements \XLite\Base\IDecorator {

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

        foreach ($this->getPermissions() as $permission) {

            if ($permission->isBusinessAllowed($code)) {
                $result = true;

                break;
            }
        }

        return $result;
    }
    /**
     * Check - specified permission is allowed or not
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isBusinessRole()
    {


        $result = false;

        foreach ($this->getPermissions() as $permission) {

            if (in_array('[business]', explode(" ", $permission->getCode()))) {

                $result = true;

                break;
            }
        }

        return $result;
    }

    public function hasAccessPermission($code) {

        $result = false;

        foreach ($this->getPermissions() as $permission) {

            if ($permission->getCode() == $code) {
                $result = true;

                break;
            }
        }

        return $result;

    }
}