<?php
$data = '';
if(!empty($_POST['data'])) {
	$data = unserialize(str_replace('""', '"', $_POST['data']));
	//$data = array('プレスリリース' => 'プレスリリース');
	//$data = array('ブログ' => 'ブログ', 'ページ' => 'ページ', 'メール' => 'メール');
	//var_dump(serialize($data));
	if($data !== false) {
		$data = base64_encode(serialize($data));	
	}
}
?>
<form action="./convert.php" method="POST">
	<textarea name="data" cols="60" rows="10"><?php echo $data ?></textarea>
	<br /><input type="submit" value="変換実行" />
</form>
