<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('adform_track_xml_product_feed')};

CREATE TABLE {$this->getTable('adform_track_xml_product_feed')} (
    `feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Feed id',
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time of entry',
    `store_id` int(10) unsigned DEFAULT NULL COMMENT 'Store id',
    `url_key` varchar(255) NOT NULL DEFAULT '' COMMENT 'Unique url key',
    `image_width` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Image width in pixels',
    `image_height` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Image height in pixels',
    `ppf` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Max number of products per feed',
    `selection_type` varchar(8) NOT NULL DEFAULT '' COMMENT 'Values like: all, selected',
    `products` text DEFAULT NULL COMMENT 'NULL or list of comma separated product ids',
    PRIMARY KEY (`feed_id`),
    UNIQUE KEY `UNQ_ADFORM_TRACK_XMLPRODUCTFEED_URLKEY` (`url_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='@author Branko Ajzele <ajzele@gmail.com>';

");

$installer->endSetup();
