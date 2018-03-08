<?php
/**
 * @package         Crowdfunding
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html GNU/GPLv3
 */

defined('_JEXEC') or die;
/**
 * @var array $itemData
 */

$rowThree = array();
if (array_key_exists('country', $itemData) and $itemData['country']) {
    $rowThree[] = CrowdfundingHelper::filterByLink($itemData['country'], 'country', ['filter' => $itemData['country_code']]);
}
if (array_key_exists('region', $itemData) and $itemData['region']) {
    $rowThree[] = CrowdfundingHelper::filterByLink($itemData['region'], 'region', ['filter' => $itemData['region_code']]);
}
if (array_key_exists('location', $itemData) and $itemData['location']) {
    $rowThree[] = CrowdfundingHelper::filterByLink($itemData['location'], 'location', ['filter' => $itemData['locid']]);
}
?>
<div class="panel panel-default">
    <?php if ((bool)$this->params->get('show_title', 0)) {?>
        <div class="panel-heading"><h1><?php echo htmlspecialchars($itemData['title'], ENT_COMPAT, 'UTF-8');?></h1></div>
    <?php } ?>
    <div class="panel-body">
        <?php if (array_key_exists('category', $itemData) and $itemData['category']) { ?>
        <div class="row cf-detailsbar-category">
            <div class="col-md-12">
                <?php echo CrowdfundingHelper::filterByLink($itemData['category'], 'category', ['filter' => $itemData['catslug']]); ?>
            </div>
        </div>
        <?php } ?>

        <?php if (array_key_exists('type', $itemData) and $itemData['type']) { ?>
        <div class="row mt-10 cf-detailsbar-type">
            <div class="col-md-12">
                <?php echo CrowdfundingHelper::filterByLink($itemData['type'], 'type', ['filter' => $itemData['type_id']]); ?>
            </div>
        </div>
        <?php } ?>

        <?php if (count($rowThree) > 0) { ?>
            <div class="row mt-10 cf-detailsbar-location">
                <div class="col-md-12">
                    <?php echo implode('&nbsp;&nbsp;', $rowThree); ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
