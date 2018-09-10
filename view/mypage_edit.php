<?php
  session_start();
  require('../dbconnect.php');
  require('../user_info.php');
  require('../function/get_categories.php');

  // ヒストリの取得
  $sql = 'SELECT * FROM `history` WHERE `user_id`=?';
  $history_data = array($login_user_info['user_id']);
  $history_stmt = $dbh->prepare($sql);
  $history_stmt->execute($history_data);
  while ($history = $history_stmt->fetch(PDO::FETCH_ASSOC)) {
    // echo $history['book_id'];
  }

  $sql = 'SELECT * FROM `books`';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();


// アンバサダーランキング配列
$ranking_users = array();

$sql = 'SELECT * FROM `users`';
$user_stmt = $dbh->prepare($sql);
$user_stmt->execute();

while ($user = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
    $user_id = $user['user_id'];
    $sql = 'SELECT * FROM `books` WHERE `user_id`=?';
    $user_book_data = array($user_id);
    $user_books_stmt = $dbh->prepare($sql);
    $user_books_stmt->execute($user_book_data);

    $ranking_user = array();
    // $user1 = array();

    $ranking_user = array(
                  'user_id' => $user['user_id'],
                  'user_name' => $user['user_name'],
                  'picture_path' => $user['picture_path'],
                  'score_total' => 0,
                  'score_finish' => 0,
                  'score_like' => 0,
                  'rank' => 0
                );

    while ($user_books = $user_books_stmt->fetch(PDO::FETCH_ASSOC)) {
        $finish_reading_id = $user_books['book_id'];
        $sql = 'SELECT COUNT(*) AS `cnt` FROM `finish_reading` WHERE book_id=?';
        $data = array($finish_reading_id);
        $finish_reading_stmt = $dbh->prepare($sql);
        $finish_reading_stmt->execute($data);
        $finish_reading = $finish_reading_stmt->fetch(PDO::FETCH_ASSOC);

        $book_mark = $user_books['book_id'];
        $sql = 'SELECT COUNT(*) AS `cnt` FROM `bookmark` WHERE book_id=?';
        $data_book_mark = array($book_mark);
        $book_mark_stmt = $dbh->prepare($sql);
        $book_mark_stmt->execute($data_book_mark);
        $book_mark = $book_mark_stmt->fetch(PDO::FETCH_ASSOC);

        // スコアを計算 読破*3 いいね*2
        $score_finish = $finish_reading['cnt'] * 3;
        $score_like = $book_mark['cnt'] * 2;
        $score_total = $score_finish + $score_like;

        $ranking_user['score_total'] = $ranking_user['score_total'] + $score_total;
        $ranking_user['score_finish'] = $ranking_user['score_finish'] + $score_finish;
        $ranking_user['score_like'] = $ranking_user['score_like'] + $score_like;
    }

    // var_dump($ranking_user);
    $ranking_users[] = $ranking_user;
}
// sort 降順指定
foreach ((array) $ranking_users as $key => $value) {
    $tmp_arr[$key] = $value['score_total'];
}
array_multisort($tmp_arr, SORT_DESC, $ranking_users);
for ($i=0; $i < count($ranking_users); $i++) {
  $ranking_users[$i]['rank'] = $i + 1;
  if ($ranking_users[$i]['user_id'] == $_SESSION['id']) {
    $_SESSION['rank'] = $ranking_users[$i]['rank'];
  }
}


