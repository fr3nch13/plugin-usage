<?php
class UsageEntitiesController extends UsageAppController 
{
	public $conditions = array();
	
	public function index($exclude_snapshots = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array_merge($this->conditions, array());
		
		if($exclude_snapshots)
			$conditions['UsageEntity.group !='] = 'snapshot';
		
		$this->UsageEntity->recursive = 0;
		$this->paginate['order'] = array('UsageEntity.modified' => 'desc');
		$this->paginate['conditions'] = $this->UsageEntity->conditions($conditions, $this->passedArgs); 
		
		$usage_entities = $this->paginate();
		$this->set('usage_entities', $usage_entities);
	}
	
	public function group($group = false) 
	{
		if (!$group or !trim($group)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Usage Entity'), __('Group')));
		}
		$this->set('group', $group);
		$this->set('group_name', Inflector::humanize($group));
		
		$this->Prg->commonProcess();
		
		$conditions = array_merge($this->conditions, array(
			'UsageEntity.group' => $group,
		));
		
		$this->UsageEntity->recursive = 0;
		$this->paginate['order'] = array('UsageEntity.modified' => 'desc');
		$this->paginate['conditions'] = $this->UsageEntity->conditions($conditions, $this->passedArgs); 
		
		$usage_entities = $this->paginate();
		
		$group_name = Inflector::humanize($group);
		if($usage_entities)
		{
			$first = current($usage_entities);
			if(isset($first['UsageEntity']['group_name']) and trim($first['UsageEntity']['group_name']))
			$group_name = $first['UsageEntity']['group_name'];
		}
		$this->set('group_name', $group_name);
		
		$this->set('usage_entities', $usage_entities);
	}
	
	public function compare($id = false)
	{
		$this->UsageEntity->id = $id;
		if (!$this->UsageEntity->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Usage Entity')));
		}
		
		// get the counts
		$this->UsageEntity->recursive = 0;
		$this->set('usage_entity', $this->UsageEntity->read(null, $id));
	}
	
	public function view($id = null) 
	{
		$this->UsageEntity->id = $id;
		if (!$this->UsageEntity->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Usage Entity')));
		}
		
		// get the counts
		$this->UsageEntity->recursive = 0;
		$this->set('usage_entity', $this->UsageEntity->read(null, $id));
	}
	
	public function multiselect()
	{
		if(!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		$redirect = false;
		if(isset($this->request->data['multiple']))
		{
			$ids = array();
			foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
			$this->request->data['multiple'] = $this->UsageEntity->find('list', array(
				'fields' => array('UsageEntity.id', 'UsageEntity.id'),
				'conditions' => array('UsageEntity.id' => $ids),
				'recursive' => -1,
			));
		}
		
		if($this->request->data['UsageEntity']['multiselect_option'] == 'compare')
		{
			$redirect = array('action' => 'multiselect_compare');
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_'.$this->UsageEntity->alias.'_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$this->bypassReferer = true;
			return $this->redirect($redirect);
		}
		
		$this->Session->setFlash(__('The %s were NOT updated.', __('Usage Entities')));
		$this->redirect($this->referer());
	}
	
	public function multiselect_compare()
	{		
		$sessionData = Cache::read('Multiselect_'.$this->UsageEntity->alias.'_'. AuthComponent::user('id'), 'sessions');
		
		$usage_entities = array();
		if(isset($sessionData['multiple']))
		{
			$usage_entities = $this->UsageEntity->find('all', array(
				'recursive' => 0,
				'conditions' => array(
					'UsageEntity.id' => $sessionData['multiple'],
				),
			));
		}
		
		if(!$usage_entities)
		{
			$this->Session->setFlash(__('No %s were selected.', __('Usage Entities')));
			$this->redirect($this->referer());
		}
		
		$this->set('usage_entities', $usage_entities);
	}
	
	public function admin_index() 
	{
		return $this->index();
	}
	
	public function admin_group($group = false) 
	{
		return $this->group($group);
	}
	
	public function admin_view($id = null) 
	{
		return $this->view($id);
	}
	
	public function admin_edit($id = null) 
	{
		$this->UsageEntity->id = $id;
		if (!$this->UsageEntity->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Usage Entity')));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->UsageEntity->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Usage Entity')));
				return $this->redirect(array('action' => 'view', $this->UsageEntity->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Usage Entity')));
			}
		}
		else
		{
			$this->UsageEntity->recursive = -1;
			$this->request->data = $this->UsageEntity->read(null, $id);
		}
	}
}