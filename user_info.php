<?php
// session_start();
session_start();

// var_dump($_SESSION);
// var_dump($_SESSION);
$login_user_info = array();
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {

	// ユーザーidの取得
	$_SESSION['time'] = time();
	$sql = 'SELECT * FROM `users` WHERE `user_id`=?';
	$data = array($_SESSION['id']);
	$stmt = $dbh->prepare($sql);
	$stmt->execute($data);
	$login_user = $stmt->fetch(PDO::FETCH_ASSOC);
	$login_user_info = $login_user;

	//ユーザーが選んだカテゴリidの取得
	$sql = 'SELECT * FROM `user_selected_category` WHERE `user_id`=?';
	$data2 = array($login_user['user_id']);
	$stmt2 = $dbh->prepare($sql);
	$stmt2->execute($data2);
	$selected_categories = array();
	while ($user_selected_categories = $stmt2->fetch(PDO::FETCH_ASSOC)) {
		$selected_categories[] = $user_selected_categories['category_id'];
	    $sql = 'SELECT * FROM `categories` WHERE `category_id`=?';
	    $data3 = array($user_selected_categories['category_id']);
	    $stmt3 = $dbh->prepare($sql);
	    $stmt3->execute($data3);
	    $user_selected_category = array();
	    while($user_selected_categories = $stmt3->fetch(PDO::FETCH_ASSOC)) {
	    }
	}
	$login_user_info['selected_categories_id'] = $selected_categories;
} else {
	header('Location: view/top.php');
	exit();
}


?>