<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('welance_kraken/image'),
        'in_queue',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Image in Queue'
        )
    );

$installer->endSetup();