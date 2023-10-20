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

namespace BcSearchIndex\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * SearchIndexesSearchForm
 */
class SearchIndexesSearchForm extends Form
{

    /**
     * build Schema
     * @param Schema $schema
     * @return Schema
     * @checked
     * @noTodo
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('type', 'string')
            ->addField('site_id', 'string')
            ->addField('folder_id', 'string')
            ->addField('keyword', 'string')
            ->addField('status', 'string')
            ->addField('priority', 'string');
    }

}
