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
/**
 * @var string $name
 * @var string $table
 * @var string $schema
 * @noTodo
 * @checked
 * @unitTest
 */
?>
use BaserCore\Database\Schema\BcSchema;

/**
 * <?php echo $name ?>Schema
 */
class <?php echo $name ?>Schema extends BcSchema
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = '<?php echo $table ?>';

    /**
     * Fields
     *
     * @var array
     */
    public $fields = <?php echo $schema ?>;

}
