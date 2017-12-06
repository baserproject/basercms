<?php
/**
 * Case Folding Properties.
 *
 * Provides case mapping of Unicode characters for code points U+0180 through U+024F
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
$config['0180_024F'][] = ['upper' => 385, 'status' => 'C', 'lower' => [595]]; /* LATIN CAPITAL LETTER B WITH HOOK */
$config['0180_024F'][] = ['upper' => 386, 'status' => 'C', 'lower' => [387]]; /* LATIN CAPITAL LETTER B WITH TOPBAR */
$config['0180_024F'][] = ['upper' => 388, 'status' => 'C', 'lower' => [389]]; /* LATIN CAPITAL LETTER TONE SIX */
$config['0180_024F'][] = ['upper' => 390, 'status' => 'C', 'lower' => [596]]; /* LATIN CAPITAL LETTER OPEN O */
$config['0180_024F'][] = ['upper' => 391, 'status' => 'C', 'lower' => [392]]; /* LATIN CAPITAL LETTER C WITH HOOK */
$config['0180_024F'][] = ['upper' => 393, 'status' => 'C', 'lower' => [598]]; /* LATIN CAPITAL LETTER AFRICAN D */
$config['0180_024F'][] = ['upper' => 394, 'status' => 'C', 'lower' => [599]]; /* LATIN CAPITAL LETTER D WITH HOOK */
$config['0180_024F'][] = ['upper' => 395, 'status' => 'C', 'lower' => [396]]; /* LATIN CAPITAL LETTER D WITH TOPBAR */
$config['0180_024F'][] = ['upper' => 398, 'status' => 'C', 'lower' => [477]]; /* LATIN CAPITAL LETTER REVERSED E */
$config['0180_024F'][] = ['upper' => 399, 'status' => 'C', 'lower' => [601]]; /* LATIN CAPITAL LETTER SCHWA */
$config['0180_024F'][] = ['upper' => 400, 'status' => 'C', 'lower' => [603]]; /* LATIN CAPITAL LETTER OPEN E */
$config['0180_024F'][] = ['upper' => 401, 'status' => 'C', 'lower' => [402]]; /* LATIN CAPITAL LETTER F WITH HOOK */
$config['0180_024F'][] = ['upper' => 403, 'status' => 'C', 'lower' => [608]]; /* LATIN CAPITAL LETTER G WITH HOOK */
$config['0180_024F'][] = ['upper' => 404, 'status' => 'C', 'lower' => [611]]; /* LATIN CAPITAL LETTER GAMMA */
$config['0180_024F'][] = ['upper' => 406, 'status' => 'C', 'lower' => [617]]; /* LATIN CAPITAL LETTER IOTA */
$config['0180_024F'][] = ['upper' => 407, 'status' => 'C', 'lower' => [616]]; /* LATIN CAPITAL LETTER I WITH STROKE */
$config['0180_024F'][] = ['upper' => 408, 'status' => 'C', 'lower' => [409]]; /* LATIN CAPITAL LETTER K WITH HOOK */
$config['0180_024F'][] = ['upper' => 412, 'status' => 'C', 'lower' => [623]]; /* LATIN CAPITAL LETTER TURNED M */
$config['0180_024F'][] = ['upper' => 413, 'status' => 'C', 'lower' => [626]]; /* LATIN CAPITAL LETTER N WITH LEFT HOOK */
$config['0180_024F'][] = ['upper' => 415, 'status' => 'C', 'lower' => [629]]; /* LATIN CAPITAL LETTER O WITH MIDDLE TILDE */
$config['0180_024F'][] = ['upper' => 416, 'status' => 'C', 'lower' => [417]]; /* LATIN CAPITAL LETTER O WITH HORN */
$config['0180_024F'][] = ['upper' => 418, 'status' => 'C', 'lower' => [419]]; /* LATIN CAPITAL LETTER OI */
$config['0180_024F'][] = ['upper' => 420, 'status' => 'C', 'lower' => [421]]; /* LATIN CAPITAL LETTER P WITH HOOK */
$config['0180_024F'][] = ['upper' => 422, 'status' => 'C', 'lower' => [640]]; /* LATIN LETTER YR */
$config['0180_024F'][] = ['upper' => 423, 'status' => 'C', 'lower' => [424]]; /* LATIN CAPITAL LETTER TONE TWO */
$config['0180_024F'][] = ['upper' => 425, 'status' => 'C', 'lower' => [643]]; /* LATIN CAPITAL LETTER ESH */
$config['0180_024F'][] = ['upper' => 428, 'status' => 'C', 'lower' => [429]]; /* LATIN CAPITAL LETTER T WITH HOOK */
$config['0180_024F'][] = ['upper' => 430, 'status' => 'C', 'lower' => [648]]; /* LATIN CAPITAL LETTER T WITH RETROFLEX HOOK */
$config['0180_024F'][] = ['upper' => 431, 'status' => 'C', 'lower' => [432]]; /* LATIN CAPITAL LETTER U WITH HORN */
$config['0180_024F'][] = ['upper' => 433, 'status' => 'C', 'lower' => [650]]; /* LATIN CAPITAL LETTER UPSILON */
$config['0180_024F'][] = ['upper' => 434, 'status' => 'C', 'lower' => [651]]; /* LATIN CAPITAL LETTER V WITH HOOK */
$config['0180_024F'][] = ['upper' => 435, 'status' => 'C', 'lower' => [436]]; /* LATIN CAPITAL LETTER Y WITH HOOK */
$config['0180_024F'][] = ['upper' => 437, 'status' => 'C', 'lower' => [438]]; /* LATIN CAPITAL LETTER Z WITH STROKE */
$config['0180_024F'][] = ['upper' => 439, 'status' => 'C', 'lower' => [658]]; /* LATIN CAPITAL LETTER EZH */
$config['0180_024F'][] = ['upper' => 440, 'status' => 'C', 'lower' => [441]]; /* LATIN CAPITAL LETTER EZH REVERSED */
$config['0180_024F'][] = ['upper' => 444, 'status' => 'C', 'lower' => [445]]; /* LATIN CAPITAL LETTER TONE FIVE */
$config['0180_024F'][] = ['upper' => 452, 'status' => 'C', 'lower' => [454]]; /* LATIN CAPITAL LETTER DZ WITH CARON */
$config['0180_024F'][] = ['upper' => 453, 'status' => 'C', 'lower' => [454]]; /* LATIN CAPITAL LETTER D WITH SMALL LETTER Z WITH CARON */
$config['0180_024F'][] = ['upper' => 455, 'status' => 'C', 'lower' => [457]]; /* LATIN CAPITAL LETTER LJ */
$config['0180_024F'][] = ['upper' => 456, 'status' => 'C', 'lower' => [457]]; /* LATIN CAPITAL LETTER L WITH SMALL LETTER J */
$config['0180_024F'][] = ['upper' => 458, 'status' => 'C', 'lower' => [460]]; /* LATIN CAPITAL LETTER NJ */
$config['0180_024F'][] = ['upper' => 459, 'status' => 'C', 'lower' => [460]]; /* LATIN CAPITAL LETTER N WITH SMALL LETTER J */
$config['0180_024F'][] = ['upper' => 461, 'status' => 'C', 'lower' => [462]]; /* LATIN CAPITAL LETTER A WITH CARON */
$config['0180_024F'][] = ['upper' => 463, 'status' => 'C', 'lower' => [464]]; /* LATIN CAPITAL LETTER I WITH CARON */
$config['0180_024F'][] = ['upper' => 465, 'status' => 'C', 'lower' => [466]]; /* LATIN CAPITAL LETTER O WITH CARON */
$config['0180_024F'][] = ['upper' => 467, 'status' => 'C', 'lower' => [468]]; /* LATIN CAPITAL LETTER U WITH CARON */
$config['0180_024F'][] = ['upper' => 469, 'status' => 'C', 'lower' => [470]]; /* LATIN CAPITAL LETTER U WITH DIAERESIS AND MACRON */
$config['0180_024F'][] = ['upper' => 471, 'status' => 'C', 'lower' => [472]]; /* LATIN CAPITAL LETTER U WITH DIAERESIS AND ACUTE */
$config['0180_024F'][] = ['upper' => 473, 'status' => 'C', 'lower' => [474]]; /* LATIN CAPITAL LETTER U WITH DIAERESIS AND CARON */
$config['0180_024F'][] = ['upper' => 475, 'status' => 'C', 'lower' => [476]]; /* LATIN CAPITAL LETTER U WITH DIAERESIS AND GRAVE */
$config['0180_024F'][] = ['upper' => 478, 'status' => 'C', 'lower' => [479]]; /* LATIN CAPITAL LETTER A WITH DIAERESIS AND MACRON */
$config['0180_024F'][] = ['upper' => 480, 'status' => 'C', 'lower' => [481]]; /* LATIN CAPITAL LETTER A WITH DOT ABOVE AND MACRON */
$config['0180_024F'][] = ['upper' => 482, 'status' => 'C', 'lower' => [483]]; /* LATIN CAPITAL LETTER AE WITH MACRON */
$config['0180_024F'][] = ['upper' => 484, 'status' => 'C', 'lower' => [485]]; /* LATIN CAPITAL LETTER G WITH STROKE */
$config['0180_024F'][] = ['upper' => 486, 'status' => 'C', 'lower' => [487]]; /* LATIN CAPITAL LETTER G WITH CARON */
$config['0180_024F'][] = ['upper' => 488, 'status' => 'C', 'lower' => [489]]; /* LATIN CAPITAL LETTER K WITH CARON */
$config['0180_024F'][] = ['upper' => 490, 'status' => 'C', 'lower' => [491]]; /* LATIN CAPITAL LETTER O WITH OGONEK */
$config['0180_024F'][] = ['upper' => 492, 'status' => 'C', 'lower' => [493]]; /* LATIN CAPITAL LETTER O WITH OGONEK AND MACRON */
$config['0180_024F'][] = ['upper' => 494, 'status' => 'C', 'lower' => [495]]; /* LATIN CAPITAL LETTER EZH WITH CARON */
$config['0180_024F'][] = ['upper' => 496, 'status' => 'F', 'lower' => [106, 780]]; /* LATIN SMALL LETTER J WITH CARON */
$config['0180_024F'][] = ['upper' => 497, 'status' => 'C', 'lower' => [499]]; /* LATIN CAPITAL LETTER DZ */
$config['0180_024F'][] = ['upper' => 498, 'status' => 'C', 'lower' => [499]]; /* LATIN CAPITAL LETTER D WITH SMALL LETTER Z */
$config['0180_024F'][] = ['upper' => 500, 'status' => 'C', 'lower' => [501]]; /* LATIN CAPITAL LETTER G WITH ACUTE */
$config['0180_024F'][] = ['upper' => 502, 'status' => 'C', 'lower' => [405]]; /* LATIN CAPITAL LETTER HWAIR */
$config['0180_024F'][] = ['upper' => 503, 'status' => 'C', 'lower' => [447]]; /* LATIN CAPITAL LETTER WYNN */
$config['0180_024F'][] = ['upper' => 504, 'status' => 'C', 'lower' => [505]]; /* LATIN CAPITAL LETTER N WITH GRAVE */
$config['0180_024F'][] = ['upper' => 506, 'status' => 'C', 'lower' => [507]]; /* LATIN CAPITAL LETTER A WITH RING ABOVE AND ACUTE */
$config['0180_024F'][] = ['upper' => 508, 'status' => 'C', 'lower' => [509]]; /* LATIN CAPITAL LETTER AE WITH ACUTE */
$config['0180_024F'][] = ['upper' => 510, 'status' => 'C', 'lower' => [511]]; /* LATIN CAPITAL LETTER O WITH STROKE AND ACUTE */
$config['0180_024F'][] = ['upper' => 512, 'status' => 'C', 'lower' => [513]]; /* LATIN CAPITAL LETTER A WITH DOUBLE GRAVE */
$config['0180_024F'][] = ['upper' => 514, 'status' => 'C', 'lower' => [515]]; /* LATIN CAPITAL LETTER A WITH INVERTED BREVE */
$config['0180_024F'][] = ['upper' => 516, 'status' => 'C', 'lower' => [517]]; /* LATIN CAPITAL LETTER E WITH DOUBLE GRAVE */
$config['0180_024F'][] = ['upper' => 518, 'status' => 'C', 'lower' => [519]]; /* LATIN CAPITAL LETTER E WITH INVERTED BREVE */
$config['0180_024F'][] = ['upper' => 520, 'status' => 'C', 'lower' => [521]]; /* LATIN CAPITAL LETTER I WITH DOUBLE GRAVE */
$config['0180_024F'][] = ['upper' => 522, 'status' => 'C', 'lower' => [523]]; /* LATIN CAPITAL LETTER I WITH INVERTED BREVE */
$config['0180_024F'][] = ['upper' => 524, 'status' => 'C', 'lower' => [525]]; /* LATIN CAPITAL LETTER O WITH DOUBLE GRAVE */
$config['0180_024F'][] = ['upper' => 526, 'status' => 'C', 'lower' => [527]]; /* LATIN CAPITAL LETTER O WITH INVERTED BREVE */
$config['0180_024F'][] = ['upper' => 528, 'status' => 'C', 'lower' => [529]]; /* LATIN CAPITAL LETTER R WITH DOUBLE GRAVE */
$config['0180_024F'][] = ['upper' => 530, 'status' => 'C', 'lower' => [531]]; /* LATIN CAPITAL LETTER R WITH INVERTED BREVE */
$config['0180_024F'][] = ['upper' => 532, 'status' => 'C', 'lower' => [533]]; /* LATIN CAPITAL LETTER U WITH DOUBLE GRAVE */
$config['0180_024F'][] = ['upper' => 534, 'status' => 'C', 'lower' => [535]]; /* LATIN CAPITAL LETTER U WITH INVERTED BREVE */
$config['0180_024F'][] = ['upper' => 536, 'status' => 'C', 'lower' => [537]]; /* LATIN CAPITAL LETTER S WITH COMMA BELOW */
$config['0180_024F'][] = ['upper' => 538, 'status' => 'C', 'lower' => [539]]; /* LATIN CAPITAL LETTER T WITH COMMA BELOW */
$config['0180_024F'][] = ['upper' => 540, 'status' => 'C', 'lower' => [541]]; /* LATIN CAPITAL LETTER YOGH */
$config['0180_024F'][] = ['upper' => 542, 'status' => 'C', 'lower' => [543]]; /* LATIN CAPITAL LETTER H WITH CARON */
$config['0180_024F'][] = ['upper' => 544, 'status' => 'C', 'lower' => [414]]; /* LATIN CAPITAL LETTER N WITH LONG RIGHT LEG */
$config['0180_024F'][] = ['upper' => 546, 'status' => 'C', 'lower' => [547]]; /* LATIN CAPITAL LETTER OU */
$config['0180_024F'][] = ['upper' => 548, 'status' => 'C', 'lower' => [549]]; /* LATIN CAPITAL LETTER Z WITH HOOK */
$config['0180_024F'][] = ['upper' => 550, 'status' => 'C', 'lower' => [551]]; /* LATIN CAPITAL LETTER A WITH DOT ABOVE */
$config['0180_024F'][] = ['upper' => 552, 'status' => 'C', 'lower' => [553]]; /* LATIN CAPITAL LETTER E WITH CEDILLA */
$config['0180_024F'][] = ['upper' => 554, 'status' => 'C', 'lower' => [555]]; /* LATIN CAPITAL LETTER O WITH DIAERESIS AND MACRON */
$config['0180_024F'][] = ['upper' => 556, 'status' => 'C', 'lower' => [557]]; /* LATIN CAPITAL LETTER O WITH TILDE AND MACRON */
$config['0180_024F'][] = ['upper' => 558, 'status' => 'C', 'lower' => [559]]; /* LATIN CAPITAL LETTER O WITH DOT ABOVE */
$config['0180_024F'][] = ['upper' => 560, 'status' => 'C', 'lower' => [561]]; /* LATIN CAPITAL LETTER O WITH DOT ABOVE AND MACRON */
$config['0180_024F'][] = ['upper' => 562, 'status' => 'C', 'lower' => [563]]; /* LATIN CAPITAL LETTER Y WITH MACRON */
$config['0180_024F'][] = ['upper' => 570, 'status' => 'C', 'lower' => [11365]]; /* LATIN CAPITAL LETTER A WITH STROKE */
$config['0180_024F'][] = ['upper' => 571, 'status' => 'C', 'lower' => [572]]; /* LATIN CAPITAL LETTER C WITH STROKE */
$config['0180_024F'][] = ['upper' => 573, 'status' => 'C', 'lower' => [410]]; /* LATIN CAPITAL LETTER L WITH BAR */
$config['0180_024F'][] = ['upper' => 574, 'status' => 'C', 'lower' => [11366]]; /* LATIN CAPITAL LETTER T WITH DIAGONAL STROKE */
$config['0180_024F'][] = ['upper' => 577, 'status' => 'C', 'lower' => [578]]; /* LATIN CAPITAL LETTER GLOTTAL STOP */
$config['0180_024F'][] = ['upper' => 579, 'status' => 'C', 'lower' => [384]]; /* LATIN CAPITAL LETTER B WITH STROKE */
$config['0180_024F'][] = ['upper' => 580, 'status' => 'C', 'lower' => [649]]; /* LATIN CAPITAL LETTER U BAR */
$config['0180_024F'][] = ['upper' => 581, 'status' => 'C', 'lower' => [652]]; /* LATIN CAPITAL LETTER TURNED V */
$config['0180_024F'][] = ['upper' => 582, 'status' => 'C', 'lower' => [583]]; /* LATIN CAPITAL LETTER E WITH STROKE */
$config['0180_024F'][] = ['upper' => 584, 'status' => 'C', 'lower' => [585]]; /* LATIN CAPITAL LETTER J WITH STROKE */
$config['0180_024F'][] = ['upper' => 586, 'status' => 'C', 'lower' => [587]]; /* LATIN CAPITAL LETTER SMALL Q WITH HOOK TAIL */
$config['0180_024F'][] = ['upper' => 588, 'status' => 'C', 'lower' => [589]]; /* LATIN CAPITAL LETTER R WITH STROKE */
$config['0180_024F'][] = ['upper' => 590, 'status' => 'C', 'lower' => [591]]; /* LATIN CAPITAL LETTER Y WITH STROKE */
