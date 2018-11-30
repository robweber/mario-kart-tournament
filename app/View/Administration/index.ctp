
<?php if($settings['tournament_active'] == 'false'): ?>
	<?php echo $this->Form->create('ActiveGame') ?>
	<div class="form-group">
		<label for="activeGame">Active Game</label>
		<?php echo $this->Form->input('ActiveGame',array('label'=>false,'div'=>false,'class'=>'form-control','type'=>'select','options'=>$games,'selected'=>$settings['active_game'])); ?>
	</div>
	
	<div class="form-group">
	    <label for="sendSms">Send SMS</label>
	    <?php echo $this->Form->input('SendSms',array('label'=>false,'div'=>false,'class'=>'form-control','type'=>'select','options'=>array(1=>'Yes',0=>'No'),'selected'=>$settings['send_sms'])); ?>
	</div>
	
	<div class="form-group">
		<?php echo $this->Form->submit('Update',array('class'=>'btn btn-large btn-info')); ?>
	</div>
	<?php echo $this->Form->end() ?>
	
	<?php echo $this->Form->create('StartGame',array('url'=>'/admin/start')) ?>
	<div class="form-group">
		<?php echo $this->Form->submit('Start Tournament',array('class'=>'btn btn-large btn-success')) ?>
	</div>
	<?php echo $this->Form->end(); ?>
<?php else: ?>
<div class="jumbotron">
	<h3>Active Game: <?php echo $games[$settings['active_game']] ?></h3>
	<?php echo $this->Form->create('StartGame',array('url'=>'/admin/stop')) ?>
	<div class="form-group">
		<?php echo $this->Form->submit('Stop Tournament',array("class"=>"btn btn-lg btn-danger")) ?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
<?php endif; ?>

