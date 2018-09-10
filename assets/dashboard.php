<?php 
require('dbconnect.php');
require('user_info.php');
// session_start();

// $category_id = $_GET['category_id'];
// $_SESSION['user_id'] = $login_user['user_id'];
$category_id = $_GET['category_id'];
$_SESSION['user_id'] = $login_user['user_id'];
$_SESSION['user_name'] = $login_user['user_name'];



$sql = 'SELECT * FROM `books` WHERE user_id!=? AND category_id=?';
$data = array($_SESSION['user_id'],
              $category_id
              ); 
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$books = array();
$errors = array();
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
if (empty($books)) {
    $errors['params'] = 'unexist';
}



  // パラメターがないバージョン
  // カテゴリーにの左にないパラメーターがない場合


// $sql = 'SELECT * FROM `books` WHERE user_id!=? AND category_id=?';
// $data = array($_SESSION['user_id'],
//               $category_id
//               ); 
// $stmt = $dbh->prepare($sql);
// $stmt->execute($data);

// if ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
//     // データが返ってきた場合

// } else {
//     // データが空だった場合

// }

// $books = array();

// if ($category_id = 1) {

//   }elseif ($category_id ) {

//   }else{

//   }
// var_dump($books);

//カテゴリー全件取得
$sql = 'SELECT * FROM `categories`';
$all_category_stmt = $dbh->prepare($sql);
$all_category_stmt->execute();
$all = array();
$categories = array();
while ($all_category = $all_category_stmt->fetch(PDO::FETCH_ASSOC)) {
  $all[] = $all_category['category_id'];
  $categories[] = array('category_id'=>$all_category['category_id'], 'name'=>$all_category['name']);
}

echo '<br>';

$c1 = count($all);
$c2 = count($selected_categories);
for ($i=0; $i < $c1; $i++) {
  for ($j=0; $j < $c2; $j++) {
    if ($all[$i] == $selected_categories[$j]) {
      unset($categories[$i]);
    }
  }
}

// 選んだカテゴリ以外のカテゴリ
$unselected_categories = array_merge($categories);
$b = count($categories);
// for ($i=0; $i < $b; $i++) {
//    echo $a[$i];
//  }

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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="assets/css/bootstrap.css" rel="stylesheet">
  <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <!-- Original css -->
  <link href="assets/css/main.css" rel="stylesheet">
  <!-- header -->
  <link href="assets/css/navibar.css" rel="stylesheet">
  <link href="assets/css/search.css" rel="stylesheet">
  <link href="assets/css/logout.css" rel="stylesheet">
  <!-- left-sidebar -->
  <link href="assets/css/book_register.css" rel="stylesheet">
  <link href="assets/css/ranking.css" rel="stylesheet">
  <link href="assets/css/your_ranking.css" rel="stylesheet">
  <link href="assets/css/category.css" rel="stylesheet">
  <!-- right-sidebar -->
  <link href="assets/css/recommend.css" rel="stylesheet">
  <link href="assets/css/profile.css" rel="stylesheet">
  <!-- main -->
  <link href="assets/css/dashboard.css" rel="stylesheet">
  <link href="assets/css/action_bottom.css" rel="stylesheet">


  <title>Bookrus-知らない本と出会う-</title>
