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

namespace BcCcBurgerEditor\View\Helper;

use BcCustomContent\Model\Entity\CustomLink;
use Cake\View\Helper;

/**
 * Class BcCcBurgerEditorHelper
 */
#[\AllowDynamicProperties]
class BcCcBurgerEditorHelper extends Helper
{

    /**
     * helpers
     *
     * @var array
     */

    protected array $helpers = [
        'BaserCore.BcAdminForm'
    ];

    /**
     * control
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     */
    public function control($field, array $options): string
    {
        if(!empty($options['preview'])) return '';
        return $this->BcAdminForm->editor($field->name, [
            'type' => 'editor',
            'editor' => "BcBurgerEditor.BurgerEditor",
            'editorUseDraft' => $field->custom_field->meta['BcCcBurgerEditor']['editor_use_draft'],
            'editorDraftField' => $field->name . '_draft',
            'editorWidth' => 'auto',
            'editorHeight' => '480px',
            'editorEnterBr' => 0,
        ]);
    }

    public function get($fieldValue, CustomLink $link, array $options = []): string
    {
        return $fieldValue;
    }

}
