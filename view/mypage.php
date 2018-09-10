<?php
session_start();
require('../dbconnect.php');
require('../user_info.php');
require('../function/login_check.php');
require('../function/get_categories.php');
require('../function/history_func.php');

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

// mypage　ここから
// 本表示
// 登録した本
$sql = 'SELECT * FROM `books` WHERE user_id=?';
$data = array($login_user_info['user_id']);
$stmt4 = $dbh->prepare($sql);
$stmt4->execute($data);
$toroku_books = array();
while ($toroku_book = $stmt4->fetch(PDO::FETCH_ASSOC)){
    $finish_reading_id = $toroku_book['book_id'];
    $sql = 'SELECT COUNT(*) AS `cnt` FROM `finish_reading` WHERE book_id=?';
    $data = array($finish_reading_id);
    $finish_reading_stmt = $dbh->prepare($sql);
    $finish_reading_stmt->execute($data);
    $finish_reading = $finish_reading_stmt->fetch(PDO::FETCH_ASSOC);

    $book_mark = $toroku_book['book_id'];
    $sql = 'SELECT COUNT(*) AS `cnt` FROM `bookmark` WHERE book_id=?';
    $data_book_mark = array($book_mark);
    $book_mark_stmt = $dbh->prepare($sql);
    $book_mark_stmt->execute($data_book_mark);
    $book_mark = $book_mark_stmt->fetch(PDO::FETCH_ASSOC);

    $toroku_books[] =  array(
                 'book_id' => $toroku_book['book_id'],
                 'title' => $toroku_book['title'],
                 'picture_url_API' => $toroku_book['picture_url_API'],
                 'author_API' => $toroku_book['author_API'],
                 'phrase' => $toroku_book['phrase'],
                 'category_id' => $toroku_book['category_id'],
                 'score_finish' => $finish_reading['cnt'],
                 'score_like' => $book_mark['cnt']
               );
}

require('../function/bookmark_func.php');
// var_dump($bookmarks);

