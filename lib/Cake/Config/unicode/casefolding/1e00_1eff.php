<?php
/**
 * Case Folding Properties.
 *
 * Provides case mapping of Unicode characters for code points U+1E00 through U+1EFF
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
$config['1e00_1eff'][] = ['upper' => 7680, 'status' => 'C', 'lower' => [7681]]; /* LATIN CAPITAL LETTER A WITH RING BELOW */
$config['1e00_1eff'][] = ['upper' => 7682, 'status' => 'C', 'lower' => [7683]]; /* LATIN CAPITAL LETTER B WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7684, 'status' => 'C', 'lower' => [7685]]; /* LATIN CAPITAL LETTER B WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7686, 'status' => 'C', 'lower' => [7687]]; /* LATIN CAPITAL LETTER B WITH LINE BELOW */
$config['1e00_1eff'][] = ['upper' => 7688, 'status' => 'C', 'lower' => [7689]]; /* LATIN CAPITAL LETTER C WITH CEDILLA AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7690, 'status' => 'C', 'lower' => [7691]]; /* LATIN CAPITAL LETTER D WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7692, 'status' => 'C', 'lower' => [7693]]; /* LATIN CAPITAL LETTER D WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7694, 'status' => 'C', 'lower' => [7695]]; /* LATIN CAPITAL LETTER D WITH LINE BELOW */
$config['1e00_1eff'][] = ['upper' => 7696, 'status' => 'C', 'lower' => [7697]]; /* LATIN CAPITAL LETTER D WITH CEDILLA */
$config['1e00_1eff'][] = ['upper' => 7698, 'status' => 'C', 'lower' => [7699]]; /* LATIN CAPITAL LETTER D WITH CIRCUMFLEX BELOW */
$config['1e00_1eff'][] = ['upper' => 7700, 'status' => 'C', 'lower' => [7701]]; /* LATIN CAPITAL LETTER E WITH MACRON AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7702, 'status' => 'C', 'lower' => [7703]]; /* LATIN CAPITAL LETTER E WITH MACRON AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7704, 'status' => 'C', 'lower' => [7705]]; /* LATIN CAPITAL LETTER E WITH CIRCUMFLEX BELOW */
$config['1e00_1eff'][] = ['upper' => 7706, 'status' => 'C', 'lower' => [7707]]; /* LATIN CAPITAL LETTER E WITH TILDE BELOW */
$config['1e00_1eff'][] = ['upper' => 7708, 'status' => 'C', 'lower' => [7709]]; /* LATIN CAPITAL LETTER E WITH CEDILLA AND BREVE */
$config['1e00_1eff'][] = ['upper' => 7710, 'status' => 'C', 'lower' => [7711]]; /* LATIN CAPITAL LETTER F WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7712, 'status' => 'C', 'lower' => [7713]]; /* LATIN CAPITAL LETTER G WITH MACRON */
$config['1e00_1eff'][] = ['upper' => 7714, 'status' => 'C', 'lower' => [7715]]; /* LATIN CAPITAL LETTER H WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7716, 'status' => 'C', 'lower' => [7717]]; /* LATIN CAPITAL LETTER H WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7718, 'status' => 'C', 'lower' => [7719]]; /* LATIN CAPITAL LETTER H WITH DIAERESIS */
$config['1e00_1eff'][] = ['upper' => 7720, 'status' => 'C', 'lower' => [7721]]; /* LATIN CAPITAL LETTER H WITH CEDILLA */
$config['1e00_1eff'][] = ['upper' => 7722, 'status' => 'C', 'lower' => [7723]]; /* LATIN CAPITAL LETTER H WITH BREVE BELOW */
$config['1e00_1eff'][] = ['upper' => 7724, 'status' => 'C', 'lower' => [7725]]; /* LATIN CAPITAL LETTER I WITH TILDE BELOW */
$config['1e00_1eff'][] = ['upper' => 7726, 'status' => 'C', 'lower' => [7727]]; /* LATIN CAPITAL LETTER I WITH DIAERESIS AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7728, 'status' => 'C', 'lower' => [7729]]; /* LATIN CAPITAL LETTER K WITH ACUTE */
$config['1e00_1eff'][] = ['upper' => 7730, 'status' => 'C', 'lower' => [7731]]; /* LATIN CAPITAL LETTER K WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7732, 'status' => 'C', 'lower' => [7733]]; /* LATIN CAPITAL LETTER K WITH LINE BELOW */
$config['1e00_1eff'][] = ['upper' => 7734, 'status' => 'C', 'lower' => [7735]]; /* LATIN CAPITAL LETTER L WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7736, 'status' => 'C', 'lower' => [7737]]; /* LATIN CAPITAL LETTER L WITH DOT BELOW AND MACRON */
$config['1e00_1eff'][] = ['upper' => 7738, 'status' => 'C', 'lower' => [7739]]; /* LATIN CAPITAL LETTER L WITH LINE BELOW */
$config['1e00_1eff'][] = ['upper' => 7740, 'status' => 'C', 'lower' => [7741]]; /* LATIN CAPITAL LETTER L WITH CIRCUMFLEX BELOW */
$config['1e00_1eff'][] = ['upper' => 7742, 'status' => 'C', 'lower' => [7743]]; /* LATIN CAPITAL LETTER M WITH ACUTE */
$config['1e00_1eff'][] = ['upper' => 7744, 'status' => 'C', 'lower' => [7745]]; /* LATIN CAPITAL LETTER M WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7746, 'status' => 'C', 'lower' => [7747]]; /* LATIN CAPITAL LETTER M WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7748, 'status' => 'C', 'lower' => [7749]]; /* LATIN CAPITAL LETTER N WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7750, 'status' => 'C', 'lower' => [7751]]; /* LATIN CAPITAL LETTER N WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7752, 'status' => 'C', 'lower' => [7753]]; /* LATIN CAPITAL LETTER N WITH LINE BELOW */
$config['1e00_1eff'][] = ['upper' => 7754, 'status' => 'C', 'lower' => [7755]]; /* LATIN CAPITAL LETTER N WITH CIRCUMFLEX BELOW */
$config['1e00_1eff'][] = ['upper' => 7756, 'status' => 'C', 'lower' => [7757]]; /* LATIN CAPITAL LETTER O WITH TILDE AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7758, 'status' => 'C', 'lower' => [7759]]; /* LATIN CAPITAL LETTER O WITH TILDE AND DIAERESIS */
$config['1e00_1eff'][] = ['upper' => 7760, 'status' => 'C', 'lower' => [7761]]; /* LATIN CAPITAL LETTER O WITH MACRON AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7762, 'status' => 'C', 'lower' => [7763]]; /* LATIN CAPITAL LETTER O WITH MACRON AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7764, 'status' => 'C', 'lower' => [7765]]; /* LATIN CAPITAL LETTER P WITH ACUTE */
$config['1e00_1eff'][] = ['upper' => 7766, 'status' => 'C', 'lower' => [7767]]; /* LATIN CAPITAL LETTER P WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7768, 'status' => 'C', 'lower' => [7769]]; /* LATIN CAPITAL LETTER R WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7770, 'status' => 'C', 'lower' => [7771]]; /* LATIN CAPITAL LETTER R WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7772, 'status' => 'C', 'lower' => [7773]]; /* LATIN CAPITAL LETTER R WITH DOT BELOW AND MACRON */
$config['1e00_1eff'][] = ['upper' => 7774, 'status' => 'C', 'lower' => [7775]]; /* LATIN CAPITAL LETTER R WITH LINE BELOW */
$config['1e00_1eff'][] = ['upper' => 7776, 'status' => 'C', 'lower' => [7777]]; /* LATIN CAPITAL LETTER S WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7778, 'status' => 'C', 'lower' => [7779]]; /* LATIN CAPITAL LETTER S WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7780, 'status' => 'C', 'lower' => [7781]]; /* LATIN CAPITAL LETTER S WITH ACUTE AND DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7782, 'status' => 'C', 'lower' => [7783]]; /* LATIN CAPITAL LETTER S WITH CARON AND DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7784, 'status' => 'C', 'lower' => [7785]]; /* LATIN CAPITAL LETTER S WITH DOT BELOW AND DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7786, 'status' => 'C', 'lower' => [7787]]; /* LATIN CAPITAL LETTER T WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7788, 'status' => 'C', 'lower' => [7789]]; /* LATIN CAPITAL LETTER T WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7790, 'status' => 'C', 'lower' => [7791]]; /* LATIN CAPITAL LETTER T WITH LINE BELOW */
$config['1e00_1eff'][] = ['upper' => 7792, 'status' => 'C', 'lower' => [7793]]; /* LATIN CAPITAL LETTER T WITH CIRCUMFLEX BELOW */
$config['1e00_1eff'][] = ['upper' => 7794, 'status' => 'C', 'lower' => [7795]]; /* LATIN CAPITAL LETTER U WITH DIAERESIS BELOW */
$config['1e00_1eff'][] = ['upper' => 7796, 'status' => 'C', 'lower' => [7797]]; /* LATIN CAPITAL LETTER U WITH TILDE BELOW */
$config['1e00_1eff'][] = ['upper' => 7798, 'status' => 'C', 'lower' => [7799]]; /* LATIN CAPITAL LETTER U WITH CIRCUMFLEX BELOW */
$config['1e00_1eff'][] = ['upper' => 7800, 'status' => 'C', 'lower' => [7801]]; /* LATIN CAPITAL LETTER U WITH TILDE AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7802, 'status' => 'C', 'lower' => [7803]]; /* LATIN CAPITAL LETTER U WITH MACRON AND DIAERESIS */
$config['1e00_1eff'][] = ['upper' => 7804, 'status' => 'C', 'lower' => [7805]]; /* LATIN CAPITAL LETTER V WITH TILDE */
$config['1e00_1eff'][] = ['upper' => 7806, 'status' => 'C', 'lower' => [7807]]; /* LATIN CAPITAL LETTER V WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7808, 'status' => 'C', 'lower' => [7809]]; /* LATIN CAPITAL LETTER W WITH GRAVE */
$config['1e00_1eff'][] = ['upper' => 7810, 'status' => 'C', 'lower' => [7811]]; /* LATIN CAPITAL LETTER W WITH ACUTE */
$config['1e00_1eff'][] = ['upper' => 7812, 'status' => 'C', 'lower' => [7813]]; /* LATIN CAPITAL LETTER W WITH DIAERESIS */
$config['1e00_1eff'][] = ['upper' => 7814, 'status' => 'C', 'lower' => [7815]]; /* LATIN CAPITAL LETTER W WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7816, 'status' => 'C', 'lower' => [7817]]; /* LATIN CAPITAL LETTER W WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7818, 'status' => 'C', 'lower' => [7819]]; /* LATIN CAPITAL LETTER X WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7820, 'status' => 'C', 'lower' => [7821]]; /* LATIN CAPITAL LETTER X WITH DIAERESIS */
$config['1e00_1eff'][] = ['upper' => 7822, 'status' => 'C', 'lower' => [7823]]; /* LATIN CAPITAL LETTER Y WITH DOT ABOVE */
$config['1e00_1eff'][] = ['upper' => 7824, 'status' => 'C', 'lower' => [7825]]; /* LATIN CAPITAL LETTER Z WITH CIRCUMFLEX */
$config['1e00_1eff'][] = ['upper' => 7826, 'status' => 'C', 'lower' => [7827]]; /* LATIN CAPITAL LETTER Z WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7828, 'status' => 'C', 'lower' => [7829]]; /* LATIN CAPITAL LETTER Z WITH LINE BELOW */

