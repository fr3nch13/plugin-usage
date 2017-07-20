<?php

$div_id = 'chart_'. rand(1, 100);
$height = 300;
$width = 1000;

$title = $this->Wrap->niceDay(date('Y-m-d H:i:s', $time_stamp));
if($timeframe == 'hour')
{
	$title = $this->Wrap->niceTime(date('Y-m-d H:i:s', $time_stamp));
}
elseif($timeframe == 'week')
{
	$title = __('Week of %s to %s', 
		$this->Wrap->niceDay(date('Y-m-d H:i:s', strtotime('Monday', $time_stamp))),
		$this->Wrap->niceDay(date('Y-m-d H:i:s', strtotime('Sunday', $time_stamp))) 
		);
}
elseif($timeframe == 'month')
{
	$title = date('F, Y', $time_stamp);
}
elseif($timeframe == 'year')
{
	$title = date('Y', $time_stamp);
}

$data = array(__('%s: %s', $usage_entity['UsageEntity']['group_name'], $usage_entity['UsageEntity']['name']) => $usage_counts);

foreach($data as $name => $numbers)
{
	$this->GoogleChart->addColumn($name, $numbers);
}

$this->GoogleChart->setOptions(array(
	'div_id' => $div_id,
));

$this->GoogleChart->setChartAttrs(array(
	'type'		=> 'line',
	'title'		=> $title,
	'size'		=> array( $width, $height ),
	'labelsXY'	=> true,
	'data'		=> $data,
	'min'		=> array(min($usage_counts),min($usage_counts)),
	'max'		=> array(max($usage_counts),max($usage_counts)),
	'color'		=> array('#0000FF', '#FF0000'),
	'image_div'	=> $div_id.'_png',
));

$url_current = $this->Html->urlBase();
$url_next = $url_prev = $url_current;
$url_prev[2] = $timestamp_prev;
$url_next[2] = $timestamp_next;

if(isset($url_current[2])) unset($url_current[2]);

$previous = $this->Html->link(__('Previous %s', ucfirst(strtolower($timeframe))), $url_prev, array('class' => 'button'));
$current = $this->Html->link(__('Current %s', ucfirst(strtolower($timeframe))), $url_current, array('class' => 'button'));
$next = $this->Html->link(__('Next %s', ucfirst(strtolower($timeframe))), $url_next, array('class' => 'button'));
$png_link = $this->Html->link(__('View PNG'), '#', array('class' => 'button png_link'));
$svg_link = $this->Html->link(__('View SVG'), '#', array('class' => 'button svg_link'));

$chart_title = $this->Html->tag('h2', $title);
$chart_div_svg = $this->Html->tag('div', '', array('class' => 'chart_svg', 'id' => $div_id, 'style' => 'width:'. $width.'px;'));
$chart_div_png = $this->Html->tag('div', '', array('class' => 'chart_png', 'id' => $div_id.'_png'));
$previous = $this->Html->tag('span', $previous, array('class' => 'chart_nav_prev'));
$current = $this->Html->tag('span', $current, array('class' => 'chart_nav_current'));
$next = $this->Html->tag('span', $next, array('class' => 'chart_nav_next'));
$toggle_links = $this->Html->tag('span', $png_link.$svg_link, array('class' => 'chart_nav_toggle'));

$chart_nav = $this->Html->tag('div', $previous. $current. $next. $toggle_links, array('class' => 'chart_nav button_holder paging_link', 'style' => 'padding: 15px; width:'. $width.'px;'));
$chart_js = $this->GoogleChart->display();

echo $this->Html->tag('div', 
	$chart_title. $chart_div_svg. $chart_div_png. $chart_nav. $chart_js,
	array('class' => 'chart_wrapper'));
?>

<script type="text/javascript">
$(document).ready(function()
{
	// if we are in a dashboard tab
	var parent = $('div.chart_nav').parent('div.ui-tabs-panel');
	if(parent)
	{
		parent.find('.paging_link a').on('click', function(event){
			event.preventDefault();
			var link = $(this);
			$.get( link.attr('href'), function( data ) {
				parent.html( data );
			});
		});
	}
	
	// hide the png version by default
	$('.chart_wrapper .svg_link').hide();
	$('.chart_wrapper .chart_png').hide();
	
	// watch for clicking on the toggle links
	
	// show the png version
	$('.chart_wrapper .png_link').on('click', function(event){
		event.preventDefault();
		$(this).hide();
		$('.chart_wrapper .chart_svg').hide();
		$('.chart_wrapper .svg_link').show();
		$('.chart_wrapper .chart_png').show();
		return false;
	});
	// show the svg version
	$('.chart_wrapper .svg_link').on('click', function(event){
		event.preventDefault();
		$(this).hide();
		$('.chart_wrapper .chart_png').hide();
		$('.chart_wrapper .png_link').show();
		$('.chart_wrapper .chart_svg').show();
		return false;
	});
});
</script>