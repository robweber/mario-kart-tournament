
<?php if($settings['tournament_active'] == 'false'): ?>
	<?php echo $this->Form->create('ActiveGame') ?>
	<div class="form-group">
		<label for="activeGame">Active Game</label>
		<?php echo $this->Form->input('ActiveGame',array('label'=>false,'div'=>false,'class'=>'form-control','type'=>'select','options'=>$games,'selected'=>$settings['active_game'])); ?>
	</div>
	<div class="form-group">
		<label for="activeGame">Total Rounds</label>
		<?php echo $this->Form->input('TotalRounds',array('label'=>false,'div'=>false,'class'=>'form-control','value'=>$settings['total_rounds'])) ?>
	</div>
	<div class="form-group">
		<label for="activeGame">Multiplier</label>
		<?php echo $this->Form->input('Multiplier',array('label'=>false,'div'=>false,'class'=>'form-control','value'=>$settings['score_multiplier'])) ?>
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
	<h3>Total Rounds: <?php echo $settings['total_rounds'] ?></h3>
	<h3>Score Multiplier: <?php echo $settings['score_multiplier'] ?></h3>
	<?php echo $this->Form->create('StartGame',array('url'=>'/admin/stop')) ?>
	<?php echo $this->Form->submit('Stop Tournament',array("class"=>"btn btn-lg btn-warning")) ?>
</div>
<?php endif; ?>

