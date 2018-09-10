<?php
session_start();


// セッションの中身を空の配列で上書き
$_SESSION = array();


// 完全にセッションを削除するためのテンプレ
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), time() -42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}


// セッションを完全に破棄
session_destroy();

header('Location: ../view/top.php');
exit();





?>