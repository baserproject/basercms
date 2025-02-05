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

 namespace CuCcBurgerEditor\View\Helper;

use BaserCore\Utility\BcContainerTrait;
use Cake\View\Helper;

/**
 * Class CuCcBurgerEditorHelper
 */
#[\AllowDynamicProperties]
class CuCcBurgerEditorHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

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
    public function control($field): string
    {

        return $this->BcAdminForm->editor($field->name, [
            'type' => 'editor',
            'editor' => "BurgerEditor.BurgerEditor",
            'editorUseDraft' => $field->custom_field->meta['CuCcBurgerEditor']['editor_use_draft'],
            'editorDraftField' => 'detail_draft',
            'editorWidth' => 'auto',
            'editorHeight' => '480px',
            'editorEnterBr' => 0,
        ]);

    }

}
