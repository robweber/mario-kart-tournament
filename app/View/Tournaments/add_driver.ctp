<?php echo $this->Form->create('Driver') ?>

<div class="form-group">
	<label for="name">Name</label>
	<?php echo $this->Form->input('name',array('label'=>false,'div'=>false,'class'=>'form-control')) ?>
</div>

<?php 
$avatars  = array('baby_daisy','baby_luigi','baby_mario','baby_peach','bowser','daisy','donkey','koopa','luigi','mario','mario2','peach','waluigi','wario','yoshi');
?>
<?php $mod = 0; ?>

<div class="avatar-selector">
<legend>Pick an Avatar</legend>
<?php foreach($avatars as $avatar): ?>
	<?php if($mod % 2 == 0): ?>
		<div class="row">
	<?php endif; ?>
		<div class="col-lg-6">
			<input type="radio" name="data[Driver][image]" id="<?php echo $avatar ?>" value="avatars/<?php echo $avatar ?>.jpg"/>
			<label class="avatar <?php echo $avatar ?>" for="<?php echo $avatar ?>"></label>
		</div>
	<?php if($mod % 2 == 1): ?>
		</div>
	<?php endif; ?>
	<?php $mod = $mod + 1 ?>
<?php endforeach; ?>
</div>
</div>
<div class="form-group">
	<?php echo $this->Form->submit('Submit',array('class'=>'btn btn-lg btn-success')) ?>
</div>
<?php echo $this->Form->end() ?>
