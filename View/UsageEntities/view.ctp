<?php 
// File: plugins/Usage/View/UsageEntities/view.ctp

$page_options = array(
);

if($this->Wrap->roleCheck(array('admin')) and in_array($this->request->prefix, array('admin')))
	$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $usage_entity['UsageEntity']['id'], 'admin' => true));

$details = array();
$details[] = array('name' => __('Group'), 'value' => $usage_entity['UsageEntity']['group_name']);
$details[] = array('name' => __('Name'), 'value' => $usage_entity['UsageEntity']['name']);

if($this->Wrap->roleCheck(array('admin')) and in_array($this->request->prefix, array('admin')))
{
	$details[] = array('name' => __('Key'), 'value' => $usage_entity['UsageEntity']['key']);
	$details[] = array('name' => __('Associated Object'), 'value' => $usage_entity['UsageEntity']['model']);
	$details[] = array('name' => __('Added'), 'value' => $this->Wrap->niceTime($usage_entity['UsageEntity']['created']));
	$details[] = array('name' => __('Updated'), 'value' => $this->Wrap->niceTime($usage_entity['UsageEntity']['modified']));
}

$stats = array(
	array(
		'id' => 'UsageCountCurrentYear',
		'name' => __('Current %s', __('Year')), 
		'value' => $usage_entity['UsageCountCurrentYear']['time_count'], 
		'tab' => array('tabs', '8'), // the tab to display
	),
	array(
		'id' => 'UsageCountCurrentMonth',
		'name' => __('Current %s', __('Month')), 
		'value' => $usage_entity['UsageCountCurrentMonth']['time_count'], 
		'tab' => array('tabs', '6'), // the tab to display
	),
	array(
		'id' => 'UsageCountCurrentWeek',
		'name' => __('Current %s', __('Week')), 
		'value' => $usage_entity['UsageCountCurrentWeek']['time_count'], 
		'tab' => array('tabs', '4'), // the tab to display
	),
	array(
		'id' => 'UsageCountCurrentDay',
		'name' => __('Current %s', __('Day')), 
		'value' => $usage_entity['UsageCountCurrentDay']['time_count'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'UsageCountCurrentHour',
		'name' => __('Current %s', __('Hour')), 
		'value' => $usage_entity['UsageCountCurrentHour']['time_count'], 
	),
	array(
		'id' => 'UsageCountCurrentMinute',
		'name' => __('Current %s', __('Minute')), 
		'value' => $usage_entity['UsageCountCurrentMinute']['time_count'], 
	),
);

$tabs = array(
	array(
		'key' => 'description',
		'title' => __('Description'),
		'content' => $this->Wrap->descView($usage_entity['UsageEntity']['desc']),
	),
	array(
	'key' => 'UsageCounts',
	'title' => __('Related %s', __('Counts')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity', $usage_entity['UsageEntity']['id']),
	),
	array(
	'key' => 'UsageGraphDaily',
	'title' => __('Daily %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_graph', $usage_entity['UsageEntity']['id'], 'day'),
	),
	array(
	'key' => 'UsageGraphCompareDaily',
	'title' => __('Compare Daily %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'day'),
	),
	array(
	'key' => 'UsageGraphWeekly',
	'title' => __('Weekly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_graph', $usage_entity['UsageEntity']['id'], 'week'),
	),
	array(
	'key' => 'UsageGraphCompareWeekly',
	'title' => __('Compare Weekly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'week'),
	),
	array(
	'key' => 'UsageGraphMonthly',
	'title' => __('Monthly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_graph', $usage_entity['UsageEntity']['id'], 'month'),
	),
	array(
	'key' => 'UsageGraphCompareMonthly',
	'title' => __('Compare Monthly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'month'),
	),
	array(
	'key' => 'UsageGraphYearly',
	'title' => __('Yearly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_graph', $usage_entity['UsageEntity']['id'], 'year'),
	),
	array(
	'key' => 'UsageGraphCompareYearly',
	'title' => __('Compare Yearly %s', __('Graph')), 
	'url' => array('controller' => 'usage_counts', 'action' => 'usage_entity_compare', $usage_entity['UsageEntity']['id'], 'year'),
	),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s - %s', __('Usage Entity'), $usage_entity['UsageEntity']['group_name'], $usage_entity['UsageEntity']['name']),
	'page_options' => $page_options,
	'details_title' => __('Details'),
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));