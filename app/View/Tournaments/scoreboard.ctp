<h2><?php echo $game['Game']['name'] ?> Tournament</h2>
<?php if($settings['tournament_active'] == 'false'): ?>
	<h2>Tournament hasn't started - create your driver!</h2>
<?php else: ?>
	<?php if($settings['game_over'] == 'false'): ?>
	<table>
		<tr>
			<td><h2 align="center"><?php echo $this->Html->image($player1['Driver']['image'])?><br><?php echo $player1['Driver']['name'] ?></h2></td>
			<td valign="bottom"><h2 style="margin:50px">VS</h2></td>
			<td><h2 align="center"><?php if($player2 != null): ?><?php echo $this->Html->image($player2['Driver']['image'])?><br><?php echo $player2['Driver']['name'] ?> <?php else: ?> ? <?php endif; ?></h2></td>
		</tr>
		<tr>
			<td colspan="3"><h2 align="center"><?php echo $settings['active_cup'] ?> Cup</h2></td>
		</tr>
	</table>
	<?php else: ?>
		<h2><?php echo $this->Html->image($drivers[0]['Driver']['image'])?><br><?php echo $drivers[0]['Driver']['name'] ?> is the Winner!</h2>
	<?php endif; ?>
<?php endif; ?>

<table border="1" width="75%">
	<?php foreach($drivers as $driver): ?>
		<tr>
			<td width="25%"><h3><?php echo $this->Html->image($driver['Driver']['image'],array('width'=>'50px','height'=>'50px'))?></h3></td>
			<td><h3><?php echo $driver['Driver']['name']?></h3></td>
			<td><h3><?php echo $driver['Driver']['score']?></h3></td>
		</tr>
	<?php endforeach; ?>
</table>

<script type="text/javascript">
  var timeout = setTimeout("location.reload(true);",10000);
</script>
