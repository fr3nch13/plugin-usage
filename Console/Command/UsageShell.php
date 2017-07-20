<?php

App::uses('UsageAppShell', 'Usage.Console/Command');

class UsageShell extends UsageAppShell
{
	// the models to use
	public $uses = array('Usage.UsageEntity');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Usage Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Usage Shell used to run cron jobs common in all of the apps for Usage Stats.'));
		
		$parser->addSubcommand('update_usage_counts', array(
			'help' => __d('cake_console', 'Updates the database with cached usage stats.'),
			'parser' => array(
			),
		));
		
		return $parser;
	}
	
	public function update_usage_counts()
	{
		// get a list of hostnames that need to be looked up
		$results = $this->UsageEntity->cron_UpdateCounts();
	}
}