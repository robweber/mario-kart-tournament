
<p>Login to enter your score: </p>
<?php if(count($drivers) > 0): ?>
	<?php foreach($drivers as $aDriver): ?>
		<p><?php echo $this->Html->image($aDriver['Driver']['image'],array('url'=>'/tournaments/login/' . $aDriver['Driver']['id'])) ?></p>
		<h2><?php echo $this->Html->link($aDriver['Driver']['name'],'/tournaments/login/' . $aDriver['Driver']['id']) ?></h2>
	<?php endforeach; ?>
<?php else: ?>
	<h3>There aren't any drivers - add one!</h3>
<?php endif; ?>

<?php echo $this->Html->link('New Driver','/tournaments/add_driver') ?>