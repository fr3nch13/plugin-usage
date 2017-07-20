<?php
class UsageCountsController extends UsageAppController 
{
//
	public function usage_entity($usage_entity_id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'UsageCount.usage_entity_id' => $usage_entity_id,
		);
		
		$this->paginate['order'] = array('UsageCount.time_stamp' => 'desc');
		$this->paginate['conditions'] = $this->UsageCount->conditions($conditions, $this->passedArgs); 
		
		$usage_counts = $this->paginate();
		$this->set('usage_counts', $usage_counts);
	}
//
	public function usage_entity_graph($usage_entity_id = false, $timeframe = 'week', $timestamp = false)
	{
		if(!$timestamp) $timestamp = date('Y-m-d-H-i-s');
		list($year, $month, $day, $hour, $minute, $second) = explode('-', $timestamp);
		$time_stamp = "$year-$month-$day $hour:$minute:$second";
		$time_stamp = strtotime($time_stamp);
		
		$usage_counts = $this->UsageCount->countMatrix($usage_entity_id, $timeframe, $timestamp);
		
		$this->set('timeframe', $timeframe);
		$this->set('timestamp', $timestamp);
		$this->set('time_stamp', $time_stamp);
		$this->set('timestamp_next', date('Y-m-d-H-i-s', strtotime('+1 '. $timeframe, $time_stamp)));
		$this->set('timestamp_prev', date('Y-m-d-H-i-s', strtotime('-1 '. $timeframe, $time_stamp)));
		$this->set('usage_counts', $usage_counts);
		$this->set('usage_entity', $this->UsageCount->UsageEntity->read(null, $usage_entity_id));
	}
//
	public function usage_entity_compare($usage_entity_id = false, $timeframe = 'week', $timestamp1 = false, $timestamp2 = false)
	{
		if(!$timestamp1) $timestamp1 = date('Y-m-d-H-i-s');
		list($year1, $month1, $day1, $hour1, $minute1, $second1) = explode('-', $timestamp1);
		$time_stamp1 = "$year1-$month1-$day1 $hour1:$minute1:$second1";
		$time_stamp1 = strtotime($time_stamp1);
		
		if(!$timestamp2) $timestamp2 = date('Y-m-d-H-i-s', strtotime('-1 '. $timeframe, $time_stamp1));
		list($year2, $month2, $day2, $hour2, $minute2, $second2) = explode('-', $timestamp2);
		$time_stamp2 = "$year2-$month2-$day2 $hour2:$minute2:$second2";
		$time_stamp2 = strtotime($time_stamp2);
		
		$usage_counts_1 = $this->UsageCount->countMatrix($usage_entity_id, $timeframe, $timestamp1);
		$usage_counts_2 = $this->UsageCount->countMatrix($usage_entity_id, $timeframe, $timestamp2);
		
		$this->set('timeframe', $timeframe);
		$this->set('timestamp1', $timestamp1);
		$this->set('time_stamp1', $time_stamp1);
		$this->set('timestamp2', $timestamp2);
		$this->set('time_stamp2', $time_stamp2);
		$this->set('timestamp_next', date('Y-m-d-H-i-s', strtotime('+1 '. $timeframe, $time_stamp1)));
		$this->set('timestamp_prev', date('Y-m-d-H-i-s', strtotime('-1 '. $timeframe, $time_stamp1)));
		$this->set('usage_counts_1', $usage_counts_1);
		$this->set('usage_counts_2', $usage_counts_2);
		$this->set('usage_entity', $this->UsageCount->UsageEntity->read(null, $usage_entity_id));
	}
	
	public function multiselect_compare_graph($timeframe = 'week', $timestamp = false)
	{
		if(!$timestamp) $timestamp = date('Y-m-d-H-i-s');
		list($year, $month, $day, $hour, $minute, $second) = explode('-', $timestamp);
		$time_stamp = "$year-$month-$day $hour:$minute:$second";
		$time_stamp = strtotime($time_stamp);
		
		$sessionData = Cache::read('Multiselect_'.$this->UsageCount->UsageEntity->alias.'_'. AuthComponent::user('id'), 'sessions');
		
		$usage_entities = array();
		if(isset($sessionData['multiple']))
		{
			if($this->UsageCount->UsageEntity instanceof AppModel)
			{
				// reload the UsageCount
				App::uses('UsageEntity', 'Usage.Model');
				$this->UsageCount->UsageEntity = new UsageEntity();
			}
			
			$usage_entities = $this->UsageCount->UsageEntity->listNice(array(
				'recursive' => -1,
				'conditions' => array(
					'UsageEntity.id' => $sessionData['multiple'],
				),
			));
		}
		
		$usage_counts = array();
		$colors = array();
		foreach($usage_entities as $usage_entity_id => $usage_entity_name)
		{
			$usage_counts[$usage_entity_id] = $this->UsageCount->countMatrix($usage_entity_id, $timeframe, $timestamp);
			$colors[$usage_entity_id] = '#'. substr(md5($usage_entity_id), 0, 6);
		}
		
		$data = array();
		$min = $max = array(0);
		foreach($usage_entities as $usage_entity_id => $usage_entity_name)
		{
			$usage_counts[$usage_entity_id] = $this->UsageCount->countMatrix($usage_entity_id, $timeframe, $timestamp);
			$data[$usage_entity_name] = array();
			
			foreach($usage_counts[$usage_entity_id] as $k => $usage_count_int)
			{
				$data[$usage_entity_name][$k] = $usage_count_int;
				
				$min[] = $usage_count_int;
				$max[] = $usage_count_int;
			}
		}
		
		$this->set('timeframe', $timeframe);
		$this->set('timestamp', $timestamp);
		$this->set('time_stamp', $time_stamp);
		$this->set('timestamp_next', date('Y-m-d-H-i-s', strtotime('+1 '. $timeframe, $time_stamp)));
		$this->set('timestamp_prev', date('Y-m-d-H-i-s', strtotime('-1 '. $timeframe, $time_stamp)));
		$this->set('data', $data);
		$this->set('min', $min);
		$this->set('max', $max);
		$this->set('colors', $colors);
		$this->set('usage_counts', $usage_counts);
	}
	
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->paginate['order'] = array('UsageCount.time_stamp' => 'desc');
		$this->paginate['conditions'] = $this->UsageCount->conditions($conditions, $this->passedArgs); 
		
		$usage_counts = $this->paginate();
		$this->set('usage_counts', $usage_counts);
	}

	public function admin_usage_entity($usage_entity_id = false)
	{
		return $this->usage_entity($usage_entity_id);
	}

	public function admin_usage_entity_graph($usage_entity_id = false, $timeframe = 'week', $timestamp = false)
	{
		return $this->usage_entity_graph($usage_entity_id, $timeframe, $timestamp);
	}
}