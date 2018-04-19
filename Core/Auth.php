<?php
/**
 * @author    Md Shofiul Alam
 * @copyright Copyright (c) 2015 Toolmateshire <admin@toolmateshire.com.au>. All rights reserved
 * Date: 28/02/2017
 * Time: 4:36 PM
 */



namespace XLite\Module\Shofi\PermissionsManager\Core;

use XLite\Core;

class Auth extends \XLite\Core\Auth implements \XLite\Base\IDecorator {

    /**
     * Check - specified permission is allowed or not
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isBusinessPermissionAllowed($code)
    {

        $profile = $this->getProfile();

        return $profile && $profile->isBusinessPermissionAllowed($code) || $profile && \XLite\Core\Auth::getInstance()->hasRootAccess();
    }

    public function hasBusinessRole() {
        $profile = $this->getProfile();
        return $profile && $profile->hasBusinessRole();
    }

    public function permittedImages()
    {

        $permissionNumber = "";

        switch($this->getUploadAccessLevel()) {

            case substr("[business] manage up to 2 photos", 0, 32):
                $permissionNumber = 2;
                break;
            case substr("[business] manage up to 6 photos", 0, 32):
                $permissionNumber = 6;
                break;
            case substr("[business] manage up to 10 photos", 0, 32):
                $permissionNumber = 10;
                break;
            case substr("[business] manage up to 12 photos", 0, 32):
                $permissionNumber = 12;
                break;

            default:
                $permissionNumber = 2;

        }


        return $permissionNumber;
    }


    protected function getUploadAccessLevel() {

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


        return \XLite\Core\Auth::getInstance()->isBusinessPermissionAllowed($access_level);
    }

    public function hasHilightPermission() {

        $result = false;

        if($this->getPermittedHilight() >= $this->getHilightProductsCount()) {
            $result = true;
        }

        return $result;

    }

    protected function getPermittedHilight() {


        $permissionNumber = "";

        switch($this->getHilightAccessLevel()) {

            case substr("[business] manage highlight 3 tools", 0, 32):
                $permissionNumber = 3;
                break;
            case substr("[business] manage highlight 5 tools", 0, 32):
                $permissionNumber = 5;
                break;
            case substr("[business] manage highlight 8 tools", 0, 32):
                $permissionNumber = 8;
                break;

            default:
                $permissionNumber = -1;

        }


        return $permissionNumber;

    }

    protected function getHilightAccessLevel() {
        $access_levels = array(
            substr("[business] manage highlight 3 tools", 0, 32),
            substr("[business] manage highlight 5 tools", 0, 32),
            substr("[business] manage highlight 8 tools", 0, 32),
        );
        foreach ($access_levels as $access_level) {

            if($this->checkUploadACL($access_level)) {
                return $access_level;
            }

        }
    }

    protected function getHilightProductsCount() {


        $profileId = null;
        $profile = null;
        $num = null;

        if(\XLite\Core\Request::getInstance()->profile_id) {
            $profileId = \XLite\Core\Request::getInstance()->profile_id;
        }

        if($profileId != null) {
            $profile = \XLite\Core\Database::getRepo('\XLite\Model\Profile')->find($profileId);
        } else {
            $profile = $this->getProfile();
        }




        $products = \XLite\Core\Database::getRepo('\XLite\Model\Product')->createQueryBuilder('p')
            ->innerJoin('p.businessHighlight', 'h', 'WITH', 'h.value IS NOT NULL')
            ->andWhere('p.vendor = :owner')
            ->andWhere('h.value != :empty')
            ->setParameter('empty', '')
            ->setParameter('owner', $profile)
            ->getResult();



        $num = count($products);

        return $num;

    }


    public function getAllBusiness() {


        $businesses = \XLite\Core\Database::getRepo('\XLite\Model\Profile')->findBy(array('isBusiness' => true));

        $companies = array();


        foreach($businesses as $key=>$business) {

            $cProfile =  $business->getCompanyProfile();

            if($cProfile != null) {
                $companies[$key]['FirstName'] = $cProfile->getCompanyName();
            } elseif($business->getVendorCompanyName() != null) {
                $companies[$key]['FirstName'] = $business->getVendorCompanyName();
            } else {
                $companies[$key]['FirstName'] = $business->getFirstName();
            }
            $logoPath = "";
            $companyP = $business->getCompanyProfile();

            if($companyP) {
                $logo = $companyP->getCompanyLogo();
                if($logo) {
                    $logoPath = $logo->getFrontURL();
                } elseif($business->getInvoiceLogo() != null) {
                    $logo = $business->getInvoiceLogo();
                    if($logo) {
                        $logoPath = $logo->getFrontURL();
                    }
                }

            }


            $companies[$key]['logo'] = $logoPath;

            $phoneArr = str_split($business->getBusinessPhoneNo(), 4);
            $phoneFormated = "";

            foreach($phoneArr as $phonePart) {

                if (current($phoneArr) != end($phoneArr)) {
                    $phoneFormated .= $phonePart . "-";

                } else {
                    $phoneFormated .= $phonePart;
                }
            }


            $companies[$key]['phone'] = $phoneFormated;
            $companies[$key]['businessHours'] = $business->getBusinessHours();
            $companies[$key]['businessCat'] = $business->getBusinessCategory();
            $companies[$key]['id'] = $business->getProfileId();

            $address = "";
            if($business->getFirstAddress()) {

                $firstAddress = $business->getFirstAddress();

                $address =
                    $firstAddress->getCity(). " " .
                    $firstAddress->getState()->getCode(). " " .
                    $firstAddress->getZipcode();
            }

            $companies[$key]['address'] = $address;

            $companies[$key]['url'] = Core\Converter::buildURL('vendor', '', array('vendor_id' => $business->getProfileId()));



        }



        return $companies;
    }

//    public function getCompanyLogo($business) {
//
//
////         $logoPath = "images/CompanyLogo/company_logo_demi.png";
//        $companyP = $business->getCompanyProfile();
//
//        if($companyP) {
//            $logo = $companyP->getCompanyLogo();
//            if($logo) {
//                $logoPath = $logo->getFrontURL();
//            }
//
//        }  elseif($business->getInvoiceLogo() != null) {
//            $logoPath = $business->getInvoiceLogo()->getPath();
//        }
//        return $logoPath;
//    }





    /**
     * |                            |
     * |Category Featued Section    |
     * |                            |
     */



