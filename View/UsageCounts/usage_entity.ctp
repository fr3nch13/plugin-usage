<?php 
// File: app/View/ProctimeQueries/admin_proctime.ctp


$page_options = array();

// content
$th = array(
/*
'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 40, 'key' => 'primary'),
		'usage_entity_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'key' => 'index'),
		'time_period' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'time_stamp' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => 30, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'time_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
		
*/
	'UsageCount.time_period' => array('content' => __('Time Period'), 'options' => array('sort' => 'UsageCount.time_period')),
	'UsageCount.time_stamp' => array('content' => __('Timestamp'), 'options' => array('sort' => 'UsageCount.time_stamp')),
	'UsageCount.time_count' => array('content' => __('Count'), 'options' => array('sort' => 'UsageCount.time_count')),
);

$td = array();
$i = 0;
foreach ($usage_counts as $i => $usage_count)
{
	$td[$i] = array(
		$usage_count['UsageCount']['time_period'],
		$usage_count['UsageCount']['time_stamp'],
		$usage_count['UsageCount']['time_count'],
	);
	$i++;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Related %s', __('Counts')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));