<?php 
// File: plugins/Usage/View/UsageEntities/compare.ctp
$page_options = array(
	$this->Html->link(__('View'), array('action' => 'view', $usage_entity['UsageEntity']['id'])),
);

$tabs = array(
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Daily %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'day'),
	),
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Weekly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'week'),
	),
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Month %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'month'),
	),
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Year %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'year'),
	),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Comparing %s: %s', __('Usage Entity'), $usage_entity['UsageEntity']['name']),
	'page_options' => $page_options,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));