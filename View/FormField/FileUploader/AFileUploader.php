<?php
/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */

namespace XLite\Module\Shofi\PermissionsManager\View\FormField\FileUploader;


use XLite\Core\Auth;

abstract class AFileUploader extends \XLite\View\FormField\FileUploader\AFileUploader implements \XLite\Base\IDecorator {

    const PARAM_PERMISSION = 'permission';
    const PARAM_SH_TEMP_PRODUCT_ID = 'tempProductId';
    const PARAM_MULTIPLE    = 'multiple';
    const PARAM_MAX_WIDTH   = 'maxWidth';
    const PARAM_MAX_HEIGHT  = 'maxHeight';
    const PARAM_IS_VIA_URL_ALLOWED  = 'isViaUrlAllowed';


    protected function getUploadPermission()
    {

        return $this->getParam(static::PARAM_PERMISSION);
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PERMISSION      => new \XLite\Model\WidgetParam\TypeString('CSS', '10'),
            static::PARAM_SH_TEMP_PRODUCT_ID      => new \XLite\Model\WidgetParam\TypeString('Id', ''),

        );

    }

    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        $list['permission'] = $this->getParam(static::PARAM_PERMISSION);
        $list['tempProductId'] = $this->getParam(static::PARAM_SH_TEMP_PRODUCT_ID);

        return $list;
    }
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {

        $template = "";

        if($this->getParam(static::PARAM_MULTIPLE)) {
            switch($this->getAccessLevel()) {

                case "[business] manage up to 2 photos":
                    $template = 'file_uploader/twoPhotos.twig';
                    break;
                case "[business] manage up to 6 photos":
                    $template = 'modules/Shofi/PermissionsManager/file_uploader/sixPhotos.twig';
                    break;
                case "[business] manage up to 10 photos":
                    $template = 'modules/Shofi/PermissionsManager/file_uploader/tenPhotos.twig';
                    break;
                case "[business] manage up to 12 photos":
                    $template = 'file_uploader/fiftinPhotos.twig';
                    break;

                default:
                    $template = 'file_uploader/twoPhotos.twig';

            }
        } else {
            $template =  'file_uploader/single.twig';
        }




        return $template;
    }


    protected function getAccessLevel() {
        $access_levels = array(
            substr("[business] manage up to 2 photos", 0, 32),
            substr("[business] manage up to 6 photos", 0, 32),
            substr("[business] manage up to 10 photos", 0, 32),
            substr("[business] manage up to 12 photos",0, 32),



        );
        foreach ($access_levels as $access_level) {

            if($this->checkUploadACL($access_level)) {
                return $access_level;
            }

        }
    }


    protected function checkUploadACL($access_level) {
        return Auth::getInstance()->isBusinessPermissionAllowed($access_level);
    }

    public function getTempFilesCount() {


        $result = $this->getFiles();

        $count = count($result);

        return $count;

    }
}