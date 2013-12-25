<?php if ($recursive == 0): ?><p>《ドキュメントルート》</p><?php endif ?>
<?php if (!empty($datas['pages']) || !empty($datas['pageCategories'])): ?>
	<ul<?php echo ($recursive == 0) ? ' id="TreeList" class="filetree"' : '' ?>>
		<?php foreach ($datas['pages'] as $data): ?>
			<li>
				<span class="file">
					<?php $this->BcBaser->link($data['Page']['title'] . ' (' . $data['Page']['name'] . ')', array('admin' => true, 'controller' => 'pages', 'action' => 'edit', $data['Page']['id'])) ?>
				</span>
			</li>
		<?php endforeach ?>
		<?php foreach ($datas['pageCategories'] as $data): ?>
			<li>
				<span class="folder" style="display:inline">
					<?php echo $data['PageCategory']['title'] ?>
				</span>
				<?php if (!empty($data['children'])): ?>
					<?php echo $this->BcPage->treeList($data['children'], $recursive + 1) ?> 
				<?php endif ?>
			</li>
		<?php endforeach ?>
	</ul>
<?php endif; ?>