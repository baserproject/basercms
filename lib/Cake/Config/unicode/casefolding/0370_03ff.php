<?php
/**
 * Case Folding Properties.
 *
 * Provides case mapping of Unicode characters for code points U+0370 through U+03FF
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
$config['0370_03ff'][] = ['upper' => 902, 'status' => 'C', 'lower' => [940]]; /* GREEK CAPITAL LETTER ALPHA WITH TONOS */
$config['0370_03ff'][] = ['upper' => 904, 'status' => 'C', 'lower' => [941]]; /* GREEK CAPITAL LETTER EPSILON WITH TONOS */
$config['0370_03ff'][] = ['upper' => 905, 'status' => 'C', 'lower' => [942]]; /* GREEK CAPITAL LETTER ETA WITH TONOS */
$config['0370_03ff'][] = ['upper' => 906, 'status' => 'C', 'lower' => [943]]; /* GREEK CAPITAL LETTER IOTA WITH TONOS */
$config['0370_03ff'][] = ['upper' => 908, 'status' => 'C', 'lower' => [972]]; /* GREEK CAPITAL LETTER OMICRON WITH TONOS */
$config['0370_03ff'][] = ['upper' => 910, 'status' => 'C', 'lower' => [973]]; /* GREEK CAPITAL LETTER UPSILON WITH TONOS */
$config['0370_03ff'][] = ['upper' => 911, 'status' => 'C', 'lower' => [974]]; /* GREEK CAPITAL LETTER OMEGA WITH TONOS */
//$config['0370_03ff'][] = array('upper' => 912, 'status' => 'F', 'lower' => array(953, 776, 769)); /* GREEK SMALL LETTER IOTA WITH DIALYTIKA AND TONOS */
$config['0370_03ff'][] = ['upper' => 913, 'status' => 'C', 'lower' => [945]]; /* GREEK CAPITAL LETTER ALPHA */
$config['0370_03ff'][] = ['upper' => 914, 'status' => 'C', 'lower' => [946]]; /* GREEK CAPITAL LETTER BETA */
$config['0370_03ff'][] = ['upper' => 915, 'status' => 'C', 'lower' => [947]]; /* GREEK CAPITAL LETTER GAMMA */
$config['0370_03ff'][] = ['upper' => 916, 'status' => 'C', 'lower' => [948]]; /* GREEK CAPITAL LETTER DELTA */
$config['0370_03ff'][] = ['upper' => 917, 'status' => 'C', 'lower' => [949]]; /* GREEK CAPITAL LETTER EPSILON */
$config['0370_03ff'][] = ['upper' => 918, 'status' => 'C', 'lower' => [950]]; /* GREEK CAPITAL LETTER ZETA */
$config['0370_03ff'][] = ['upper' => 919, 'status' => 'C', 'lower' => [951]]; /* GREEK CAPITAL LETTER ETA */
$config['0370_03ff'][] = ['upper' => 920, 'status' => 'C', 'lower' => [952]]; /* GREEK CAPITAL LETTER THETA */
$config['0370_03ff'][] = ['upper' => 921, 'status' => 'C', 'lower' => [953]]; /* GREEK CAPITAL LETTER IOTA */
$config['0370_03ff'][] = ['upper' => 922, 'status' => 'C', 'lower' => [954]]; /* GREEK CAPITAL LETTER KAPPA */
$config['0370_03ff'][] = ['upper' => 923, 'status' => 'C', 'lower' => [955]]; /* GREEK CAPITAL LETTER LAMDA */
$config['0370_03ff'][] = ['upper' => 924, 'status' => 'C', 'lower' => [956]]; /* GREEK CAPITAL LETTER MU */
$config['0370_03ff'][] = ['upper' => 925, 'status' => 'C', 'lower' => [957]]; /* GREEK CAPITAL LETTER NU */
$config['0370_03ff'][] = ['upper' => 926, 'status' => 'C', 'lower' => [958]]; /* GREEK CAPITAL LETTER XI */
$config['0370_03ff'][] = ['upper' => 927, 'status' => 'C', 'lower' => [959]]; /* GREEK CAPITAL LETTER OMICRON */
$config['0370_03ff'][] = ['upper' => 928, 'status' => 'C', 'lower' => [960]]; /* GREEK CAPITAL LETTER PI */
$config['0370_03ff'][] = ['upper' => 929, 'status' => 'C', 'lower' => [961]]; /* GREEK CAPITAL LETTER RHO */
$config['0370_03ff'][] = ['upper' => 931, 'status' => 'C', 'lower' => [963]]; /* GREEK CAPITAL LETTER SIGMA */
$config['0370_03ff'][] = ['upper' => 932, 'status' => 'C', 'lower' => [964]]; /* GREEK CAPITAL LETTER TAU */
$config['0370_03ff'][] = ['upper' => 933, 'status' => 'C', 'lower' => [965]]; /* GREEK CAPITAL LETTER UPSILON */
$config['0370_03ff'][] = ['upper' => 934, 'status' => 'C', 'lower' => [966]]; /* GREEK CAPITAL LETTER PHI */
$config['0370_03ff'][] = ['upper' => 935, 'status' => 'C', 'lower' => [967]]; /* GREEK CAPITAL LETTER CHI */
$config['0370_03ff'][] = ['upper' => 936, 'status' => 'C', 'lower' => [968]]; /* GREEK CAPITAL LETTER PSI */
$config['0370_03ff'][] = ['upper' => 937, 'status' => 'C', 'lower' => [969]]; /* GREEK CAPITAL LETTER OMEGA */
$config['0370_03ff'][] = ['upper' => 938, 'status' => 'C', 'lower' => [970]]; /* GREEK CAPITAL LETTER IOTA WITH DIALYTIKA */
$config['0370_03ff'][] = ['upper' => 939, 'status' => 'C', 'lower' => [971]]; /* GREEK CAPITAL LETTER UPSILON WITH DIALYTIKA */
$config['0370_03ff'][] = ['upper' => 944, 'status' => 'F', 'lower' => [965, 776, 769]]; /* GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND TONOS */
$config['0370_03ff'][] = ['upper' => 962, 'status' => 'C', 'lower' => [963]]; /* GREEK SMALL LETTER FINAL SIGMA */
$config['0370_03ff'][] = ['upper' => 976, 'status' => 'C', 'lower' => [946]]; /* GREEK BETA SYMBOL */
$config['0370_03ff'][] = ['upper' => 977, 'status' => 'C', 'lower' => [952]]; /* GREEK THETA SYMBOL */
$config['0370_03ff'][] = ['upper' => 981, 'status' => 'C', 'lower' => [966]]; /* GREEK PHI SYMBOL */
$config['0370_03ff'][] = ['upper' => 982, 'status' => 'C', 'lower' => [960]]; /* GREEK PI SYMBOL */
$config['0370_03ff'][] = ['upper' => 984, 'status' => 'C', 'lower' => [985]]; /* GREEK LETTER ARCHAIC KOPPA */
$config['0370_03ff'][] = ['upper' => 986, 'status' => 'C', 'lower' => [987]]; /* GREEK LETTER STIGMA */
$config['0370_03ff'][] = ['upper' => 988, 'status' => 'C', 'lower' => [989]]; /* GREEK LETTER DIGAMMA */
$config['0370_03ff'][] = ['upper' => 990, 'status' => 'C', 'lower' => [991]]; /* GREEK LETTER KOPPA */
$config['0370_03ff'][] = ['upper' => 992, 'status' => 'C', 'lower' => [993]]; /* GREEK LETTER SAMPI */
$config['0370_03ff'][] = ['upper' => 994, 'status' => 'C', 'lower' => [995]]; /* COPTIC CAPITAL LETTER SHEI */
$config['0370_03ff'][] = ['upper' => 996, 'status' => 'C', 'lower' => [997]]; /* COPTIC CAPITAL LETTER FEI */
$config['0370_03ff'][] = ['upper' => 998, 'status' => 'C', 'lower' => [999]]; /* COPTIC CAPITAL LETTER KHEI */
$config['0370_03ff'][] = ['upper' => 1000, 'status' => 'C', 'lower' => [1001]]; /* COPTIC CAPITAL LETTER HORI */
$config['0370_03ff'][] = ['upper' => 1002, 'status' => 'C', 'lower' => [1003]]; /* COPTIC CAPITAL LETTER GANGIA */
$config['0370_03ff'][] = ['upper' => 1004, 'status' => 'C', 'lower' => [1005]]; /* COPTIC CAPITAL LETTER SHIMA */
$config['0370_03ff'][] = ['upper' => 1006, 'status' => 'C', 'lower' => [1007]]; /* COPTIC CAPITAL LETTER DEI */
$config['0370_03ff'][] = ['upper' => 1008, 'status' => 'C', 'lower' => [954]]; /* GREEK KAPPA SYMBOL */
$config['0370_03ff'][] = ['upper' => 1009, 'status' => 'C', 'lower' => [961]]; /* GREEK RHO SYMBOL */
$config['0370_03ff'][] = ['upper' => 1012, 'status' => 'C', 'lower' => [952]]; /* GREEK CAPITAL THETA SYMBOL */
$config['0370_03ff'][] = ['upper' => 1013, 'status' => 'C', 'lower' => [949]]; /* GREEK LUNATE EPSILON SYMBOL */
$config['0370_03ff'][] = ['upper' => 1015, 'status' => 'C', 'lower' => [1016]]; /* GREEK CAPITAL LETTER SHO */
$config['0370_03ff'][] = ['upper' => 1017, 'status' => 'C', 'lower' => [1010]]; /* GREEK CAPITAL LUNATE SIGMA SYMBOL */
$config['0370_03ff'][] = ['upper' => 1018, 'status' => 'C', 'lower' => [1019]]; /* GREEK CAPITAL LETTER SAN */
$config['0370_03ff'][] = ['upper' => 1021, 'status' => 'C', 'lower' => [891]]; /* GREEK CAPITAL REVERSED LUNATE SIGMA SYMBOL */
$config['0370_03ff'][] = ['upper' => 1022, 'status' => 'C', 'lower' => [892]]; /* GREEK CAPITAL DOTTED LUNATE SIGMA SYMBOL */
$config['0370_03ff'][] = ['upper' => 1023, 'status' => 'C', 'lower' => [893]]; /* GREEK CAPITAL REVERSED DOTTED LUNATE SIGMA SYMBOL */
