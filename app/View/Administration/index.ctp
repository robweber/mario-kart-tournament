
<?php if($settings['tournament_active'] == 'false'): ?>
	<?php echo $this->Form->create('ActiveGame') ?>
	<?php echo $this->Form->input('ActiveGame',array('type'=>'select','options'=>$games,'selected'=>$settings['active_game'])); ?>
	<?php echo $this->Form->input('TotalRounds',array('value'=>$settings['total_rounds'])) ?>
	<?php echo $this->Form->end('Update'); ?>

	<?php echo $this->Form->create('StartGame',array('url'=>'/admin/start')) ?>
	<?php echo $this->Form->end('Start Tournament') ?>
<?php else: ?>
	<h3>Active Game: <?php echo $games[$settings['active_game']] ?></h3>
	<h3>Total Rounds: <?php echo $settings['total_rounds'] ?></h3>
	<?php echo $this->Form->create('StartGame',array('url'=>'/admin/stop')) ?>
	<?php echo $this->Form->end('Stop Tournament') ?>
<?php endif; ?>

<?php echo $this->Html->link('Users Admin','/admin/users') ?>

