<?php
/**
 * フッター
 */
?>


<footer id="Footer" >
	<div id="Map">
	    <h2><span class="main-title1">ACCESS MAP</span></h2>
	    <div id="AccessMap">
	        <?php $this->BcBaser->googleMaps(array("width" => "100%","height" => 420)) ?>
	    </div>
	</div>

    <div id="Contact" class="main-color">
        <div class="body-wrap">
            <p><span class="title">CONTACT</span><span class="tel">092-123-456</span></p>
        </div>
    </div>
    <div class="body-wrap">
		<div id="FooterLogo">
			<?php $this->BcBaser->logo() ?>
		</div>
		<nav id="FooterLink">
			<?php $this->BcBaser->globalMenu() ?>
		</nav>
		<div id="Copyright">
			<address>Copyright &copy; <a href="http://komomo.biz/" target="_blank">小桃</a><a href="http://clipdesign.jp/" target="_blank">クリップ</a> Allrights Reserved.</address>
			<p id="PowerdBy" class="ta-center">
				<a href="https://basercms.net/" target="_blank"><?php $this->BcBaser->img('baser.power.gif', array('alt' => 'baserCMS : Based Website Development Project')); ?></a>
				&nbsp;
				<a href="http://cakephp.org/" target="_blank"><?php $this->BcBaser->img('cake.power.gif', array('alt' => 'CakePHP(tm) : Rapid Development Framework')); ?></a>
				&nbsp;
				<a href="http://clipdesign.jp/" target="_blank"><?php $this->BcBaser->img('footer/clip.gif', array('alt' => 'clipdesign')); ?></a>
				&nbsp;
				<a href="http://komomo.biz/" target="_blank"><?php $this->BcBaser->img('footer/komomo.gif', array('alt' => '小桃デザイン')); ?></a>&nbsp;
			</p>
		</div>

	</div>
</footer>
