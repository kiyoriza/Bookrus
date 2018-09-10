<?php
require('../dbconnect.php');
require('../user_info.php');
session_start();
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


// book_detail ここから
// ページ閲覧制限
if (empty($_REQUEST['book_id'])) {
    // パラメータにbook_idが存在しなかければdashbord.phpへ遷移
    header('Location: dashboard.php');
    exit();
}

$book_id = $_REQUEST['book_id'];

// 本データ一件取得
$sql = 'SELECT u.user_name, u.picture_path, b.* FROM `users` u, `books` b WHERE u.user_id=b.user_id AND b.book_id=?';
$data = array($book_id);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// いいね！のロジック実装
if (!empty($_POST['like'])) {
    if ($_POST['like'] == 'like') {
    // いいね！データの登録
        $sql = 'INSERT INTO `bookmark` SET `user_id`=?, `book_id`=?, `created`=NOW()';
        $data = array($_SESSION['id'], $book_id);
        $like_stmt = $dbh->prepare($sql);
        $like_stmt->execute($data);
        header('Location: book_detail.php?book_id='.$book_id);
        exit();

    }  else  {
        // いいね！データの削除
        $sql = 'DELETE FROM `bookmark` WHERE `user_id`=? AND `book_id`=?';
        $data = array($_SESSION['id'], $book_id);
        $like_stmt = $dbh->prepare($sql);
        $like_stmt->execute($data);
        header('Location: book_detail.php?book_id='.$book_id);
        exit();
    }
} elseif (!empty($_POST['finish_reading'])) {// 読破ボタンのロジック実装
    if ($_POST['finish_reading'] == 'finish_reading') {
        // 読破データの登録
        $sql = 'INSERT INTO `finish_reading` SET `user_id`=?, `book_id`=?, `created`=NOW()';
        $data = array($_SESSION['id'], $book_id);
        $finish_reading_stmt = $dbh->prepare($sql);
        $finish_reading_stmt->execute($data);
        header('Location: book_detail.php?book_id='.$book_id);
        exit();
    }  else  {
        // 読破データの削除
        $sql = 'DELETE FROM `finish_reading` WHERE `user_id`=? AND `book_id`=?';
        $data = array($_SESSION['id'], $book_id);
        $finish_reading_stmt = $dbh->prepare($sql);
        $finish_reading_stmt->execute($data);
        header('Location: book_detail.php?book_id='.$book_id);
        exit();
    }
}

// いいね！済み判定
$sql = 'SELECT * FROM `bookmark` WHERE `user_id`=? AND `book_id`=?';
$data = array($_SESSION['id'], $book_id);
$is_like_stmt = $dbh->prepare($sql);
$is_like_stmt->execute($data);
// いいね！数のカウント
$sql = 'SELECT COUNT(*) AS count_like FROM `bookmark` WHERE `book_id`=?';
$data = array($book_id);
$count_like_stmt = $dbh->prepare($sql);
$count_like_stmt->execute($data);
$count1 = 0;
if ($count_like = $count_like_stmt->fetch(PDO::FETCH_ASSOC)) {
    // いいね！数を変数に保持
    $count1 = $count_like['count_like'];
}

