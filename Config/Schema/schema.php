<?php 
class UsageSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $usage_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 40, 'unsigned' => false, 'key' => 'primary'),
		'usage_entity_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'time_period' => array('type' => 'string', 'null' => false, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'time_stamp' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'time_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'usage_entity_id' => array('column' => 'usage_entity_id', 'unique' => 0),
			'time_period' => array('column' => 'time_period', 'unique' => 0),
			'time_stamp' => array('column' => 'time_stamp', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $usage_entities = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'model_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20, 'unsigned' => false, 'key' => 'index'),
		'compiled' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
		'dynamic' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
		'type' => array('type' => 'string', 'null' => false, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'group' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'group_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'desc' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'finder_options' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'last_updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'model' => array('column' => 'model', 'unique' => 0),
			'model_id' => array('column' => 'model_id', 'unique' => 0),
			'compiled' => array('column' => 'compiled', 'unique' => 0),
			'dynamic' => array('column' => 'dynamic', 'unique' => 0),
			'group' => array('column' => 'group', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

}
