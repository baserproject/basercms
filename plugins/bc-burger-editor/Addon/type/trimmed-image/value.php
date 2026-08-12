<div class="bgt-trimmed-image"
	data-bge-popup="<?php echo \Cake\Core\Configure::read("Bge.defaultImagePopup") ? '1' : '0' ?>"
	data-bge-empty="0"
	data-bge="popup:data-bge-popup, empty:data-bge-empty"
>
	<a
		class="bgt-image__link"
		<?php if (\Cake\Core\Configure::read("Bge.defaultImagePopup")): ?>
			href="<?php echo \BaserCore\Utility\BcUtil::baseUrl(); ?>files/bgeditor/bg-sample.png"
		<?php endif; ?>
		data-bge="path:href"
	>
		<figure class="bgt-link__box">
			<div class="bgt-box__image" data-bge="path:style(background-image), alt" style="background-image: url(<?php echo \BaserCore\Utility\BcUtil::baseUrl(); ?>files/bgeditor/bg-sample.png);"></div>
			<figcaption class="bgt-box__caption" data-bge="caption"></figcaption>
		</figure>
	</a>
</div>
