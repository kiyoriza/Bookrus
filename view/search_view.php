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

 // var_dump($ranking_users);


// search_view　ここから
// サイト内検索
$search_word = '';
$count = '--';
if (isset($_GET['search_word']) && !empty($_GET['search_word'])) {

  $search_word = $_GET['search_word'];
  $sql = 'SELECT b.*, u.user_id, u.user_name, u.picture_path FROM `books` AS b, `users` AS u WHERE b.user_id = u.user_id AND b.title LIKE ? ORDER BY b.created DESC';
  $w = '%' . $_GET['search_word'] . '%';
  $data = array($w);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  // 検索結果カウント用
  $sql = 'SELECT COUNT(*) AS `cnt` FROM `books` AS b, `users` AS u WHERE b.user_id = u.user_id AND b.title LIKE ? ORDER BY b.created DESC';
  $data = array($w);
  $count_stmt = $dbh->prepare($sql);
  $count_stmt->execute($data);
  $count = $count_stmt->fetch(PDO::FETCH_ASSOC);
} else {
  // dashboard.phpへ遷移
  header('Location: dashboard_category.php');
  exit();
}

// ページ閲覧制限
// if (empty($_REQUEST['book_id'])) {
//     // パラメータにbook_idが存在しなかければdashbord.phpへ遷移
//     header('Location: dashbord.php');
//     exit();
// }



// // 本データ一件取得
// $sql = 'SELECT u.user_name, u.picture_path, b.* FROM `users` u, `books` b WHERE u.user_id=b.user_id AND b.book_id=?';
// $data = array($book_id);
// $stmt = $dbh->prepare($sql);
// $stmt->execute($data);

