
<div class="jumbotron">
	<div class="row">
		<div class="col-lg-6">
			<p><?php echo $this->Html->image($driver['Driver']['image']) ?></p>
		</div>
		<div class="col-lg-6">
			<h2><?php echo $driver['Driver']['name'] ?></h2>
			<h3>Current Score: <?php echo $driver['Driver']['score'] ?></h3>
		</div>
	</div>
</div>

<?php if($driver['Driver']['active'] == 'true'): ?>
	<?php echo $this->Form->create('Driver',array('url'=>'/tournaments/update_score')) ?>
	<?php echo $this->Form->hidden('current_score',array('value'=>$driver['Driver']['score'])) ?>
	<div class="form-group">
		<label for="enterYourScore">Enter Your Score</label>
		<?php echo $this->Form->input('score',array('label'=>false,'div'=>false,'class'=>'form-control')) ?>
	</div>
	<div class="form-group">
		<?php echo $this->Form->submit('Submit',array('class'=>'btn btn-lg btn-success')) ?>
	</div>
	<?php echo $this->Form->end(); ?>
<?php endif; ?>

<?php 

?>
