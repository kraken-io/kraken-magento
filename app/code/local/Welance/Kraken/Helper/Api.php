<?php

class Welance_Kraken_Helper_Api extends Mage_Core_Helper_Abstract
{

    /**
     * @return mixed
     */

    public function getUserStatus()
    {
        $data = array();
        $data['data'] = json_encode($this->getAuthentication());
        $url = Welance_Kraken_Model_Abstract::KRAKEN_USER_STATUS_API_URL;

        return $this->krakenRequest($data,$url);
    }


    /**
     * @return array
     */

    public function getAuthentication()
    {
        $data = array('auth' => array(
            'api_key' => Mage::getStoreConfig('welance_kraken/kraken_auth/api_user'),
            'api_secret' => Mage::getStoreConfig('welance_kraken/kraken_auth/api_secret')
        ));

        return $data;
    }


    /**
     * @param $data
     * @param $url
     * @return mixed
     * @throws Zend_Http_Client_Exception
     */

    public function krakenRequest($data, $url)
    {
        $client = new Zend_Http_Client($url);
        $client->setAdapter('Zend_Http_Client_Adapter_Curl');

        if (isset($data['file'])) {
            $client->setFileUpload($data['file'],'file');
            unset($data['file']);
        }

        $client->setParameterPost($data);

        try {
            $response = $client->request('POST');
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'kraken_response_error.log');
            $body = array('success' => false, 'errorMessage' => $e->getMessage());
            $response = new Zend_Http_Response(404, array(), json_encode($body));
        }

        $this->_setStatusAndNotification($url, $response);

        $body = json_decode($response->getBody());
        $body->statusCode = $response->getStatus();

        return $body;
    }


    /**
     * @param $url
     * @param $response
     * @return $this
     */

    protected function _setStatusAndNotification($url, $response) {
        $status = Mage::getStoreConfig('welance_kraken/kraken_auth/kraken_status');
        $statusCode = $response->getStatus();

        if ($url == Welance_Kraken_Model_Abstract::KRAKEN_UPLOAD_API_URL && $status == true && $statusCode == 429) {
            $responseBody = json_decode($response->getBody());
            $responseText = $responseBody->message;
            Mage::getModel('core/config')->saveConfig('welance_kraken/kraken_auth/kraken_status',0);
            Mage::getModel('adminnotification/inbox')->addNotice('Your free quota (50 MB) has reached its limit.',$responseText);
        }

        if($url == Welance_Kraken_Model_Abstract::KRAKEN_UPLOAD_API_URL && $status == false && $statusCode == 200){
            Mage::getModel('core/config')->saveConfig('welance_kraken/kraken_auth/kraken_status',1);
        }

        return $this;
    }


    /**
     * @return array
     */

    public function getOptions()
    {
        $compression = (int) Mage::getStoreConfig('welance_kraken/kraken_config/compression');
        $quality = (int) Mage::getStoreConfig('welance_kraken/kraken_config/quality');

        $options = array();
        $options['wait'] = true;

        if ($compression != Welance_Kraken_Model_Adminhtml_System_Config_Source_Compression::COMPRESSION_TYPE_LOSSLESS) {
            $options['lossy']   = true;
        }

        if ($quality && $compression == Welance_Kraken_Model_Adminhtml_System_Config_Source_Compression::COMPRESSION_TYPE_CUSTOM) {
            $options['quality'] = $quality;
        }

        return $options;
    }

    public function uploadAndSave($image, $type)
    {
        $krakenImage = Mage::getModel('welance_kraken/images_' . $type);

        $auth = $this->getAuthentication();
        $options = $this->getOptions();

        $data = array();
        $data['file'] = Mage::getBaseDir() . DS . $image['dir'] . DS . $image['name'];

        $data = array_merge(
            array(
                "file" => $data['file'],
                "data" => json_encode(
                    array_merge(
                        $auth,
                        $options
                    )
                )
            )
        );

        $response = $this->krakenRequest($data, Welance_Kraken_Model_Abstract::KRAKEN_UPLOAD_API_URL);

        $response->startTime = microtime(true);
        $response->imageName = $image['name'];
        $response->path = $image['dir'];
        $response->checksum = $image['checksum'];

        if ($response->success) {
            try {
                $krakenImage->saveResponse($response);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'kraken_upload.log');
            }
        }

        return $response;
    }
}