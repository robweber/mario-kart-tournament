<table width="75%" border="1">
	<tr>
		<td width="25%"></td>
		<td><h3>Name</h3></td>
		<td><h3>Level</h3></td>
		<td><h3>Score</h3></td>
		<td></td>
	</tr>
<?php foreach($drivers as $driver): ?>
		<tr>
			<td width="25%"><h3><?php echo $this->Html->image($driver['Driver']['image'],array('width'=>'50px','height'=>'50px'))?></h3></td>
			<td><h3><?php echo $driver['Driver']['name']?></h3></td>
			<td><h3><?php echo $driver['Driver']['level']?></h3></td>
			<td><h3><?php echo $driver['Driver']['score']?></h3></td>
			<td><h3><?php echo $this->Html->link('Delete','/admin/delete/' . $driver['Driver']['id'])?></h3></td>
		</tr>
<?php endforeach; ?>
</table>