<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('welance_kraken/image_cache');
$installer->getConnection()
    ->addColumn($table,
        'file_name',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'File Name'
        )
    );

$installer->getConnection()
    ->addColumn($table,
        'mime_type',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Mime Type'
        )
    );

$installer->endSetup();