// 読破済み判定
$sql = 'SELECT * FROM `finish_reading` WHERE `user_id`=? AND `book_id`=?';
$data = array($_SESSION['id'], $book_id);
$is_finish_reading_stmt = $dbh->prepare($sql);
$is_finish_reading_stmt->execute($data);
// 読破数のカウント
$sql = 'SELECT COUNT(*) AS count_finish_reading FROM `finish_reading` WHERE `book_id`=?';
$data = array($book_id);
$count_finish_reading_stmt = $dbh->prepare($sql);
$count_finish_reading_stmt->execute($data);
$count2 = 0;
if ($count_finish_reading = $count_finish_reading_stmt->fetch(PDO::FETCH_ASSOC)) {
    // 読破数を変数に保持
    $count2 = $count_finish_reading['count_finish_reading'];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <!-- 共通リンク -->
  <?php require('layout/common_links.php'); ?>
  <!-- dashboaed css -->
  <link href="../assets/css/main.css" rel="stylesheet">
  <!-- ユーザーアイコンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/user-detail -->
  <link href="../assets/css/book_user.css" rel="stylesheet">
  <!-- アクションボタンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
  <link href="../assets/css/book_action_button.css" rel="stylesheet">
  <!-- 編集ボタンと削除ボタンのcss -->
  <!-- http://bootsnipp.com/snippets/A2Mx5 -->
  <link href="../assets/css/book_detail_button.css" rel="stylesheet">

  <title>Bookrus-知らない本と出会う-</title>
</head>
<body>
  <?php require('layout/header.php') ?>

  <div class="container">
    <div class="row">

      <?php require('layout/left_sidebar.php') ?>

      <div class="col-sm-10 col-md-8 col-lg-8" style="margin-top: 70px; padding-left: 90px; padding-right: 70px; ">
        <!-- ここにコードを書いてください！！ -->
        <!-- 下から本の詳細 -->
        <!-- ユーザーアイコン -->
        <?php if (!empty($book['book_id'])): ?>
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object dp img-circle" src="../member_picture/<?php echo $book['picture_path']; ?>" style="width: 100px;height:100px;">
            </a>
            <div class="media-body">
              <br>
              <h3 class="media-heading">ユーザー名: <?php echo $book['user_name']; ?> </h3>
              <h4 class="ambassador_ranking"><!-- アンバサダーランキング --></h4>
              <hr style="margin:8px auto">
            </div>
          </div>
          <!-- 本の詳細 -->
          <div class="container">
            <div class="well well-sm" id="hoge">
              <div class="row">
                <div class="col-sm-4 col-md-4">
                  <img src="<?php echo $book['picture_url_API'] ?>" alt="" class="img-rounded img-responsive" width="250">
                </div>
                <div class="col-sm-8 col-md-8">
                　<!-- タイトル -->
                  <div class="title"><h2><?php echo $book['title']; ?></h2></div>
                  <!-- 著者名 -->
                  <div class="author">（著）<?php echo $book['author_API']; ?> </div>
                  <!-- フレーズ -->
                  <div class="phrase"> フレーズ :<?php echo $book['phrase']; ?> </div>
                  <!-- 登録日 -->
                  <div class="date"> 登録日：<?php echo $book['created']; ?></div>
                </div>
            <div class="col-sm-12 col-md-12">
              <br>
              <!-- 読破ボタン -->
              <form action="" method="POST" class="action-inline">
              <?php if($is_finish_reading = $is_finish_reading_stmt->fetch(PDO::FETCH_ASSOC)):?>
                <!-- 読破データがある時、読破を取り消すボタンを表示 -->
                <input type="hidden" name="finish_reading" value="unfinish_reading">
                <button type="submit" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i></button>
                <font class="number"><?php echo $count2; ?></font>
              <?php else:?>
                <!-- 読破データがないとき、読破ボタンを表示 -->
                <input type="hidden" name="finish_reading" value="finish_reading">
                <button type="submit" style="background: #FFF; border: 3px solid #5bc0de; color: #5bc0de;" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i></button>
                <font class="number"><?php echo $count2; ?></font>
              <?php endif; ?>
              </form>
              <!-- いいねボタン -->
              <form action="" method="POST" class="action-inline">
              <?php if($is_like = $is_like_stmt->fetch(PDO::FETCH_ASSOC)):?>
                <!-- いいね！データがある時、いいね！を取り消すボタンを表示 -->
                <input type="hidden" name="like" value="unlike">
                <button type="submit" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart" aria-hidden="true"></i></button>
                <font class="number"><?php echo $count1; ?></font>
              <?php else:?>
                <!-- いいね！データがないとき、いいね！ボタンを表示 -->
                <input type="hidden" name="like" value="like">
                <button type="submit" style="background: #FFF; border: 3px solid #d9534f; color: #d9534f;" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart" aria-hidden="true"></i></button>
                <font class="number"><?php echo $count1; ?></font>
              <?php endif; ?>
              </form>
            <br><br>
        <!-- 編集ボタンと削除ボタン -->
        <?php if ($book['user_id'] == $_SESSION['id']): ?>
          <a href="book_edit.php?book_id=<?php echo $book['book_id'] ?>" class="btn btn-primary a-btn-slide-text">
              <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
              <span><strong>編集</strong></span>
          </a>
          <a href="delete.php?book_id=<?php echo $book['book_id'] ?>" class="btn btn-danger a-btn-slide-text">
            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            <span><strong>削除</strong></span>
          </a>
          </div>
          <br><br>
        <?php endif; ?>
        <?php else: ?>
          <div class="panel panel-default">
            <div class="panel-body">
              <h4>その本の情報は登録されていません。</h4>
              <br>
              <br>
              <!-- noimage画像など -->
              <div class="col-lg-12 col-md-12">
                <img src="../assets/img/no_image.jpg" width="200px" height="300px" alt="" align="left" class="space">
                <br>
                <div><h2>NO TITLE</h2></div>
                <div class="author">NO AUTHOR</div>
              </div>
            </div>
          </div>
        <?php endif;?>
        
        <!-- ここまでが本の詳細ページ -->

      </div><!-- col-xs-8 閉じタグ-->
      
      <?php require('layout/right_sidebar.php'); ?>
    </div><!-- row 閉じタグ -->
  </div><!-- container 閉じタグ -->


  <script src="../assets/js/jquery.js"></script>
  <script src="../assets/js/jquery-migrate.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <!-- ダッシュボードのjs -->
  <script src="../assets/js/book_regi.js"></script>
  <script src="../assets/js/dashboard.js"></script>


</body>
</html>