
<?php if($settings['tournament_active'] == 'false'): ?>
	<h2><?php echo $this->Html->link('New Driver','/tournaments/add_driver',array('class'=>'label label-success')) ?></h2>
<?php endif; ?>

<div class="jumbotron">
<h3>Login to enter your score: </h3>
<?php $mod = 0; ?>
<?php if(count($drivers) > 0): ?>
	<div class="row">
	<?php foreach($drivers as $aDriver): ?>
		<?php if($mod % 2 == 0): ?>
			<div class="col-lg-4">
		<?php endif;?>
		
		<h4><?php echo $this->Html->image('avatars/' . $aDriver['Driver']['image'] . '.jpg',array('url'=>'/tournaments/login/' . $aDriver['Driver']['id'])) ?></h4>
		<h4><?php echo $this->Html->link($aDriver['Driver']['name'],'/tournaments/login/' . $aDriver['Driver']['id'],array('class'=>'label label-primary')) ?></h4>
		
		<?php if($mod % 2 == 1): ?>
			</div>
		<?php endif; ?>
		<?php $mod = $mod + 1; ?>
	<?php endforeach; ?>
	</div>
<?php else: ?>
	<h3>There aren't any drivers - add one!</h3>
<?php endif; ?>
</div>