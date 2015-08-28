<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('welance_kraken/image'),
        'response_time',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
            'nullable' => true,
            'default' => 0,
            'comment' => 'Response Time'
        )
    );

$installer->endSetup();