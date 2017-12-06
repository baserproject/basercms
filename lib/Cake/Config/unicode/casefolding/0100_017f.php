<?php
/**
 * Case Folding Properties.
 *
 * Provides case mapping of Unicode characters for code points U+0100 through U+017F
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
$config['0100_017f'][] = ['upper' => 256, 'status' => 'C', 'lower' => [257]]; /* LATIN CAPITAL LETTER A WITH MACRON */
$config['0100_017f'][] = ['upper' => 258, 'status' => 'C', 'lower' => [259]]; /* LATIN CAPITAL LETTER A WITH BREVE */
$config['0100_017f'][] = ['upper' => 260, 'status' => 'C', 'lower' => [261]]; /* LATIN CAPITAL LETTER A WITH OGONEK */
$config['0100_017f'][] = ['upper' => 262, 'status' => 'C', 'lower' => [263]]; /* LATIN CAPITAL LETTER C WITH ACUTE */
$config['0100_017f'][] = ['upper' => 264, 'status' => 'C', 'lower' => [265]]; /* LATIN CAPITAL LETTER C WITH CIRCUMFLEX */
$config['0100_017f'][] = ['upper' => 266, 'status' => 'C', 'lower' => [267]]; /* LATIN CAPITAL LETTER C WITH DOT ABOVE */
$config['0100_017f'][] = ['upper' => 268, 'status' => 'C', 'lower' => [269]]; /* LATIN CAPITAL LETTER C WITH CARON */
$config['0100_017f'][] = ['upper' => 270, 'status' => 'C', 'lower' => [271]]; /* LATIN CAPITAL LETTER D WITH CARON */
$config['0100_017f'][] = ['upper' => 272, 'status' => 'C', 'lower' => [273]]; /* LATIN CAPITAL LETTER D WITH STROKE */
$config['0100_017f'][] = ['upper' => 274, 'status' => 'C', 'lower' => [275]]; /* LATIN CAPITAL LETTER E WITH MACRON */
$config['0100_017f'][] = ['upper' => 276, 'status' => 'C', 'lower' => [277]]; /* LATIN CAPITAL LETTER E WITH BREVE */
$config['0100_017f'][] = ['upper' => 278, 'status' => 'C', 'lower' => [279]]; /* LATIN CAPITAL LETTER E WITH DOT ABOVE */
$config['0100_017f'][] = ['upper' => 280, 'status' => 'C', 'lower' => [281]]; /* LATIN CAPITAL LETTER E WITH OGONEK */
$config['0100_017f'][] = ['upper' => 282, 'status' => 'C', 'lower' => [283]]; /* LATIN CAPITAL LETTER E WITH CARON */
$config['0100_017f'][] = ['upper' => 284, 'status' => 'C', 'lower' => [285]]; /* LATIN CAPITAL LETTER G WITH CIRCUMFLEX */
$config['0100_017f'][] = ['upper' => 286, 'status' => 'C', 'lower' => [287]]; /* LATIN CAPITAL LETTER G WITH BREVE */
$config['0100_017f'][] = ['upper' => 288, 'status' => 'C', 'lower' => [289]]; /* LATIN CAPITAL LETTER G WITH DOT ABOVE */
$config['0100_017f'][] = ['upper' => 290, 'status' => 'C', 'lower' => [291]]; /* LATIN CAPITAL LETTER G WITH CEDILLA */
$config['0100_017f'][] = ['upper' => 292, 'status' => 'C', 'lower' => [293]]; /* LATIN CAPITAL LETTER H WITH CIRCUMFLEX */
$config['0100_017f'][] = ['upper' => 294, 'status' => 'C', 'lower' => [295]]; /* LATIN CAPITAL LETTER H WITH STROKE */
$config['0100_017f'][] = ['upper' => 296, 'status' => 'C', 'lower' => [297]]; /* LATIN CAPITAL LETTER I WITH TILDE */
$config['0100_017f'][] = ['upper' => 298, 'status' => 'C', 'lower' => [299]]; /* LATIN CAPITAL LETTER I WITH MACRON */
$config['0100_017f'][] = ['upper' => 300, 'status' => 'C', 'lower' => [301]]; /* LATIN CAPITAL LETTER I WITH BREVE */
$config['0100_017f'][] = ['upper' => 302, 'status' => 'C', 'lower' => [303]]; /* LATIN CAPITAL LETTER I WITH OGONEK */
$config['0100_017f'][] = ['upper' => 304, 'status' => 'F', 'lower' => [105, 775]]; /* LATIN CAPITAL LETTER I WITH DOT ABOVE */
$config['0100_017f'][] = ['upper' => 304, 'status' => 'T', 'lower' => [105]]; /* LATIN CAPITAL LETTER I WITH DOT ABOVE */
$config['0100_017f'][] = ['upper' => 306, 'status' => 'C', 'lower' => [307]]; /* LATIN CAPITAL LIGATURE IJ */
$config['0100_017f'][] = ['upper' => 308, 'status' => 'C', 'lower' => [309]]; /* LATIN CAPITAL LETTER J WITH CIRCUMFLEX */
$config['0100_017f'][] = ['upper' => 310, 'status' => 'C', 'lower' => [311]]; /* LATIN CAPITAL LETTER K WITH CEDILLA */
$config['0100_017f'][] = ['upper' => 313, 'status' => 'C', 'lower' => [314]]; /* LATIN CAPITAL LETTER L WITH ACUTE */
$config['0100_017f'][] = ['upper' => 315, 'status' => 'C', 'lower' => [316]]; /* LATIN CAPITAL LETTER L WITH CEDILLA */
$config['0100_017f'][] = ['upper' => 317, 'status' => 'C', 'lower' => [318]]; /* LATIN CAPITAL LETTER L WITH CARON */
$config['0100_017f'][] = ['upper' => 319, 'status' => 'C', 'lower' => [320]]; /* LATIN CAPITAL LETTER L WITH MIDDLE DOT */
$config['0100_017f'][] = ['upper' => 321, 'status' => 'C', 'lower' => [322]]; /* LATIN CAPITAL LETTER L WITH STROKE */
$config['0100_017f'][] = ['upper' => 323, 'status' => 'C', 'lower' => [324]]; /* LATIN CAPITAL LETTER N WITH ACUTE */
$config['0100_017f'][] = ['upper' => 325, 'status' => 'C', 'lower' => [326]]; /* LATIN CAPITAL LETTER N WITH CEDILLA */
$config['0100_017f'][] = ['upper' => 327, 'status' => 'C', 'lower' => [328]]; /* LATIN CAPITAL LETTER N WITH CARON */
$config['0100_017f'][] = ['upper' => 329, 'status' => 'F', 'lower' => [700, 110]]; /* LATIN SMALL LETTER N PRECEDED BY APOSTROPHE */
$config['0100_017f'][] = ['upper' => 330, 'status' => 'C', 'lower' => [331]]; /* LATIN CAPITAL LETTER ENG */
$config['0100_017f'][] = ['upper' => 332, 'status' => 'C', 'lower' => [333]]; /* LATIN CAPITAL LETTER O WITH MACRON */
$config['0100_017f'][] = ['upper' => 334, 'status' => 'C', 'lower' => [335]]; /* LATIN CAPITAL LETTER O WITH BREVE */
$config['0100_017f'][] = ['upper' => 336, 'status' => 'C', 'lower' => [337]]; /* LATIN CAPITAL LETTER O WITH DOUBLE ACUTE */
$config['0100_017f'][] = ['upper' => 338, 'status' => 'C', 'lower' => [339]]; /* LATIN CAPITAL LIGATURE OE */
$config['0100_017f'][] = ['upper' => 340, 'status' => 'C', 'lower' => [341]]; /* LATIN CAPITAL LETTER R WITH ACUTE */
$config['0100_017f'][] = ['upper' => 342, 'status' => 'C', 'lower' => [343]]; /* LATIN CAPITAL LETTER R WITH CEDILLA */
$config['0100_017f'][] = ['upper' => 344, 'status' => 'C', 'lower' => [345]]; /* LATIN CAPITAL LETTER R WITH CARON */
$config['0100_017f'][] = ['upper' => 346, 'status' => 'C', 'lower' => [347]]; /* LATIN CAPITAL LETTER S WITH ACUTE */
$config['0100_017f'][] = ['upper' => 348, 'status' => 'C', 'lower' => [349]]; /* LATIN CAPITAL LETTER S WITH CIRCUMFLEX */
$config['0100_017f'][] = ['upper' => 350, 'status' => 'C', 'lower' => [351]]; /* LATIN CAPITAL LETTER S WITH CEDILLA */
$config['0100_017f'][] = ['upper' => 352, 'status' => 'C', 'lower' => [353]]; /* LATIN CAPITAL LETTER S WITH CARON */
$config['0100_017f'][] = ['upper' => 354, 'status' => 'C', 'lower' => [355]]; /* LATIN CAPITAL LETTER T WITH CEDILLA */
$config['0100_017f'][] = ['upper' => 356, 'status' => 'C', 'lower' => [357]]; /* LATIN CAPITAL LETTER T WITH CARON */
$config['0100_017f'][] = ['upper' => 358, 'status' => 'C', 'lower' => [359]]; /* LATIN CAPITAL LETTER T WITH STROKE */
$config['0100_017f'][] = ['upper' => 360, 'status' => 'C', 'lower' => [361]]; /* LATIN CAPITAL LETTER U WITH TILDE */
$config['0100_017f'][] = ['upper' => 362, 'status' => 'C', 'lower' => [363]]; /* LATIN CAPITAL LETTER U WITH MACRON */
$config['0100_017f'][] = ['upper' => 364, 'status' => 'C', 'lower' => [365]]; /* LATIN CAPITAL LETTER U WITH BREVE */
$config['0100_017f'][] = ['upper' => 366, 'status' => 'C', 'lower' => [367]]; /* LATIN CAPITAL LETTER U WITH RING ABOVE */
$config['0100_017f'][] = ['upper' => 368, 'status' => 'C', 'lower' => [369]]; /* LATIN CAPITAL LETTER U WITH DOUBLE ACUTE */
$config['0100_017f'][] = ['upper' => 370, 'status' => 'C', 'lower' => [371]]; /* LATIN CAPITAL LETTER U WITH OGONEK */
$config['0100_017f'][] = ['upper' => 372, 'status' => 'C', 'lower' => [373]]; /* LATIN CAPITAL LETTER W WITH CIRCUMFLEX */
$config['0100_017f'][] = ['upper' => 374, 'status' => 'C', 'lower' => [375]]; /* LATIN CAPITAL LETTER Y WITH CIRCUMFLEX */
$config['0100_017f'][] = ['upper' => 376, 'status' => 'C', 'lower' => [255]]; /* LATIN CAPITAL LETTER Y WITH DIAERESIS */
$config['0100_017f'][] = ['upper' => 377, 'status' => 'C', 'lower' => [378]]; /* LATIN CAPITAL LETTER Z WITH ACUTE */
$config['0100_017f'][] = ['upper' => 379, 'status' => 'C', 'lower' => [380]]; /* LATIN CAPITAL LETTER Z WITH DOT ABOVE */
$config['0100_017f'][] = ['upper' => 381, 'status' => 'C', 'lower' => [382]]; /* LATIN CAPITAL LETTER Z WITH CARON */
$config['0100_017f'][] = ['upper' => 383, 'status' => 'C', 'lower' => [115]]; /* LATIN SMALL LETTER LONG S */
