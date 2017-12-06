<?php
/**
 * Case Folding Properties.
 *
 * Provides case mapping of Unicode characters for code points U+0080 through U+00FF
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
$config['0080_00ff'][] = ['upper' => 181, 'status' => 'C', 'lower' => [956]];
$config['0080_00ff'][] = ['upper' => 924, 'status' => 'C', 'lower' => [181]];
$config['0080_00ff'][] = ['upper' => 192, 'status' => 'C', 'lower' => [224]]; /* LATIN CAPITAL LETTER A WITH GRAVE */
$config['0080_00ff'][] = ['upper' => 193, 'status' => 'C', 'lower' => [225]]; /* LATIN CAPITAL LETTER A WITH ACUTE */
$config['0080_00ff'][] = ['upper' => 194, 'status' => 'C', 'lower' => [226]]; /* LATIN CAPITAL LETTER A WITH CIRCUMFLEX */
$config['0080_00ff'][] = ['upper' => 195, 'status' => 'C', 'lower' => [227]]; /* LATIN CAPITAL LETTER A WITH TILDE */
$config['0080_00ff'][] = ['upper' => 196, 'status' => 'C', 'lower' => [228]]; /* LATIN CAPITAL LETTER A WITH DIAERESIS */
$config['0080_00ff'][] = ['upper' => 197, 'status' => 'C', 'lower' => [229]]; /* LATIN CAPITAL LETTER A WITH RING ABOVE */
$config['0080_00ff'][] = ['upper' => 198, 'status' => 'C', 'lower' => [230]]; /* LATIN CAPITAL LETTER AE */
$config['0080_00ff'][] = ['upper' => 199, 'status' => 'C', 'lower' => [231]]; /* LATIN CAPITAL LETTER C WITH CEDILLA */
$config['0080_00ff'][] = ['upper' => 200, 'status' => 'C', 'lower' => [232]]; /* LATIN CAPITAL LETTER E WITH GRAVE */
$config['0080_00ff'][] = ['upper' => 201, 'status' => 'C', 'lower' => [233]]; /* LATIN CAPITAL LETTER E WITH ACUTE */
$config['0080_00ff'][] = ['upper' => 202, 'status' => 'C', 'lower' => [234]]; /* LATIN CAPITAL LETTER E WITH CIRCUMFLEX */
$config['0080_00ff'][] = ['upper' => 203, 'status' => 'C', 'lower' => [235]]; /* LATIN CAPITAL LETTER E WITH DIAERESIS */
$config['0080_00ff'][] = ['upper' => 204, 'status' => 'C', 'lower' => [236]]; /* LATIN CAPITAL LETTER I WITH GRAVE */
$config['0080_00ff'][] = ['upper' => 205, 'status' => 'C', 'lower' => [237]]; /* LATIN CAPITAL LETTER I WITH ACUTE */
$config['0080_00ff'][] = ['upper' => 206, 'status' => 'C', 'lower' => [238]]; /* LATIN CAPITAL LETTER I WITH CIRCUMFLEX */
$config['0080_00ff'][] = ['upper' => 207, 'status' => 'C', 'lower' => [239]]; /* LATIN CAPITAL LETTER I WITH DIAERESIS */
$config['0080_00ff'][] = ['upper' => 208, 'status' => 'C', 'lower' => [240]]; /* LATIN CAPITAL LETTER ETH */
$config['0080_00ff'][] = ['upper' => 209, 'status' => 'C', 'lower' => [241]]; /* LATIN CAPITAL LETTER N WITH TILDE */
$config['0080_00ff'][] = ['upper' => 210, 'status' => 'C', 'lower' => [242]]; /* LATIN CAPITAL LETTER O WITH GRAVE */
$config['0080_00ff'][] = ['upper' => 211, 'status' => 'C', 'lower' => [243]]; /* LATIN CAPITAL LETTER O WITH ACUTE */
$config['0080_00ff'][] = ['upper' => 212, 'status' => 'C', 'lower' => [244]]; /* LATIN CAPITAL LETTER O WITH CIRCUMFLEX */
$config['0080_00ff'][] = ['upper' => 213, 'status' => 'C', 'lower' => [245]]; /* LATIN CAPITAL LETTER O WITH TILDE */
$config['0080_00ff'][] = ['upper' => 214, 'status' => 'C', 'lower' => [246]]; /* LATIN CAPITAL LETTER O WITH DIAERESIS */
$config['0080_00ff'][] = ['upper' => 216, 'status' => 'C', 'lower' => [248]]; /* LATIN CAPITAL LETTER O WITH STROKE */
$config['0080_00ff'][] = ['upper' => 217, 'status' => 'C', 'lower' => [249]]; /* LATIN CAPITAL LETTER U WITH GRAVE */
$config['0080_00ff'][] = ['upper' => 218, 'status' => 'C', 'lower' => [250]]; /* LATIN CAPITAL LETTER U WITH ACUTE */
$config['0080_00ff'][] = ['upper' => 219, 'status' => 'C', 'lower' => [251]]; /* LATIN CAPITAL LETTER U WITH CIRCUMFLEX */
$config['0080_00ff'][] = ['upper' => 220, 'status' => 'C', 'lower' => [252]]; /* LATIN CAPITAL LETTER U WITH DIAERESIS */
$config['0080_00ff'][] = ['upper' => 221, 'status' => 'C', 'lower' => [253]]; /* LATIN CAPITAL LETTER Y WITH ACUTE */
$config['0080_00ff'][] = ['upper' => 222, 'status' => 'C', 'lower' => [254]]; /* LATIN CAPITAL LETTER THORN */
$config['0080_00ff'][] = ['upper' => 223, 'status' => 'F', 'lower' => [115, 115]]; /* LATIN SMALL LETTER SHARP S */