// // 読破の本
require('../function/finish_reading_func.php');
     // var_dump($Dokuha_books);

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

      <div class="col-sm-10 col-md-8 col-lg-8" style="margin-top: 70px; padding-left: 90px; padding-right: 70px; ">

        <!-- ここにコードを書いてください！！ -->
        <!-- http://bootsnipp.com/snippets/featured/simple-carousel -->
        <!-- 本表示 -->
        <!-- <div class="container"> -->
            <!-- <div class="row"> -->
            <!-- <div class="col-md-12"> -->
        <div id="Carousel" class="carousel slide">

            <h2>登録した本</h2>
            <?php
              // 下記構造にあう配列を作成
              $c = count($toroku_books);
              $toroku_page = array();
              $toroku_page = array_chunk($toroku_books, 4);
              $page = count($toroku_page);
              // var_dump($toroku_page[0]);
            ?>

            <ol class="carousel-indicators">
                <?php for($i=0; $i < $page; $i++):?>
                    <?php if ($i == 0): ?>
                      <li data-target="#Carousel" data-slide-to="<?php echo $i;?>" class="active"></li>
                    <?php else: ?>
                      <li data-target="#Carousel" data-slide-to="<?php echo $i;?>" class=""></li>
                    <?php endif; ?>
                <?php endfor; ?>
            </ol>

            <!-- Carousel items -->
            <div class="carousel-inner">


              <?php for ($i=0; $i < $page; $i++):?>
                  <?php if ($i == 0): ?>
                    <div class="item active">
                  <?php else:?>
                    <div class="item">
                  <?php endif; ?>

                      <div class="row">
                        <?php foreach ($toroku_page[$i] as $toroku_book): ?>
                            <div class="col-xs-3">
                              <a href="book_detail.php?book_id=<?php echo $toroku_book['book_id']; ?>" class="thumbnail">
                              <img src="<?php echo $toroku_book['picture_url_API']; ?>" alt="Image" style="max-width:100%;">
                              </a>
                              <?php echo $toroku_book['title']; ?>
                              <a href="#" class="edit2">編集</a>
                              <br><?php echo $toroku_book['author_API']; ?>
                              <a href="#" class="delete2">削除</a>
                              <br><?php echo $toroku_book['phrase']; ?>
                              <br>
                              <button type="button" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok"></i></button><?php echo $toroku_book['score_finish']; ?>
                              <button type="button" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart"></i></button><?php echo $toroku_book['score_like']; ?>
                            </div>
                        <?php endforeach; ?>
                      </div><!--.row-->
                    </div><!--.item-->
              <?php endfor; ?>
            </div><!--.carousel-inner-->
          <a data-slide="prev" href="#Carousel" class="left carousel-control">‹</a>
          <a data-slide="next" href="#Carousel" class="right carousel-control">›</a>
        </div><!--.Carousel-->


        <!-- 2行目 -->
        <div id="Carousel2" class="carousel slide">

            <h2>ブックマーク</h2>
            <?php
                // 下記構造にあう配列を作成
                $c = count($bookmarks);
                $bookmark_page = array();
                $bookmark_page = array_chunk($bookmarks, 4);
                $page = count($bookmark_page);
                // var_dump($bookmark_page);
            ?>
            <ol class="carousel-indicators">
            <?php for($i=0; $i < $page; $i++):?>
                <?php if ($i == 0): ?>
                  <li data-target="#Carousel" data-slide-to="<?php echo $i;?>" class="active"></li>
                <?php else: ?>
                  <li data-target="#Carousel" data-slide-to="<?php echo $i;?>" class=""></li>
                <?php endif; ?>
            <?php endfor; ?>
            </ol>

            <!-- Carousel items -->
            <div class="carousel-inner">

                <?php for ($i=0; $i < $page; $i++):?>
                    <?php if ($i == 0): ?>
                      <div class="item active">
                    <?php else:?>
                      <div class="item">
                    <?php endif; ?>
                        <div class="row">
                          <?php foreach ($bookmark_page[$i] as $bookmark): ?>
                              <div class="col-xs-3">
                                <a href="book_detail.php?book_id=<?php echo $bookmark['book_id']; ?>" class="thumbnail">
                                  <img src="<?php echo $bookmark['picture_url_API']; ?>" alt="Image" style="max-width:100%;">
                                </a>
                                <?php echo $bookmark['title']; ?>
                                <br><?php echo $bookmark['author_API']; ?>
                                <br><?php echo $bookmark['phrase']; ?>
                                <br>
                                <a href="#" style="text-decoration: none; color: black;">ユーザー名</a>
                                <br>
                                <button type="button" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok"></i></button><?php echo $bookmark['score_finish']; ?>
                                <button type="button" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart"></i></button><?php echo $bookmark['score_like']; ?>
                              </div>
                          <?php endforeach; ?>
                        </div><!--.row-->
                      </div><!--.item-->
                <?php endfor; ?>

            </div><!--.carousel-inner-->
          <a data-slide="prev" href="#Carousel2" class="left carousel-control">‹</a>
          <a data-slide="next" href="#Carousel2" class="right carousel-control">›</a>
        </div><!--.Carousel-->

        <!-- 3行目 -->
        <div id="Carousel3" class="carousel slide">

            <h2>読破数</h2>
            <?php
                // 下記構造にあう配列を作成
                $c = count($Dokuha_books);
                $Dokuha_page = array();
                $Dokuha_page = array_chunk($Dokuha_books, 4);
                $page = count($Dokuha_page);
            // var_dump($Dokuha_page);
            ?>
            <ol class="carousel-indicators">
              <?php for($i=0; $i < $page; $i++):?>
                  <?php if ($i == 0): ?>
                    <li data-target="#Carousel" data-slide-to="<?php echo $i;?>" class="active"></li>
                  <?php else: ?>
                    <li data-target="#Carousel" data-slide-to="<?php echo $i;?>" class=""></li>
                  <?php endif; ?>
              <?php endfor; ?>
              </ol>

            <!-- Carousel items -->
            <div class="carousel-inner">

            <?php for ($i=0; $i < $page; $i++):?>
                <?php if ($i == 0): ?>
                  <div class="item active">
                <?php else:?>
                  <div class="item">
                <?php endif; ?>
                  <div class="row">
                    <?php foreach ($Dokuha_page[$i] as $Dokuha_book): ?>
                      <div class="col-xs-3">
                        <a href="book_detail.php?book_id=<?php echo $Dokuha_book['book_id']; ?>" class="thumbnail">
                          <img src="<?php echo $Dokuha_book['picture_url_API']; ?>" alt="Image" style="max-width:100%;">
                        </a>
                        <?php echo $Dokuha_book['title']; ?>
                        <br><?php echo $Dokuha_book['author_API']; ?>
                        <br><?php echo $Dokuha_book['phrase']; ?>
                        <br>
                        <a href="#" style="text-decoration: none; color: black;">ユーザー名</a>
                        <br>
                        <button type="button" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok"></i></button><?php echo $Dokuha_book['score_finish']; ?>
                        <button type="button" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart"></i></button><?php echo $Dokuha_book['score_like']; ?>
                      </div>
                    <?php endforeach; ?>
                  </div><!--.row-->
                </div><!--.item-->
            <?php endfor; ?>
            </div><!--.carousel-inner-->
          <a data-slide="prev" href="#Carousel3" class="left carousel-control">‹</a>
          <a data-slide="next" href="#Carousel3" class="right carousel-control">›</a>
        </div><!--.Carousel-->

      </div><!-- col-xs-8 閉じタグ-->

      <?php require('layout/right_sidebar.php'); ?>

    </div><!-- row 閉じタグ -->
  </div><!-- container 閉じタグ -->

  <script src="../assets/js/jquery.js"></script>
  <script src="../assets/js/jquery-migrate.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <script src="../assets/js/book_regi.js"></script>
  <!-- <script src="../assets/js/dashboard.js"></script> -->

</body>
</html>