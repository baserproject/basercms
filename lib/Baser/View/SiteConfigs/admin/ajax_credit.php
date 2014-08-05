<?php
/**
 * [管理画面] クレジット
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
if(!$credits) {
	return;
}
$types = array('designers', 'deveroppers', 'supporters', 'publishers');
?>

<div id="Credit">
	<div id="CreditInner">
		<div id="CreditScroller">
			<div id="CreditScrollerInner">

				<h1>Special Thanks Credit</h1>
				<?php foreach ($types as $type) : ?>
					<div class="section">
						<h2><?php echo Inflector::camelize($type) ?></h2>
						<?php $i = 0 ?>
						<?php foreach ($credits->{$type} as $key => $contributor): ?>
							<?php $i++ ?>
							<?php if ($i % 6 == 1): ?>
								<ul>
								<?php endif ?>
								<li>
									<?php if (!empty($contributor->siteUrl)): ?>
										<?php $this->BcBaser->link($contributor->alphabet, $contributor->siteUrl, array('target' => '_blank')) ?>
									<?php elseif (!empty($contributor->affiliationUrl)): ?>
										<?php $this->BcBaser->link($contributor->alphabet, $contributor->affiliationUrl, array('target' => '_blank')) ?>
									<?php else: ?>
										<?php echo $contributor->alphabet ?>
									<?php endif ?> 
									<?php if (!empty($contributor->twitter)): ?>
										(<?php $this->BcBaser->link($contributor->twitter, 'http://twitter.com/' . $contributor->twitter, array('target' => '_blank')) ?>) 
									<?php endif ?>
								</li>
								<?php if ($i % 6 == 0 || $this->BcArray->last($credits->{$type}, $key)): ?>
								</ul>
							<?php endif ?>
						<?php endforeach ?>
					</div>
				<?php endforeach ?>

				<h1 style="margin-top:400px;">baserCMS Users Community</h1>

			</div>
		</div>
	</div>
</div>