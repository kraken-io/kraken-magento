<?php

/**
 * Class Welance_Kraken_Helper_Data
 */

class Welance_Kraken_Helper_Data extends Mage_Core_Helper_Abstract
{

    const BACKUP_DIR = 'backup';
    const ALLOWED_FILE_EXTENSIONS = 'gif;png;jpg;jpeg;svg';
    const EXCLUDE_DIRS = 'cache|backup';


    /**
     * @var string $type
     * @return bool
     */

    public function canShowBackupButton($type)
    {
        $backupAllowed = Mage::getStoreConfig('welance_kraken/kraken_config/backup');
        $backupDir = Mage::getBaseDir(). DS . $type . DS . self::BACKUP_DIR;
        $imageCount = $this->getImageCount($type);

        if ($backupAllowed && is_dir($backupDir) && count(glob($backupDir . DS .'*')) > 0 && $imageCount > 0) {
            return true;
        }

        return false;
    }


    /**
     * @var string $dir
     * @return array
     */

    public function getAllImages($dir)
    {
        $images = $this->_searchDirectories($dir);

        return $images;
    }

    /**
     * @param $dir
     * @return array
     */

    protected function _searchDirectories($dir)
    {
        $rootDir = Mage::getBaseDir();

        $excludeDirs = self::EXCLUDE_DIRS;

        if ($dir == Welance_Kraken_Model_Abstract::TYPE_MEDIA) {
            $excludeDirs .= '|catalog';
        }

        $regexp = '/(' . $excludeDirs . ')/i';

        $imageTypes = explode(';', self::ALLOWED_FILE_EXTENSIONS);

        $handle = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir . DS . $dir), RecursiveIteratorIterator::SELF_FIRST);

        $images = array();

        foreach ($handle as $object) {

            $imageName = $object->getFileName();
            $path = $object->getPath();

            if (preg_match($regexp,$path) || $object->isDir() || !$object->isReadable()) {
                continue;
            }

            // SplFileInfo::getExtension NOT supported < 5.3.6
            if (method_exists('SplFileInfo', 'getExtension')) {
                if (!in_array(strtolower($object->getExtension()),$imageTypes)) {
                    continue;
                }
            } else {
                if (!in_array(strtolower(pathinfo($imageName, PATHINFO_EXTENSION)),$imageTypes)) {
                    continue;
                }
            }

            $checksum = sha1_file($path);
            $_dir = str_replace($rootDir.DS,'',$path);

            $images[] = array(
                'dir' => $_dir,
                'name' => $imageName,
                'checksum' => $checksum
            );
        }

        return $images;
    }

    /**
     * @param $type
     * @param $dir
     * @param $imageName
     * @return bool
     */

    public function backupImage($type,$dir,$imageName)
    {
        $backupDir = Mage::getBaseDir() . DS . $type . DS . self::BACKUP_DIR;
        $saveDir = $backupDir;

        if (!is_dir($backupDir)) {
            mkdir($backupDir);
        }

        foreach (explode(DS, $dir) as $folder) {
            $saveDir .= DS . $folder;

            if (!is_dir($saveDir)) {
                mkdir($saveDir);
            }
        }

        return copy(Mage::getBaseDir() . DS . $dir . DS . $imageName , $backupDir . DS . $dir . DS . $imageName);
    }


    /**
     * @param $type
     * @return mixed
     * @throws Mage_Core_Exception
     */

    public function restoreImagesFromBackup($type)
    {
        $backupDir = Mage::getBaseDir(). DS . $type . DS . self::BACKUP_DIR;

        $imageCollection = Mage::getResourceModel('welance_kraken/images_' . $type . '_collection')->addFieldToSelect('*');

        $imageCollectionSize = $imageCollection->getSize();

        if ($imageCollectionSize > 0) {
            foreach ($imageCollection as $krakenImage) {
                try {
                    $backupFile = $backupDir . DS . $krakenImage->getPath() . DS . $krakenImage->getImageName();
                    if(is_file($backupFile)){
                        copy(
                            $backupFile,
                            Mage::getBaseDir() . DS . $krakenImage->getPath() . DS . $krakenImage->getImageName()
                        );
                    }
                } catch (Exception $e) {
                    Mage::throwException($e->getMessage());
                }
            }

            $this->_clearImages($type);
        }

        return $imageCollectionSize;
    }


    /**
     * @param $size
     * @param int $precision
     * @return string
     */

    public function getImageSizeConverted($size, $precision = 2)
    {
        if ($size <= 0) {
            return '0 B';
        }

        $base = log($size) / log(1024);
        $suffixes = array(' B', ' KB', ' MB', ' GB', ' TB');

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }


    /**
     * @param $type
     * @return $this
     */

    protected function _clearImages($type)
    {
        $resource = Mage::getSingleton('core/resource');

        $writeConnection = $resource->getConnection('core_write');

        $table = $resource->getTableName('welance_kraken/images_'.$type);

        $query = "DELETE FROM {$table} WHERE 1";

        $writeConnection->query($query);

        return $this;
    }


    /**
     * @param $type
     * @return string
     */

    public function getImageCount($type)
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        if ($type == Welance_Kraken_Model_Abstract::TYPE_CACHE) {
            $table = $resource->getTableName('welance_kraken/image_'.$type);
        } else {
            $table = $resource->getTableName('welance_kraken/images_'.$type);
        }

        $query = "SELECT COUNT(*) FROM {$table}";
        return $readConnection->fetchOne($query);
    }


    /**
     * @param $images
     * @return array
     */


    public function getNotOptimizedImages($images,$type)
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table = $resource->getTableName('welance_kraken/images_'.$type);

        $query = "SELECT `path`,`image_name`,`original_checksum`,`checksum_after_upload` FROM `{$table}` ORDER BY `image_name` ASC";

        $results = $readConnection->query($query)->fetchAll();

        foreach ($images as $imageKey => $image) {
            Mage::log(count($results),null,'results.log');
            if(count($results) < 1) {
                break;
            }

            foreach ($results as $resultKey => $result) {
                if($image['dir'] == $result['path'] &&
                    $image['name'] == $result['image_name'] &&
                    ($image['checksum'] == $result['original_checksum'] || $image['checksum'] == $result['checksum_after_upload'])) {
                        unset($images[$imageKey]);
                        unset($results[$resultKey]);
                }
            }

        }

        return $images;
    }



    /**
     * check if image in Database exits
     * if not remove it from Database
     */
    public function removeDeletedImagesFromDatabase()
    {
        $types = array(Welance_Kraken_Model_Abstract::TYPE_MEDIA, Welance_Kraken_Model_Abstract::TYPE_SKIN);

        foreach ($types as $type) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $table = $resource->getTableName('welance_kraken/images_'.$type);
            $query = "SELECT * FROM {$table}";
            $entries = $readConnection->fetchAll($query);

            if (count($entries) > 0) {
                $this->_cleanUpImages($entries,$table);
            }
        }
    }

    /**
     * @param array $entries
     */
    protected function _cleanUpImages($entries,$table)
    {
        $toDelete = array();
        foreach ($entries as $entry) {
            $file =  Mage::getBaseDir() . DS . $entry['path'] . DS . $entry['image_name'];
            if (!is_file($file)) {
                $toDelete[] = $entry['id'];
            }
        }

        if (count($toDelete) > 0) {
            $toDeleteString = implode(',',$toDelete);
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');

            $query = "DELETE FROM {$table} WHERE id IN ({$toDeleteString})";

            $writeConnection->query($query);
        }
    }

    static function cmp($a, $b)
    {
        return strcmp($a["name"], $b["name"]);
    }

}
