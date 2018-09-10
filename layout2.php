<?php 
require('dbconnect.php');
session_start();
$_SESSION['id'] = 12;
$ranking_users = array();

$sql = 'SELECT * FROM `users`';
$stmt = $dbh->prepare($sql);
$stmt->execute();

while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                  'score_like' => 0
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
// var_dump($ranking_users);

// rankをつける
$c = count($ranking_users);
for ($i = 0; $i < $c; $i++) {
    if ($ranking_users[$i]['user_id'] == $_SESSION['id']) {
        $_SESSION['rank'] = $i + 1;
    }
}
$rank_before = $_SESSION['rank'] - 2;
$rank_last = $_SESSION['rank'] + 1;
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

  <link href="assets/css/bootstrap_ranking.css" rel="stylesheet">
  <!-- アクションボタンの実装 -->
  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
  <link href="assets/css/bootstrap_action_button.css" rel="stylesheet">
  <!-- Original css -->
  <!-- http://runstant.com/chicken_yuta/projects/abca71b5 -->
  <!-- 王冠 -->
  <!-- http://runstant.com/chicken_yuta/projects/abca71b5 -->
  <link href="assets/css/ranking_crown.css" rel="stylesheet">
  <!-- アコーディオンの矢印の実装 -->
  <!-- http://fontawesome.io/icon/angle-double-up/ -->
  <!-- http://fontawesome.io/icon/angle-double-down/ -->
  <!-- アコーディオンの実装 -->
  <!-- http://webgaku.hateblo.jp/entry/jquery-table-accordion -->
  <link href="assets/css/ranking_arrow.css" rel="stylesheet">
  <!-- カーソルの実装 -->
  <!-- http://h2ham.seesaa.net/article/208820291.html -->
  <!-- http://matsudam.com/blog/entry/712 -->
  <link href="assets/css/ranking_cursor.css" rel="stylesheet">
  

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
                        <a class="navbar-brand" href="#" style="font-size: 40px; padding-top: 20px;">Bookrus</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="#" class="" style="padding-top: 25px;">ランキング</a></li>
                            <li class=" dropdown">
                                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">マイページ</a>
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
                                                                <img class="img-circle" src="member_picture/201703010000yoh.jpg" />
                                                                <span class="profile-name">Yo Sano</span>
                                                            </div>
                                                      </strong>
                                                  </a>
                                                  <ul class="dropdown-menu">
                                                      <li>
                                                          <div class="navbar-login">
                                                              <div class="row">
                                                                  <div class="col-lg-4">
                                                                      <p class="text-center">
                                                                          <img src="member_picture/201703010000yoh.jpg" width="100">
                                                                      </p>
                                                                  </div>
                                                                  <div class="col-lg-8">
                                                                      <p class="text-left"><strong>Yo Sano</strong></p>
                                                                      <p class="text-left small">好きなカテゴリ</p>
                                                                      <p class="text-left">
                                                                          <a href="#" class="btn btn-primary btn-block btn-sm">ログアウト</a>
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
                <input type="text" class="form-control input-lg" placeholder="検索">
                <span class="input-group-btn" style="width: 42px;">
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
                      <li class="list-group-item">
                          <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                              <a href="" style="color: black; text-decoration: none;">カテゴリ 1</a>
                          </div>
                      </li>
                      <li class="list-group-item" style="border-bottom: 10px;">
                          <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                              <a href="" style="color: black; text-decoration: none;">カテゴリ 2</a>
                          </div>
                      </li>
                      <li class="list-group-item">
                          <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                              <a href="" style="color: black; text-decoration: none;">カテゴリ 3</a>
                          </div>
                      </li>
                      <li class="list-group-item">
                          <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                              <a href="" style="color: black; text-decoration: none;">カテゴリ 4</a>
                          </div>
                      </li>
                      <li class="list-group-item">
                          <div class="row toggle" id="dropdown-detail-5" data-toggle="detail-5">
                              <a href="" style="color: black; text-decoration: none;">カテゴリ 5</a>
                          </div>
                      </li>
                  </ul>
            </div>

      </div><!-- col-xs-2 閉じタグ-->


      <div class="col-sm-10 col-md-8 col-lg-8" style="margin-top: 70px; padding-left: 90px; padding-right: 70px; ">
    
        <!-- ここにコードを書いてください！！ -->
        
        <div class="ranking">
          <p class="rank">
            アンバサダーランキング
          </p>
          <div class="rank2">
            <div class="panel panel-primary filterable">
              <table class="table">
              
              <!-- 1~5位までに自分がいるとき -->
                <tbody class="tbody">
                  <?php for ($i=0; $i < 5; $i++): ?>
                    <tr data-href="">
                      <td class="crown">
                      　<!--　王冠-->
                        <div class="panel-crown">
                          <div class="g-crown">
                            <div class="g-crown-circle"></div>
                          </div>
                        </div>
                      </td>
                      <td>
                         <img src="<?php echo $ranking_users[$i]['picture_path'] ?>">
                      </td>
                      <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                      <td>
                        <?php echo $ranking_users[$i]['score_like'] ?>
                        <button type="button" class="btn btn-info btn-circle">
                          <i class="glyphicon glyphicon-ok"></i>
                        </button>
                      </td>
                      <td>
                        <?php echo $ranking_users[$i]['score_finish'] ?>
                        <button type="button" class="btn btn-danger btn-circle">
                          <i class="glyphicon glyphicon-heart"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endfor; ?>
                    <tr>
                      <td></td>
                      <td></td>
                      <td id="open" class="allow_toggle_btn">
                        <!-- オープンボタン -->
                        <a href="javascript:void(0)" id="btn-login" class="open-btn" style="display: block;">
                          <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                        </a>
                        <!-- クローズボタン -->
                        <a href="javascript:void(0)" class="close-btn" style="display: none;">
                          <i class="fa fa-angle-double-up" aria-hidden="true"></i>
                        </a>
                      </td>
                    </tr>
                </tbody>

                <!-- 6位以降の表示 -->
                <?php if ($_SESSION['rank'] >=1 && $_SESSION['rank'] <=5): ?>
                  <tbody class="hoge1 hiderank" id="tbody">
                  <?php for ($i=5; $i < $c; $i++): ?>
                  <!-- 6位から最後まで表示 -->
                    <tr data-href="">
                      <td class="rank-size"><?php echo $i + 1 . '位' ?></td>
                      <td>
                        <a>
                          <img src="<?php echo $ranking_users[$i]['picture_path'] ?>">
                        </a>
                      </td>
                      <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                      <td>
                        <?php echo $ranking_users[$i]['score_like'] ?>
                        <button type="button" class="btn btn-info btn-circle">
                          <i class="glyphicon glyphicon-ok"></i>
                        </button>
                      </td>
                      <td>
                        <?php echo $ranking_users[$i]['score_finish']?>
                        <button type="button" class="btn btn-danger btn-circle">
                          <i class="glyphicon glyphicon-heart"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endfor; ?>
                  </tbody>

                <!-- 6位から最後から4番目まで入る時 -->
                <?php elseif ($_SESSION['rank'] > 5 && $_SESSION['rank'] <= $c - 2): ?>
                  <tbody class="hoge1 hiderank" id="tbody">
                  <?php for ($i=5; $i < $rank_before; $i++): ?>
                    <tr data-href="">
                      <td class="rank-size"><?php echo $i + 1 . '位' ?></td>
                      <td>
                        <a>
                          <img src="<?php echo $ranking_users[$i]['picture_path'] ?>" width="50px">
                        </a>
                      </td>
                      <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                      <td>
                        <?php echo $ranking_users[$i]['score_like'] ?>
                        <button type="button" class="btn btn-info btn-circle">
                          <i class="glyphicon glyphicon-ok"></i>
                        </button>
                      </td>
                      <td>
                        <?php echo $ranking_users[$i]['score_finish']?>
                        <button type="button" class="btn btn-danger btn-circle">
                          <i class="glyphicon glyphicon-heart"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endfor; ?>
                  </tbody>
                  <tbody id="tbody2">
                  <!-- 自分の順位とその前後表示 -->
                    <?php for ($i=0; $i < $c; $i++): ?>
                      <?php if ($_SESSION['rank'] == $i + 1): ?>
                      <!-- 自分の前のユーザー情報表示 -->
                      <tr data-href="">
                        <td class="rank-size"><?php echo $_SESSION['rank'] - 1 . '位' ?></td>
                        <td>
                          <a>
                            <img src="<?php echo $ranking_users[$i]['picture_path'] ?>" width="50px">
                          </a>
                        </td>
                        <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                        <td>
                          <?php echo $ranking_users[$i]['score_like'] ?>
                          <button type="button" class="btn btn-info btn-circle">
                            <i class="glyphicon glyphicon-ok"></i>
                          </button>
                        </td>
                        <td>
                          <?php echo $ranking_users[$i]['score_finish']?>
                          <button type="button" class="btn btn-danger btn-circle">
                            <i class="glyphicon glyphicon-heart"></i>
                          </button>
                        </td>
                      </tr>
                      <!-- 自分のランキング表示 -->
                      <tr data-href="">
                        <td class="rank-size"><?php echo $_SESSION['rank'] . '位' ?></td>
                        <td>
                          <a>
                            <img src="<?php echo $ranking_users[$i + 1]['picture_path'] ?>" width="50px">
                          </a>
                        </td>
                        <td><?php echo $ranking_users[$i + 1]['user_name'] ?></td>
                        <td>
                          <?php echo $ranking_users[$i + 1]['score_like'] ?>
                          <button type="button" class="btn btn-info btn-circle">
                            <i class="glyphicon glyphicon-ok"></i>
                          </button>
                        </td>
                        <td>
                          <?php echo $ranking_users[$i + 1]['score_finish'] ?>
                          <button type="button" class="btn btn-danger btn-circle">
                            <i class="glyphicon glyphicon-heart"></i>
                          </button>
                        </td>
                      </tr>
                      <!-- 自分の次の人ランキング情報表示 -->
                      <tr data-href="">
                        <td class="rank-size"><td class="rank-size"><?php echo $_SESSION['rank'] + 1 . '位' ?></td>
                        <td>
                          <a>
                            <img src="<?php echo $ranking_users[$i + 2]['picture_path'] ?>" width="50px">
                          </a>
                        </td>
                        <td><?php echo $ranking_users[$i + 2]['user_name'] ?></td>
                        <td>
                          <?php echo $ranking_users[$i + 2]['score_like'] ?>
                          <button type="button" class="btn btn-info btn-circle">
                            <i class="glyphicon glyphicon-ok"></i>
                          </button>
                        </td>
                        <td>
                          <?php echo $ranking_users[$i + 2]['score_finish'] ?>
                          <button type="button" class="btn btn-danger btn-circle">
                            <i class="glyphicon glyphicon-heart"></i>
                          </button>
                        </td>
                      </tr>
                      <?php endif; ?>
                    <?php endfor; ?>
                      <tr>
                        <td></td>
                        <td></td>
                        <td id="open-all" class="allow_toggle_btn-all">
                          <!-- オープンボタン -->
                          <a href="javascript:void(0)" id="btn-login" class="open-btn-all" style="display: block;">
                            <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                          </a>
                          <!-- クローズボタン -->
                          <a href="javascript:void(0)" class="close-btn-all" style="display: none;">
                            <i class="fa fa-angle-double-up" aria-hidden="true"></i>
                          </a>
                        </td>
                      </tr>
                  </tbody>
                  <tbody class="hoge1 hiderank1" id="tbody3">
                  <?php  for ($i=$rank_last; $i < $c; $i++) : ?>
                    <tr data-href="">
                      <td class="rank-size"><?php echo $i + 1 . '位' ?></td>
                      <td>
                        <a>
                          <img src="<?php echo $ranking_users[$i]['picture_path'] ?>" width="50px">
                        </a>
                      </td>
                      <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                      <td>
                        <?php echo $ranking_users[$i]['score_like'] ?>
                        <button type="button" class="btn btn-info btn-circle">
                          <i class="glyphicon glyphicon-ok"></i>
                        </button>
                      </td>
                      <td>
                        <?php echo $ranking_users[$i]['score_finish'] ?>
                        <button type="button" class="btn btn-danger btn-circle">
                          <i class="glyphicon glyphicon-heart"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endfor; ?>
                  </tbody>

                <?php elseif($_SESSION['rank'] > $c - 2): ?>]
                  <!-- ワースト３に入る時 -->
                  <tbody class="hoge1 hiderank" id="tbody">
                  <?php for ($i=5; $i < $rank_before; $i++): ?>
                     <!-- 6位から自分のランクの2個前まで -->
                    <tr data-href="">
                     <td class="rank-size"><?php echo $i + 1 . '位' ?></td>
                     <td>
                       <a>
                         <img src="<?php echo $ranking_users[$i]['picture_path'] ?>" width="50px">
                       </a>
                     </td>
                     <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                     <td>
                       <?php echo $ranking_users[$i]['score_like'] ?>
                       <button type="button" class="btn btn-info btn-circle">
                         <i class="glyphicon glyphicon-ok"></i>
                       </button>
                     </td>
                     <td>
                       <?php echo $ranking_users[$i]['score_finish']?>
                       <button type="button" class="btn btn-danger btn-circle">
                         <i class="glyphicon glyphicon-heart"></i>
                       </button>
                     </td>
                    </tr>
                  <?php endfor; ?>
                  </tbody>
                  <tbody class="hoge1 hiderank1" id="tbody3">
                    <?php for ($i=0; $i < $c; $i++): ?>
                      <?php if ($_SESSION['rank'] == $i + 1 && !empty($ranking_users[$i + 2])): ?>
                    <!-- 自分の順位とその前後表示 -->
                      <tr data-href="">
                      <!-- 自分の前のユーザー情報表示 -->
                        <td class="rank-size"><?php echo $_SESSION['rank'] - 1 . '位 : ' ?></td>
                        <td>
                         <a>
                           <img src="<?php echo $ranking_users[$i]['picture_path'] ?>" width="50px">
                         </a>
                        </td>
                        <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                        <td>
                         <?php echo $ranking_users[$i]['score_like'] . '<br>'; ?>
                         <button type="button" class="btn btn-info btn-circle">
                           <i class="glyphicon glyphicon-ok"></i>
                         </button>
                        </td>
                        <td>
                         <?php echo $ranking_users[$i]['score_finish'] . '<br>'; ?>
                         <button type="button" class="btn btn-danger btn-circle">
                           <i class="glyphicon glyphicon-heart"></i>
                         </button>
                        </td>
                      </tr>
                      <tr data-href="">
                      <!-- 自分のランキング表示 -->
                        <td class="rank-size"><?php echo $_SESSION['rank'] . '位 : ' ?></td>
                        <td>
                         <a>
                           <img src="<?php echo $ranking_users[$i + 1]['picture_path'] ?>" width="50px">
                         </a>
                        </td>
                        <td><?php echo $ranking_users[$i + 1]['user_name'] ?></td>
                        <td>
                         <?php echo $ranking_users[$i + 1]['score_like'] . '<br>'; ?>
                         <button type="button" class="btn btn-info btn-circle">
                           <i class="glyphicon glyphicon-ok"></i>
                         </button>
                        </td>
                        <td>
                         <?php echo $ranking_users[$i + 1]['score_finish'] . '<br>'; ?>
                         <button type="button" class="btn btn-danger btn-circle">
                           <i class="glyphicon glyphicon-heart"></i>
                         </button>
                        </td>
                      </tr>
                      <tr data-href="">
                      <!-- 自分の次の人ランキング情報表示 -->
                        <td class="rank-size"><?php echo $_SESSION['rank'] + 1 . '位 : ' ?></td>
                        <td>
                          <a>
                            <img src="<?php echo $ranking_users[$i + 2]['picture_path'] ?>" width="50px">
                          </a>
                        </td>
                        <td><?php echo $ranking_users[$i + 2]['user_name'] ?></td>
                        <td>
                          <?php echo $ranking_users[$i + 2]['score_like'] . '<br>'; ?>
                          <button type="button" class="btn btn-info btn-circle">
                            <i class="glyphicon glyphicon-ok"></i>
                          </button>
                        </td>
                        <td>
                          <?php echo $ranking_users[$i + 2]['score_finish'] . '<br>'; ?>
                          <button type="button" class="btn btn-danger btn-circle">
                            <i class="glyphicon glyphicon-heart"></i>
                          </button>
                        </td>
                      </tr>
                      <?php elseif ($_SESSION['rank'] == $i + 1 && empty($ranking_users[$i + 2])): ?>
                        <!-- 自分の順位とその前後表示 -->
                        <tr data-href="">
                        <!-- 自分の前のユーザー情報表示 -->
                          <td class="rank-size"><?php echo $_SESSION['rank'] - 1 . '位 : ' ?></td>
                          <td>
                           <a>
                             <img src="<?php echo $ranking_users[$i]['picture_path'] ?>" width="50px">
                           </a>
                          </td>
                          <td><?php echo $ranking_users[$i]['user_name'] ?></td>
                          <td>
                           <?php echo $ranking_users[$i]['score_like'] . '<br>'; ?>
                           <button type="button" class="btn btn-info btn-circle">
                             <i class="glyphicon glyphicon-ok"></i>
                           </button>
                          </td>
                          <td>
                           <?php echo $ranking_users[$i]['score_finish'] . '<br>'; ?>
                           <button type="button" class="btn btn-danger btn-circle">
                             <i class="glyphicon glyphicon-heart"></i>
                           </button>
                          </td>
                        </tr>
                        <tr data-href="">
                        <!-- 自分のランキング表示 -->
                          <td class="rank-size"><?php echo $_SESSION['rank'] . '位 : ' ?></td>
                          <td>
                           <a>
                             <img src="<?php echo $ranking_users[$i + 1]['picture_path'] ?>" width="50px">
                           </a>
                          </td>
                          <td><?php echo $ranking_users[$i + 1]['user_name'] ?></td>
                          <td>
                           <?php echo $ranking_users[$i + 1]['score_like'] . '<br>'; ?>
                           <button type="button" class="btn btn-info btn-circle">
                             <i class="glyphicon glyphicon-ok"></i>
                           </button>
                          </td>
                          <td>
                           <?php echo $ranking_users[$i + 1]['score_finish'] . '<br>'; ?>
                           <button type="button" class="btn btn-danger btn-circle">
                             <i class="glyphicon glyphicon-heart"></i>
                           </button>
                          </td>
                        </tr>
                      <?php endif; ?>
                    <?php endfor; ?>
                  </tbody>
                <?php endif; ?>
              </table>
            </div>
          </div><!-- rank2 閉じタグ-->
        </div>

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
          <h4 style="padding-bottom: 40px; padding-left: 15px;">あなたは20位です</h4>
          </div>
          <!-- http://bootsnipp.com/snippets/featured/expandable-panel-list -->
          <!-- ランキング表示 -->
          <div class="panel panel-default" id="ranking">

          <div class="panel-heading">
              <h3 class="panel-title">ランキング順位</h3>
          </div>   
            <ul class="list-group">
                <li class="list-group-item">

                    <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                        <a href="" style="color: black; text-decoration: none;">ランキング 1</a>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                        <a href="" style="color: black; text-decoration: none;">ランキング 2</a>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                        <a href="" style="color: black; text-decoration: none;">ランキング 3</a>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                        <a href="" style="color: black; text-decoration: none;">ランキング 4</a>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-5" data-toggle="detail-5">
                        <a href="" style="color: black; text-decoration: none;">ランキング 5</a>
                    </div>
                </li>
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
  <script src="assets/js/bootstrap_ranking.js"></script>
  <script src="assets/js/ranking.js"></script>
  <script src="assets/js/ranking_crwon.js"></script>
  <script src="assets/js/ranking_arrow.js"></script>
  <script src="assets/js/ranking_cursor.js"></script>


</body>
</html>