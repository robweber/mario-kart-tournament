<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		Mario Kart Tournament - 
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('jumbotron-narrow');
		
		echo $this->Html->script('jquery.1.12.4');
		echo $this->Html->script('bootstrap.min');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div class="container">
		<div class="header clearfix">
			<nav>
				<?php echo $this->element('menu',array('controller'=>$this->params['controller'],'path'=>$this->request->params['action'])); ?>
			</nav>
			<h3 class="text-muted">Welcome to Mario Kart!</h3>
		</div>
		<div align="center">
			<div id="content">
	
				<?php echo $this->Flash->render(); ?>
	
				<?php echo $this->fetch('content'); ?>
			</div>
		</div>
		<footer class="footer">
        	<p>Created By Rob Weber</p>
      </footer>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
