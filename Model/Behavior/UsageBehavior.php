<?php

//App::uses('CakeEmail', 'Network/Email');
//App::uses('Shell', 'Console');

class UsageBehavior extends ModelBehavior
{
	public $settings = array();
	
	protected $_defaults = array(
		'useCache' => true,
		'onCreate' => false,
		'onRead' => false,
		'onUpdate' => false,
		'onDelete' => false,
		'UsageEntityAlias' => 'UsageEntity',
		'UsageEntityClass' => 'Usage.UsageEntity',
		'UsageCountAlias' => 'UsageCount',
		'UsageCountClass' => 'Usage.UsageCount',
		'modelKeyField' => 'model_id',
		'modelField' => 'model',
		'resetBinding' => false,
	);
	
	public $Model = false;
	
	// If we are actually in the cron, save to the database
	public $inCron = false;
	
	public function setup(Model $Model, $settings = array())
	{
//		register_shutdown_function(array($this, "shutdown"), $Model);
		$this->Model = $Model;
		
		if (!isset($this->settings[$Model->alias])) 
		{
			$this->settings[$Model->alias] = $this->_defaults;
		}
		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
		
		// bind the UsageEntity 
		extract($this->settings[$Model->alias]);
		$Model->bindModel(array('hasMany' => array(
			$UsageEntityAlias => array(
				'className' => $UsageEntityClass,
				'foreignKey' => $modelKeyField,
				'conditions' => array(
					$UsageEntityAlias. '.'. $modelField => $Model->name,
				),
				'dependent' => false,
				),
			),
		), $resetBinding);
	}
	
	public function afterFind(Model $Model, $results = array(), $primary = false)
	{
// -- Disabled here --
return true;
		if(!$this->settings[$Model->alias]['onRead']) return true;
		
		// don't count the find('count')
		if(isset($results[0][0])) return true;
		
		$key = Inflector::underscore($Model->alias). '_read';
		$count = count($results);
		$this->Usage_updateCounts($Model, $key, $Model->name, $count);
		$this->Usage_updateCounts($Model, Inflector::underscore($Model->name), 'read', $count);
	}
	
	public function afterSave(Model $Model, $created = false, $options = array())
	{
// -- Disabled here --
return true;
		if($created)
		{
			if(!$this->settings[$Model->alias]['onCreate']) return true;
		
			$key = Inflector::underscore($Model->alias). '_created';
			$this->Usage_updateCounts($Model, $key, $Model->name);
			$this->Usage_updateCounts($Model, Inflector::underscore($Model->name), 'created');
		}
		else
		{
			if(!$this->settings[$Model->alias]['onUpdate']) return true;
		
			$key = Inflector::underscore($Model->alias). '_updated';
			$this->Usage_updateCounts($Model, $key, $Model->name);
			$this->Usage_updateCounts($Model, Inflector::underscore($Model->name), 'updated');
		}
	}
	
	public function afterDelete(Model $Model)
	{
// -- Disabled here --
return true;
		if(!$this->settings[$Model->alias]['onDelete']) return true;
		$key = Inflector::underscore($Model->alias). '_deleted';
		$this->Usage_updateCounts($Model, $key, $Model->name);
		$this->Usage_updateCounts($Model, Inflector::underscore($Model->name), 'deleted');
	}
	
	public function Usage_updateCounts(Model $Model, $key = false, $group = null, $count = 1, $modelName = false, $time_period = false, $time_stamp = false)
	{
		if(!trim($key))
		{
			$Model->modelError = __('Unknown Key (%s)', 1);
			$Model->shellOut($Model->modelError, 'usage', 'warning');
			return false;
		}
		
		if(!is_int($count))
		{
			$Model->modelError = __('Count is not an integer');
			$Model->shellOut($Model->modelError, 'usage', 'warning');
			return false;
		}
		
		$key = trim($key);
		$key = strtolower($key);
		
		if(!$modelName)
		{
			$modelName = $Model->name;
		}
		
		if(!$group)
		{
			$group = $Model->name;
		}
		
		// save/update the entry and counts
		
		if($this->settings[$Model->alias]['useCache'] and !$this->inCron)
		{
			$this->Usage_updateCacheCounts($Model, $key, $group, $count, $modelName);
		}
		else
		{ 
			extract($this->settings[$Model->alias]);
			
			if($Model->alias == $UsageEntityAlias)
			{
				if($Model->updateCount($key, $group, $count, $modelName, $time_period, $time_stamp))
				{
					$Model->shellOut(__('Counts saved: Model: %s(%s) - key: %s - group: %s time_period: %s - time_stamp: %s - count: %s', $Model->name, $Model->alias, $key, $group, $time_period, $time_stamp, $count), 'usage', 'info');
				}
			}
			else
			{
				if($Model->{$UsageEntityAlias}->updateCount($key, $group, $count, $modelName, $time_period, $time_stamp))
				{
					$Model->shellOut(__('Counts saved: Model: %s(%s) - key: %s - group: %s time_period: %s - time_stamp: %s - count: %s', $Model->name, $Model->alias, $key, $group, $time_period, $time_stamp, $count), 'usage', 'info');
				}
			}
		}
	}
	
	public function Usage_updateCacheCounts(Model $Model, $key = false, $group = null, $count = 1, $modelName = false)
	{
		// times to track
		extract($this->settings[$Model->alias]);
		$time_stamps = $Model->{$UsageEntityAlias}->getTimeStamps();
		
		if(!$modelName)
		{
			$modelName = $Model->name;
		}
		
		if(!$group)
		{
			$group = $Model->name;
		}
		
		$cache = Cache::read('counts', 'usage');
		
		if(!$cache) $cache = array();
		if(!isset($cache[$modelName][$group][$key])) $cache[$modelName][$group][$key] = array();
		
		foreach($time_stamps as $time_period => $time_stamp)
		{
			if(!isset($cache[$modelName][$group][$key][$time_stamp])) $cache[$modelName][$group][$key][$time_stamp] = array();
			if(!isset($cache[$modelName][$group][$key][$time_stamp][$time_period])) $cache[$modelName][$group][$key][$time_stamp][$time_period] = 0;
			
			$cache_count = $cache[$modelName][$group][$key][$time_stamp][$time_period];
			$cache_count = ($cache_count + $count);
			$cache[$modelName][$group][$key][$time_stamp][$time_period] = $cache_count;
		}
		
		Cache::write('counts', $cache, 'usage');
	}
	
	public function Usage_saveCachedCounts(Model $Model)
	{	
		$cache = Cache::read('counts', 'usage');
		
		// clear the current counts before anything else writes to them
		
		// cache was already written and cleared
		if(!is_array($cache)) return true;
		if(!count($cache)) return true;
		
		$this->inCron = true;
		
		$cache = Set::flatten($cache);
		foreach($cache as $cache_settings => $count)
		{
			list($model_name, $group, $key, $time_stamp, $time_period) = explode('.', $cache_settings);
			$this->Usage_updateCounts($Model, $key, $group, $count, $model_name, $time_period, $time_stamp);
		}
		Cache::delete('counts', 'usage');
	}
	
	public function Usage_stats(Model $Model)
	{
		// return all current time period stats for this model
		extract($this->settings[$Model->alias]);
		
		$implied_group = Inflector::tableize($Model->alias);
		
		$stats = $Model->{$UsageEntityAlias}->find('all', array(
			'recursive' => 0,
			'conditions' => array(
				$UsageEntityAlias.'.group' => $implied_group,
			),
			'order' => array($UsageEntityAlias.'.name' => 'ASC'),
		));
		
		return $stats;
	}
}