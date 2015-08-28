<?php

class Welance_Kraken_Model_Observer
{

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

    public function checkPlan($observer)
    {
        $apiKey = Mage::getStoreConfig('welance_kraken/kraken_auth/api_user');
        $apiSecret = Mage::getStoreConfig('welance_kraken/kraken_auth/api_secret');

        if ($apiKey && $apiSecret) {
            $helper = Mage::helper('welance_kraken/api');
            $response = $helper->getUserStatus();
            $config = Mage::getModel('core/config');

            if ($response->plan_name == 'Micro' || $response->plan_name == 'Basic') {
                $config->saveConfig('welance_kraken/kraken_auth/api_user', null);
                $config->saveConfig('welance_kraken/kraken_auth/api_secret', null);
                $config->saveConfig('welance_kraken/kraken_auth/kraken_status', 0);

                Mage::getSingleton('adminhtml/session')->addError($helper->__('
                    We support Magento only from the Advanced plan up. Your current plan is %s.
                ', $response->plan_name));
            } else {
                $config->saveConfig('welance_kraken/kraken_auth/kraken_status',1);
            }
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
        $helper = Mage::helper('welance_kraken/api');

        $auth = $helper->getAuthentication();
        $options = $helper->getOptions();

        $data = array_merge(array(
            "file" => $cacheImage,
            "data" => json_encode(array_merge(
                $auth, $options
            ))
        ));

        try {
            $response = $helper->krakenRequest($data, Welance_Kraken_Model_Abstract::KRAKEN_UPLOAD_API_URL);

            if ($response->success == true) {
                copy($response->kraked_url,$cacheImage);
                Mage::getModel('welance_kraken/image_cache')->saveResponse($response);
            } else {
                Mage::log($response, null, 'kraken_response.log');
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'kraken_response.log');
        }

        return $this;
    }

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