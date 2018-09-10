<?php
require('../dbconnect.php');
session_start();

// ログイン
$error['login'] = '';
$email= '';

if (!empty($_POST)) {
    echo "hoge";
    $email = $_POST['email'];
    if ($_POST['email'] != '' && $_POST['password'] != '') {
        $sql = 'SELECT * FROM `users` WHERE `email`= ? AND `password` = ?';
        $data = array($_POST['email'], sha1($_POST['password']));
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);
            if ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION['id'] = $record['user_id'];
                $_SESSION['time'] = time();
                header('Location:dashboard.php');
                exit();
            }else{
                $error['login'] = 'failed';
            }
        }else {
            $error['login'] = 'blank';
        }
    }

// 本表示
$sql = 'SELECT * FROM `books`';
$stmt = $dbh->prepare($sql);
$stmt->execute();

$book_detail = array();
while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // 読破カウント
    $finish_reading_id = $book['book_id'];
    // var_dump($finish_reading_id);
    $sql = 'SELECT COUNT(*) AS `cnt` FROM `finish_reading` WHERE book_id=?';
    $data = array($finish_reading_id);
    $finish_reading_stmt = $dbh->prepare($sql);
    $finish_reading_stmt->execute($data);
    $finish_reading = $finish_reading_stmt->fetch(PDO::FETCH_ASSOC);
    // bookmarkカウント
    $book_mark = $book['book_id'];
    $sql = 'SELECT COUNT(*) AS `cnt` FROM `bookmark` WHERE book_id=?';
    $data_book_mark = array($book_mark);
    $book_mark_stmt = $dbh->prepare($sql);
    $book_mark_stmt->execute($data_book_mark);
    $book_mark = $book_mark_stmt->fetch(PDO::FETCH_ASSOC);

    $score_finish = $finish_reading['cnt'] * 3;
    $score_like = $book_mark['cnt'] * 2;
    $score_total = $score_finish + $score_like;

    $book_detail[] = array(
        "picture_url_API" =>$book['picture_url_API'],
        "title" => $book['title'],
        "author_API" => $book['author_API'],
        "phrase" => $book['phrase'],
        "finish_reading" => $finish_reading['cnt'],
        "book_mark" => $book_mark['cnt'],
        "score_total" => $score_total);
    }
// 降順指定
foreach ($book_detail as $key => $value) {
    $tmp_arr[$key]=$value['score_total'];
}
array_multisort($tmp_arr,SORT_DESC,$book_detail);
// var_dump($book_detail);
// 上位2件表示

// for ($i=0; $i <2 ; $i++) {
//   // echo $book_detail[$i]['picture'] . '<br>';
//   echo $book_detail[$i]['title'] . '<br>';
//   // echo $book_detail[$i]['autor'] . '<br>';
//   echo $book_detail[$i]['phrase'] . '<br>';
//   echo $book_detail[$i]['book_mark'] . '<br>';
//   echo $book_detail[$i]['finish_reading'] . '<br>';
// }

?>
</body>
</html>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="../assets/css/bootstrap.css" rel="stylesheet">
  <!-- <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet"> -->
  <!-- Original css -->

  <!-- 本の実装 -->
  <!-- http://bootsnipp.com/snippets/featured/simple-carousel -->
  <!-- <link href="../assets/css/bootstrap_top_book.css" rel="stylesheet"> -->
  <!-- ボタンの実装 -->
  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
  <!-- <link href="../assets/css/bootstrap_action_button.css" rel="stylesheet"> -->
  <!-- 文字の実装 -->
  <!-- http://www.nxworld.net/tips/50-css-heading-styling.html -->
  <link href="../assets/css/top_letter.css" rel="stylesheet">
  <!-- ログイン新規登録 -->
  <!-- http://bootsnipp.com/snippets/xdp9 -->
  <link href="../assets/css/bootstrap_top_login.css" rel="stylesheet">
  <!-- 背景画像 -->
  <link href="../assets/css/top_backgrand_picture.css" rel="stylesheet">

  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <link href="../assets/css/action_bottom.css" rel="stylesheet">


  <title>Bookrus-知らない本と出会う-</title>

