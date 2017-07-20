<?php 
// File: plugins/Usage/View/UsageEntities/multiselect_compare.ctp


$tabs = array(
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Daily %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'multiselect_compare_graph', 'day'),
	),
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Weekly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'multiselect_compare_graph', 'week'),
	),
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Month %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'multiselect_compare_graph', 'month'),
	),
	array(
	'key' => 'CategoriesUsageEntity',
	'title' => __('Year %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'multiselect_compare_graph', 'year'),
	),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Comparing %s %s', count($usage_entities), __('Usage Entities')),
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));