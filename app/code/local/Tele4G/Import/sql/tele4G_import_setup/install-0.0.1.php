<?php
$installer = $this;

$installer->startSetup();

$dataflowData = array(
    array(
        'name'         => 'Articles',
        'actions_xml'  => '<action type="dataflow/convert_adapter_io" method="load"><var name="type">file</var><var name="path">var/import</var><var name="filename"><![CDATA[articles.xml]]></var><var name="format"><![CDATA[xml]]></var></action><action type="comviq_import/convert_parser_xml" method="parse"><var name="adapter">comviq_import/convert_adapter_product</var><var name="method">saveRow</var><var name="attributeset">device</var><var name="category"><![CDATA[Simple Devices]]></var></action>',
    ),
    array(
        'name'         => 'Subscriptions',
        'actions_xml'  => '<action type="dataflow/convert_adapter_io" method="load"><var name="type">file</var><var name="path">var/import</var><var name="filename"><![CDATA[subscriptions.xml]]></var><var name="format"><![CDATA[xml]]></var></action><action type="comviq_import/convert_parser_xml" method="parse"><var name="adapter">comviq_import/convert_adapter_product</var><var name="method">saveRow</var><var name="attributeset">subscription</var><var name="category"><![CDATA[Subscriptions]]></var></action>',
    ),
    array(
        'name'         => 'Accessory Compatibilities',
        'actions_xml'  => '<action type="dataflow/convert_adapter_io" method="load"><var name="type">file</var><var name="path">var/import</var><var name="filename"><![CDATA[compatibilities.xml]]></var><var name="format"><![CDATA[xml]]></var></action><action type="comviq_import/convert_parser_xml" method="acccompat"><var name="adapter">comviq_import/convert_adapter_product</var><var name="method">saveRow</var></action>',
    ),
    array(
        'name'         => 'Addon Compatibilities',
        'actions_xml'  => '<action type="dataflow/convert_adapter_io" method="load"><var name="type">file</var><var name="path">var/import</var><var name="filename"><![CDATA[compatibilities.xml]]></var><var name="format"><![CDATA[xml]]></var></action><action type="comviq_import/convert_parser_xml" method="addcompat"><var name="adapter">comviq_import/convert_adapter_product</var><var name="method">saveRow</var></action>',
    ),
    array(
        'name'         => 'Hardware Variants',
        'actions_xml'  => '<action type="dataflow/convert_adapter_io" method="load"><var name="type">file</var><var name="path">var/import</var><var name="filename"><![CDATA[compatibilities.xml]]></var><var name="format"><![CDATA[xml]]></var></action><action type="comviq_import/convert_parser_xml" method="variants"><var name="adapter">comviq_import/convert_adapter_product</var><var name="method">saveRow</var></action>',
    ),
);

foreach ($dataflowData as $bind) {
    Mage::getModel('dataflow/profile')->setData($bind)->save();
}