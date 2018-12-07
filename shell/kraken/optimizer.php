<?php

require_once '../abstract.php';

/**
 * Welance Kraken optimizer script
 *
 * @category    Welance
 * @package     Welance_Kraken
 */
class Welance_Kraken_Optimizer extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        if(isset($this->_args['type'])) {
            $types = array($this->_args['type']);
        } else {
            $types = array('media','skin');
        }

        $helper = Mage::helper('welance_kraken');
        foreach($types as $type) {
            $_images = $helper->getAllImages($type);
            usort($_images,array("Welance_Kraken_Helper_Data","cmp"));

            $newImages = $helper->getNotOptimizedImages($_images, $type);

            $imageFolderCount = count($newImages);
            $apiHelper = Mage::helper('welance_kraken/api');

            if($imageFolderCount < 1) {
                echo $helper->__('No %s images to optimze',$type);
                continue;
            }
            $count = 0;

            foreach($newImages as $image) {

                if (Mage::getStoreConfig('welance_kraken/kraken_config/backup')) {
                    Mage::helper('welance_kraken')->backupImage($type, $image['dir'], $image['name']);
                }

                try {
                    $apiHelper->uploadAndSave($image, $type);
                    $count++;
                    echo $helper->__('%s of %s %s images optimized',$count,$imageFolderCount,$type).PHP_EOL;
                } catch (Exception $e) {
                    Mage::log($e->getMessage(),null,'kraken_script.log');
                }
            }
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f optimzer.php --[options]

  --type       type of images to optimize media | skin (optional)
  --help          This help

USAGE;
    }
}

$shell = new Welance_Kraken_Optimizer();
$shell->run();
