
<p><?php echo $this->Html->image($driver['Driver']['image']) ?></p>
<h2><?php echo $driver['Driver']['name'] ?></h2>
	
<h3>Current Score: <?php echo $driver['Driver']['score'] ?></h3>
<p>Your skill level is <?php echo findSkill($driver['Driver']['level']) ?></p>

<?php function findSkill($skill){
	
	if($skill == 0){
		return 'Normal - your score will be multiplied by 1.5';
	}
	else {
		return 'Advanced - Your score will not recieve a multiplier';
	}
	
}
?>
