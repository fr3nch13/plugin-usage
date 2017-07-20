<?php
/***
 *
 * Configuration settings for the Usage plugin
 *
 */
 

CakePlugin::load('GChart');

$extensions = Configure::read('Site.export_extensions');
if(!$extensions)
	$extensions = array();
if(!in_array('png', $extensions))
	$extensions[] = 'png';
Configure::write('Site.export_extensions', $extensions);

Cache::config('usage', array(
	'engine' => 'Memcache',
    'mask' => 0666,
    'duration' => 604800, // 1 WEEK
    //'path' => CACHE,
    'prefix' => 'usage_'
));

CakeLog::config('usage', array(
	'engine' => 'FileLog',
	'mask' => 0666,
	'size' => 0, // disable file log rotation, handled by logrotate
	'types' => array('info', 'notice', 'error', 'warning', 'debug'),
	'scopes' => array('usage'),
	'file' => 'usage.log',
));