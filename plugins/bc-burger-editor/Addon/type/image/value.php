<div class="bgt-image"
	data-bge-popup="<?php echo \Cake\Core\Configure::read("Bge.defaultImagePopup") ? '1' : '' ?>"
	data-bge-empty="0"
	data-bge-hr=""
	data-bge="popup:data-bge-popup, empty:data-bge-empty, hr:data-bge-hr"
>
	<a
		class="bgt-image__link"
		<?php if (\Cake\Core\Configure::read("Bge.defaultImagePopup")): ?>
			href="<?php echo \BaserCore\Utility\BcUtil::baseUrl(); ?>files/bgeditor/bg-sample.png"
		<?php endif; ?>
		data-bge="path:href"
	>
		<figure class="bgt-link__box">
			<div class="bgt-box__image-container">
				<img src="<?php echo \BaserCore\Utility\BcUtil::baseUrl(); ?>files/bgeditor/bg-sample.png" data-bge="path:src, srcset:srcset, alt:alt, width:width, height:height, loading:loading, decoding:decoding">
			</div>
			<figcaption class="bgt-box__caption" data-bge="caption"></figcaption>
		</figure>
	</a>
</div>
