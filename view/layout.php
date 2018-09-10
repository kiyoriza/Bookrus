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


// layout　ここから
// session_start();
// $_SESSION['id'] = 12;
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
  <!-- 共通リンク -->
  <?php require('layout/common_links.php'); ?>
  <!-- Original css -->
  <link href="../assets/css/main.css" rel="stylesheet">
  <link href="../assets/css/bootstrap_ranking.css" rel="stylesheet">
  <!-- アクションボタンの実装 -->
  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
  <link href="../assets/css/bootstrap_action_button.css" rel="stylesheet">
  <!-- Original css -->
  <!-- http://runstant.com/chicken_yuta/projects/abca71b5 -->
  <!-- 王冠 -->
  <!-- http://runstant.com/chicken_yuta/projects/abca71b5 -->
  <link href="../assets/css/ranking_crown.css" rel="stylesheet">
  <!-- アコーディオンの矢印の実装 -->
  <!-- http://fontawesome.io/icon/angle-double-up/ -->
  <!-- http://fontawesome.io/icon/angle-double-down/ -->
  <!-- アコーディオンの実装 -->
  <!-- http://webgaku.hateblo.jp/entry/jquery-table-accordion -->
  <link href="../assets/css/ranking_arrow.css" rel="stylesheet">
  <!-- カーソルの実装 -->
  <!-- http://h2ham.seesaa.net/article/208820291.html -->
  <!-- http://matsudam.com/blog/entry/712 -->
  <link href="../assets/css/ranking_cursor.css" rel="stylesheet">


  <title>Bookrus-知らない本と出会う-</title>
</head>
<body>


  <?php require('layout/header.php'); ?>

  <div class="container">
    <div class="row">
      <?php require('layout/left_sidebar.php'); ?>


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


      <?php require('layoput/right_sidebar.php'); ?>
    </div><!-- row 閉じタグ -->
  </div><!-- container 閉じタグ -->

  <script src="../assets/js/jquery.js"></script>
  <script src="../assets/js/jquery-migrate.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <script src="../assets/js/book_regi.js"></script>
  <script src="../assets/js/dashboard.js"></script>
  <script src="../assets/js/bootstrap_ranking.js"></script>
  <script src="../assets/js/ranking.js"></script>
  <script src="../assets/js/ranking_crwon.js"></script>
  <script src="../assets/js/ranking_arrow.js"></script>
  <script src="../assets/js/ranking_cursor.js"></script>


</body>
</html>