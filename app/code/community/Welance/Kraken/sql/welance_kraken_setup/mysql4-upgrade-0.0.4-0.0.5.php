<?php

$installer = $this;

$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS kraken_image_queue");

$_tables = array('media','skin');
foreach($_tables as $_table){
    $table = $installer->getConnection()->newTable($installer->getTable('welance_kraken/images_'.$_table))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true,), 'Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Created at')
        ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Image Path')
        ->addColumn('image_name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Filename')
        ->addColumn('uploaded_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true
        ), 'Uploaded At')
        ->addColumn('original_checksum', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Original File Checksum')
        ->addColumn('original_size', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Original File Size')
        ->addColumn('checksum_after_upload', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true
        ), 'Checksum after Upload')
        ->addColumn('size_after_upload', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
            'unsigned' => true
        ), 'Uploaded File Size')
        ->addColumn('saved_file_size', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
            'unsigned' => true
        ), 'Saved File Size')
        ->addColumn('percent_saved', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
            'nullable' => true
        ), 'File Size Saved in Percent')
        ->addColumn('success',Varien_Db_Ddl_Table::TYPE_BOOLEAN,null,array(),'Response Succes')
        ->addColumn('response_error',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Response Error Message')
        ->addColumn('response_time',Varien_Db_Ddl_Table::TYPE_FLOAT,null,array(),'Response Time');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();