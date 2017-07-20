<?php
App::uses('UsageCount', 'Usage.Model');

class UsageCountTest extends CakeTestCase 
{
	public $fixtures = array(
		'plugin.usage.usage_count',
		'plugin.usage.usage_entity'
	);
	
	public function setUp() {
		parent::setUp();
		$this->UsageCount = ClassRegistry::init('Usage.UsageCount');
	}
	
	public function test_checkAdd()
	{
		$result = $this->UsageCount->getTimeStamps();
pr($result);
		$this->assertTrue(array_key_exists('year', $result));
		$this->assertTrue(array_key_exists('month', $result));
		$this->assertTrue(array_key_exists('week', $result));
		$this->assertTrue(array_key_exists('day', $result));
		$this->assertTrue(array_key_exists('hour', $result));
		$this->assertTrue(array_key_exists('minute', $result));
		
		$this->assertTrue($result['year'] == date('Y'));
		$this->assertTrue($result['month'] == date('Ym'));
		$this->assertTrue($result['week'] == date('YW'));
		$this->assertTrue($result['day'] == date('Ymd'));
		$this->assertTrue($result['hour'] == date('YmdH'));
		$this->assertTrue($result['minute'] == date('YmdHi'));
		
	}
	
	public function tearDown() {
		unset($this->UsageCount);

		parent::tearDown();
	}

}
