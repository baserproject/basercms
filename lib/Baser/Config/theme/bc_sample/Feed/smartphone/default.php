<?php
/**
 * フィード表示（スマホ版）
 * 呼出箇所：フィード読込Javascript
 *
 * フィード読込JavascriptよりAjaxとして呼び出される
 */
?>


<?php if (!empty($items)): ?>
	<ul>
		<?php foreach ($items as $key => $item): ?>
			<?php $no = sprintf('%02d', $key + 1) ?>
			<?php if ($key == 0): ?>
				<?php $class = ' class="clearfix first feed' . $no . '"' ?>
			<?php elseif ($key == count($items) - 1): ?>
				<?php $class = ' class="clearfix last feed' . $no . '"' ?>
			<?php else: ?>
				<?php $class = ' class="clearfix feed' . $no . '"' ?>
			<?php endif ?>
			<li<?php echo $class ?>> <p><?php echo date("Y.m.d", strtotime($item['pubDate']['value'])); ?><br>
					<a href="<?php echo $item['link']['value']; ?>"><?php echo $item['title']['value']; ?></a></p></li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p style="text-align:center">－</p>
<?php endif; ?>