</head>
<body class="body">
  <div class="container jumbotron top-content">
    <div class="row wrapper">
      <div class="col-xs-6">
        <!-- 文言 -->
        <p class="letter">Bookrus 読書のすすめ!</p>
        <p class="letter2">あなたの知らない本がそこにある!!!</p>
        <!-- 新規会員登録ボタン -->
        <!-- <button type="button" class="click" href="">新規登録</button> -->
      </div>
      <div class="col-xs-6">
        <!-- ログインフォーム -->
        <div class="container-login">
          <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                <div style="padding-top:30px;" class="panel-body" id="hoge2" >
                  <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                  </div>
                  <form method="POST" action="" id="loginform" class="form-horizontal" role="form">
                    <p class="letter3">メールアドレス</p>
                    <div style="margin-bottom: 25px" class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-user"></i>
                      </span>
                      <input id="login-username" type="email" class="form-control" name="email"value="<?php echo $email; ?>" placeholder="ユーザーネーム">
                    </div>
                    <p class="letter3">パスワード</p>
                    <div style="margin-bottom: 25px" class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-lock"></i>
                      </span>
                      <input id="login-password" type="password" class="form-control" name="password" placeholder="パスワード">
                    </div>
                    <div>
                      <?php if ($error['login'] == 'blank'): ?>
                        <p class="error">*メールアドレスとパスワードをご記入ください。</p>
                      <?php endif; ?>
                      <?php if ($error['login'] == 'failed'): ?>
                        <p class="error">*ログインに失敗しました。正しくご記入ください。</p>
                      <?php endif; ?>
                    </div>
                    <div style="margin-top:10px; margin-bottom:0px;" class="form-group">
                      <div class="col-sm-12">
                        <!-- <a id="btn-login" href="#" class="btn btn-primary">ログイン</a> -->
                        <input type="submit" value="ログイン" id="btn-login" href="#" class="btn btn-primary">
                        <a href="../join/index.php" style="float: right;" class="btn btn-danger"> 新規登録 </a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div id="signupbox" style="display:none; margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <div class="panel-title">Sign Up</div>
                    <div style="float:right; font-size: 85%; position: relative; top:-10px">
                      <a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">
                        Sign In
                      </a>
                    </div>
                  </div>
                  <div class="panel-body">
                    <form id="signupform" class="form-horizontal" role="form">
                      <div id="signupalert" style="display:none" class="alert alert-danger">
                        <p>Error:</p>
                        <span></span>
                      </div>
                      <div class="col-md-9">
                        <input type="text" class="form-control" name="email" placeholder="Email Address">
                      </div>
                      <div class="form-group">
                        <label for="firstname" class="col-md-3 control-label">First Name</label>
                        <div class="col-md-9">
                          <input type="text" class="form-control" name="firstname" placeholder="First Name">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="lastname" class="col-md-3 control-label">Last Name</label>
                        <div class="col-md-9">
                          <input type="text" class="form-control" name="lastname" placeholder="Last Name">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="password" class="col-md-3 control-label">Password</label>
                        <div class="col-md-9">
                          <input type="password" class="form-control" name="passwd" placeholder="Password">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="icode" class="col-md-3 control-label">Invitation Code</label>
                        <div class="col-md-9">
                          <input type="text" class="form-control" name="icode" placeholder="">
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
     // 下記構造にあう配列を作成
     $c = count($book_detail);
     $recommend_books = array_chunk($book_detail, 4);
     $page = count($recommend_books);
     // var_dump($recommend_books);
  ?>
  <!-- 本表示 -->
  <div class="container">
    <div id="Carousel" class="carousel slide">
      <h3>おすすめの書籍</h3>
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
              <?php foreach ($recommend_books[$i] as $recommend_book): ?>
                <div class="col-xs-3">
                  <!-- <a href="book_detail.php" class="thumbnail"> -->
                  <img src="<?php echo $recommend_book['picture_url_API']; ?>" alt="Image" style="max-width:100%;">
                  </a>
                  <?php echo $recommend_book['title']; ?>
                  <br>
                  <?php echo $recommend_book['author_API']; ?>
                  <br>
                  <?php echo $recommend_book['phrase']; ?>
                  <br>
                  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
                  <!-- ボタン -->
                  <button type="button" class="btn btn-info btn-circle">
                    <i class="glyphicon glyphicon-ok"></i>
                  </button><?php echo $recommend_book['book_mark']; ?>
                  <button type="button" class="btn btn-danger btn-circle">
                    <i class="glyphicon glyphicon-heart"></i>
                  </button><?php echo $recommend_book['finish_reading']; ?>
                </div><!-- col-xs-3 -->
              <?php endforeach; ?>
            </div><!--.row-->
          </div><!--.item-->
      <?php endfor; ?>
      </div><!--.carousel-inner-->
      <a data-slide="prev" href="#Carousel" class="left carousel-control">‹</a>
      <a data-slide="next" href="#Carousel" class="right carousel-control">›</a>
    </div><!--.Carousel-->
  </div><!-- .container-->

  <script src="../assets/js/jquery.js"></script>
  <script src="../assets/js/jquery-migrate.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <!-- <script src="../assets/js/bootstrap_top_book.js"></script> -->
  <script src="../assets/js/book_regi.js"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>