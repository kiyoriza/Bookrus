<?php 
// ヒストリの取得
$sql = 'SELECT * FROM `history` WHERE `user_id`=?';
$history_data = array($login_user_info['user_id']);
$history_stmt = $dbh->prepare($sql);
$history_stmt->execute($history_data);
while ($history = $history_stmt->fetch(PDO::FETCH_ASSOC)) {
// echo $history['book_id'];
}
 ?>