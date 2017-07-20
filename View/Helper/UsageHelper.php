<?php

// plugins/Usage/View/Helper/UsageHelper.php
App::uses('UsageAppHelper', 'Usage.View/Helper');

/*
 * Used as a helper with common functionality for generic functions
 */

App::uses('Hash', 'Core');
class UsageHelper extends UsageAppHelper 
{
	public $chartDefaults = array(
		'type' => 'line',
		'id' => false,
		'labels' => array(),
		'data' => array(),
	);
	
	public $chartSettings = array();
	
	public function transformStats($stats_groups = array())
	{
		$settings = array(
			'update' => 'graph',
		);
		
		if(isset($stats_groups['settings']))
		{
			$settings = array_merge($settings, $stats_groups['settings']);
			unset($stats_groups['settings']);
		}
		
		$out = array();
		
		// each root entry is a group of stats
		$tab_i = 1;
		foreach($stats_groups as $stats_group)
		{
			if(!isset($stats_group['UsageEntity']))
				continue;
			
			$graph_url = array('plugin' => 'usage', 'controller' => 'usage_counts', 'action' => 'usage_entity_graph', 
				$stats_group['UsageEntity']['id'],
			);
			
			$this_group = array(
				'key' => $stats_group['UsageEntity']['id'],
				'title' => $stats_group['UsageEntity']['name'],
				'options' => array('class' => 'usage_stats'),
				'stats' => array(
					'year' => array(
						'name' => __('Current Year'), 
						'value' => (isset($stats_group['UsageCountCurrentYear']['time_count'])?$stats_group['UsageCountCurrentYear']['time_count']:0), 
						'url' => '#tabs-'. $stats_group['UsageEntity']['id'],
						'data-href' => array_merge($graph_url, array(1 => 'year')),
					),
					'month' => array(
						'name' => __('Current Month'), 
						'value' => (isset($stats_group['UsageCountCurrentMonth']['time_count'])?$stats_group['UsageCountCurrentMonth']['time_count']:0), 
						'url' => '#tabs-'. $stats_group['UsageEntity']['id'],
						'data-href' => array_merge($graph_url, array(1 => 'month')),
					),
					'week' => array(
						'name' => __('Current Week'), 
						'value' => (isset($stats_group['UsageCountCurrentWeek']['time_count'])?$stats_group['UsageCountCurrentWeek']['time_count']:0), 
						'url' => '#tabs-'. $stats_group['UsageEntity']['id'],
						'data-href' => array_merge($graph_url, array(1 => 'week')),
					),
					'day' => array(
						'name' => __('Today'), 
						'value' => (isset($stats_group['UsageCountCurrentDay']['time_count'])?$stats_group['UsageCountCurrentDay']['time_count']:0), 
						'url' => '#tabs-'. $stats_group['UsageEntity']['id'],
						'data-href' => array_merge($graph_url, array(1 => 'day')),
					),
					'hour' => array(
						'name' => __('Current Hour'), 
						'value' => (isset($stats_group['UsageCountCurrentHour']['time_count'])?$stats_group['UsageCountCurrentHour']['time_count']:0), 
						'url' => '#tabs-'. $stats_group['UsageEntity']['id'],
						'data-href' => array_merge($graph_url, array(1 => 'hour')),
					),
				)
			);
			
			$out[] = $this_group;
			$tab_i++;
		}
		return $out;
	}
	
	public function chartSetup($settings = array(), $reset = false)
	{
		if(!$this->chartSettings or $reset)
			$this->chartSettings = $this->chartDefaults;
		$this->chartSettings = array_merge($this->chartSettings, $settings);
	}
	
	public function chartSetting($k = false, $v = null)
	{
		if(!$k)
			return $this->chartSettings;
		
		if($v !== null)
		{
			$this->chartSettings[$k] = $v;
			return $v;
		}
		
		if(!isset($this->chartSettings[$k]))
			$this->chartSettings[$k] = $v;
		
		return $this->chartSettings[$k];
	}
	
	public function chartData($label = false, $data = array(), $type = 'number')
	{
		//label: label of this column/line/fraction of a pie
		//data: data for this column/line/fraction of a pie
		if(!$label)
			return false;
		
		$label_slug = Inflector::slug(strtolower($label));
		
		$this->chartSettings['labels'][$label_slug] = array('type' => $type, 'label' => $label);
		
		$label_pos = 0;
		foreach($this->chartSettings['labels'] as $slug => $label_settings)
		{
			if($slug == $label_slug)
				break;
			$label_pos++;
		}
		
		$i = 0;
		if(is_array($data))
		{
			foreach($data as $num)
			{
				$this->chartSettings['data'][$i][$label_pos] = $num;
				if($type == 'number')
					$this->chartSettings['data'][$i][$label_pos] = (int)$num;
				$i++;
			}
		}
	}
	
	public function drawChart($settings = array())
	{
		$this->chartSetup($settings);
		$id = $this->chartSetting('id');
		$type = $this->chartSetting('type');
		$this->chartSetting('id', $type.'_chart_'.$id);
		
		$out = $this->GChart->start($this->chartSetting('id'));
		$out .= $this->GChart->visualize($this->chartSetting('id'), $this->chartSetting());
		return $out;
	}
}