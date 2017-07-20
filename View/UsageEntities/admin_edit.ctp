<?php
// File: app/View/UsageEntities/admin_edit.ctp 
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Usage Entity')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('UsageEntity');?>
			<fieldset>
				<legend><?php echo __('Edit %s', __('Usage Entity')); ?></legend>
				<?php
					echo $this->Form->input('id', array(
						'type' => 'hidden'
					));
					echo $this->Form->input('name');
					
					echo $this->Form->input('desc', array(
						'label' => __('Description'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Update %s', __('Usage Entity'))); ?>
	</div>
</div>
