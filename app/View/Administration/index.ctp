<?php echo $this->Form->create('ActiveGame') ?>
<?php echo $this->Form->input('ActiveGame',array('type'=>'select','options'=>$games,'selected'=>$settings['active_game'])); ?>
<?php echo $this->Form->submit('Update'); ?>
<ul>
	<li>Users</li>
</ul>