    public function hasCategoryFeaturedPermission() {
        $result = false;

        if($this->getCategoryFeaturedPermitted() > $this->getCatFeaturedProductsCount() ||  \XLite\Core\Auth::getInstance()->hasRootAccess()) {
            $result = true;
        }

        return $result;
    }

    public function getCategoryFeaturedPermitted() {


        $permissionNumber = "";

        switch($this->getCategoryFeaturedAccessLevel()) {

            case substr("[business] manage 5 category feature tools", 0, 32):
                $permissionNumber = 5;
                break;
            case substr("[business] manage 8 category feature tools", 0, 32):
                $permissionNumber = 8;
                break;


            default:
                $permissionNumber = -1;
        }

        return $permissionNumber;


    }

    protected function getCategoryFeaturedAccessLevel() {
        $access_levels = array(
            substr("[business] manage 5 category feature tools", 0, 32),
            substr("[business] manage 8 category feature tools", 0, 32),
        );
        foreach ($access_levels as $access_level) {

            if($this->checkUploadACL($access_level)) {
                return $access_level;
            }

        }

    }


    public function getCatFeaturedProductsCount() {


        $profileId = null;
        $categoryFeaturedProducts = null;
        $profile = null;
        $num = null;
        if(\XLite\Core\Request::getInstance()->id != NULL) {
            $id = \XLite\Core\Request::getInstance()->id;
        } else {
            $id = 1;
        }


        if(\XLite\Core\Request::getInstance()->profile_id) {
            $profileId = \XLite\Core\Request::getInstance()->profile_id;
            $profile = \XLite\Core\Database::getRepo('\XLite\Model\Profile')->find($profileId);
        } else {
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
            $profile = \XLite\Core\Auth::getInstance()->getProfile();
        }

        $category = \XLite\Core\Database::getRepo('\XLite\Model\Category')->find($id);


        $categoryFeaturedProducts = \XLite\Core\Database::getRepo('\XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct')->createQueryBuilder('p')
            ->andWhere('p.profile = :owner')
            ->andWhere('p.category = :category')
            ->setParameter('owner', $profile)
            ->setParameter('category', $category)
            ->getResult();


        $num = count($categoryFeaturedProducts);

        return $num;

    }


    /**
     * |                            |
     * |front Page Featued Section    |
     * |                            |
     */



    public function hasFrontPageFeaturedPermission() {
        $result = false;

        if($this->getFrontPageFeaturedPermitted() > $this->getCatFeaturedProductsCount() || \XLite\Core\Auth::getInstance()->hasRootAccess()) {
            $result = true;
        }

        return $result;
    }

    public function getFrontPageFeaturedPermitted() {


        $permissionNumber = "";

        switch($this->getFrontPageFeaturedAccessLevel()) {

            case substr("[business] manage feature 3 tools", 0, 32):
                $permissionNumber = 3;
                break;
            default:
                $permissionNumber = -1;
        }

        return $permissionNumber;


    }

    protected function getFrontPageFeaturedAccessLevel() {
        $access_levels = array(
            substr("[business] manage feature 3 tools", 0, 32),
        );
        foreach ($access_levels as $access_level) {

            if($this->checkUploadACL($access_level)) {
                return $access_level;
            }

        }

    }


    public function hasAccessPermission($code) {

        $profile = $this->getProfile();

        return $profile && $profile->hasAccessPermission($code) || $profile && \XLite\Core\Auth::getInstance()->hasRootAccess();
    }



}