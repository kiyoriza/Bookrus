<?php 
session_start();
require('../dbconnect.php');
require('../user_info.php');
require('../function/login_check.php');
require('../function/ranking_func.php');
require('../function/get_categories.php');

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
// var_dump($ranking_users);

// rankをつける
$c = count($ranking_users);
for ($i = 0; $i < $c; $i++) {
    $ranking_users[$i]['rank'] = $i+1;
    if ($ranking_users[$i]['user_id'] == $_SESSION['id']) {
        $_SESSION['rank'] = $i + 1;
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
                <tbody class="hoge1 id="tbody">
                <?php for ($i=0; $i < $c; $i++): ?>
                  <?php $rank = $i + 1; ?>

                  <?php if($_SESSION['rank'] <= 5 && $rank <= 5): ?>
                    <?php if($_SESSION['rank'] == $ranking_users[$i]['rank']): ?>
                        <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                          <?php crown($ranking_users, $i,$rank); ?>
                          <td>
                             <img src="../member_picture/<?php showSelfUser_picture($ranking_users, $i); ?>" width="50px">
                          </td>
                          <td><?php showSelfUser_name($ranking_users, $i); ?></td>
                          <td>
                            <?php showSelfUser_like($ranking_users, $i); ?>
                            <button type="button" class="btn btn-info btn-circle">
                              <i class="glyphicon glyphicon-ok"></i>
                            </button>
                          </td>
                          <td>
                          <?php showSelfUser_finish($ranking_users, $i); ?>
                            <button type="button" class="btn btn-danger btn-circle">
                              <i class="glyphicon glyphicon-heart"></i>
                            </button>
                          </td>
                        </tr>
                    <?php else: ?>
                        <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                          <?php crown($ranking_users, $i,$rank); ?>
                          <td>
                             <img src="../member_picture/<?php showAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                          </td>
                          <td><?php showAnotherUser_name($ranking_users, $i) ; ?></td>
                          <td>
                            <?php showAnotherUser_like($ranking_users, $i); ?>
                            <button type="button" class="btn btn-info btn-circle">
                              <i class="glyphicon glyphicon-ok"></i>
                            </button>
                          </td>
                          <td>
                          <?php showAnotherUser_finish($ranking_users, $i); ?>
                            <button type="button" class="btn btn-danger btn-circle">
                              <i class="glyphicon glyphicon-heart"></i>
                            </button>
                          </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($rank == 5 && $_SESSION['rank'] != 5):?>
                      <?php arrow(); ?>
                    <?php endif; ?>

                  <?php elseif($_SESSION['rank'] == 6): ?>
                    <?php if ($rank <= 5): ?>
                        <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                          <?php crown($ranking_users, $i,$rank); ?>
                          <td>
                             <img src="../member_picture/<?php showAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                          </td>
                          <td><?php showAnotherUser_name($ranking_users, $i) ; ?></td>
                          <td>
                            <?php showAnotherUser_like($ranking_users, $i); ?>
                            <button type="button" class="btn btn-info btn-circle">
                              <i class="glyphicon glyphicon-ok"></i>
                            </button>
                          </td>
                          <td>
                          <?php showAnotherUser_finish($ranking_users, $i); ?>
                            <button type="button" class="btn btn-danger btn-circle">
                              <i class="glyphicon glyphicon-heart"></i>
                            </button>
                          </td>
                        </tr>
                    <?php elseif(6 <= $rank && 8 >= $rank): ?>
                      <?php if($_SESSION['rank'] == $ranking_users[$i]['rank']): ?>
                          <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                            <td class="rank-size">
                                <?php showSelfUser_rank($ranking_users, $i); ?>
                            </td>
                            <td>
                               <img src="../member_picture/<?php showSelfUser_picture($ranking_users, $i); ?>" width="50px">
                            </td>
                            <td><?php showSelfUser_name($ranking_users, $i); ?></td>
                            <td>
                              <?php showSelfUser_like($ranking_users, $i); ?>
                              <button type="button" class="btn btn-info btn-circle">
                                <i class="glyphicon glyphicon-ok"></i>
                              </button>
                            </td>
                            <td>
                            <?php showSelfUser_finish($ranking_users, $i); ?>
                              <button type="button" class="btn btn-danger btn-circle">
                                <i class="glyphicon glyphicon-heart"></i>
                              </button>
                            </td>
                          </tr>
                      <?php else: ?>
                          <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                            <td class="rank-size">
                              <?php showAnotherUser_rank($ranking_users, $i); ?>
                            </td>
                            <td>
                               <img src="<?php showAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                            </td>
                            <td><?php showAnotherUser_name($ranking_users, $i) ; ?></td>
                            <td>
                              <?php showAnotherUser_like($ranking_users, $i); ?>
                              <button type="button" class="btn btn-info btn-circle">
                                <i class="glyphicon glyphicon-ok"></i>
                              </button>
                            </td>
                            <td>
                            <?php showAnotherUser_finish($ranking_users, $i); ?>
                              <button type="button" class="btn btn-danger btn-circle">
                                <i class="glyphicon glyphicon-heart"></i>
                              </button>
                            </td>
                          </tr>
                      <?php endif; ?>
                    <?php else: ?>
                            <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>" class="hiderank">
                              <td class="rank-size">
                                <?php hideAnotherUser_rank($ranking_users, $i); ?>
                              </td>
                              <td>
                                 <img src="../member_picture/<?php hideAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                              </td>
                              <td><?php hideAnotherUser_name($ranking_users, $i) ; ?></td>
                              <td>
                                <?php hideAnotherUser_like($ranking_users, $i); ?>
                                <button type="button" class="btn btn-info btn-circle">
                                  <i class="glyphicon glyphicon-ok"></i>
                                </button>
                              </td>
                              <td>
                              <?php hideAnotherUser_finish($ranking_users, $i); ?>
                                <button type="button" class="btn btn-danger btn-circle">
                                  <i class="glyphicon glyphicon-heart"></i>
                                </button>
                              </td>
                            </tr>
                    <?php endif; ?>
                    <?php if ($rank == 8): ?>
                        <?php arrow(); ?>
                    <?php endif; ?>

                  <?php elseif ($_SESSION['rank'] == $c || $_SESSION['rank'] == $c-1 || $_SESSION['rank'] == $c-2) :?>
                    <?php if($rank <= 5): ?>
                        <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                          <?php crown($ranking_users, $i,$rank); ?>
                          <td>
                             <img src="../member_picture/<?php showAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                          </td>
                          <td><?php showAnotherUser_name($ranking_users, $i) ; ?></td>
                          <td>
                            <?php showAnotherUser_like($ranking_users, $i); ?>
                            <button type="button" class="btn btn-info btn-circle">
                              <i class="glyphicon glyphicon-ok"></i>
                            </button>
                          </td>
                          <td>
                          <?php showAnotherUser_finish($ranking_users, $i); ?>
                            <button type="button" class="btn btn-danger btn-circle">
                              <i class="glyphicon glyphicon-heart"></i>
                            </button>
                          </td>
                        </tr>
                    <?php elseif($c-2 <= $rank && $rank <= $c): ?>
                      <?php if($_SESSION['rank'] == $ranking_users[$i]['rank']): ?>
                          <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>  ">
                            <td class="rank-size">
                                <?php showSelfUser_rank($ranking_users, $i); ?>
                            </td>
                            <td>
                               <img src="../member_picture/<?php showSelfUser_picture($ranking_users, $i); ?>" width="50px">
                            </td>
                            <td><?php showSelfUser_name($ranking_users, $i); ?></td>
                            <td>
                              <?php showSelfUser_like($ranking_users, $i); ?>
                              <button type="button" class="btn btn-info btn-circle">
                                <i class="glyphicon glyphicon-ok"></i>
                              </button>
                            </td>
                            <td>
                            <?php showSelfUser_finish($ranking_users, $i); ?>
                              <button type="button" class="btn btn-danger btn-circle">
                                <i class="glyphicon glyphicon-heart"></i>
                              </button>
                            </td>
                          </tr>
                      <?php else: ?>
                          <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                            <td class="rank-size">
                              <?php hideAnotherUser_rank($ranking_users, $i); ?>
                            </td>
                            <td>
                               <img src="../member_picture/<?php hideAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                            </td>
                            <td><?php hideAnotherUser_name($ranking_users, $i) ; ?></td>
                            <td>
                              <?php hideAnotherUser_like($ranking_users, $i); ?>
                              <button type="button" class="btn btn-info btn-circle">
                                <i class="glyphicon glyphicon-ok"></i>
                              </button>
                            </td>
                            <td>
                            <?php hideAnotherUser_finish($ranking_users, $i); ?>
                              <button type="button" class="btn btn-danger btn-circle">
                                <i class="glyphicon glyphicon-heart"></i>
                              </button>
                            </td>
                          </tr>
                      <?php endif; ?>
                    <?php else: ?>
                        <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>" class="hiderank">
                          <td class="rank-size">
                            <?php hideAnotherUser_rank($ranking_users, $i); ?>
                          </td>
                          <td>
                             <img src="../member_picture/<?php hideAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                          </td>
                          <td><?php hideAnotherUser_name($ranking_users, $i) ; ?></td>
                          <td>
                            <?php hideAnotherUser_like($ranking_users, $i); ?>
                            <button type="button" class="btn btn-info btn-circle">
                              <i class="glyphicon glyphicon-ok"></i>
                            </button>
                          </td>
                          <td>
                          <?php hideAnotherUser_finish($ranking_users, $i); ?>
                            <button type="button" class="btn btn-danger btn-circle">
                              <i class="glyphicon glyphicon-heart"></i>
                            </button>
                          </td>
                        </tr>
                    <?php endif; ?>
                    <?php if($rank == 5): ?>
                      <?php arrow(); ?>
                    <?php endif; ?>

                  <?php else: ?>
                    <?php if ($rank <= 5): ?>
                        <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                          <?php crown($ranking_users, $i,$rank); ?>
                          <td>
                             <img src="../member_picture/<?php showAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                          </td>
                          <td><?php showAnotherUser_name($ranking_users, $i) ; ?></td>
                          <td>
                            <?php showAnotherUser_like($ranking_users, $i); ?>
                            <button type="button" class="btn btn-info btn-circle">
                              <i class="glyphicon glyphicon-ok"></i>
                            </button>
                          </td>
                          <td>
                          <?php showAnotherUser_finish($ranking_users, $i); ?>
                            <button type="button" class="btn btn-danger btn-circle">
                              <i class="glyphicon glyphicon-heart"></i>
                            </button>
                          </td>
                        </tr>
                    <?php elseif($_SESSION['rank']-1 <= $rank && $_SESSION['rank']+1 >= $rank): ?>
                      <?php if($_SESSION['rank'] == $ranking_users[$i]['rank']): ?>
                          <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                            <td class="rank-size">
                                <?php showSelfUser_rank($ranking_users, $i); ?>
                            </td>
                            <td>
                               <img src="../member_picture/<?php showSelfUser_picture($ranking_users, $i); ?>" width="50px">
                            </td>
                            <td><?php showSelfUser_name($ranking_users, $i); ?></td>
                            <td>
                              <?php showSelfUser_like($ranking_users, $i); ?>
                              <button type="button" class="btn btn-info btn-circle">
                                <i class="glyphicon glyphicon-ok"></i>
                              </button>
                            </td>
                            <td>
                            <?php showSelfUser_finish($ranking_users, $i); ?>
                              <button type="button" class="btn btn-danger btn-circle">
                                <i class="glyphicon glyphicon-heart"></i>
                              </button>
                            </td>
                          </tr>
                      <?php else: ?>
                          <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>">
                            <td class="rank-size">
                              <?php showAnotherUser_rank($ranking_users, $i); ?>
                            </td>
                            <td>
                               <img src="../member_picture/<?php showAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                            </td>
                            <td><?php showAnotherUser_name($ranking_users, $i) ; ?></td>
                            <td>
                              <?php showAnotherUser_like($ranking_users, $i); ?>
                              <button type="button" class="btn btn-info btn-circle">
                                <i class="glyphicon glyphicon-ok"></i>
                              </button>
                            </td>
                            <td>
                            <?php showAnotherUser_finish($ranking_users, $i); ?>
                              <button type="button" class="btn btn-danger btn-circle">
                                <i class="glyphicon glyphicon-heart"></i>
                              </button>
                            </td>
                          </tr>
                      <?php endif; ?>
                    <?php else: ?>
                        <tr data-href="mypage.php?user_id=<?php echo $ranking_users[$i]['user_id']; ?>" class="hiderank">
                          <td class="rank-size">
                            <?php hideAnotherUser_rank($ranking_users, $i); ?>
                          </td>
                          <td>
                             <img src="../member_picture/<?php hideAnotherUser_picture($ranking_users, $i); ?>" width="50px">
                          </td>
                          <td><?php hideAnotherUser_name($ranking_users, $i) ; ?></td>
                          <td>
                            <?php hideAnotherUser_like($ranking_users, $i); ?>
                            <button type="button" class="btn btn-info btn-circle">
                              <i class="glyphicon glyphicon-ok"></i>
                            </button>
                          </td>
                          <td>
                          <?php hideAnotherUser_finish($ranking_users, $i); ?>
                            <button type="button" class="btn btn-danger btn-circle">
                              <i class="glyphicon glyphicon-heart"></i>
                            </button>
                          </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($rank == 5 || $rank == $_SESSION['rank']+1): ?>
                      <?php arrow(); ?>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php endfor; ?>
                </tbody>
              </table>
            </div><!-- panel panel-primary filterable 閉じタグ -->
          </div><!--  rank2 閉じタグ -->
        </div><!--  ranking 閉じタグ -->

      </div><!-- col-xs-8 閉じタグ-->
      <?php require('layout/right_sidebar.php'); ?>
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