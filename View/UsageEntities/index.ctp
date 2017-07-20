<?php 
// File: plugins/Usage/View/UsageEntities/group.ctp


$page_options = array();

if(!in_array($this->request->action, array('index', 'admin_index')))
	$page_options[] = $this->Html->link(__('View All'), array('action' => 'index'));

// content
$th = array();

if(!in_array($this->request->action, array('group', 'admin_group')))
{
	$th['UsageEntity.group'] = array('content' => __('Group'), 'options' => array('sort' => 'UsageEntity.group'));
}
$th['UsageEntity.name'] = array('content' => __('Name'), 'options' => array('sort' => 'UsageEntity.name'));
if($this->Wrap->roleCheck(array('admin')) and in_array($this->request->prefix, array('admin')))
{
	$th['UsageEntity.key'] = array('content' => __('Key'), 'options' => array('sort' => 'UsageEntity.key'));
}
$th['UsageCountCurrentYear.time_count'] = array('content' => __('Year #'), 'options' => array('sort' => 'UsageCountCurrentYear.time_count'));
$th['UsageCountCurrentMonth.time_count'] = array('content' => __('Month #'), 'options' => array('sort' => 'UsageCountCurrentMonth.time_count'));
$th['UsageCountCurrentWeek.time_count'] = array('content' => __('Week #'), 'options' => array('sort' => 'UsageCountCurrentWeek.time_count'));
$th['UsageCountCurrentDay.time_count'] = array('content' => __('Day #'), 'options' => array('sort' => 'UsageCountCurrentDay.time_count'));
$th['UsageCountCurrentHour.time_count'] = array('content' => __('Hour #'), 'options' => array('sort' => 'UsageCountCurrentHour.time_count'));
$th['UsageCountCurrentMinute.time_count'] = array('content' => __('Minute #'), 'options' => array('sort' => 'UsageCountCurrentMinute.time_count'));
if($this->Wrap->roleCheck(array('admin')) and in_array($this->request->prefix, array('admin')))
{
	$th['UsageEntity.model'] = array('content' => __('Assoc. Object'), 'options' => array('sort' => 'UsageEntity.model'));
	$th['UsageEntity.created'] = array('content' => __('Added'), 'options' => array('sort' => 'UsageEntity.created'));
	$th['UsageEntity.modified'] = array('content' => __('Updated'), 'options' => array('sort' => 'UsageEntity.modified'));
}
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));
$th['multiselect'] = true;

$td = array();
foreach ($usage_entities as $i => $usage_entity)
{
	$actions = $this->Html->link(__('View'), array('action' => 'view', $usage_entity['UsageEntity']['id']));
	$actions .= $this->Html->link(__('Compare'), array('action' => 'compare', $usage_entity['UsageEntity']['id']));
	
	if($this->Wrap->roleCheck(array('admin')) and in_array($this->request->prefix, array('admin')))
		$actions .= $this->Html->link(__('Edit'), array('action' => 'edit', $usage_entity['UsageEntity']['id']));
	
	
	$td[$i] = array();
	if(!in_array($this->request->action, array('group', 'admin_group')))
	{
		$td[$i][] = $this->Html->link($usage_entity['UsageEntity']['group_name'], array('controller' => 'usage_entities', 'action' => 'group', $usage_entity['UsageEntity']['group']));
	}
	$td[$i][] = $this->Html->link($usage_entity['UsageEntity']['name'], array('controller' => 'usage_entities', 'action' => 'view', $usage_entity['UsageEntity']['id']));
	if($this->Wrap->roleCheck(array('admin')) and in_array($this->request->prefix, array('admin')))
	{
		$td[$i][] = $usage_entity['UsageEntity']['key'];
	}
	$td[$i][] = $usage_entity['UsageCountCurrentYear']['time_count'];
	$td[$i][] = $usage_entity['UsageCountCurrentMonth']['time_count'];
	$td[$i][] = $usage_entity['UsageCountCurrentWeek']['time_count'];
	$td[$i][] = $usage_entity['UsageCountCurrentDay']['time_count'];
	$td[$i][] = $usage_entity['UsageCountCurrentHour']['time_count'];
	$td[$i][] = $usage_entity['UsageCountCurrentMinute']['time_count'];
	if($this->Wrap->roleCheck(array('admin')) and in_array($this->request->prefix, array('admin')))
	{
		$td[$i][] = $usage_entity['UsageEntity']['model'];
		$td[$i][] = $this->Wrap->niceTime($usage_entity['UsageEntity']['created']);
		$td[$i][] = $this->Wrap->niceTime($usage_entity['UsageEntity']['modified']);
	}
	$td[$i][] = array(
		$actions,
		array('class' => 'actions'),
	);
	$td[$i]['multiselect'] = $usage_entity['UsageEntity']['id'];
}

$page_title = __('All %s', __('Usage Entities'));
if(in_array($this->request->action, array('group', 'admin_group')))
{
	$page_title = __(' %s in Group: %s', __('Usage Entities'), $group_name);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => $page_title,
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	'use_multiselect' => true,
	'multiselect_options' => array(
		'compare' => __('Compare selected %s',  __('Usage Entities')),
	),
));