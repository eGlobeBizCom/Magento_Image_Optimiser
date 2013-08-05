<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('imageoptimiser')};
CREATE TABLE {$this->getTable('imageoptimiser')} (
 `imageoptimiser_id` int(11) unsigned NOT NULL auto_increment,
 `filename` varchar(255) NOT NULL default '',
 PRIMARY KEY  (`imageoptimiser_id`),
 UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
    ");

$installer->endSetup(); 