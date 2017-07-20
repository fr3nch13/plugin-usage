<?php ?>
<li>
	<?php echo $this->Html->link(__('Usage Stats'), '#', array('class' => 'top')); ?>
	<ul>
		<li><?php echo $this->Html->link(__('All'), array('controller' => 'usage_entities', 'action' => 'index', 'admin' => false, 'plugin' => 'usage')); ?></li> 
		<li><?php echo $this->Html->link(__('Snapshots'), array('controller' => 'usage_entities', 'action' => 'group', 'snapshot', 'admin' => false, 'plugin' => 'usage')); ?></li> 
		<li><?php echo $this->Html->link(__('Not Snapshots'), array('controller' => 'usage_entities', 'action' => 'index', 1, 'admin' => false, 'plugin' => 'usage')); ?></li> 
	</ul>
</li>

