<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ブログカレンダー
 */
if (isset($blogContent)) {
	$id = $blogContent['BlogContent']['id'];
} else {
	$id = $blog_content_id;
}
$url = '/blog/blog/get_calendar/' . $id;
if (!empty($year)) {
	$url .= '/' . $year;
}
if (!empty($month)) {
	$url .= '/' . $month;
}
$data = $this->requestAction($url);
$blogContent = $data['blogContent'];
$entryDates = $data['entryDates'];
?>
<?php // TODO コード整理する事  ?>

<div class="widget widget-blog-calendar widget-blog-calendar-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php
	//本日の日付を取得する
	$time = time();

	//各日付をセットする
	$year = date("Y", $time);
	$month = date("n", $time);
	$day = date("j", $time);

	//GETにきた年月をチェックする
	if (isset($this->params['pass']['0']) && $this->params['pass']['0'] == 'date') {
		$year2 = h(@$this->params['pass']['1']);
		$month2 = h(@$this->params['pass']['2']);
		$day2 = h(@$this->params['pass']['3']);
	} else {
		$year2 = '';
		$month2 = '';
		$day2 = '';
	}

	//先月、来月をクリックした場合の処理
	if ($year2 != "" || $month2 != "" || $day2 != "") {
		if ($year2 != "") {
			$year = $year2;
		}
		if ($month2 != "") {
			$month = $month2;
		}
		if ($day2 != "") {
			$day = $day2;
		} else {
			$day = 1;
		}
		if ($_time = mktime(0, 0, 0, $month, $day, $year)) {
			$time = $_time;
		}
		$year = date("Y", $time);
		$month = date("n", $time);
		$day = date("j", $time);
	}

	//今月の日付の数
	$num = date("t", $time);

	//曜日を取得するために時間をセット
	$today = mktime(0, 0, 0, $month, $day, $year);

	//曜日の配列
	$date = [__('日'), __('月'), __('火'), __('水'), __('木'), __('金'), __('土')];

	//カレンダーを表示する
	//先月の場合
	if ($month == 1) {
		$year3 = $year - 1;
		$month3 = 12;
	} else {
		$year3 = $year;
		$month3 = $month - 1;
	}

	//来月の場合
	if ($month == 12) {
		$year4 = $year + 1;
		$month4 = 1;
	} else {
		$year4 = $year;
		$month4 = $month + 1;
	}

	//カレンダーを表示するHTML
	print '<table class="blog-calendar"><tr><td colspan=7>';
	print "<center>";
	if ($data['prev']) {
		print $this->BcBaser->getLink('≪', $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/' . $year3 . '/' . $month3, null, false);
	}
	print "　" . $year . "/" . $month . "　";
	if ($data['next']) {
		print $this->BcBaser->getLink('≫', $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/' . $year4 . '/' . $month4, null, false);
	}
	print "</center>";
	print "</td></tr>";

	print '
<tr>
<th class="sunday">' . __('日') . '</th>
<th>' . __('月') . '</th>
<th>' . __('火') . '</th>
<th>' . __('水') . '</th>
<th>' . __('木') . '</th>
<th>' . __('金') . '</th>
<th class="saturday">' . __('土') . '</th>
</tr>
';

	//カレンダーの日付を作る
	for($i = 1; $i <= $num; $i++) {

		//本日の曜日を取得する
		$print_today = mktime(0, 0, 0, $month, $i, $year);
		//曜日は数値
		$w = date("w", $print_today);

		//一日目の曜日を取得する
		if ($i == 1) {
			//一日目の曜日を提示するまでを繰り返し
			print "<tr>";
			for($j = 1; $j <= $w; $j++) {
				print "<td>&nbsp;</td>";
			}

			$data = check($i, $w, $year, $month, $day, $entryDates, $this->BcBaser, $blogContent);
			print "$data";
			if ($w == 6) {
				print "</tr>";
			}
			//一日目以降の場合
		} else {
			if ($w == 0) {
				print "<tr>";
			}
			$data = check($i, $w, $year, $month, $day, $entryDates, $this->BcBaser, $blogContent);
			print "$data";
			if ($w == 6) {
				print "</tr>";
			}
		}
	}
	print "</table>";

	/**
	 * 特定の日付の場合の処理
	 */
	function check($i, $w, $year, $month, $day, $entryDates, $BcBaser, $blogContent)
	{
		if (in_array(date('Y-m-d', strtotime($year . '-' . $month . '-' . $i)), $entryDates)) {
			if (date('Y-m-d') == date('Y-m-d', strtotime($year . '-' . $month . '-' . $i))) {
				$change = '<td class="today">' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent['BlogContent']['id']) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
			} elseif ($w == 0) {
				$change = '<td class="sunday">' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent['BlogContent']['id']) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
			} elseif ($w == 6) {
				$change = '<td class="saturday">' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent['BlogContent']['id']) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
			} else {
				$change = '<td>' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent['BlogContent']['id']) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
			}
		} else {
			if (date('Y-m-d') == date('Y-m-d', strtotime($year . '-' . $month . '-' . $i))) {
				$change = '<td class="today">' . $i . '</td>';
			} else {
				$change = '<td>' . $i . '</td>';
			}
		}
		return $change;
	}

	?>
</div>
