<?php
/**
 * フィード
 */
$this->Feed->saveCachetime();
?>
<cake:nocache>
<?php $this->Feed->cacheHeader() ?>
</cake:nocache>

<?php if (!empty($items)): ?>
<dl class="recentNews">
<?php foreach ($items as $key => $item): ?>
<dt><time datetime="<?php echo date("Y-m-d", strtotime($item['pubDate']['value'])); ?>"><?php echo date("Y.m.d", strtotime($item['pubDate']['value'])); ?></time></dt>
<dd><a href="<?php echo $item['link']['value']; ?>"><?php echo $item['title']['value']; ?></a></dd>
<?php endforeach; ?>
</dl>
<?php else: ?>
<p>記事がありません</p>
<?php endif; ?>