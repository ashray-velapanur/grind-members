<!DOCTYPE html>
<html>
	<body>
		<?php
		foreach ($resource_data as $resource) {?>
			<img height='100px' width='150px' src="<?php echo($resource['img_src']) ?>">
			<p><?php echo($resource['id']) ?></p>
			<p><?php echo($resource['description']) ?></p>
			<p><?php echo($resource['capacity']) ?></p>
			<p><?php echo($resource['rate']) ?></p>
		<?
		}
		?>
	</body>
</html>