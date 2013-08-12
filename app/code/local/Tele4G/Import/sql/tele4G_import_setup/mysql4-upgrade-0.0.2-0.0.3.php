<?php
$installer = $this;

$installer->startSetup();

$profileData = array(
        'name'         => 'Addon Groups',
        'actions_xml'  => '<action type="dataflow/convert_adapter_io" method="load"><var name="type">file</var><var name="path">var/import</var><var name="filename"><![CDATA[compatibilities.xml]]></var><var name="format"><![CDATA[xml]]></var></action><action type="comviq_import/convert_parser_xml" method="addongroups"><var name="adapter">comviq_import/convert_adapter_addon</var></action>',
);

Mage::getModel('dataflow/profile')->setData($profileData)->save();
