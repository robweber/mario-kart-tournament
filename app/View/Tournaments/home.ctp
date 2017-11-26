
<?php if($settings['tournament_active'] == 'false'): ?>
	<h3><?php echo $this->Html->link('New Driver','/tournaments/add_driver') ?></h3>
<?php endif; ?>

<p>Login to enter your score: </p>
<?php $mod = 0; ?>
<?php if(count($drivers) > 0): ?>
	<table>
	<?php foreach($drivers as $aDriver): ?>
		<?php if($mod % 2 == 0): ?>
			<tr>
		<?php endif;?>
		<td>
			<p><?php echo $this->Html->image($aDriver['Driver']['image'],array('url'=>'/tournaments/login/' . $aDriver['Driver']['id'])) ?></p>
			<h2 align="center"><?php echo $this->Html->link($aDriver['Driver']['name'],'/tournaments/login/' . $aDriver['Driver']['id']) ?></h2>
		</td>
		<?php if($mod % 2 == 1): ?>
			</tr>
		<?php endif; ?>
		<?php $mod = $mod + 1; ?>
	<?php endforeach; ?>
	</table>
<?php else: ?>
	<h3>There aren't any drivers - add one!</h3>
<?php endif; ?>