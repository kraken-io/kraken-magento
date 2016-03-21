<?php

/**
 * Class Welance_Kraken_KrakenController
 */

class Welance_Kraken_KrakenController extends Mage_Adminhtml_Controller_Action
{
    public function userstatusAction()
    {
        $response = Mage::helper('welance_kraken/api')->getUserStatus();

        $session = Mage::getSingleton('adminhtml/session');
        $helper = Mage::helper('welance_kraken');

        if ($response->success == true) {
            $message = $this->__('Your Kraken.io plan: %s <br \/>Quota total: %s <br \/>Quota used: %s <br \/>Quota remaining: %s <br \/>',
                $response->plan_name, $helper->getImageSizeConverted($response->quota_total), $helper->getImageSizeConverted($response->quota_used), $helper->getImageSizeConverted($response->quota_remaining));
            $session->addSuccess($message);
        } else {
            $session->addError($response->error);
        }

        $this->_redirectReferer();
    }

    public function indexAction()
    {
        $helper = Mage::helper('welance_kraken');
        $helper->deletePendingEntries();
        $helper->removeDeletedImagesFromDatabase();


        $this->loadLayout();
        $this->renderLayout();
    }

    public function optimizeAction()
    {
        $type = $this->getRequest()->getParam('type');
        $image = json_decode($this->getRequest()->getParam('image'),true);

        $apiHelper = Mage::helper('welance_kraken/api');

        $this->getResponse()->setHeader('Content-type', 'application/json');

        if (Mage::getStoreConfig('welance_kraken/kraken_config/backup')) {
            Mage::helper('welance_kraken')->backupImage($type, $image['dir'], $image['name']);
        }

        $response = $apiHelper->uploadAndSave($image, $type);

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function restoreAction()
    {

        $_helper = Mage::helper('welance_kraken');
        $session = Mage::getSingleton('adminhtml/session');
        $type = $this->getRequest()->getParam('type');

        $imageCollectionSize = $_helper->restoreImagesFromBackup($type);

        if ($imageCollectionSize > 0) {
            $session->addSuccess($_helper->__('%s images were restored', $imageCollectionSize));
        } else {
            $session->addError($_helper->__('No images to restore'));
        }

        $this->_redirectReferer();
    }

    public function statisticsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sizeAction()
    {
        $type = $this->getRequest()->getParam('type');
        $helper = Mage::helper('welance_kraken');

        $response = array(
            'size' => $helper->getImageCount($type),
            'total' => Mage::getSingleton('adminhtml/session')->getImageCount()
        );

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        $this->getResponse()->setHeader('Content-type', 'application/json');
    }
}