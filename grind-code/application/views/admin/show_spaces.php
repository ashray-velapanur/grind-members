<!DOCTYPE html>
<html>
	<body>
		<?php
		foreach ($space_data as $space) {?>
			<img height='100px' width='150px' src="<?php echo($space['img_src']) ?>">
			<p><?php echo($space['id']) ?></p>
			<p><?php echo($space['description']) ?></p>
		<?
		}
		?>
	</body>
</html>