// いいね！のロジック実装
if (!empty($_POST['like'])) {
    $book_mark_id = $_POST['book_id']; // $_REQUEST['book_id']
    if ($_POST['like'] == 'like') {
    // いいね！データの登録
        $sql = 'INSERT INTO `bookmark` SET `user_id`=?, `book_id`=?, `created`=NOW()';
        $data = array($_SESSION['id'], $book_mark_id);
        $like_stmt = $dbh->prepare($sql);
        $like_stmt->execute($data);
        header('Location: search_view.php?book_id='.$book_mark_id);
        exit();
    }  else  {
        // いいね！データの削除
        $sql = 'DELETE FROM `bookmark` WHERE `user_id`=? AND `book_id`=?';
        $data = array($_SESSION['id'], $book_mark_id);
        $like_stmt = $dbh->prepare($sql);
        $like_stmt->execute($data);
        header('Location: search_view.php?book_id='.$book_mark_id);
        exit();
    }
} elseif (!empty($_POST['finish_reading'])) {// 読破ボタンのロジック実装
    $finish_reading_id = $_POST['book_id'];
    if ($_POST['finish_reading'] == 'finish_reading') {
        // 読破データの登録
        $sql = 'INSERT INTO `finish_reading` SET `user_id`=?, `book_id`=?, `created`=NOW()';
        $data = array($_SESSION['id'], $finish_reading_id);
        $finish_reading_stmt = $dbh->prepare($sql);
        $finish_reading_stmt->execute($data);
        header('Location: search_view.php?book_id='.$finish_reading_id);
        exit();
    }  else  {
        // 読破データの削除
        $sql = 'DELETE FROM `finish_reading` WHERE `user_id`=? AND `book_id`=?';
        $data = array($_SESSION['id'], $finish_reading_id);
        $finish_reading_stmt = $dbh->prepare($sql);
        $finish_reading_stmt->execute($data);
        header('Location: search_view.php?book_id='.$finish_reading_id);
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="../assets/css/bootstrap.css" rel="stylesheet">
  <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <!-- Original css -->
  <link href="../assets/css/main.css" rel="stylesheet">
  <!-- header -->
  <link href="../assets/css/navibar.css" rel="stylesheet">
  <link href="../assets/css/search.css" rel="stylesheet">
  <link href="../assets/css/logout.css" rel="stylesheet">
  <!-- left-sidebar -->
  <link href="../assets/css/book_register.css" rel="stylesheet">
  <link href="../assets/css/ranking.css" rel="stylesheet">
  <link href="../assets/css/your_ranking.css" rel="stylesheet">
  <link href="../assets/css/category.css" rel="stylesheet">
  <!-- right-sidebar -->
  <link href="../assets/css/recommend.css" rel="stylesheet">
  <link href="../assets/css/profile.css" rel="stylesheet">
  <!-- main -->
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <link href="../assets/css/action_bottom.css" rel="stylesheet">
  <!-- 検索結果 -->
  <!-- ユーザーアイコンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/user-detail -->
  <link href="../assets/css/book_user.css" rel="stylesheet">
  <!-- 本の詳細のcss -->
  <!-- http://bootsnipp.com/snippets/xO1M -->
  <!-- <link href="../assets/css/book_detail.css" rel="stylesheet"> -->
  <!-- アクションボタンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
  <link href="../assets/css/book_action_button.css" rel="stylesheet">
  <!-- 編集ボタンと削除ボタンのcss -->
  <!-- http://bootsnipp.com/snippets/A2Mx5 -->
  <link href="../assets/css/book_detail_button.css" rel="stylesheet">

  <title>Bookrus-知らない本と出会う-</title>
  <style type="text/css">
    .action-inline {
      display: inline;
    }
  </style>
</head>
<body>
  <?php require('layout/header.php'); ?>

  <div class="container">
    <div class="row">
      <?php require('layout/left_sidebar.php'); ?>
      
      <div class="col-sm-10 col-md-8 col-lg-8" style="margin-top: 70px; padding-left: 90px; padding-right: 70px; ">


          <!-- ここにコードを書いてください！！ -->
          <!-- 下から検索結果のコード -->
          <div class="wrapper_search">
              <h3>検索結果  <?php echo $count['cnt']; ?> 件</h3>
              <br>
            <div class="well well-sm" id="hoge">
                <div class="row search_view">
                  <?php while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                    <?php
                        $finish_reading_id = $book['book_id'];
                        $sql = 'SELECT COUNT(*) AS `cnt` FROM `finish_reading` WHERE book_id=?';
                        $data = array($finish_reading_id);
                        $finish_reading_stmt = $dbh->prepare($sql);
                        $finish_reading_stmt->execute($data);
                        $finish_reading = $finish_reading_stmt->fetch(PDO::FETCH_ASSOC);

                        $book_mark_id = $book['book_id'];
                        $sql = 'SELECT COUNT(*) AS `cnt` FROM `bookmark` WHERE book_id=?';
                        $data_book_mark = array($book_mark_id);
                        $book_mark_stmt = $dbh->prepare($sql);
                        $book_mark_stmt->execute($data_book_mark);
                        $book_mark = $book_mark_stmt->fetch(PDO::FETCH_ASSOC);
                     ?>
                    <div class="col-xs-4">
                      <a href="book_detail.php?book_id=<?php echo $book['book_id']; ?>">
                        <img src="<?php echo $book['picture_url_API']; ?>" alt="" class="img-rounded img-responsive" >
                      </a>
                      <br>
                      <?php 
                          // いいね！済み判定
                          $sql = 'SELECT * FROM `bookmark` WHERE `user_id`=? AND `book_id`=?';
                          $data = array($_SESSION['id'], $book['book_id']);
                          $is_like_stmt = $dbh->prepare($sql);
                          $is_like_stmt->execute($data);


                          // 読破済み判定
                          $sql = 'SELECT * FROM `finish_reading` WHERE `user_id`=? AND `book_id`=?';
                          $data = array($_SESSION['id'], $book['book_id']);
                          $is_finish_reading_stmt = $dbh->prepare($sql);
                          $is_finish_reading_stmt->execute($data);
                       ?>
                      <!-- 読破ボタン -->
                      <form action="" method="POST" class="action-inline">
                      <?php if($is_finish_reading = $is_finish_reading_stmt->fetch(PDO::FETCH_ASSOC)):?>
                        <!-- 読破データがある時、読破を取り消すボタンを表示 -->
                        <input type="hidden" name="finish_reading" value="unfinish_reading">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <button type="submit" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i></button>
                        <font class="number"><?php echo $finish_reading['cnt']; ?></font>
                      <?php else:?>
                        <!-- 読破データがないとき、読破ボタンを表示 -->
                        <input type="hidden" name="finish_reading" value="finish_reading">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <button type="submit" style="background: #FFF; border: 3px solid #5bc0de; color: #5bc0de;" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i></button>
                        <font class="number"><?php echo $finish_reading['cnt']; ?></font>
                      <?php endif; ?>
                      </form>
                      <!-- いいねボタン -->
                      <form action="" method="POST" class="action-inline">
                      <?php if($is_like = $is_like_stmt->fetch(PDO::FETCH_ASSOC)):?>
                        <!-- いいね！データがある時、いいね！を取り消すボタンを表示 -->
                        <input type="hidden" name="like" value="unlike">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart" aria-hidden="true"></i></button>
                        <font class="number"><?php echo $book_mark['cnt']; ?></font>
                      <?php else:?>
                        <!-- いいね！データがないとき、いいね！ボタンを表示 -->
                        <input type="hidden" name="like" value="like">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <button type="submit" style="background: #FFF; border: 3px solid #d9534f; color: #d9534f;" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart" aria-hidden="true"></i></button>
                        <font class="number"><?php echo $book_mark['cnt']; ?></font>
                      <?php endif; ?>
                      </form>
                    <br>
                    </div><!-- [end].col-xs-4 -->
                    <div class="col-xs-8 book_detail">
                      <a href="book_detail.php?book_id=<?php echo $book['book_id']; ?>">
                        <div class="title"><h3><?php echo $book['title']; ?></h3></div>
                        <div class="author"><h4>（著）<?php echo $book['author_API'];?> </h4></div>
                        <div class="date"> <h4>登録日:<?php echo $book['created']; ?></h4></div>
                        <div class="phrase"><h4>フレーズ :<?php echo $book['phrase']; ?></h4></div>
                      </a>
                      <!-- ユーザーアイコン -->
                      <div class="media row">
                        <div class="col-xs-3">
                          <a class="pull-left" href="mypage.php?user_id=<?php echo $book['user_id']; ?>">
                            <img class="media-object dp img-circle" src="member_picture/<?php echo $book['picture_path']; ?>" style="width: 100px;height:100px;">
                          </a><!-- [end].pull-left -->
                        </div><!--[end].col-xs-3-->
                        <div class="col-xs-9">
                          <a href="mypage.php?user_id=<?php echo $book['user_id']; ?>">
                          <div class="media-body">
                            <h4 class="media-heading"><?php echo $book['user_name']; ?></h4>
                            <?php foreach($ranking_users as $user): ?>
                              <?php if ($user['user_id'] == $book['user_id']): ?>
                                <h5 class="ambassador_ranking">アンバサダーランキング <?php echo $user['rank']; ?> 位</h5>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </div><!--[end].media-body -->
                          </a>
                        </div><!--[end].col-xs-9-->
                      </div><!--[end].media -->
                    </div><!-- [end].col-xs-8 -->
                  <?php endwhile; ?>
                </div><!-- [end].row search_view -->
            </div><!--[end].well well-sm #hoge -->
          </div><!--[end].wrapper_search-->
          <!-- ここまでが本の検索ページ -->
      </div><!-- col-sm-10 col-md-8 col-lg-8 閉じタグ-->


    <?php require('layout/right_sidebar.php'); ?>
    </div><!-- row 閉じタグ -->
  </div><!-- container 閉じタグ -->

  <script src="../assets/js/jquery.js"></script>
  <script src="../assets/js/jquery-migrate.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <script src="../assets/js/book_regi.js"></script>
  <script src="../assets/js/dashboard.js"></script>

</body>
</html>