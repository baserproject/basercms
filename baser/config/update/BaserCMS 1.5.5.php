<?php
$User = ClassRegistry::init('User');
$db = ConnectionManager::getDataSource('baser');

/* authority_group を 1 (admin)に書き換え */
$users = $User->find('all');
foreach($users as $user){
	$user['User']['authority_group']='1';
	$User->save($user);
}

/* authority_group を user_group_id にカラム名変更 */
switch (str_replace('_ex','',$db->config['driver'])) {
case 'mysql':
	$userGroupIdCol = array('type'=>'INT','length'=>4);
	break;
case 'postgres':
	$userGroupIdCol = array('type'=>'INT4');
	break;
case 'sqlite':
case 'sqlite3':
	$userGroupIdCol = array('type'=>'INTEGER');
case 'csv':
	$userGroupIdCol = null;
	break;
}
$db->editColumn($User,'authority_group','user_group_id',$userGroupIdCol);


$updateMessage[] = '';
?>