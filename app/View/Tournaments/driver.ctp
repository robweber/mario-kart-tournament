
<p><?php echo $this->Html->image($driver['Driver']['image']) ?></p>
<h2><?php echo $driver['Driver']['name'] ?></h2>
	
<h3>Current Score: <?php echo $driver['Driver']['score'] ?></h3>
<p>Your skill level is <?php echo findSkill($driver['Driver']['level'],$settings['score_multiplier']) ?></p>

<?php if($driver['Driver']['active'] == 'true'): ?>
	<?php echo $this->Form->create('Driver',array('url'=>'/tournaments/update_score')) ?>
	<?php echo $this->Form->hidden('current_score',array('value'=>$driver['Driver']['score'])) ?>
	<?php echo $this->Form->input('score',array('label'=>'Enter Your Score')) ?>
	<?php echo $this->Form->end('Submit') ?>
<?php endif; ?>

<?php 

function findSkill($skill,$mult){
	
	if($skill == 0){
		return 'Normal - your score will be multiplied by ' . $mult;
	}
	else {
		return 'Advanced - Your score will not recieve a multiplier';
	}
	
}

?>
