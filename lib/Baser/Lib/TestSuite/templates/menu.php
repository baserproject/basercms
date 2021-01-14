<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib.TestSuite.templates
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */
?>
<div class="test-menu">
	<ul>
		<li>
			<span style="font-size: 18px">App</span>
			<ul>
				<li><a href='<?php echo $cases; ?>'>Tests</a></li>
			</ul>
		</li>
		<?php if (!empty($plugins)): ?>
			<li style="padding-top: 10px">
				<span style="font-size: 18px">Plugins</span>
				<?php foreach($plugins as $plugin): ?>
					<ul>
						<li style="padding-top: 10px">
							<span style="font-size: 18px"><?php echo $plugin; ?></span>
							<ul>
								<li><?php printf('<a href="%s&amp;plugin=%s">Tests</a>', $cases, $plugin); ?></li>
							</ul>
						</li>
					</ul>
				<?php endforeach; ?>
			</li>
		<?php endif; ?>
		<li style="padding-top: 10px">
			<span style="font-size: 18px">Baser</span>
			<ul>
				<li><a href='<?php echo $cases; ?>&amp;baser=true'>Tests</a></li>
			</ul>
		</li>
		<li style="padding-top: 10px">
			<span style="font-size: 18px">Core</span>
			<ul>
				<li><a href='<?php echo $cases; ?>&amp;core=true'>Tests</a></li>
			</ul>
		</li>
	</ul>
</div>
<div class="test-results">
