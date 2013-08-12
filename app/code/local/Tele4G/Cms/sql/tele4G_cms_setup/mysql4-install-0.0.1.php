<?php

$cmsBlocks = array();

$postTitle = <<<POST_TITLE
<h2>K&ouml;p en mobiltelefon med abonnemanget Comviq Fastpris</h2>
<p class="checked-labels"><span class="checked-label">Fria samtal</span> <span class="checked-label">Fria SMS</span> <span class="checked-label">3GB surf ing&aring;r</span> <span class="checked-label">Inga dolda avgifter</span></p>
POST_TITLE;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - post - title',
    'identifier'    => 'allproducts_post_title',
    'content'       => $postTitle,
    'is_active'     => 1,
);

$preTitle = <<<PRE_TITLE
<h2>Vi ger dig en mobil n&auml;r du tankar f&ouml;r minst 150 kr/m&aring;n! Ring f&ouml;r hela beloppet.</h2>
PRE_TITLE;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - pre - title',
    'identifier'    => 'allproducts_pre_title',
    'content'       => $preTitle,
    'is_active'     => 1,
);

$postColumn0 = <<<POST_COLUMN_0
<h3>Enklare mobiler</h3>
<p><span class="price"> <small></small> 295 kr <small>/m&aring;n</small> </span></p>
POST_COLUMN_0;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - post - column - 0',
    'identifier'    => 'allproducts_post_column_0',
    'content'       => $postColumn0,
    'is_active'     => 1,
);

$postColumn1 = <<<POST_COLUMN_1
<h3>Prisv&auml;rda mobiler</h3>
<p><span class="price"> <small></small> 335 kr <small>/m&aring;n</small> </span></p>
POST_COLUMN_1;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - post - column - 1',
    'identifier'    => 'allproducts_post_column_1',
    'content'       => $postColumn1,
    'is_active'     => 1,
);

$postColumn2 = <<<POST_COLUMN_2
<h3>V&auml;rstingmobiler</h3>
<p><span class="price"> <small>Fr&aring;n</small> 385 kr <small>/m&aring;n</small> </span></p>
POST_COLUMN_2;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - post - column - 2',
    'identifier'    => 'allproducts_post_column_2',
    'content'       => $postColumn2,
    'is_active'     => 1,
);

$preColumn0 = <<<PRE_COLUMN_0
<h3>Tanka f&ouml;r minst</h3>
<p><span class="price"> <small></small> 150 kr <small>/m&aring;n</small> </span></p>
PRE_COLUMN_0;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - pre - column - 0',
    'identifier'    => 'allproducts_pre_column_0',
    'content'       => $preColumn0,
    'is_active'     => 1,
);

$preColumn1 = <<<PRE_COLUMN_1
<h3>Tanka f&ouml;r minst</h3>
<p><span class="price"> <small></small> 250 kr <small>/m&aring;n</small> </span></p>
PRE_COLUMN_1;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - pre - column - 1',
    'identifier'    => 'allproducts_pre_column_1',
    'content'       => $preColumn1,
    'is_active'     => 1,
);

$preColumn2 = <<<PRE_COLUMN_2
<h3>Tanka f&ouml;r minst</h3>
<p><span class="price"> <small></small> 350 kr <small>/m&aring;n</small> </span></p>
PRE_COLUMN_2;
$cmsBlocks[] = array(
    'title'         => 'AllProducts - pre - column - 2',
    'identifier'    => 'allproducts_pre_column_2',
    'content'       => $preColumn2,
    'is_active'     => 1,
);

/**
 * Insert blocks for AllProducts page
 */
foreach ($cmsBlocks as $data) {
    Mage::getModel('cms/block')->setData($data)->setStores(0)->save();
}