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

namespace BcCcCheckbox\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\View\Helper\CustomContentArrayTrait;
use Cake\View\Helper;

/**
 * Class BcCcCheckboxHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
#[\AllowDynamicProperties]
class BcCcCheckboxHelper extends Helper
{

    /**
     * Trait
     */
    use CustomContentArrayTrait;

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form']
    ];

    /**
     * control
     *
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $label = '';
        if(!empty($link->custom_field->meta['BcCcCheckbox']['label'])) {
            $label = $link->custom_field->meta['BcCcCheckbox']['label'];
        }
        $options = array_merge([
            'type' => 'checkbox',
            'label' => $label,
        ], $options);
        return $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return string
     */
    public function preview(CustomLink $link)
    {
        $options = [
            'v-model' => 'checkboxDefaultValue',
            'label' => '{{checkboxLabel}}',
        ];
        return $this->control($link, $options);
    }

    /**
     * Search Control
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        return $this->control($link, $options);
    }

    /**
     * Get
     *
     * @param mixed $fieldValue
     * @param CustomLink $link
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
        if($fieldValue === true) {
            $fieldValue = $link->custom_field->meta['BcCcCheckbox']['label'];
            return h($fieldValue);
        } else {
            return '';
        }
    }

}
