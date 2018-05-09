<?php 
	echo $this->Html->css('jquery.bracket.min');
	echo $this->Html->script('jquery.bracket.min');
?>
<script type="text/javascript">
$(function() {
	
	<?php if($settings['tournament_active'] == 'true'): ?> 
	//load tournmanet bracket if active
	$.get('/mario_kart/tournaments/bracket',function(data,status){
		$('#tournament_bracket').bracket({
      		init: JSON.parse(data), /* data to initialize the bracket with */ 
      		teamWidth: 70,
      		scoreWidth: 30,
      		matchMargin: 50,
      		roundMargin: 90,
      	});
	});
	<?php endif; ?>
  })
</script>
<div class="jumbotron">
	<h2><?php echo $game['Game']['name'] ?> Tournament</h2>
	<?php if($settings['tournament_active'] == 'false'): ?>
		<h2><a href="#" class="label label-warning">Tournament hasn't started - create your driver!</a></h2>
	<?php else: ?>
		<?php if($settings['game_over'] == 'false'): ?>
		<table class="table">
			<tr>
				<td><h2 align="center"><?php echo $this->Html->image($player1['Driver']['image'])?><br><?php echo $player1['Driver']['name'] ?></h2></td>
				<td valign="bottom"><h2 style="margin:50px">VS</h2></td>
				<td><h2 align="center"><?php if($player2 != null): ?><?php echo $this->Html->image($player2['Driver']['image'])?><br><?php echo $player2['Driver']['name'] ?> <?php else: ?> ? <?php endif; ?></h2></td>
			</tr>
			<tr>
				<td colspan="3"><h2 align="center"><a href="#" class="label label-info"><?php echo $settings['active_cup'] ?> Cup</a></h2></td>
			</tr>
		</table>
		<?php else: ?>
			<?php if($match[0]['Match']['score'] > $match[1]['Match']['score']): ?>
				<?php $winner = $match[0]['Driver']; ?>
			<?php else: ?>
				<?php $winner = $match[1]['Driver']; ?>
			<?php endif; ?>
			<h2><?php echo $this->Html->image($winner['image'])?><br><br><?php echo $winner['name'] ?> is the Winner!</h2>
		<?php endif; ?>
	<?php endif; ?>
</div>
<div id="tournament_bracket" align="left">
	
</div>

<script type="text/javascript">
 var timeout = setTimeout("location.reload(true);",10000);
</script>