// mypage_edit　ここから
  $sql = 'SELECT u.*, s.category_id FROM `users` u, `user_selected_category` s WHERE u.user_id = s.user_id';
  // $data = array($_REQUEST['tweet_id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  // 更新ボタンがおされたとき
  if (!empty($_POST)) {
    if ($_POST != '') {
      $sql = 'UPDATE `users` SET `user_name`=?, `email`=?, `password`=?, `picture_path`=? WHERE `user_id`=?';
      $data = array($_POST['user_name'], $_POST['email'], sha1($_POST['password']), $_POST['picture_path'], $_SESSION['id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

      $sql = 'UPDATE user_selected_category.*, users.user_id SET `category_id`=? WHERE user_selected_category.user_id = users.user_id';
      $data = 
      $stmt2 = $dbh->prepare($sql);
      $stmt2->execute($data);

      header('Location: mypage_edit.php');
      exit();
    }

  }
?>



<!DOCTYPE html>
<html lang="ja">
<head>
  <!-- 共通リンク -->
  <?php require('layout/common_links.php'); ?>
  <!-- Original css -->
  <link href="../assets/css/main.css" rel="stylesheet">


  <title>Bookrus-知らない本と出会う-</title>
</head>
<body>

  <?php require('layout/header.php'); ?>

  <div class="container">
    <div class="row">

    <?php require('layout/left_sidebar.php'); ?>

      <div class="col-xs-1"></div>


      <div class="col-sm-8 col-md-6 col-lg-6" style="margin-top: 30px; padding: 10px 30px;">

        <!-- ここにコードを書いてください！！ -->
        <!-- http://bootsnipp.com/snippets/featured/simple-contact-form -->
        <!-- 本の編集 -->
            <div class="form-area2">
                <form role="form" method="POST" action="">
                <br style="clear:both">
                  <h3 style="margin-bottom: 60px; text-align: center;">プロフィールの編集</h3>
                  <div class="form-group">
                    名前
                    <input type="text" class="form-control" id="name" name="name" placeholder="名前" required style="width: 250px;">
                  </div>
                  <div class="form-group">
                    メールアドレス
                    <input type="email" class="form-control" id="email" name="email" placeholder="メールアドレス" required style="width: 400px;">
                  </div>
                  <div class="form-group">
                    パスワード
                    <input type="password" class="form-control" id="password" name="password" placeholder="パスワード" required style="width: 250px;">
                  </div>
                  <div class="form-group">
                    パスワード再入力
                    <input type="password" class="form-control" id="password" name="password" placeholder="パスワード" required style="width: 250px;">
                  </div>
                  <div class="form-group">
                  <!-- <textarea class="form-control" type="textarea" id="message" placeholder="Message" maxlength="140" rows="7"></textarea> -->
                  <h3>現在の画像</h3>
                  <img src="member_picture/201703010000yoh.jpg" width="150">
                      <!-- <span class="help-block"><p id="characterLeft" class="help-block ">You have reached the limit</p></span> -->
                  <input type="file" name="picture_path" id="picture_path" placeholder="変更する画像">
                  </div>

                  <!-- http://bootsnipp.com/snippets/8A2vZ -->
                  <!-- checkbox -->
                  <!-- <form class="form-horizontal"> -->
                          <div class="form-group">
                                <!-- <label class="col-md-2 control-label" for="Checkboxes">Checkboxes</label>   -->

                                <div class="col-md-10 columns" style="padding-left: 1px;">
                                      <label class="checkbox-inline" for="Checkboxes_Apple">
                                        <input type="checkbox" name="Checkboxes" id="Checkboxes_Apple" value="Apple">
                                        現在のカテゴリ
                                      </label>
                                      <label class="checkbox-inline" for="Checkboxes_Orange">
                                        <input type="checkbox" name="Checkboxes" id="Checkboxes_Orange" value="Orange">
                                        Orange
                                      </label>
                                      <label class="checkbox-inline" for="Checkboxes_Bananas">
                                        <input type="checkbox" name="Checkboxes" id="Checkboxes_Bananas" value="Bananas">
                                        Banana
                                      </label>
                                      <label class="checkbox-inline" for="Checkboxes_Kumquats">
                                        <input type="checkbox" name="Checkboxes" id="Checkboxes_Kumquats" value="Kumquats">
                                        Kumquat
                                      </label>
                                      <span class="additional-info-wrap">
                                          <label class="checkbox-inline" for="Checkboxes_Grape">
                                            <input type="checkbox" name="Checkboxes" id="Checkboxes_Grape" value="Grape">
                                            Grape
                                          </label>
                                          <div class="additional-info hide">
                                                <input type="text" id="CheckboxesNameOfGrape" name="CheckboxesNameOfGrape" placeholder="Name of Grape" class="form-control" disabled="">
                                          </div>
                                      </span>
                                      <span class="additional-info-wrap">
                                          <label class="checkbox-inline" for="Checkboxes_Other">
                                            <input type="checkbox" name="Checkboxes" id="Checkboxes_Other" value="Other">
                                            Other
                                          </label>
                                          <div class="additional-info hide">
                                                <input type="text" id="CheckboxesOther" name="CheckboxesOther" placeholder="Describe" class="form-control" disabled="">
                                          </div>
                                      </span>
                                  </div>
                              </div>

                <button type="submit" id="submit" name="submit" class="btn btn-primary pull-right" style="margin-top: 120px">編集する</button>
                </form>

            </div>
      </div><!-- col-xs-6 閉じタグ-->

      <div class="col-xs-1"></div>

      <?php require('layout/right_sidebar.php') ?>
    </div><!-- row 閉じタグ -->
  </div><!-- container 閉じタグ -->

  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <script src="../assets/js/book_regi.js"></script>
  <script src="../assets/js/dashboard.js"></script>

</body>
</html>