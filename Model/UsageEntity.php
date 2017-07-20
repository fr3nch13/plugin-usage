<?php
App::uses('UsageAppModel', 'Usage.Model');
/**
 * UsageEntity Model
 *
 * @property UsageCount $UsageCount
 */
class UsageEntity extends UsageAppModel 
{
	public $displayField = 'name';
	public $useTable = 'usage_entities';
	
	public $validate = array(
		'key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $hasMany = array(
		'UsageCount' => array(
			'className' => 'Usage.UsageCount',
			'foreignKey' => 'usage_entity_id',
			'dependent' => true,
		),
	);
	
	public $hasOne = array(
		'UsageCountCurrentYear' => array(
			'className' => 'UsageCount',
			'foreignKey' => 'usage_entity_id',
			'dependent' => true,
			'conditions' => array(
				'UsageCountCurrentYear.time_period' => 'year', 
				'UsageCountCurrentYear.time_stamp = DATE_FORMAT(NOW(),"%Y")',
			),
		),
		'UsageCountCurrentMonth' => array(
			'className' => 'UsageCount',
			'foreignKey' => 'usage_entity_id',
			'dependent' => true,
			'conditions' => array(
				'UsageCountCurrentMonth.time_period' => 'month', 
				'UsageCountCurrentMonth.time_stamp = DATE_FORMAT(NOW(),"%Y%m")',
			),
		),
		'UsageCountCurrentWeek' => array(
			'className' => 'UsageCount',
			'foreignKey' => 'usage_entity_id',
			'dependent' => true,
			'conditions' => array(
				'UsageCountCurrentWeek.time_period' => 'week', 
				'UsageCountCurrentWeek.time_stamp = DATE_FORMAT(NOW(),"%Y%v")',
			),
		),
		'UsageCountCurrentDay' => array(
			'className' => 'UsageCount',
			'foreignKey' => 'usage_entity_id',
			'dependent' => true,
			'conditions' => array(
				'UsageCountCurrentDay.time_period' => 'day', 
				'UsageCountCurrentDay.time_stamp = DATE_FORMAT(NOW(),"%Y%m%d")',
			),
		),
		'UsageCountCurrentHour' => array(
			'className' => 'UsageCount',
			'foreignKey' => 'usage_entity_id',
			'dependent' => true,
			'conditions' => array(
				'UsageCountCurrentHour.time_period' => 'hour', 
				'UsageCountCurrentHour.time_stamp = DATE_FORMAT(NOW(),"%Y%m%d%H")',
			),
		),
		'UsageCountCurrentMinute' => array(
			'className' => 'UsageCount',
			'foreignKey' => 'usage_entity_id',
			'dependent' => true,
			'conditions' => array(
				'UsageCountCurrentMinute.time_period' => 'minute', 
				'UsageCountCurrentMinute.time_stamp = DATE_FORMAT(NOW(),"%Y%m%d%H%i")',
			),
		),
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
		'UsageEntity.name',
		'UsageEntity.key',
		'UsageEntity.group',
		'UsageEntity.model',
	);
	
	public $checkAddCache = array();
	
	public $checkAddCreated = false;
	
	public function beforeSave($options = array()) 
	{
		if(isset($this->data[$this->alias]['finder_options']) and is_array($this->data[$this->alias]['finder_options']))
		{
			$this->data[$this->alias]['finder_options'] = json_encode($this->data[$this->alias]['finder_options']);
			$this->data[$this->alias]['finder_options'] = trim($this->data[$this->alias]['finder_options']);
		}
		
		return parent::beforeSave($options);
	}
	
