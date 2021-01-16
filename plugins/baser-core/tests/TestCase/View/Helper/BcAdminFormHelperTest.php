<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\View\Helper;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminFormHelper;
use BaserCore\View\Helper\BcFormHelper;

/**
 * Class BcAdminFormHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property BcAdminFormHelper $BcAdminForm
 * @property BcFormHelper $BcForm
 */
class BcAdminFormHelperTest extends BcTestCase {

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminForm = new BcAdminFormHelper(new BcAdminAppView($this->getRequest()));
        $this->BcForm = new BcFormHelper(new BcAdminAppView($this->getRequest()));
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminForm);
        parent::tearDown();
    }

    /**
     * Test control
     *
     * preturn void
     */
    public function testControl()
    {
        $name = 'test';

        $this->assertEquals($this->BcForm->control($name), $this->BcAdminForm->control($name));

        $options = [
            'label'  => false,
            'legend' => false,
            'error'  => false,
            'templateVars' => [
                'tag'      => 'span',
                'groupTag' => 'span'
            ]
        ];

        $optionsFile = array_replace_recursive($options, [
            'type' => 'file',
            'link' => [
                'class' => 'bca-file__link'
            ],
            'class' => 'bca-file__input',
            'templateVars' => [
                'class' => 'bca-file'
            ],
            'deleteSpan' => [
                'class' => 'bca-file__delete'
            ],
            'deleteCheckbox' => [
                'class' => 'bca-file__delete-input'
            ],
            'deleteLabel' => [
                'class' => 'bca-file__delete-label'
            ],
            'figure' => [
                'class' => 'bca-file__figure'
            ],
            'img' => [
                'class' => 'bca-file__img'
            ],
            'figcaption' => [
                'class' => 'bca-file__figcaption'
            ]
        ]);
        $controlFile = $this->BcForm->control($name, $optionsFile);
        $this->assertEquals($controlFile, $this->BcAdminForm->control($name, ['type' => 'file']));

        $optionsDateTimePicker = array_replace_recursive($options, [
            'type'  => 'dateTimePicker',
            'templateVars' => [
                'class' => 'bca-datetimepicker'
            ],
            'dateInput' => [
                'class' => 'bca-datetimepicker__date-input'
            ],
            'dateDiv' => [
                'tag'   => 'span',
                'class' => 'bca-datetimepicker__date'
            ],
            'dateLabel' => [
                'text'  => '日付',
                'class' => 'bca-datetimepicker__date-label'
            ],
            'timeInput' => [
                'class' => 'bca-datetimepicker__time-input'
            ],
            'timeDiv' => [
                'tag'   => 'span',
                'class' => 'bca-datetimepicker__time'
            ],
            'timeLabel' => [
                'text'  => '時間',
                'class' => 'bca-datetimepicker__time-label'
            ],
            'class' => 'bca-hidden__input'
        ]);
        $controlDateTimePicker = $this->BcForm->control($name, $optionsDateTimePicker);
        $this->assertEquals($controlDateTimePicker, $this->BcAdminForm->control($name, ['type' => 'dateTimePicker']));

        $optionsText = array_replace_recursive($options, [
            'type'  => 'text',
            'class' => 'bca-textbox__input',
            'templateVars' => [
                'class' => 'bca-textbox'
            ],
            'labelOptions' => [
                'class' => 'bca-textbox__label'
            ]
        ]);
        $controlText = $this->BcForm->control($name, $optionsText);
        $this->assertEquals($controlText, $this->BcAdminForm->control($name, ['type' => 'text']));

        $optionsPassword = array_replace_recursive($options, [
            'type'  => 'password',
            'class' => 'bca-textbox__input',
            'templateVars' => [
                'class' => 'bca-textbox'
            ],
            'labelOptions' => [
                'class' => 'bca-textbox__label'
            ]
        ]);
        $controlPassword = $this->BcForm->control($name, $optionsPassword);
        $this->assertEquals($controlPassword, $this->BcAdminForm->control($name, ['type' => 'password']));

        $optionsDatePicker = array_replace_recursive($options, [
            'type'  => 'datePicker',
            'class' => 'bca-textbox__input',
            'templateVars' => [
                'class' => 'bca-textbox'
            ],
            'labelOptions' => [
                'class' => 'bca-textbox__label'
            ]
        ]);
        $controlDatePicker = $this->BcForm->control($name, $optionsDatePicker);
        $this->assertEquals($controlDatePicker, $this->BcAdminForm->control($name, ['type' => 'datePicker']));

        $optionsTextarea = array_replace_recursive($options, [
            'type'  => 'textarea',
            'class' => 'bca-textarea__textarea',
            'templateVars' => [
                'class' => 'bca-textarea'
            ]
        ]);
        $controlTextarea = $this->BcForm->control($name, $optionsTextarea);
        $this->assertEquals($controlTextarea, $this->BcAdminForm->control($name, ['type' => 'textarea']));

        $optionsCheckbox = array_replace_recursive($options, [
            'type'  => 'checkbox',
            'class' => 'bca-checkbox__textarea',
            'templateVars' => [
                'class'      => 'bca-checkbox',
                'labelClass' => 'bca-checkbox__label'
            ],
            'labelOptions' => [
                'class' => 'bca-checkbox__label'
            ]
        ]);
        $controlCheckbox = $this->BcForm->control($name, $optionsCheckbox);

        $optionsMultiCheckbox = array_replace_recursive($options, [
            'type'  => 'multiCheckbox',
            'class' => 'bca-checkbox__textarea',
            'templateVars' => [
                'class'      => 'bca-checkbox',
                'groupClass' => 'bca-checkbox-group'
            ],
            'labelOptions' => [
                'class' => 'bca-checkbox__label'
            ]
        ]);
        $controlMultiCheckbox = $this->BcForm->control($name, $optionsMultiCheckbox);
        $this->assertEquals($controlMultiCheckbox, $this->BcAdminForm->control($name, ['type' => 'multiCheckbox']));

        $optionsSelect = array_replace_recursive($options, [
            'type'  => 'select',
            'class' => 'bca-select__select',
            'templateVars' => [
                'class' => 'bca-select'
            ]
        ]);
        $controlSelect = $this->BcForm->control($name, $optionsSelect);
        $this->assertEquals($controlSelect, $this->BcAdminForm->control($name, ['type' => 'select']));

        $optionsRadio = array_replace_recursive($options, [
            'type'  => 'radio',
            'class' => 'bca-radio__input',
            'templateVars' => [
                'class'      => 'bca-radio',
                'groupClass' => 'bca-radio-group'
            ],
            'labelOptions' => [
                'class' => 'bca-radio__label'
            ],
            'separator' => '　'
        ]);
        $controlRadio = $this->BcForm->control($name, $optionsRadio);
        $this->assertEquals($controlRadio, $this->BcAdminForm->control($name, ['type' => 'radio']));
    }
}
