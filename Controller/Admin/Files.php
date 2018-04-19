<?php
/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */
namespace XLite\Module\Shofi\PermissionsManager\Controller\Admin;


class Files extends \XLite\Controller\Admin\Files implements \XLite\Base\IDecorator {
    protected function countUploadedFiles() {

        $temProductId = \XLite\Core\Request::getInstance()->tempproductid;

        $files = \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->findBy(array("productId" => $temProductId));

        $num = count($files);

        return $num;

    }

    protected function checkUploadPermission($uploadedFiles, $file) {



        if($this->id == "form-default-images") {
            $permissionLimit = \XLite\Core\Auth::getInstance()->permittedImages();
        } else {
            $permissionLimit = 10;
        }


        if($permissionLimit==null) {
            $permissionLimit = 1;
        }

        if($uploadedFiles > $permissionLimit) {

            $file->removeFile();
            $this->sendResponse(null, static::t('You do not have Previligaes to upload more photos.'));
        }

    }
    /**
     * Uploads file from form data.
     * Uses 'file' request form value.
     *
     * @return void
     */
    protected function doActionUploadFromFile()
    {

        $file = \XLite\Core\Request::getInstance()->register
            ? new \XLite\Model\Image\Content()
            : new \XLite\Model\TemporaryFile();

        $message = '';
        if ($file->loadFromRequest('file')) {
            $this->checkFile($file);
            if($this->id == "form-default-images") {
                $uploadedFiles = $this->countUploadedFiles();
            } else {
                $uploadedFiles = 0;
            }

            if(\XLite\Core\Request::getInstance()->multiple) {
                $this->checkUploadPermission($uploadedFiles, $file);
            }


            $temProductId = \XLite\Core\Request::getInstance()->tempproductid;
            $file->setProductId($temProductId);
            \XLite\Core\Database::getEM()->persist($file);
            \XLite\Core\Database::getEM()->flush();

        } else {
            $message = static::t('File is not uploaded');
        }

        $this->sendResponse($file, $message);
    }


}