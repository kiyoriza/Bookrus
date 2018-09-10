<?php
session_start();
require('../dbconnect.php');

// ログインチェック
if (isset($_SESSION['id'])) {
    // 削除処理
    // $tweet_id = $_GET['tweet_id'];
    $book_id = $_REQUEST['book_id'];

    // ログインしているユーザーの投稿データか？
    $sql = 'SELECT * FROM `books` WHERE `book_id` = ?';
    $data = array($book_id);

    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    // $book['member_id'] → 削除しようとしているツイートデータの投稿ユーザーIDがわかる

    if ($book['user_id'] == $_SESSION['id']) {
        $sql = 'DELETE FROM `books` WHERE `book_id` = ?';
        $data = array($book_id);

        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);
    }

}

header('Location: ../view/mypage.php');
exit();
?>