</head>
<body>


  <header class="header">

    <!-- http://bootsnipp.com/snippets/featured/rainbow-nav -->
    <!-- Rainbow Nav バー -->
    <div class="navbar-wrapper">
        <div class="container-fluid">
            <nav class="navbar navbar-fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="dashboard.php" style="font-size: 40px; padding-top: 20px;">Bookrus</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="ranking.php" class="" style="padding-top: 25px;">ランキング</a></li>
                            <li class=" dropdown">
                                <a href="mypage.php" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">マイページ</a>
                            </li>
                            <li class=" dropdown"></li>
                        </ul>
                        <ul class="nav navbar-nav pull-right">
                              <!-- http://bootsnipp.com/snippets/featured/account-in-navbar -->
                              <!-- ログアウト -->
                            <ul class="nav navbar-nav navbar-right">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                      <strong>
                                        <!-- プロフィール画像 -->
                                        <!-- <div class="profile-header-container">  -->
                                        <div class="profile-header-img">
                                            <img class="img-circle" src="member_picture/<?php echo $login_user_info['picture_path']; ?>" />
                                            <span class="profile-name"><?php echo $login_user_info['user_name']; ?></span>
                                        </div>
                                      </strong>
                                    </a>
                                    <ul class="dropdown-menu">
                                      <li>
                                        <div class="navbar-login">
                                          <div class="row">
                                            <div class="col-lg-4">
                                              <p class="text-center">
                                                <img src="member_picture/<?php echo $login_user_info['picture_path']; ?>" width="100">
                                              </p>
                                            </div>
                                              <div class="col-lg-8">
                                                  <p class="text-left"><strong><?php echo $login_user_info['user_name']; ?></strong></p>
                                                  <p class="text-left">
                                                      <a href="logout.php" class="btn btn-primary btn-block btn-sm">ログアウト</a>
                                                  </p>
                                              </div>
                                            </div>
                                          </div>
                                      </li>
                                     </ul>
                                 </li>
                            </ul>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>

           <!-- http://bootsnipp.com/snippets/featured/custom-search-input -->
           <!-- Custom Search input 検索バー -->
           <div class="container" id="search_container">
             <div class="row">
               <div class="col-sm-6 col-md-6 col-lg-7">
                 <div id="custom-search-input">
                   <form action="search_view.php" method="GET" class="form-inline">
                     <div class="input-group col-md-12">
                       <input name="search_word" type="text" class="form-control input-lg" placeholder="検索" value="">
                       <span class="input-group-btn" style="width: 50px; height: 40px;">
                         <button type="submit" class="btn btn-info btn-lg">
                           <i class="glyphicon glyphicon-search"></i>
                         </button>
                       </span>
                     </div>
                   </form>
                 </div>
               </div>
             </div>
           </div>

  </header>

  <div class="container">
    <div class="row">

      <div class="col-xs-2">

          <div class="login-container">
              <div id="output"></div>
              <a href=""><img src="assets/img/content2.jpeg" width="120px" height="160px"></a>
              <div class="form-box">
                <h4>タイトル</h4><br>
                <h4>フレーズ</h4><br>
                <h4>著者名</h4>
              </div>
          </div>

          <!-- http://bootsnipp.com/snippets/featured/expandable-panel-list -->
                    <!-- ランキング表示 -->
                    <!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css"> -->

                      <div class="panel panel-default" id="panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">カテゴリ</h3>
                            </div>
                            <ul class="list-group">
                            <?php for($i=0; $i < $b; $i++):?>
                            <?php // for(初期化式(一回しか読み込まれない); 条件式; 変化式):?>
                              <li class="list-group-item">
                                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                                      <a href="dashboard.php?category_id=<?php echo $unselected_categories[$i]['category_id']; ?>" style="color: black; text-decoration: none;"><?php echo $unselected_categories[$i]['name']; ?></a>
                                  </div>
                              </li>
                            <?php endfor; ?>
                          </ul>
                      </div>
      </div><!-- col-xs-2 閉じタグ-->


      <div class="col-sm-10 col-md-8 col-lg-8" style="margin-top: 70px; padding-left: 90px; padding-right: 70px; ">

        <!-- ここにコードを書いてください！！ -->
        <!-- http://bootsnipp.com/snippets/featured/simple-carousel -->
        <!-- 本表示 -->
        <?php if(isset($errors['params']) && $errors['params'] == 'unexist'): ?>
          <p>その本は削除されたかURLが間違っています。</p>
        <?php else: ?>
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
        <?php endif; ?>

      </div><!-- col-xs-8 閉じタグ-->





      <div class="hidden-sm col-md-2 col-lg-2" style="">

          <!-- http://bootsnipp.com/snippets/featured/simple-contact-form -->
          <!-- Simple Contact Form 本の登録 -->
          <div class="form-area" style="background-image: url(assets/img/book2.jpg); background-size: cover; opacity: 0.9; height: 250px">
              <form role="form" class="form" method="POST" action="book_edit.php">
              <br style="clear:both">
                         <h3 style="margin-bottom: 25px; text-align: center; font-size: 20px; font-weight: bold; color: white; padding-left: 10px;">本の登録</h3>
                  <div class="form-group">
                  <input type="text" class="form-control" id="name" name="title" placeholder="本のタイトル" required>
                </div>

                <div class="form-group">
                  <input type="text" class="form-control" id="mobile" name="phrase" placeholder="フレーズ" required>
                </div>

              <!-- <button type="button" id="submit" name="submit" class="btn btn-primary pull-right" style="background-color: black; border-color: black;">登録する</button> -->
                <input type="submit" value="登録する" class="btn btn-primary pull-right" style="background-color: black; border-color: black;">
              </form>
          </div>


          <!-- http://bootsnipp.com/snippets/featured/new-style-alerts -->
          <!-- あなたのランキング -->
          <div>
          <h4 style="padding-bottom: 40px; padding-left: 15px;">
            あなたは <?php echo $_SESSION['rank']; ?> 位です</h4>
          </div>
          <!-- http://bootsnipp.com/snippets/featured/expandable-panel-list -->
          <!-- ランキング表示 -->
          <div class="panel panel-default" id="ranking">

          <div class="panel-heading">
              <h3 class="panel-title">ランキング順位</h3>
          </div>
            <ul class="list-group">
              <?php for ($i=0; $i < 5; $i++) :?>
                <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                        <a href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id'];?>" style="color: black; text-decoration: none;">
                            <?php echo $ranking_users[$i]['rank']; ?> 位<br>
                            <?php echo $ranking_users[$i]['user_name']; ?>
                        </a>
                    </div>
                </li>
              <?php endfor; ?>
            </ul>
          </div>

      </div><!-- col-xs-2 閉じタグ-->
    </div><!-- row 閉じタグ -->
  </div><!-- container 閉じタグ -->

  <script src="assets/js/jquery.js"></script>
  <script src="assets/js/jquery-migrate.js"></script>
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/book_regi.js"></script>
  <script src="assets/js/dashboard.js"></script>

</body>
</html>