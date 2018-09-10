<?php 
if (isset($_SESSION['id'])) {
// ログイン状態の定義
// $_SESSION['id']が存在する
// $_SESSION['time']が現在の１時間前(3600秒)以内である。
// ユーザーがアクションするごとに　(ページがリロードされるごとに)　時間を更新
// $_SESSION['time'] = time();

// ログインしていればログインユーザーから情報をDBから取得
    $sql ='SELECT * FROM `users` WHERE `user_id`=?';
    $data = array($_SESSION['id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $login_user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'ログイン中';
} else {
    // ログインせずにページを訪れた場合はlogin.phpへ遷移
    header('Location: ../view/top.php');
    exit();
}
 ?>