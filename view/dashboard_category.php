<?php 
require('../dbconnect.php');
require('../user_info.php');
session_start();

$_SESSION['category_id'] = $_GET['category_id'];
$_SESSION['user_id'] = $login_user['user_id'];
$_SESSION['user_name'] = 'you';
$sql = 'SELECT * FROM `books` WHERE user_id!=? AND category_id=?';
$data = array($_SESSION['user_id'],
              $_SESSION['category_id']
              ); 
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$books = array();
    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)){
        $sql = 'SELECT * FROM `categories` WHERE category_id=?';
        $data1 = array($book['category_id']);
        $category_stmt = $dbh->prepare($sql);
        $category_stmt->execute($data1);
        $category_name = $category_stmt->fetch(PDO::FETCH_ASSOC);

        $sql = 'SELECT COUNT(*) AS `cnt` FROM `finish_reading` WHERE book_id=?';
        $data2 = array($book['book_id']);
        $finish_reading_stmt = $dbh->prepare($sql);
        $finish_reading_stmt->execute($data2);
        $finish_reading = $finish_reading_stmt->fetch(PDO::FETCH_ASSOC);

        $sql = 'SELECT COUNT(*) AS `cnt` FROM `bookmark` WHERE book_id=?';
        $data3 = array($book['book_id']);
        $book_mark_stmt = $dbh->prepare($sql);
        $book_mark_stmt->execute($data3);
        $book_mark = $book_mark_stmt->fetch(PDO::FETCH_ASSOC);

        $books[] = array(
                  'user_id' => $_SESSION['user_id'],
                  'user_name' => $_SESSION['user_name'],
                  'book_id' => $book['book_id'],
                  'category_name' => $category_name['name'],
                  'picture_url_API' => $book['picture_url_API'],
                  'title' => $book['title'],
                  'author_API' => $book['author_API'],
                  'phrase' => $book['phrase'],
                  'finish_cnt' => $finish_reading['cnt'],
                  'like_cnt' => $book_mark['cnt']
                );
        }

require('../function/get_categories.php');


// ヒストリの取得
$sql = 'SELECT * FROM `history` WHERE `user_id`=?';
$history_data = array($login_user['user_id']);
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
      <div id="Carousel" class="carousel slide">

          <h2><?php echo $books[0]['category_name']; ?></h2>
          <?php 
            // 下記構造にあう配列を作成
            $c = count($books);
            $books_page = array();
            $books_page = array_chunk($books, 4);
            $page = count($books_page);
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
                      <?php foreach ($books_page[$i] as $category_books): ?>
                          <div class="col-xs-3">
                            <a href="book_detail.php?id=<?php echo $category_books['book_id']; ?>" class="thumbnail">
                            <img src="<?php echo $category_books['picture_url_API']; ?>" alt="Image" style="max-width:100%;">
                            </a>
                            <?php echo $category_books['title']; ?>
                            <a href="#" class="edit2">編集</a>
                            <br><?php echo $category_books['author_API']; ?>
                            <a href="#" class="delete2">削除</a>
                            <br><?php echo $category_books['phrase']; ?>
                            <br>
                            <button type="button" class="btn btn-info btn-circle"><i class="glyphicon glyphicon-ok"></i></button><?php echo $category_books['finish_cnt']; ?>
                            <button type="button" class="btn btn-danger btn-circle"><i class="glyphicon glyphicon-heart"></i></button><?php echo $category_books['like_cnt']; ?>
                          </div>
                      <?php endforeach; ?>  
                    </div><!--.row-->
                  </div><!--.item-->
            <?php endfor; ?>
          </div><!--.carousel-inner-->
          <a data-slide="prev" href="#Carousel" class="left carousel-control">‹</a>
          <a data-slide="next" href="#Carousel" class="right carousel-control">›</a>
        </div><!--.Carousel-->

      </div><!-- col-xs-8 閉じタグ-->

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