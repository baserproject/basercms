<?php
/**
 * Case Folding Properties.
 *
 * Provides case mapping of Unicode characters for code points U+1F00 through U+1FFF
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
$config['1f00_1fff'][] = ['upper' => 7944, 'status' => 'C', 'lower' => [7936, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 7945, 'status' => 'C', 'lower' => [7937]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 7946, 'status' => 'C', 'lower' => [7938]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7947, 'status' => 'C', 'lower' => [7939]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7948, 'status' => 'C', 'lower' => [7940]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7949, 'status' => 'C', 'lower' => [7941]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7950, 'status' => 'C', 'lower' => [7942]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 7951, 'status' => 'C', 'lower' => [7943]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 7960, 'status' => 'C', 'lower' => [7952]]; /* GREEK CAPITAL LETTER EPSILON WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 7961, 'status' => 'C', 'lower' => [7953]]; /* GREEK CAPITAL LETTER EPSILON WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 7962, 'status' => 'C', 'lower' => [7954]]; /* GREEK CAPITAL LETTER EPSILON WITH PSILI AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7963, 'status' => 'C', 'lower' => [7955]]; /* GREEK CAPITAL LETTER EPSILON WITH DASIA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7964, 'status' => 'C', 'lower' => [7956]]; /* GREEK CAPITAL LETTER EPSILON WITH PSILI AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7965, 'status' => 'C', 'lower' => [7957]]; /* GREEK CAPITAL LETTER EPSILON WITH DASIA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7976, 'status' => 'C', 'lower' => [7968]]; /* GREEK CAPITAL LETTER ETA WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 7977, 'status' => 'C', 'lower' => [7969]]; /* GREEK CAPITAL LETTER ETA WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 7978, 'status' => 'C', 'lower' => [7970]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7979, 'status' => 'C', 'lower' => [7971]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7980, 'status' => 'C', 'lower' => [7972]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7981, 'status' => 'C', 'lower' => [7973]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7982, 'status' => 'C', 'lower' => [7974]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 7983, 'status' => 'C', 'lower' => [7975]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 7992, 'status' => 'C', 'lower' => [7984]]; /* GREEK CAPITAL LETTER IOTA WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 7993, 'status' => 'C', 'lower' => [7985]]; /* GREEK CAPITAL LETTER IOTA WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 7994, 'status' => 'C', 'lower' => [7986]]; /* GREEK CAPITAL LETTER IOTA WITH PSILI AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7995, 'status' => 'C', 'lower' => [7987]]; /* GREEK CAPITAL LETTER IOTA WITH DASIA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 7996, 'status' => 'C', 'lower' => [7988]]; /* GREEK CAPITAL LETTER IOTA WITH PSILI AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7997, 'status' => 'C', 'lower' => [7989]]; /* GREEK CAPITAL LETTER IOTA WITH DASIA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 7998, 'status' => 'C', 'lower' => [7990]]; /* GREEK CAPITAL LETTER IOTA WITH PSILI AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 7999, 'status' => 'C', 'lower' => [7991]]; /* GREEK CAPITAL LETTER IOTA WITH DASIA AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8008, 'status' => 'C', 'lower' => [8000]]; /* GREEK CAPITAL LETTER OMICRON WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 8009, 'status' => 'C', 'lower' => [8001]]; /* GREEK CAPITAL LETTER OMICRON WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 8010, 'status' => 'C', 'lower' => [8002]]; /* GREEK CAPITAL LETTER OMICRON WITH PSILI AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8011, 'status' => 'C', 'lower' => [8003]]; /* GREEK CAPITAL LETTER OMICRON WITH DASIA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8012, 'status' => 'C', 'lower' => [8004]]; /* GREEK CAPITAL LETTER OMICRON WITH PSILI AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8013, 'status' => 'C', 'lower' => [8005]]; /* GREEK CAPITAL LETTER OMICRON WITH DASIA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8016, 'status' => 'F', 'lower' => [965, 787]]; /* GREEK SMALL LETTER UPSILON WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 8018, 'status' => 'F', 'lower' => [965, 787, 768]]; /* GREEK SMALL LETTER UPSILON WITH PSILI AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8020, 'status' => 'F', 'lower' => [965, 787, 769]]; /* GREEK SMALL LETTER UPSILON WITH PSILI AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8022, 'status' => 'F', 'lower' => [965, 787, 834]]; /* GREEK SMALL LETTER UPSILON WITH PSILI AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8025, 'status' => 'C', 'lower' => [8017]]; /* GREEK CAPITAL LETTER UPSILON WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 8027, 'status' => 'C', 'lower' => [8019]]; /* GREEK CAPITAL LETTER UPSILON WITH DASIA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8029, 'status' => 'C', 'lower' => [8021]]; /* GREEK CAPITAL LETTER UPSILON WITH DASIA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8031, 'status' => 'C', 'lower' => [8023]]; /* GREEK CAPITAL LETTER UPSILON WITH DASIA AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8040, 'status' => 'C', 'lower' => [8032]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 8041, 'status' => 'C', 'lower' => [8033]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 8042, 'status' => 'C', 'lower' => [8034]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8043, 'status' => 'C', 'lower' => [8035]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8044, 'status' => 'C', 'lower' => [8036]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8045, 'status' => 'C', 'lower' => [8037]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8046, 'status' => 'C', 'lower' => [8038]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8047, 'status' => 'C', 'lower' => [8039]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8064, 'status' => 'F', 'lower' => [7936, 953]]; /* GREEK SMALL LETTER ALPHA WITH PSILI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8065, 'status' => 'F', 'lower' => [7937, 953]]; /* GREEK SMALL LETTER ALPHA WITH DASIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8066, 'status' => 'F', 'lower' => [7938, 953]]; /* GREEK SMALL LETTER ALPHA WITH PSILI AND VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8067, 'status' => 'F', 'lower' => [7939, 953]]; /* GREEK SMALL LETTER ALPHA WITH DASIA AND VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8068, 'status' => 'F', 'lower' => [7940, 953]]; /* GREEK SMALL LETTER ALPHA WITH PSILI AND OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8069, 'status' => 'F', 'lower' => [7941, 953]]; /* GREEK SMALL LETTER ALPHA WITH DASIA AND OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8070, 'status' => 'F', 'lower' => [7942, 953]]; /* GREEK SMALL LETTER ALPHA WITH PSILI AND PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8071, 'status' => 'F', 'lower' => [7943, 953]]; /* GREEK SMALL LETTER ALPHA WITH DASIA AND PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8072, 'status' => 'F', 'lower' => [7936, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8072, 'status' => 'S', 'lower' => [8064]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8073, 'status' => 'F', 'lower' => [7937, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8073, 'status' => 'S', 'lower' => [8065]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8074, 'status' => 'F', 'lower' => [7938, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8074, 'status' => 'S', 'lower' => [8066]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8075, 'status' => 'F', 'lower' => [7939, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8075, 'status' => 'S', 'lower' => [8067]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8076, 'status' => 'F', 'lower' => [7940, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8076, 'status' => 'S', 'lower' => [8068]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8077, 'status' => 'F', 'lower' => [7941, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8077, 'status' => 'S', 'lower' => [8069]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8078, 'status' => 'F', 'lower' => [7942, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8078, 'status' => 'S', 'lower' => [8070]]; /* GREEK CAPITAL LETTER ALPHA WITH PSILI AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8079, 'status' => 'F', 'lower' => [7943, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8079, 'status' => 'S', 'lower' => [8071]]; /* GREEK CAPITAL LETTER ALPHA WITH DASIA AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8080, 'status' => 'F', 'lower' => [7968, 953]]; /* GREEK SMALL LETTER ETA WITH PSILI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8081, 'status' => 'F', 'lower' => [7969, 953]]; /* GREEK SMALL LETTER ETA WITH DASIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8082, 'status' => 'F', 'lower' => [7970, 953]]; /* GREEK SMALL LETTER ETA WITH PSILI AND VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8083, 'status' => 'F', 'lower' => [7971, 953]]; /* GREEK SMALL LETTER ETA WITH DASIA AND VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8084, 'status' => 'F', 'lower' => [7972, 953]]; /* GREEK SMALL LETTER ETA WITH PSILI AND OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8085, 'status' => 'F', 'lower' => [7973, 953]]; /* GREEK SMALL LETTER ETA WITH DASIA AND OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8086, 'status' => 'F', 'lower' => [7974, 953]]; /* GREEK SMALL LETTER ETA WITH PSILI AND PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8087, 'status' => 'F', 'lower' => [7975, 953]]; /* GREEK SMALL LETTER ETA WITH DASIA AND PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8088, 'status' => 'F', 'lower' => [7968, 953]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8088, 'status' => 'S', 'lower' => [8080]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8089, 'status' => 'F', 'lower' => [7969, 953]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8089, 'status' => 'S', 'lower' => [8081]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8090, 'status' => 'F', 'lower' => [7970, 953]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8090, 'status' => 'S', 'lower' => [8082]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8091, 'status' => 'F', 'lower' => [7971, 953]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8091, 'status' => 'S', 'lower' => [8083]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8092, 'status' => 'F', 'lower' => [7972, 953]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8092, 'status' => 'S', 'lower' => [8084]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8093, 'status' => 'F', 'lower' => [7973, 953]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8093, 'status' => 'S', 'lower' => [8085]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8094, 'status' => 'F', 'lower' => [7974, 953]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8094, 'status' => 'S', 'lower' => [8086]]; /* GREEK CAPITAL LETTER ETA WITH PSILI AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8095, 'status' => 'F', 'lower' => [7975, 953]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8095, 'status' => 'S', 'lower' => [8087]]; /* GREEK CAPITAL LETTER ETA WITH DASIA AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8096, 'status' => 'F', 'lower' => [8032, 953]]; /* GREEK SMALL LETTER OMEGA WITH PSILI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8097, 'status' => 'F', 'lower' => [8033, 953]]; /* GREEK SMALL LETTER OMEGA WITH DASIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8098, 'status' => 'F', 'lower' => [8034, 953]]; /* GREEK SMALL LETTER OMEGA WITH PSILI AND VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8099, 'status' => 'F', 'lower' => [8035, 953]]; /* GREEK SMALL LETTER OMEGA WITH DASIA AND VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8100, 'status' => 'F', 'lower' => [8036, 953]]; /* GREEK SMALL LETTER OMEGA WITH PSILI AND OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8101, 'status' => 'F', 'lower' => [8037, 953]]; /* GREEK SMALL LETTER OMEGA WITH DASIA AND OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8102, 'status' => 'F', 'lower' => [8038, 953]]; /* GREEK SMALL LETTER OMEGA WITH PSILI AND PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8103, 'status' => 'F', 'lower' => [8039, 953]]; /* GREEK SMALL LETTER OMEGA WITH DASIA AND PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8104, 'status' => 'F', 'lower' => [8032, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8104, 'status' => 'S', 'lower' => [8096]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8105, 'status' => 'F', 'lower' => [8033, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8105, 'status' => 'S', 'lower' => [8097]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8106, 'status' => 'F', 'lower' => [8034, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8106, 'status' => 'S', 'lower' => [8098]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8107, 'status' => 'F', 'lower' => [8035, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8107, 'status' => 'S', 'lower' => [8099]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND VARIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8108, 'status' => 'F', 'lower' => [8036, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8108, 'status' => 'S', 'lower' => [8100]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8109, 'status' => 'F', 'lower' => [8037, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8109, 'status' => 'S', 'lower' => [8101]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND OXIA AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8110, 'status' => 'F', 'lower' => [8038, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8110, 'status' => 'S', 'lower' => [8102]]; /* GREEK CAPITAL LETTER OMEGA WITH PSILI AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8111, 'status' => 'F', 'lower' => [8039, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8111, 'status' => 'S', 'lower' => [8103]]; /* GREEK CAPITAL LETTER OMEGA WITH DASIA AND PERISPOMENI AND PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8114, 'status' => 'F', 'lower' => [8048, 953]]; /* GREEK SMALL LETTER ALPHA WITH VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8115, 'status' => 'F', 'lower' => [945, 953]]; /* GREEK SMALL LETTER ALPHA WITH YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8116, 'status' => 'F', 'lower' => [940, 953]]; /* GREEK SMALL LETTER ALPHA WITH OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8118, 'status' => 'F', 'lower' => [945, 834]]; /* GREEK SMALL LETTER ALPHA WITH PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8119, 'status' => 'F', 'lower' => [945, 834, 953]]; /* GREEK SMALL LETTER ALPHA WITH PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8120, 'status' => 'C', 'lower' => [8112]]; /* GREEK CAPITAL LETTER ALPHA WITH VRACHY */
$config['1f00_1fff'][] = ['upper' => 8121, 'status' => 'C', 'lower' => [8113]]; /* GREEK CAPITAL LETTER ALPHA WITH MACRON */
$config['1f00_1fff'][] = ['upper' => 8122, 'status' => 'C', 'lower' => [8048]]; /* GREEK CAPITAL LETTER ALPHA WITH VARIA */
$config['1f00_1fff'][] = ['upper' => 8123, 'status' => 'C', 'lower' => [8049]]; /* GREEK CAPITAL LETTER ALPHA WITH OXIA */
$config['1f00_1fff'][] = ['upper' => 8124, 'status' => 'F', 'lower' => [945, 953]]; /* GREEK CAPITAL LETTER ALPHA WITH PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8124, 'status' => 'S', 'lower' => [8115]]; /* GREEK CAPITAL LETTER ALPHA WITH PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8126, 'status' => 'C', 'lower' => [953]]; /* GREEK PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8130, 'status' => 'F', 'lower' => [8052, 953]]; /* GREEK SMALL LETTER ETA WITH VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8131, 'status' => 'F', 'lower' => [951, 953]]; /* GREEK SMALL LETTER ETA WITH YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8132, 'status' => 'F', 'lower' => [942, 953]]; /* GREEK SMALL LETTER ETA WITH OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8134, 'status' => 'F', 'lower' => [951, 834]]; /* GREEK SMALL LETTER ETA WITH PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8135, 'status' => 'F', 'lower' => [951, 834, 953]]; /* GREEK SMALL LETTER ETA WITH PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8136, 'status' => 'C', 'lower' => [8050]]; /* GREEK CAPITAL LETTER EPSILON WITH VARIA */
$config['1f00_1fff'][] = ['upper' => 8137, 'status' => 'C', 'lower' => [8051]]; /* GREEK CAPITAL LETTER EPSILON WITH OXIA */
$config['1f00_1fff'][] = ['upper' => 8138, 'status' => 'C', 'lower' => [8052]]; /* GREEK CAPITAL LETTER ETA WITH VARIA */
$config['1f00_1fff'][] = ['upper' => 8139, 'status' => 'C', 'lower' => [8053]]; /* GREEK CAPITAL LETTER ETA WITH OXIA */
$config['1f00_1fff'][] = ['upper' => 8140, 'status' => 'F', 'lower' => [951, 953]]; /* GREEK CAPITAL LETTER ETA WITH PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8140, 'status' => 'S', 'lower' => [8131]]; /* GREEK CAPITAL LETTER ETA WITH PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8146, 'status' => 'F', 'lower' => [953, 776, 768]]; /* GREEK SMALL LETTER IOTA WITH DIALYTIKA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8147, 'status' => 'F', 'lower' => [953, 776, 769]]; /* GREEK SMALL LETTER IOTA WITH DIALYTIKA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8150, 'status' => 'F', 'lower' => [953, 834]]; /* GREEK SMALL LETTER IOTA WITH PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8151, 'status' => 'F', 'lower' => [953, 776, 834]]; /* GREEK SMALL LETTER IOTA WITH DIALYTIKA AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8152, 'status' => 'C', 'lower' => [8144]]; /* GREEK CAPITAL LETTER IOTA WITH VRACHY */
$config['1f00_1fff'][] = ['upper' => 8153, 'status' => 'C', 'lower' => [8145]]; /* GREEK CAPITAL LETTER IOTA WITH MACRON */
$config['1f00_1fff'][] = ['upper' => 8154, 'status' => 'C', 'lower' => [8054]]; /* GREEK CAPITAL LETTER IOTA WITH VARIA */
$config['1f00_1fff'][] = ['upper' => 8155, 'status' => 'C', 'lower' => [8055]]; /* GREEK CAPITAL LETTER IOTA WITH OXIA */
$config['1f00_1fff'][] = ['upper' => 8162, 'status' => 'F', 'lower' => [965, 776, 768]]; /* GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND VARIA */
$config['1f00_1fff'][] = ['upper' => 8163, 'status' => 'F', 'lower' => [965, 776, 769]]; /* GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND OXIA */
$config['1f00_1fff'][] = ['upper' => 8164, 'status' => 'F', 'lower' => [961, 787]]; /* GREEK SMALL LETTER RHO WITH PSILI */
$config['1f00_1fff'][] = ['upper' => 8166, 'status' => 'F', 'lower' => [965, 834]]; /* GREEK SMALL LETTER UPSILON WITH PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8167, 'status' => 'F', 'lower' => [965, 776, 834]]; /* GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8168, 'status' => 'C', 'lower' => [8160]]; /* GREEK CAPITAL LETTER UPSILON WITH VRACHY */
$config['1f00_1fff'][] = ['upper' => 8169, 'status' => 'C', 'lower' => [8161]]; /* GREEK CAPITAL LETTER UPSILON WITH MACRON */
$config['1f00_1fff'][] = ['upper' => 8170, 'status' => 'C', 'lower' => [8058]]; /* GREEK CAPITAL LETTER UPSILON WITH VARIA */
$config['1f00_1fff'][] = ['upper' => 8171, 'status' => 'C', 'lower' => [8059]]; /* GREEK CAPITAL LETTER UPSILON WITH OXIA */
$config['1f00_1fff'][] = ['upper' => 8172, 'status' => 'C', 'lower' => [8165]]; /* GREEK CAPITAL LETTER RHO WITH DASIA */
$config['1f00_1fff'][] = ['upper' => 8178, 'status' => 'F', 'lower' => [8060, 953]]; /* GREEK SMALL LETTER OMEGA WITH VARIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8179, 'status' => 'F', 'lower' => [969, 953]]; /* GREEK SMALL LETTER OMEGA WITH YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8180, 'status' => 'F', 'lower' => [974, 953]]; /* GREEK SMALL LETTER OMEGA WITH OXIA AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8182, 'status' => 'F', 'lower' => [969, 834]]; /* GREEK SMALL LETTER OMEGA WITH PERISPOMENI */
$config['1f00_1fff'][] = ['upper' => 8183, 'status' => 'F', 'lower' => [969, 834, 953]]; /* GREEK SMALL LETTER OMEGA WITH PERISPOMENI AND YPOGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8184, 'status' => 'C', 'lower' => [8056]]; /* GREEK CAPITAL LETTER OMICRON WITH VARIA */
$config['1f00_1fff'][] = ['upper' => 8185, 'status' => 'C', 'lower' => [8057]]; /* GREEK CAPITAL LETTER OMICRON WITH OXIA */
$config['1f00_1fff'][] = ['upper' => 8186, 'status' => 'C', 'lower' => [8060]]; /* GREEK CAPITAL LETTER OMEGA WITH VARIA */
$config['1f00_1fff'][] = ['upper' => 8187, 'status' => 'C', 'lower' => [8061]]; /* GREEK CAPITAL LETTER OMEGA WITH OXIA */
$config['1f00_1fff'][] = ['upper' => 8188, 'status' => 'F', 'lower' => [969, 953]]; /* GREEK CAPITAL LETTER OMEGA WITH PROSGEGRAMMENI */
$config['1f00_1fff'][] = ['upper' => 8188, 'status' => 'S', 'lower' => [8179]]; /* GREEK CAPITAL LETTER OMEGA WITH PROSGEGRAMMENI */