//$config['1e00_1eff'][] = array('upper' => 7830, 'status' => 'F', 'lower' => array(104, 817)); /* LATIN SMALL LETTER H WITH LINE BELOW */
//$config['1e00_1eff'][] = array('upper' => 7831, 'status' => 'F', 'lower' => array(116, 776)); /* LATIN SMALL LETTER T WITH DIAERESIS */
//$config['1e00_1eff'][] = array('upper' => 7832, 'status' => 'F', 'lower' => array(119, 778)); /* LATIN SMALL LETTER W WITH RING ABOVE */
//$config['1e00_1eff'][] = array('upper' => 7833, 'status' => 'F', 'lower' => array(121, 778)); /* LATIN SMALL LETTER Y WITH RING ABOVE */
//$config['1e00_1eff'][] = array('upper' => 7834, 'status' => 'F', 'lower' => array(97, 702)); /* LATIN SMALL LETTER A WITH RIGHT HALF RING */
//$config['1e00_1eff'][] = array('upper' => 7835, 'status' => 'C', 'lower' => array(7777)); /* LATIN SMALL LETTER LONG S WITH DOT ABOVE */

$config['1e00_1eff'][] = ['upper' => 7840, 'status' => 'C', 'lower' => [7841]]; /* LATIN CAPITAL LETTER A WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7842, 'status' => 'C', 'lower' => [7843]]; /* LATIN CAPITAL LETTER A WITH HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7844, 'status' => 'C', 'lower' => [7845]]; /* LATIN CAPITAL LETTER A WITH CIRCUMFLEX AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7846, 'status' => 'C', 'lower' => [7847]]; /* LATIN CAPITAL LETTER A WITH CIRCUMFLEX AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7848, 'status' => 'C', 'lower' => [7849]]; /* LATIN CAPITAL LETTER A WITH CIRCUMFLEX AND HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7850, 'status' => 'C', 'lower' => [7851]]; /* LATIN CAPITAL LETTER A WITH CIRCUMFLEX AND TILDE */
$config['1e00_1eff'][] = ['upper' => 7852, 'status' => 'C', 'lower' => [7853]]; /* LATIN CAPITAL LETTER A WITH CIRCUMFLEX AND DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7854, 'status' => 'C', 'lower' => [7855]]; /* LATIN CAPITAL LETTER A WITH BREVE AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7856, 'status' => 'C', 'lower' => [7857]]; /* LATIN CAPITAL LETTER A WITH BREVE AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7858, 'status' => 'C', 'lower' => [7859]]; /* LATIN CAPITAL LETTER A WITH BREVE AND HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7860, 'status' => 'C', 'lower' => [7861]]; /* LATIN CAPITAL LETTER A WITH BREVE AND TILDE */
$config['1e00_1eff'][] = ['upper' => 7862, 'status' => 'C', 'lower' => [7863]]; /* LATIN CAPITAL LETTER A WITH BREVE AND DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7864, 'status' => 'C', 'lower' => [7865]]; /* LATIN CAPITAL LETTER E WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7866, 'status' => 'C', 'lower' => [7867]]; /* LATIN CAPITAL LETTER E WITH HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7868, 'status' => 'C', 'lower' => [7869]]; /* LATIN CAPITAL LETTER E WITH TILDE */
$config['1e00_1eff'][] = ['upper' => 7870, 'status' => 'C', 'lower' => [7871]]; /* LATIN CAPITAL LETTER E WITH CIRCUMFLEX AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7872, 'status' => 'C', 'lower' => [7873]]; /* LATIN CAPITAL LETTER E WITH CIRCUMFLEX AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7874, 'status' => 'C', 'lower' => [7875]]; /* LATIN CAPITAL LETTER E WITH CIRCUMFLEX AND HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7876, 'status' => 'C', 'lower' => [7877]]; /* LATIN CAPITAL LETTER E WITH CIRCUMFLEX AND TILDE */
$config['1e00_1eff'][] = ['upper' => 7878, 'status' => 'C', 'lower' => [7879]]; /* LATIN CAPITAL LETTER E WITH CIRCUMFLEX AND DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7880, 'status' => 'C', 'lower' => [7881]]; /* LATIN CAPITAL LETTER I WITH HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7882, 'status' => 'C', 'lower' => [7883]]; /* LATIN CAPITAL LETTER I WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7884, 'status' => 'C', 'lower' => [7885]]; /* LATIN CAPITAL LETTER O WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7886, 'status' => 'C', 'lower' => [7887]]; /* LATIN CAPITAL LETTER O WITH HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7888, 'status' => 'C', 'lower' => [7889]]; /* LATIN CAPITAL LETTER O WITH CIRCUMFLEX AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7890, 'status' => 'C', 'lower' => [7891]]; /* LATIN CAPITAL LETTER O WITH CIRCUMFLEX AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7892, 'status' => 'C', 'lower' => [7893]]; /* LATIN CAPITAL LETTER O WITH CIRCUMFLEX AND HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7894, 'status' => 'C', 'lower' => [7895]]; /* LATIN CAPITAL LETTER O WITH CIRCUMFLEX AND TILDE */
$config['1e00_1eff'][] = ['upper' => 7896, 'status' => 'C', 'lower' => [7897]]; /* LATIN CAPITAL LETTER O WITH CIRCUMFLEX AND DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7898, 'status' => 'C', 'lower' => [7899]]; /* LATIN CAPITAL LETTER O WITH HORN AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7900, 'status' => 'C', 'lower' => [7901]]; /* LATIN CAPITAL LETTER O WITH HORN AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7902, 'status' => 'C', 'lower' => [7903]]; /* LATIN CAPITAL LETTER O WITH HORN AND HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7904, 'status' => 'C', 'lower' => [7905]]; /* LATIN CAPITAL LETTER O WITH HORN AND TILDE */
$config['1e00_1eff'][] = ['upper' => 7906, 'status' => 'C', 'lower' => [7907]]; /* LATIN CAPITAL LETTER O WITH HORN AND DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7908, 'status' => 'C', 'lower' => [7909]]; /* LATIN CAPITAL LETTER U WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7910, 'status' => 'C', 'lower' => [7911]]; /* LATIN CAPITAL LETTER U WITH HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7912, 'status' => 'C', 'lower' => [7913]]; /* LATIN CAPITAL LETTER U WITH HORN AND ACUTE */
$config['1e00_1eff'][] = ['upper' => 7914, 'status' => 'C', 'lower' => [7915]]; /* LATIN CAPITAL LETTER U WITH HORN AND GRAVE */
$config['1e00_1eff'][] = ['upper' => 7916, 'status' => 'C', 'lower' => [7917]]; /* LATIN CAPITAL LETTER U WITH HORN AND HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7918, 'status' => 'C', 'lower' => [7919]]; /* LATIN CAPITAL LETTER U WITH HORN AND TILDE */
$config['1e00_1eff'][] = ['upper' => 7920, 'status' => 'C', 'lower' => [7921]]; /* LATIN CAPITAL LETTER U WITH HORN AND DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7922, 'status' => 'C', 'lower' => [7923]]; /* LATIN CAPITAL LETTER Y WITH GRAVE */
$config['1e00_1eff'][] = ['upper' => 7924, 'status' => 'C', 'lower' => [7925]]; /* LATIN CAPITAL LETTER Y WITH DOT BELOW */
$config['1e00_1eff'][] = ['upper' => 7926, 'status' => 'C', 'lower' => [7927]]; /* LATIN CAPITAL LETTER Y WITH HOOK ABOVE */
$config['1e00_1eff'][] = ['upper' => 7928, 'status' => 'C', 'lower' => [7929]]; /* LATIN CAPITAL LETTER Y WITH TILDE */
