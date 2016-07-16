<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.Model
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * MultiBlogPost
 *
 * @package MultiBlog.Model
 * @property MultiBlogContent $MultiBlogContent
 */
class MultiBlogPost extends AppModel {

/**
 * useDbConfig
 *
 * @var string
 */
    public $useDbConfig = 'plugin';

/**
 * belongsTo
 *
 * @var array
 */
    public $belongsTo = array(
        'MultiBlogContent' => array(
            'className' => 'MultiBlog.MultiBlogContent',
            'foreignKey' => 'blog_content_id'
        )
    );
}