<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ダッシュボード
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<div class="float-left">

    <div id="ranking" class="box-01">
        <div class="box-head">
            <h3>最近の動き</h3>
        </div>
        <div class="box-body">
            <?php if($viewDblogs): ?>
            <ul>
                <?php foreach ($viewDblogs as $record): ?>
                    <li><?php echo $time->format('Y.m.d',$record['Dblog']['created']) ?><br /><?php echo $record['Dblog']['name'] ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        <div class="box-foot">
            &nbsp;
        </div>
    </div>

</div>

<div class="float-right">

    <div id="ranking" class="box-01">
        <div class="box-head">
            <h3>BaserCMSニュース</h3>
        </div>
        <div class="box-body">
            <?php echo $javascript->link('/feed/ajax/2') ?>
        </div>
        <div class="box-foot">
            &nbsp;
        </div>
    </div>

</div>