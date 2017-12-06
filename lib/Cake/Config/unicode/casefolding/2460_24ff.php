<?php
/**
 * Case Folding Properties.
 *
 * Provides case mapping of Unicode characters for code points U+2460 through U+24FF
 *
 * @see http://www.unicode.org/Public/UNIDATA/UCD.html
 * @see http://www.unicode.org/Public/UNIDATA/CaseFolding.txt
 * @see http://www.unicode.org/reports/tr21/tr21-5.html
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Config.unicode.casefolding
 * @since         CakePHP(tm) v 1.2.0.5691
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * The upper field is the decimal value of the upper case character
 *
 * The lower filed is an array of the decimal values that form the lower case version of a character.
 *
 *	The status field is:
 * C: common case folding, common mappings shared by both simple and full mappings.
 * F: full case folding, mappings that cause strings to grow in length. Multiple characters are separated by spaces.
 * S: simple case folding, mappings to single characters where different from F.
 * T: special case for uppercase I and dotted uppercase I
 *   - For non-Turkic languages, this mapping is normally not used.
 *   - For Turkic languages (tr, az), this mapping can be used instead of the normal mapping for these characters.
 *     Note that the Turkic mappings do not maintain canonical equivalence without additional processing.
 *     See the discussions of case mapping in the Unicode Standard for more information.
 */
$config['2460_24ff'][] = ['upper' => 9398, 'status' => 'C', 'lower' => [9424]]; /* CIRCLED LATIN CAPITAL LETTER A */
$config['2460_24ff'][] = ['upper' => 9399, 'status' => 'C', 'lower' => [9425]]; /* CIRCLED LATIN CAPITAL LETTER B */
$config['2460_24ff'][] = ['upper' => 9400, 'status' => 'C', 'lower' => [9426]]; /* CIRCLED LATIN CAPITAL LETTER C */
$config['2460_24ff'][] = ['upper' => 9401, 'status' => 'C', 'lower' => [9427]]; /* CIRCLED LATIN CAPITAL LETTER D */
$config['2460_24ff'][] = ['upper' => 9402, 'status' => 'C', 'lower' => [9428]]; /* CIRCLED LATIN CAPITAL LETTER E */
$config['2460_24ff'][] = ['upper' => 9403, 'status' => 'C', 'lower' => [9429]]; /* CIRCLED LATIN CAPITAL LETTER F */
$config['2460_24ff'][] = ['upper' => 9404, 'status' => 'C', 'lower' => [9430]]; /* CIRCLED LATIN CAPITAL LETTER G */
$config['2460_24ff'][] = ['upper' => 9405, 'status' => 'C', 'lower' => [9431]]; /* CIRCLED LATIN CAPITAL LETTER H */
$config['2460_24ff'][] = ['upper' => 9406, 'status' => 'C', 'lower' => [9432]]; /* CIRCLED LATIN CAPITAL LETTER I */
$config['2460_24ff'][] = ['upper' => 9407, 'status' => 'C', 'lower' => [9433]]; /* CIRCLED LATIN CAPITAL LETTER J */
$config['2460_24ff'][] = ['upper' => 9408, 'status' => 'C', 'lower' => [9434]]; /* CIRCLED LATIN CAPITAL LETTER K */
$config['2460_24ff'][] = ['upper' => 9409, 'status' => 'C', 'lower' => [9435]]; /* CIRCLED LATIN CAPITAL LETTER L */
$config['2460_24ff'][] = ['upper' => 9410, 'status' => 'C', 'lower' => [9436]]; /* CIRCLED LATIN CAPITAL LETTER M */
$config['2460_24ff'][] = ['upper' => 9411, 'status' => 'C', 'lower' => [9437]]; /* CIRCLED LATIN CAPITAL LETTER N */
$config['2460_24ff'][] = ['upper' => 9412, 'status' => 'C', 'lower' => [9438]]; /* CIRCLED LATIN CAPITAL LETTER O */
$config['2460_24ff'][] = ['upper' => 9413, 'status' => 'C', 'lower' => [9439]]; /* CIRCLED LATIN CAPITAL LETTER P */
$config['2460_24ff'][] = ['upper' => 9414, 'status' => 'C', 'lower' => [9440]]; /* CIRCLED LATIN CAPITAL LETTER Q */
$config['2460_24ff'][] = ['upper' => 9415, 'status' => 'C', 'lower' => [9441]]; /* CIRCLED LATIN CAPITAL LETTER R */
$config['2460_24ff'][] = ['upper' => 9416, 'status' => 'C', 'lower' => [9442]]; /* CIRCLED LATIN CAPITAL LETTER S */
$config['2460_24ff'][] = ['upper' => 9417, 'status' => 'C', 'lower' => [9443]]; /* CIRCLED LATIN CAPITAL LETTER T */
$config['2460_24ff'][] = ['upper' => 9418, 'status' => 'C', 'lower' => [9444]]; /* CIRCLED LATIN CAPITAL LETTER U */
$config['2460_24ff'][] = ['upper' => 9419, 'status' => 'C', 'lower' => [9445]]; /* CIRCLED LATIN CAPITAL LETTER V */
$config['2460_24ff'][] = ['upper' => 9420, 'status' => 'C', 'lower' => [9446]]; /* CIRCLED LATIN CAPITAL LETTER W */
$config['2460_24ff'][] = ['upper' => 9421, 'status' => 'C', 'lower' => [9447]]; /* CIRCLED LATIN CAPITAL LETTER X */
$config['2460_24ff'][] = ['upper' => 9422, 'status' => 'C', 'lower' => [9448]]; /* CIRCLED LATIN CAPITAL LETTER Y */
$config['2460_24ff'][] = ['upper' => 9423, 'status' => 'C', 'lower' => [9449]]; /* CIRCLED LATIN CAPITAL LETTER Z */
