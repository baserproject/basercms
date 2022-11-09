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
class BcAdminFormHelperTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $View = new BcAdminAppView($this->getRequest());
        $View->setRequest($View->getRequest()->withAttribute('formTokenData', [
            'unlockedFields' => [],
        ]));
        $this->BcAdminForm = new BcAdminFormHelper($View);
        $this->BcForm = new BcFormHelper($View);
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
        $name = 'Contents.test';
        $contentsTable = $this->getTableLocator()->get('BaserCore.Contents');
        $content = $contentsTable->find()->where(['id' => '4'])->first();
        $this->BcAdminForm->create($content);
        $this->BcAdminForm->create($content);
        $this->assertEquals($this->BcAdminForm->control($name), $this->BcAdminForm->control($name));

        $options = [
            'label' => false,
            'legend' => false,
            'error' => false,
            'templateVars' => [
                'tag' => 'span',
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
        $this->BcAdminForm->BcUpload->setTable('BaserCore.Contents');
        $controlFile = $this->BcAdminForm->control($name, $optionsFile);
        $this->assertEquals($controlFile, $this->BcAdminForm->control($name, ['type' => 'file']));

        $optionsDateTimePicker = array_replace_recursive($options, [
            'type' => 'dateTimePicker',
            'templateVars' => [
                'class' => 'bca-datetimepicker'
            ],
            'dateInput' => [
                'class' => 'bca-datetimepicker__date-input'
            ],
            'dateDiv' => [
                'tag' => 'span',
                'class' => 'bca-datetimepicker__date'
            ],
            'dateLabel' => [
                'text' => '日付',
                'class' => 'bca-datetimepicker__date-label'
            ],
            'timeInput' => [
                'class' => 'bca-datetimepicker__time-input'
            ],
            'timeDiv' => [
                'tag' => 'span',
                'class' => 'bca-datetimepicker__time'
            ],
            'timeLabel' => [
                'text' => '時間',
                'class' => 'bca-datetimepicker__time-label'
            ],
            'class' => 'bca-hidden__input'
        ]);
        $controlDateTimePicker = $this->BcAdminForm->control($name, $optionsDateTimePicker);
        $this->assertEquals($controlDateTimePicker, $this->BcAdminForm->control($name, ['type' => 'dateTimePicker']));

        $optionsText = array_replace_recursive($options, [
            'type' => 'text',
            'class' => 'bca-textbox__input',
            'templateVars' => [
                'class' => 'bca-textbox'
            ],
            'labelOptions' => [
                'class' => 'bca-textbox__label'
            ]
        ]);
        $controlText = $this->BcAdminForm->control($name, $optionsText);
        $this->assertEquals($controlText, $this->BcAdminForm->control($name, ['type' => 'text']));

        $optionsPassword = array_replace_recursive($options, [
            'type' => 'password',
            'class' => 'bca-textbox__input',
            'templateVars' => [
                'class' => 'bca-textbox'
            ],
            'labelOptions' => [
                'class' => 'bca-textbox__label'
            ]
        ]);
        $controlPassword = $this->BcAdminForm->control($name, $optionsPassword);
        $this->assertEquals($controlPassword, $this->BcAdminForm->control($name, ['type' => 'password']));

        $optionsDatePicker = array_replace_recursive($options, [
            'type' => 'datePicker',
            'class' => 'bca-textbox__input',
            'templateVars' => [
                'class' => 'bca-textbox'
            ],
            'labelOptions' => [
                'class' => 'bca-textbox__label'
            ]
        ]);
        $controlDatePicker = $this->BcAdminForm->control($name, $optionsDatePicker);
        $this->assertEquals($controlDatePicker, $this->BcAdminForm->control($name, ['type' => 'datePicker']));

        $optionsTextarea = array_replace_recursive($options, [
            'type' => 'textarea',
            'class' => 'bca-textarea__textarea',
            'templateVars' => [
                'class' => 'bca-textarea'
            ]
        ]);
        $controlTextarea = $this->BcAdminForm->control($name, $optionsTextarea);
        $this->assertEquals($controlTextarea, $this->BcAdminForm->control($name, ['type' => 'textarea']));

        $optionsCheckbox = array_replace_recursive($options, [
            'type' => 'checkbox',
            'class' => 'bca-checkbox__textarea',
            'templateVars' => [
                'class' => 'bca-checkbox',
                'labelClass' => 'bca-checkbox__label'
            ],
            'labelOptions' => [
                'class' => 'bca-checkbox__label'
            ]
        ]);
        $controlCheckbox = $this->BcAdminForm->control($name, $optionsCheckbox);

        $optionsMultiCheckbox = array_replace_recursive($options, [
            'type' => 'multiCheckbox',
            'class' => 'bca-checkbox__textarea',
            'templateVars' => [
                'class' => 'bca-checkbox',
                'groupClass' => 'bca-checkbox-group'
            ],
            'labelOptions' => [
                'class' => 'bca-checkbox__label'
            ]
        ]);
        $controlMultiCheckbox = $this->BcAdminForm->control($name, $optionsMultiCheckbox);
        $this->assertEquals($controlMultiCheckbox, $this->BcAdminForm->control($name, ['type' => 'multiCheckbox']));

        $optionsSelect = array_replace_recursive($options, [
            'type' => 'select',
            'class' => 'bca-select__select',
            'templateVars' => [
                'class' => 'bca-select'
            ]
        ]);
        $controlSelect = $this->BcAdminForm->control($name, $optionsSelect);
        $this->assertEquals($controlSelect, $this->BcAdminForm->control($name, ['type' => 'select']));

        $optionsRadio = array_replace_recursive($options, [
            'type' => 'radio',
            'class' => 'bca-radio__input',
            'templateVars' => [
                'class' => 'bca-radio',
                'groupClass' => 'bca-radio-group'
            ],
            'labelOptions' => [
                'class' => 'bca-radio__label'
            ],
            'separator' => '　'
        ]);
        $controlRadio = $this->BcAdminForm->control($name, $optionsRadio);
        $this->assertEquals($controlRadio, $this->BcAdminForm->control($name, ['type' => 'radio']));
    }

    /**
     * Test postLink
     *
     * @return void
     */
    public function testPostLink(): void
    {
        $result = $this->BcAdminForm->postLink('test');
        $class = 'bca-submit-token';
        $this->assertTextContains('class="' . $class . '"', $result);
    }
}
