<?php
session_start();
require('../function/dbconnect.php');
require('../function/user_info.php');


// dashbord　ここから
require('../function/getcategories.php');

  
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


// book_edit　ここから
if (empty($_REQUEST['book_id'])) {
  header('Location: dashboard_category.php');
  exit();
}

// $_SESSION['id'] =2;
$book_id = $_REQUEST['book_id'];
// // 本データ一件取得
$sql = 'SELECT u.user_name, u.picture_path, b.* FROM `users` u, `books` b WHERE u.user_id=b.user_id AND b.book_id=?';
$data = array($_REQUEST['book_id']);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);


// 更新ボタンが押された時
if (!empty($_POST['renew'])) {
    if ($_POST['phrase'] != '' && $_POST['title'] != '') {
        $sql = 'UPDATE `books` SET `phrase`=?, `title`=? WHERE `book_id`=?';
       // $dataには何が入るのか
        $data = array($_POST['phrase'], $_POST['title'], $_REQUEST['book_id']);
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

        header("Location: book_detail.php?book_id={$_REQUEST['book_id']}");
        exit();
    }
}


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
  <!-- Original css -->
  <!-- ユーザーアイコンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/user-detail -->
  <link href="assets/css/book_user.css" rel="stylesheet">
  <!-- 大枠の本の編集のcss -->
  <link href="assets/css/book_edit_body.css" rel="stylesheet">
  <!-- アクションボタンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
  <link href="assets/css/book_action_button.css" rel="stylesheet">
  <!-- 編集ボタンと削除ボタンのcss -->
  <!-- http://bootsnipp.com/snippets/A2Mx5 -->
  <link href="assets/css/book_detail_button.css" rel="stylesheet">
  <!-- book_editのcss -->
  <link href="assets/css/book_edit.css" rel="stylesheet">
  <!-- 本のスペースのcss -->
  <link href="assets/css/space.css" rel="stylesheet">
  <!-- ようさんOriginal css -->
  <link href="assets/css/main.css" rel="stylesheet">


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
                            <li class="active"><a href="layout.php" class="" style="padding-top: 25px;">ランキング</a></li>
                            <li class=" dropdown">
                                <a href="mypage.php" role="button" style="padding-top: 25px;">マイページ</a>
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
                                                                          <a href="logout.php" class="btn btn-primary btn-block btn-sm" id="red-btn">ログアウト</a>
                                                                      </p>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </li>
                                                      <li class="divider"></li>
                                                      <li>
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
            <form action="" method="" class="form-inline">
              <div class="input-group col-md-12">
                <input name="search_word" type="text" class="form-control input-lg" placeholder="検索">
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


        <!-- 以下から本の編集のコード -->
        <!-- ユーザーアイコン -->
        <?php if($book = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
          <form method="POST" action="" name="form1" class="form-horizontal">
          <div class="media col-lg-12 col-md-12">
            <a class="pull-left" href="#">
            <img class="media-object dp img-circle" src="member_picture/<?php echo $login_user_info['picture_path']; ?>" style="width: 100px;height:100px;">
            </a>
            <div class="media-body">
              <br>
              <h4 class="media-heading"><?php echo $login_user_info['user_name']; ?>さんの本の編集</h4>
              <hr style="margin:8px auto">
            </div>
          </div>
          <!-- 大枠の編集ページ -->
          <div class="panel">
            <div class="panel-body col-lg-12 col-md-12"">
                <label for="title">タイトル</label>
                <input name="title" type="text" class="form-control" id="title" placeholder="タイトル" value="<?php echo $book['title'] ?>">
                <br>
                <label for="phrase">フレーズ</label>
                <input name="phrase" type="text" class="form-control" id="phrase" placeholder="フレーズ" value="<?php echo $book['phrase']; ?>">
                <br><br>
            </div>
          </div>
          <div class="col-sm-4 col-md-4">
            <img src="<?php echo $book['picture_url_API'] ?>" width="200px" height="300px" alt="" align="left" class="space">
          </div>
          <div class="col-sm-8 col-md-8">
            <div class="title"><h2><?php echo $book['title']; ?></h2></div>
            <div class="author">（著）<?php echo $book['author_API']; ?></div>
          </div>
          <div class="col-lg-12 col-md-12">
              <br>
                <button type="button" class="btn btn-info btn-circle btn-disabled"><i class="glyphicon glyphicon-ok"></i></button>
                <font class="number"><?php echo $count2; ?></font>
                <button type="button" class="btn btn-danger btn-circle btn-disabled"><i class="glyphicon glyphicon-heart"></i></button>
                <font class="number"><?php echo $count1; ?></font>
                <br><br>
          <!-- 更新ボタンと削除ボタン -->
          <div class="container">
          <?php if ($book['user_id'] == $_SESSION['id']): ?>
            <input type="hidden" name="renew" value="更新">
            <!-- <button type="submit" class="btn btn-primary" value="更新"><i class="fa fa-fw fa-check" aria-hidden="true"> </i> 更新</button>
            <a class="btn btn-danger" href="delete.php?book_id=<?php //echo $book['book_id'] ?>"><i class="fa fa-fw fa-times" aria-hidden="true"></i> 削除</a>-->

            <a href="javascript:document.form1.submit()" class="btn btn-primary a-btn-slide-text">
              <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
              <span><strong>更新</strong></span>
            </a>
            <a href="delete.php?book_id=<?php echo $book['book_id'] ?>" class="btn btn-danger a-btn-slide-text">
              <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
              <span><strong>削除</strong></span>
            </a>
            <br><br>
        <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="panel panel-default">
          <div class="panel-body">
                <h4>その本の情報は登録されていません。</h4>
                <br>
                <br>
                  <!-- ユーザーアイコン -->
            <div class="media">
                <a class="pull-left" href="#">
                  <img class="media-object dp img-circle" src="assets/img/no_image.jpg" style="width: 100px;height:100px;">
                </a>
                <div class="media-body">
                    <br>
                    <br>
                    <h4 class="media-heading">ほげほげさん</h4>
                    <hr style="margin:8px auto">
                </div>
            </div>
              <!-- noimage画像など -->
            <div class="col-lg-12 col-md-12">
               <img src="assets/img/no_image.jpg" width="200px" height="300px" alt="" align="left" class="space">
                <br>
                <div class="title">NO TITLE</div>
                <div class="author">NO AUTHOR</div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        </div>
        </form>
  <!-- ここまでが本の編集のコード -->
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
  <!--  本の編集のjs -->
  <script src="assets/js/book_edit_body.js"></script>
  <!-- ダッシュボードのjs -->
  <script src="assets/js/book_regi.js"></script>
  <script src="assets/js/dashboard.js"></script>

</body>
</html>