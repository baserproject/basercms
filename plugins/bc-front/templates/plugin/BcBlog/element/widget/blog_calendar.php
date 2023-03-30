<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link      https://basercms.net baserCMS Project
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログカレンダー
 * 呼出箇所：ウィジェット
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 * @checked
 * @noTodo
 * @unitTest
 */
if (isset($blogContent)) {
  $id = $blogContent->id;
} else {
  $id = $blog_content_id;
}
if (empty($year)) $year = '';
if (empty($month)) $month = '';
$data = $this->Blog->getViewVarsForBlogCalendarWidget($id, $year, $month);
$blogContent = $data['blogContent'];
$entryDates = $data['entryDates'];
?>
<?php // TODO コード整理する事  ?>

<div class="bs-widget bs-widget-blog-calendar bs-widget-blog-calendar-<?php echo $id ?> bs-blog-widget">
  <?php if ($name && $use_title): ?>
    <h2 class="bs-widget-head"><?php echo $name ?></h2>
  <?php endif ?>
  <?php
  //本日の日付を取得する
  $time = time();

  //各日付をセットする
  $year = date("Y", $time);
  $month = date("n", $time);
  $day = date("j", $time);

  //GETにきた年月をチェックする
  if ($this->getRequest()->getParam('pass.0') === 'date') {
    $year2 = h($this->getRequest()->getParam('pass.1'));
    $month2 = h($this->getRequest()->getParam('pass.2'));
    $day2 = h($this->getRequest()->getParam('pass.3'));
  } else {
    $year2 = '';
    $month2 = '';
    $day2 = '';
  }

  //先月、来月をクリックした場合の処理
  if ($year2 !== '' || $month2 !== '' || $day2 !== '') {
    if ($year2 !== '') $year = $year2;
    if ($month2 !== '') $month = $month2;
    if ($day2 !== '') {
      $day = $day2;
    } else {
      $day = 1;
    }
    $time = mktime(0, 0, 0, $month, $day, $year);
  }

  //今月の日付の数
  $num = date("t", $time);
  //曜日を取得するために時間をセット
  $today = mktime(0, 0, 0, $month, $day, $year);
  //曜日の配列
  $date = [__d('baser_core', '日'), __d('baser_core', '月'), __d('baser_core', '火'), __d('baser_core', '水'), __d('baser_core', '木'), __d('baser_core', '金'), __d('baser_core', '土')];

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
  echo '<table class="bs-widget-body"><tr><td colspan=7>';
  if ($data['prev']) {
    echo $this->BcBaser->getLink('≪', $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/' . $year3 . '/' . $month3, null, false);
  }
  echo "　" . $year . "/" . $month . "　";
  if ($data['next']) {
    echo $this->BcBaser->getLink('≫', $this->BcBaser->getBlogContentsUrl($id) . 'archives/date/' . $year4 . '/' . $month4, null, false);
  }
  echo "</td></tr>";

  echo '
<tr>
<th class="sunday">' . __d('baser_core', '日') . '</th>
<th>' . __d('baser_core', '月') . '</th>
<th>' . __d('baser_core', '火') . '</th>
<th>' . __d('baser_core', '水') . '</th>
<th>' . __d('baser_core', '木') . '</th>
<th>' . __d('baser_core', '金') . '</th>
<th class="saturday">' . __d('baser_core', '土') . '</th>
</tr>
';

  //カレンダーの日付を作る
  for($i = 1; $i <= $num; $i++) {

    //本日の曜日を取得する
    $printToday = mktime(0, 0, 0, $month, $i, $year);
    //曜日は数値
    $w = date("w", $printToday);

    //一日目の曜日を取得する
    if ($i == 1) {
      //一日目の曜日を提示するまでを繰り返し
      echo "<tr>";
      for($j = 1; $j <= $w; $j++) {
        echo "<td>&nbsp;</td>";
      }

      $data = check($i, $w, $year, $month, $entryDates, $this->BcBaser, $blogContent);
      echo "$data";
      if ($w == 6) {
        echo "</tr>";
      }
      //一日目以降の場合
    } else {
      if ($w == 0) {
        echo "<tr>";
      }
      $data = check($i, $w, $year, $month, $entryDates, $this->BcBaser, $blogContent);
      echo "$data";
      if ($w == 6) {
        echo "</tr>";
      }
    }
  }
  echo "</table>";

  /**
   * 特定の日付の場合の処理
   */
  function check($i, $w, $year, $month, $entryDates, $BcBaser, $blogContent)
  {
    if (in_array(date('Y-m-d', strtotime($year . '-' . $month . '-' . $i)), $entryDates)) {
      if (date('Y-m-d') == date('Y-m-d', strtotime($year . '-' . $month . '-' . $i))) {
        $change = '<td class="today">' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent->id) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
      } elseif ($w == 0) {
        $change = '<td class="sunday">' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent->id) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
      } elseif ($w == 6) {
        $change = '<td class="saturday">' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent->id) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
      } else {
        $change = '<td>' . $BcBaser->getLink($i, $BcBaser->getBlogContentsUrl($blogContent->id) . 'archives/date/' . $year . '/' . $month . '/' . $i, null, false) . '</td>';
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
