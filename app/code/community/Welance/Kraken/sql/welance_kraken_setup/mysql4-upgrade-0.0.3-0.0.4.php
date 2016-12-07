<?php

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->newTable($installer->getTable('welance_kraken/image_cache'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'auto_increment' => true,), 'Id')
    ->addColumn('original_size', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Original File Size')
    ->addColumn('size_after_upload', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'unsigned' => true
    ), 'Uploaded File Size')
    ->addColumn('saved_file_size', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'unsigned' => true
    ), 'Saved File Size');

$installer->getConnection()->createTable($table);

$installer->endSetup();