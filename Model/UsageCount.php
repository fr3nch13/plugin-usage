<?php
App::uses('UsageAppModel', 'Usage.Model');
/**
 * UsageCount Model
 *
 * @property UsageEntity $UsageEntity
 */
class UsageCount extends UsageAppModel 
{
	public $displayField = 'time_period';
	
	public $validate = array(
		'usage_entity_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'time_period' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'time_stamp' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'time_count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'UsageEntity' => array(
			'className' => 'Usage.UsageEntity',
			'foreignKey' => 'usage_entity_id',
		)
	);
	
	public $actsAs = array(
		'Usage.Usage' => array(
			'onCreate' => false,
			'onDelete' => false,
			'useCache' => false
		)
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'UsageCount.time_period',
		'UsageCount.time_stamp',
	);
	
	public $time_periods = array(
		'year' => 'Y',
		'month' => 'Ym',
		'week' => 'YW',
		'day' => 'Ymd',
		'hour' => 'YmdH',
		'minute' => 'YmdHi',
	);
	
	public $nice_time_periods = array(
		'year' => 'F',
		'month' => 'jS',
		'week' => 'l',
		'day' => 'ga',
		'hour' => 'i',
		'minute' => 'i',
	);
	
	public $nice_time_periods_range = array(
		'year' => 'Y',
		'month' => 'M Y',
		'week' => 'D',
		'day' => 'M j, Y',
		'hour' => 'ga',
		'minute' => 'i',
	);
	
	public $time_period_matrix = array(
		'year' => 'month',
		'month' => 'day',
		'week' => 'day',
		'day' => 'hour',
		'hour' => 'minute',
	);
	
	public function countMatrixRange($usage_entity_id = false, $time_period = false, $start = false, $end = false, $keepRaw = false)
	{
		$out = array();
		
		if(!isset($this->time_period_matrix[$time_period]))
		{
			return $out;
		}
		
		if(!$start)
			$start = time();
		else
			$start = strtotime($start);
		
		if(!$end) 
			$end = strtotime('-1 '. $time_period, $start);
		else
			$end = strtotime($end);
		
		$TimeStart = new DateTime();
		$TimeStart->setTimestamp($start);
		$TimeEnd = clone $TimeStart;
		$TimeEnd->setTimestamp($end);
		$TimeNow = clone $TimeEnd;
		
		$formattedStart = $TimeStart->format($this->time_periods[$time_period]);
		$formattedEnd = $TimeEnd->format($this->time_periods[$time_period]);
		
		// figure out the ranges, and build the matrix with 0 counts
		$matrix = array();
		$matrixFormatted = array();
		
		$now = $start;
		$formattedNow = $formattedEnd;
		while($formattedNow <= $formattedStart)
		{
			$matrix[$formattedNow] = 0;
			
			if(!$keepRaw)
			{
				$matrixFormatted[$formattedNow] = $TimeNow->format($this->nice_time_periods_range[$time_period]);
			}
			$TimeNow->modify('+1 '. $time_period);
			$formattedNow = $TimeNow->format($this->time_periods[$time_period]);
		}
		
		if($usage_entity_id)
		{
			$conditions = array(
				'UsageCount.usage_entity_id' => $usage_entity_id,
				'UsageCount.time_period' => $time_period,
				'UsageCount.time_stamp <=' => $formattedStart,
				'UsageCount.time_stamp >=' => $formattedEnd,
			);
			
			$usageCounts = $this->find('list', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'fields' => array('UsageCount.time_stamp', 'UsageCount.time_count'),
			));
			if($usageCounts)
			{
				foreach($usageCounts as $k => $v)
				{
					$matrix[$k] = $v;
				}
			}
		}
		
		if($keepRaw)
		{
			return $matrix;
		}
		
