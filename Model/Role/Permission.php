<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */

namespace XLite\Module\Shofi\PermissionsManager\Model\Role;

/**
 * Permission
 */
class Permission extends \XLite\Model\Role\Permission implements \XLite\Base\IDecorator
{
    /**
     * Permission code which indicates that it is an exclusive business's permission
     */
    const BUSINESS_PERM_CODE_PREFIX = '[business]';

    /**
     * Check if permission code is business's and should be treated separately
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public static function isBusinessPermissionCode($code)
    {
        return strpos($code, static::BUSINESS_PERM_CODE_PREFIX) === 0;
    }

    /**
     * Use this method to check if the given permission code allows with the permission
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isBusinessAllowed($code)
    {

        return static::isBusinessPermissionCode($code)
            ? $this->getCode() == $code
            : in_array($this->getCode(), array(static::ROOT_ACCESS, $code));
    }

    /**
     * Check if it's a vendors permission
     *
     * @return boolean
     */
    public function isBusinessPermission()
    {
        return static::isBusinessPermissionCode($this->getCode());
    }



}
