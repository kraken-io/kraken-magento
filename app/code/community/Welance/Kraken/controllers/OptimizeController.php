<?php

class Welance_Kraken_OptimizeController extends Mage_Core_Controller_Front_Action
{
    private function _initCacheImage($id)
    {
        $cacheImage = Mage::getModel('welance_kraken/image_cache')->load($id);

        if ($cacheImage->getIsRunning() != 1) {
            $cacheImage->setIsRunning(1)->save();
        } else {
            $cacheImage = false;
        }

        return $cacheImage;
    }

    public function cacheAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $cacheImage = $this->_initCacheImage($request->getParam('id'));

            if ($cacheImage != false) {
                $fileName = $cacheImage->getFileName();

                $helper = Mage::helper('welance_kraken/api');

                $auth = $helper->getAuthentication();
                $options = $helper->getOptions();

                $data = array_merge(array(
                    "file" => $fileName,
                    "data" => json_encode(array_merge(
                        $auth, $options
                    ))
                ));

                try {
                    $response = $helper->krakenRequest($data, Welance_Kraken_Model_Abstract::KRAKEN_UPLOAD_API_URL);

                    if ($response->success == true) {
                        $response->product_id = $request->getParam('product_id');

                        copy($response->kraked_url,$fileName);

                        $cacheImage->saveResponse($response);

                        $this->getResponse()->setHeader('Content-type', 'application/json');
                        $this->getResponse()->setBody(json_encode($response));
                    } else {
                        Mage::log($response, null, 'kraken_response.log');
                    }
                } catch(Exception $e) {
                    Mage::log($e->getMessage(), null, 'kraken_response.log');
                }
            }
        }
    }
}