		$_matrix = array();
		foreach($matrixFormatted as $k => $v)
		{
			$_matrix[$v] = $matrix[$k];
		}
		unset($matrix);
		unset($matrixFormatted);
		return $_matrix;
	}
	
	public function countMatrix($usage_entity_id = false, $time_period = false, $time_stamp = false)
	{
		$out = array();
		
		if(!isset($this->time_period_matrix[$time_period]))
		{
			return $out;
		}
		
		if(!$time_stamp) 
			$time_stamp = time();
		else
		{
			list($year, $month, $day, $hour, $minute, $second) = explode('-', $time_stamp);
			$time_stamp = "$year-$month-$day $hour:$minute:$second";
			$time_stamp = strtotime($time_stamp);
		}
		
		$granularity = $this->time_period_matrix[$time_period];
		
		$time_stamps = $this->getTimeStamps($time_stamp);
		
		$dtNow = new DateTime();
		$dtNow->setTimestamp($time_stamp);
		$beginTime = clone $dtNow;
		
		$endTime = clone $beginTime;
		
		$condition_time_stamp = $time_stamps[$time_period];
		
		if($time_period == 'hour')
		{
			list($hour, $minute) = explode('-', date('H-i', $time_stamp));
			$beginTime->setTime($hour, 0, 0);
			$endTime->setTime($hour, 59, 59);
		}
		elseif($time_period == 'day')
		{
			$beginTime->setTime(0, 0, 0);
			$endTime->setTime(23, 59, 59);
		}
		elseif($time_period == 'week')
		{
			$beginTime->setTimestamp(strtotime('Last Monday', $time_stamp));
			$endTime->setTimestamp(strtotime('Next Sunday', $time_stamp));
			$condition_time_stamp = date('Ym', strtotime('Last Monday', $time_stamp));
		}
		elseif($time_period == 'month')
		{
			list($year, $month, $day) = explode('-', date('Y-m-1', $time_stamp));
			$beginTime->setDate($year, $month, $day);
			
			list($year, $month, $day) = explode('-', date('Y-m-t', $time_stamp));
			$endTime->setDate($year, $month, $day);
		}
		elseif($time_period == 'year')
		{
			list($year, $month, $day) = explode('-', date('Y-1-1', $time_stamp));
			$beginTime->setDate($year, $month, $day);
			
			list($year, $month, $day) = explode('-', date('Y-12-31', $time_stamp));
			$endTime->setDate($year, $month, $day);
		}
		
		$start = $beginTime->format($this->time_periods[$granularity]);
		
		$end = $endTime->format($this->time_periods[$granularity]);
		
		$nowTime = clone $beginTime;
		$ranges = array();
		$labels = array();
		for($now = $start; $now < $end; )
		{
			$now = $nowTime->format($this->time_periods[$granularity]);
			$ranges[$now] = 0;
			$labels[$now] = $nowTime->format($this->nice_time_periods[$time_period]);
			$nowTime->modify('+1 '. $granularity);
		}
		
		
		$conditions = array(
			'UsageCount.usage_entity_id' => $usage_entity_id,
			'UsageCount.time_period' => $this->time_period_matrix[$time_period],
			'UsageCount.time_stamp LIKE' => $condition_time_stamp. '%',
		);
		
		$usage_counts = $this->find('list', array(
			'recursive' => -1,
			'fields' => array('UsageCount.time_stamp', 'UsageCount.time_count'),
			'conditions' => $conditions,
		));
		
		foreach($ranges as $timestamp => $count)
		{
			if(isset($usage_counts[$timestamp])) $ranges[$timestamp] = $usage_counts[$timestamp];
		}
		
		foreach($ranges as $timestamp => $count)
		{
			$index = $timestamp;
			if(isset($labels[$timestamp])) $index = $labels[$timestamp];
			$out[$index] = $count;
		}
		
		return $out;
	}
	
	public function checkAddUpdate($usage_entity_id = false, $time_period = false, $time_stamp = false, $time_count = 0, $overwrite_count = false)
	{

		if(!$time_period) return false;
		if(!$time_stamp) return false;
		if(!is_int($time_stamp)) return false;
		if(!$time_count) return false;
		if(!is_int($time_count)) return false;
		
		// make sure we're using the default db config
		$this->useDbConfig = 'default';
		
		$this->recursive = -1;
		$this->getCounts = false;
		$usage_count = $this->find('first', array(
			'conditions' => array(
				'usage_entity_id' => $usage_entity_id,
				'time_period' => $time_period,
				'time_stamp' => $time_stamp,
			),
		));
		
		$id = false;
		
		if(!$usage_count)
		{
			$this->create();
			$this->data = array(
				'usage_entity_id' => $usage_entity_id,
				'time_period' => $time_period,
				'time_stamp' => $time_stamp,
				'time_count' => $time_count,
			);
		
			if($this->save($this->data))
			{
				$id = $this->id;
			}
		}
		else
		{
			$this->id = $usage_count[$this->alias]['id'];
			
			if(!$overwrite_count)
			{
				$time_count_old = $usage_count[$this->alias]['time_count'];
				$time_count = ($time_count + $time_count_old);
			}
			$this->data = array(
				'time_count' => $time_count,
			);
		
			if($this->save($this->data))
			{
				$id = $this->id;
			}
		}
		return $id;
	}
	
	public function updateCounts($usage_entity_id = false, $count = 1, $time_period = false, $time_stamp = false, $overwrite_count = false)
	{
		if(!$usage_entity_id) return false;
		if(!$count) return false;
		if(!is_int($count)) return false;
		
		$time_periods = array();
		
		// mode likely coming from the caching system in the UsageBehavior
		if($time_period and $time_stamp)
		{
			return $this->checkAddUpdate($usage_entity_id, $time_period, (int)$time_stamp, (int)$count, $overwrite_count);
		}
		else
		{
			$cnt = 0;
			foreach($this->time_periods as $time_period => $time_format)
			{
				$time_stamp = date($time_format);
				$time_stamp = (int)$time_stamp;
				if($this->checkAddUpdate($usage_entity_id, $time_period, (int)$time_stamp, (int)$count, $overwrite_count))
				{
					$cnt++;
				}
			}
			return $cnt;
		}
	}
	
	public function getTimeStamps($timestamp = false)
	{
		if(!$timestamp) $timestamp = time();
		
		$timestamps = array();
		foreach($this->time_periods as $time_period => $time_format)
		{
			$time_stamp = date($time_format, $timestamp);
			$time_stamp = (int)$time_stamp;
			$timestamps[$time_period] = $time_stamp;
		}
		return $timestamps;
	}
}
