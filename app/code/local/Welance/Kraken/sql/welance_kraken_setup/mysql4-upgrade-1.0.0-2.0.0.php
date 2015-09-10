<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('welance_kraken/image_cache');
$installer->getConnection()
    ->addColumn($table,
        'is_running',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'nullable' => true,
            'default'   => 0,
            'comment' => 'Is Running'
        )
    );

$installer->getConnection()
    ->addColumn($table,
        'is_processed',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'nullable' => true,
            'default'   => 0,
            'comment' => 'Is Processed'
        )
    );

$installer->getConnection()
    ->addColumn($table,
        'product_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'unsigned' => true,
            'comment' => 'Product Id'
        )
    );

$installer->endSetup();