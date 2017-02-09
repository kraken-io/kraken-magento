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
     * @return $this
     */

    public function getAllImages($dir)
    {
        $images = $this->_searchDirectories($dir);

        return $images;
    }


    /**
     * @return int
     */

    public function countImages($dir)
    {
        return count($this->_searchDirectories($dir));
    }


    /**
     * @param $dir
     * @return $this
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

        foreach ($handle as $fullpath => $object) {
            $imageName = $object->getFileName();

            if (preg_match($regexp,$object->getPath())) {
                continue;
            }

            if ($object->isDir()) {
                continue;
            }

            if (!$object->isReadable()) {
                Mage::log('not readable: '.$fullpath,null,'read.log');
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

            $checksum = sha1_file($fullpath);
            $_dir = str_replace($rootDir.DS,'',$object->getPath());

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
     * @return bool
     */

    public function imageExists($type, $path, $imageName, $checksum)
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table = $resource->getTableName('welance_kraken/images_'.$type);

        $query = "SELECT `id` FROM `{$table}` WHERE `path` = :path AND `image_name` = :image_name AND
                  (`original_checksum` = :checksum OR `checksum_after_upload` = :checksum)";

        $bind = array(
            'path' => $path,
            'image_name' => $imageName,
            'checksum' => $checksum
        );

        /**
         * Using $readConnection->query() with bind parameter automatically escapes the string
         */

        $select = $readConnection->query($query,$bind);

        if ($select->fetch() !== false) {
            return true;
        }

        return false;
    }


    /**
     * delete entries, which started with the upload but did not finish
     */

    public function deletePendingEntries()
    {
        $types = array(Welance_Kraken_Model_Abstract::TYPE_MEDIA, Welance_Kraken_Model_Abstract::TYPE_SKIN);

        foreach ($types as $type) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $table = $resource->getTableName('welance_kraken/images_'.$type);
            $query = "SELECT * FROM {$table} WHERE `uploaded_at` IS NULL AND `checksum_after_upload` IS NULL";

            if (count($readConnection->fetchAll($query)) > 0) {
                $writeConnection = $resource->getConnection('core_write');
                $deleteQuery = "DELETE FROM {$table} WHERE `uploaded_at` IS NULL AND `checksum_after_upload` IS NULL";
                $writeConnection->query($deleteQuery);
            }
        }
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
}
