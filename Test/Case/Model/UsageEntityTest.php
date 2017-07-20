<?php
App::uses('UsageEntity', 'Usage.Model');

class UsageEntityTest extends CakeTestCase 
{

	public $fixtures = array(
		'plugin.usage.usage_entity',
		'plugin.usage.usage_count'
	);
	
	public $UsageEntity = null;
	
	public function setUp() 
	{
		parent::setUp();
		$this->UsageEntity = ClassRegistry::init('Usage.UsageEntity');
	}
	
	public function test_checkAdd()
	{
		$result = $this->UsageEntity->checkAdd('test_checkAdd', 'tests');
		$this->assertTrue(($result > 0));
		
	}
	
	public function test_checkAddCacheKey()
	{
	// this should run after the above test
		for ($i = 1; $i <= 10; $i++) 
		{
			$result = $this->UsageEntity->checkAdd('test_checkAdd', 'tests');
		}
		
		$key = strtolower(Inflector::underscore(trim('test_checkAdd')));
		$group = strtolower(trim('tests'));
		
		$cache_key = $group. '_'. $key;
		$this->assertTrue(isset($this->UsageEntity->checkAddCache[$cache_key]));
	}
	
	public function test_listNice()
	{
		$result = $this->UsageEntity->checkAdd('test_checkAdd', 'tests');
		$result = $this->UsageEntity->listNice();
pr($result);
	}
	
	public function tearDown() 
	{
		$this->UsageEntity = null;

		parent::tearDown();
	}

}
