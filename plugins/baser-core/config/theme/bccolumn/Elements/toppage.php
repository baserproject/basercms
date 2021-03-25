<?php
/**
 * toppage
 */
?>


<?php $this->BcBaser->flash() ?>

<div id="TopPage">
    <div id="Works" class="body-wrap">
        <h2><span class="main-title1">WORKS</span><br><span class="sub-title"><?php echo __('実績') ?></span></h2>
        <?php $this->BcBaser->blogPosts('works', 4) ?>
    
    </div>
    
    <div id="Feed" class="body-wrap clearfix">
        <div id="News" class="left">
            <h2><span class="main-title2">NEWS</span></h2>
            <?php $this->BcBaser->blogPosts('news', 4) ?>
            <p class="btn-more"><?php $this->BcBaser->link('MORE', '/news/index', array('class' => 'btn btn-small')) ?></p>
        </div>
        <div id="Blog" class="right">
            <h2><span class="main-title2">BLOG</span></h2>
            <?php $this->BcBaser->blogPosts('topics', 4) ?>
    
            <p class="btn-more"><?php $this->BcBaser->link('MORE', '/topics/index', array('class' => 'btn btn-small')) ?></p>
        </div>
    </div>
    
    <div id="About" class="wide-wrap">
        <div class="body-wrap">
            <?php $this->BcBaser->content() ?>
        </div>
    </div>
</div>

