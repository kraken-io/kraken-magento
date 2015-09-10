<?php

class Welance_Kraken_Model_Observer
{

    /**
     * @param $observer
     * @return $this
     */

    public function checkVersion($observer)
    {
        $section = $observer->getEvent()->getControllerAction()->getRequest()->getParam('section');

        if ($section == 'welance_kraken') {
            $magentoVersion = Mage::getVersion();
            $session = Mage::getSingleton('adminhtml/session');
            $helper = Mage::helper('welance_kraken');

            if (version_compare($magentoVersion, '1.7.0.2', '<')) {
                $session->addNotice(
                    $helper->__(
                        'Kraken.io extension only supports Magento form version 1.7.0.2. You have version %s. Use at your own risk.',$magentoVersion
                    )
                );
            }

            if (!extension_loaded('curl')) {
                $session->addError($helper->__('Curl is not installed. Please install it on your server.'));
            }
        }

        return $this;
    }


    /**
     * @param $observer
     * @return $this
     */

    public function checkPlan($observer)
    {
        $apiKey = Mage::getStoreConfig('welance_kraken/kraken_auth/api_user');
        $apiSecret = Mage::getStoreConfig('welance_kraken/kraken_auth/api_secret');

        if ($apiKey && $apiSecret) {
            $helper = Mage::helper('welance_kraken/api');
            $response = $helper->getUserStatus();
            $config = Mage::getModel('core/config');
            $config->saveConfig('welance_kraken/kraken_auth/kraken_status',1);
        }

        return $this;
    }


    /**
     * @param $observer
     * @return $this
     */

    public function optimizeCacheImage($observer)
    {
        $cacheImage = $observer->getEvent()->getObject()->getNewFile();

        Mage::getModel('welance_kraken/image_cache')->saveCacheImage($cacheImage);

        return $this;
    }


    /**
     * @param $observer
     * @return void
     */

    public function cacheRedirect($observer)
    {
        $request = Mage::app()->getRequest();

        Mage::getModel('welance_kraken/image_cache')->clearCache();

        if ($request->getParam('kraken')) {
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The image cache was cleared.'));

            $response = Mage::app()->getResponse();
            $response->setRedirect(Mage::helper("adminhtml")->getUrl('*/kraken/index', array()));
            $response->sendResponse();

            exit;
        }
    }
}