	public function checkAdd($key = false, $group = false, $data = array())
	{
		$this->checkAddCreated = false;
		if(!$key) return false;
		
		$key = strtolower(Inflector::underscore(trim($key)));
		$group = strtolower(trim($group));
		
		$cache_key = $group. '_'. $key;
		
		if(isset($this->checkAddCache[$cache_key])) return $this->checkAddCache[$cache_key];
		
		// make sure we're using the default db config
		$this->setDataSource('default');
		
		$this->recursive = -1;
		$this->getCounts = false;
		$record = $this->find('first', array(
			'conditions' => array(
				'key' => $key,
				'group' => $group,
			),
		));
		
		$id = false;
		
		if(!$record)
		{
			$this->create();
			$this->checkAddCreated = true;
			if(!isset($data['group_name']))
			{
				$data['group_name'] = $group;
				$data['group_name'] = str_replace('.', ' - ', $data['group_name']);
				$data['group_name'] = Inflector::humanize($data['group_name']);
			}
			if(!isset($data['name']))
			{
				$data['name'] = $key;
				$data['name'] = str_replace('.', ' - ', $data['name']);
				$data['name'] = Inflector::humanize($data['name']);
			}
			if(!isset($data['compiled']))
				$data['compiled'] = false;
		}
		else
		{
			$this->checkAddCreated = false;
			$id = $this->id = $record[$this->alias]['id'];
			$this->checkAddCache[$cache_key] = $id;
		}
		
		// the finder options, mainly used for the Snapshot Stats
		if(isset($data['finder_options']) and isset($record[$this->alias]['finder_options']))
		{
			$data['finder_options'] = json_encode($data['finder_options']);
			$data['finder_options'] = trim($data['finder_options']);
			
			$finder_options_current = $record[$this->alias]['finder_options'];
			
			$finder_options_current = json_encode($finder_options_current);
			$finder_options_current = trim($finder_options_current);
			
			if($data['finder_options'] == $finder_options_current)
			{
				unset($data['finder_options']);
			}
		}
		
		// the finder options, mainly used for the Snapshot Stats
		if(isset($data['model']) and isset($record[$this->alias]['model']))
		{
			if($data['model'] == $record[$this->alias]['model'])
			{
				unset($data['model']);
			}
		}
		
		if($data)
		{
			$data['key'] = $key;
			$data['group'] = $group;
			
			if($id)
				$data['id'] = $id;
			
			$this->data[$this->alias] = $data;
			
			if($this->save($this->data))
			{
				$id = $this->id;
			}
		}
		
		$this->checkAddCache[$cache_key] = $id;
		
		
		return $id;
	}
	
	public function existingIds($keys = array(), $group = false)
	{
		if(!$keys) return [];
		
		$conditions = [];
		
		if($group)
		{
			$group = strtolower(trim($group));
			$conditions[$this->alias.'.group'] = $group;
		}
		
		if($keys)
		{
			foreach($keys as $i => $key)
			{
				$keys[$i] = strtolower(Inflector::underscore(trim($key)));
			}
			$conditions[$this->alias.'.key'] = $keys;
		}
		
		if(!$conditions) return [];
		
		return $this->find('list', [
			'recursive' => -1,
			'conditions' => $conditions,
			'fields' => [$this->alias.'.key', $this->alias.'.id']
		]);
	}
	
	public function updateCount($key = false, $group = false, $count = 1, $modelName = false, $time_period = false, $time_stamp = false, $overwrite_count = false, $id = false)
	{
		if(!$id)
			$id = $this->checkAdd($key, $group);
		
		// check the UsageCount
		if($this->UsageCount instanceof AppModel)
		{
			// reload the UsageCount
			App::uses('UsageCount', 'Usage.Model');
			$this->UsageCount = new UsageCount();
		}
		
		// update the counts using the UsageCount model
		return $this->UsageCount->updateCounts($id, $count, $time_period, $time_stamp, $overwrite_count);
	}
	
	public function getTimeStamps()
	{
		// check the UsageCount
		if($this->UsageCount instanceof AppModel)
		{
			// reload the UsageCount
			App::uses('UsageCount', 'Usage.Model');
			$this->UsageCount = new UsageCount();
		}
		return $this->UsageCount->getTimeStamps();
	}
	
	public function cron_UpdateCounts()
	{
	// ran only from a cron script
	// looks for all cached counts, and saves them to the database
	// uses the Usage behavior to do this
	
		return $this->Usage_saveCachedCounts();
	}
	
	public function stats($model = false, $group = false, $key = false)
	{
		
	}
	
	public function listNice($options = array())
	{
		$entities = $this->find('all', $options);
		
		$out = array();
		
		foreach($entities as $entity)
		{
			$id = $entity[$this->alias]['id'];
			$fullname = __('%s: %s', $entity[$this->alias]['group_name'], $entity[$this->alias]['name']);
			$out[$id] = $fullname;
		}
		return $out;
	}
}
