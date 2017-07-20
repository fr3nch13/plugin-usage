<?php


$timestamp_title1 = $this->Wrap->niceDay(date('Y-m-d H:i:s', $time_stamp1));
$timestamp_title2 = $this->Wrap->niceDay(date('Y-m-d H:i:s', $time_stamp2));

if($timeframe == 'hour')
{
	$timestamp_title1 = $this->Wrap->niceTime(date('Y-m-d H:i:s', $time_stamp1));
	$timestamp_title2 = $this->Wrap->niceTime(date('Y-m-d H:i:s', $time_stamp2));
}
elseif($timeframe == 'day')
{
	$timestamp_title1 = $this->Wrap->niceDay(date('Y-m-d H:i:s', $time_stamp1));
	$timestamp_title2 = $this->Wrap->niceDay(date('Y-m-d H:i:s', $time_stamp2));
}
elseif($timeframe == 'week')
{
	$timestamp_title1 = __('Week starting %s', $this->Wrap->niceDay(date('Y-m-d H:i:s', strtotime('Last Monday', $time_stamp1))));
	$timestamp_title2 = __('Week starting %s', $this->Wrap->niceDay(date('Y-m-d H:i:s', strtotime('Last Sunday', $time_stamp2))));
}
elseif($timeframe == 'month')
{
	$timestamp_title1 = date('F, Y', $time_stamp1);
	$timestamp_title2 = date('F, Y', $time_stamp2);
}
elseif($timeframe == 'year')
{
	$timestamp_title1 = date('Y', $time_stamp1);
	$timestamp_title2 = date('Y', $time_stamp2);
}

$div_id = 'chart_'. rand(1, 100);
$data = array();

// in the case that we're comparing months with different length of days
if(count($usage_counts_1) != count($usage_counts_2))
{
	if(count($usage_counts_1) > count($usage_counts_2))
	{
		$diff = array_diff(array_keys($usage_counts_1), array_keys($usage_counts_2));
		foreach($diff as $key)
			$usage_counts_2[$key] = 0;
	}
	elseif(count($usage_counts_2) > count($usage_counts_1))
	{
		$diff = array_diff(array_keys($usage_counts_2), array_keys($usage_counts_1));
		foreach($diff as $key)
			$usage_counts_1[$key] = 0;
	}
}

$data[$timestamp_title1] = array();
$data[$timestamp_title2] = array();

$min = $max = array(0);
$title_count1 = 0;
$title_count2 = 0;
foreach($usage_counts_1 as $usage_count_time => $usage_count_int)
{
	$title_count1 = ($title_count1 + $usage_count_int);
	$data[$timestamp_title1][$usage_count_time] = $usage_count_int;
	$min[] = $usage_count_int;
	$max[] = $usage_count_int;
}
foreach($usage_counts_2 as $usage_count_time => $usage_count_int)
{	
	$title_count2 = ($title_count2 + $usage_count_int);
	$data[$timestamp_title2][$usage_count_time] = $usage_count_int;
	$min[] = $usage_count_int;
	$max[] = $usage_count_int;
}

$title = __('Comparing %s (%s) to %s (%s)', 
	$timestamp_title2, $title_count2,
	$timestamp_title1, $title_count1
);

$page_content = '';

$this->Usage->chartSetup();
// what type it is
$this->Usage->chartData(__('Days'), array_keys($data[$timestamp_title1]), 'string');
$this->Usage->chartData($timestamp_title1, $data[$timestamp_title1]);
$this->Usage->chartData($timestamp_title2, $data[$timestamp_title2]);
$page_content = $this->Usage->drawChart(array(
	'title' => $title,
	'id' => rand(1, 100),
));


$this->Usage->chartSetup(array(), true);
$this->Usage->chartData(__('Entities'), __('Total Count'));
$this->Usage->chartData($timestamp_title1, $title_count1);
$this->Usage->chartData($timestamp_title2, $title_count2);
$page_content .= $this->Usage->drawChart(array(
	'title' => $title,
	'id' => rand(1, 100),
	'type' => 'pie',
));

echo $this->element('Utilities.page_generic', array(
	'page_subtitle' => $title,
	'page_content' => $page_content,
));