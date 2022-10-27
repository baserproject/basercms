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

/**
 * SearchIndexesFrontForm
 */
class SearchIndexesFrontForm extends Form
{

    /**
     * build Schema
     * @param Schema $schema
     * @return Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('f', 'string')
            ->addField('q', 'string')
            ->addField('s', 'string');
    }

}
