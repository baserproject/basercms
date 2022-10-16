<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ContentsSearchForm
 */
class ContentsSearchForm extends Form
{

    /**
     * build Schema
     * @param Schema $schema
     * @return Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('folder_id', 'string')
            ->addField('name', 'string')
            ->addField('type', 'string')
            ->addField('self_status', 'string')
            ->addField('author_id', 'string');
    }

}
