<?php

App::uses('AppController', 'Controller');

class UsageAppController extends AppController 
{
	public $components = array(
		// Common functions we would like to have all apps available to them
		'Utilities.Common',
	);
	
	public $helpers = array(
		'Utilities.GoogleChart',
	);
}