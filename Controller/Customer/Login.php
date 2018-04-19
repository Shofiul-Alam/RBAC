<?php

/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */

namespace XLite\Module\Shofi\PermissionsManager\Controller\Customer;


class Login extends \XLite\Controller\Customer\Login implements \XLite\Base\IDecorator {

    /**
     * Log out
     *
     * @return void
     */
    protected function doActionLogoff()
    {
        if (\XLite\Core\Auth::getInstance()->isOperatingAsUserMode()) {
            $this->setReturnURL(
                \XLite\Core\Converter::buildURL(
                    'profile',
                    '',
                    array(
                        'profile_id' => \XLite\Core\Auth::getInstance()->getOperatingAs()
                    ),
                    \XLite::getAdminScript()
                )
            );

            \XLite\Core\Auth::getInstance()->finishOperatingAs();
            \XLite\Core\TopMessage::addInfo('Finished operating as user');
        } else {
            \XLite\Core\Auth::getInstance()->logoff();

            \Includes\Utils\Session::clearAdminCookie();

            $this->setReturnURL('/');

            $this->getCart()->logoff();
            $this->updateCart();

            \XLite\Core\Database::getEM()->flush();
        }
    }
}