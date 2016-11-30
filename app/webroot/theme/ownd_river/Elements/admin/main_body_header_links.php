<?php if ($mainBodyHeaderLinks): ?>
	<?php foreach($mainBodyHeaderLinks as $link): ?>
		<?php
		$url = null;
		$confirmMessage = null;
		if (isset($link['url'])) {
			$url = $link['url'];
			unset($link['url']);
		}
		if (isset($link['confirm'])) {
			$confirmMessage = $link['confirm'];
			unset($link['confirm']);
		}
		?>
		<?php $this->BcBaser->link($link['title'], $url, $link, $confirmMessage); ?>
	<?php endforeach; ?>
<?php endif; ?>

		