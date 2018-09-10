<?php
session_start();
require ('../dbconnect.php');

// カテゴリ取得
$sql = 'SELECT * FROM `categories`';
$stmt = $dbh->prepare($sql);
$stmt->execute();

$categories = array();
while (1) {
  $record = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($record == false) {
    break;
  }
  $categories[] = $record;
}
$count = count($categories);


if (!isset($_SESSION['join'])){
  header('Location: index.php');
  exit();
}
if (!empty($_POST)) {
  try {
    // ユーザーデータ登録
    $sql = 'INSERT INTO `users` SET `user_name`=?, `email`=?, `password`=?, `picture_path`=?, `created`=NOW()';

    $data = array($_SESSION['join']['user_name'], $_SESSION['join']['email'], sha1($_SESSION['join']['password']), $_SESSION['join']['picture_path']);

    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    // ユーザーデータを取得（メールアドレスを条件に）
    $sql = 'SELECT `user_id` FROM `users` WHERE `email`=?';
    $data = array($_SESSION['join']['email']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // $user['user_id']

    // 選択されたカテゴリーデータの登録（繰り返し処理）
    $count = count($_SESSION['join']['category_id']);
    for ($i=0; $i < $count; $i++) {
      $sql = 'INSERT INTO `user_selected_category` SET `category_id`=?, `user_id`=?, `created`=NOW()';

      $data = array($_SESSION['join']['category_id'][$i], $user['user_id']);

      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
    }

    header('Location: thanks.php');
    exit();

  } catch (PDOException $e) {
    echo 'SQL実行時エラー: ' . $e->getMessage();
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
  <link rel="stylesheet" href="../assets/css/join/index.css">
  <link rel="stylesheet" href="../assets/css/join/profile_picture_with_badge.css">
  <link rel="stylesheet" href="../assets/css/join/step_wizard_working">
  <link rel="stylesheet" href="../assets/css/join/password-strength-popover">
  <link rel="stylesheet" href="../assets/css/join/check_boxes_animated.css">

  <title>Bookrus-知らない本と出会う-</title>
</head>
<body>
<div class="container" id="wrapper">
  <!-- プロセス -->
  <!-- http://bootsnipp.com/snippets/featured/step-wizard-working -->
  <div class="row form-group">
    <div class="col-xs-2">
    </div><!--[end].col-xs-2-->
    <div class="col-xs-8">
      <ul class="nav nav-pills nav-justified thumbnail setup-panel">
        <li class="disabled"><a href="#step-1" class="disabled_disabled">
          <h4 class="list-group-item-heading">会員情報の入力</h4>
        </a></li>
        <li class="active"><a href="#step-2" class="active_active">
          <h4 class="list-group-item-heading">入力内容の確認</h4>
        </a></li>
        <li class="disabled"><a href="#step-3" class="disabled_disabled">
          <h4 class="list-group-item-heading">登録完了</h4>
        </a></li>
      </ul>
    </div><!--[end].col-xs-8-->
    <div class="col-xs-2">
    </div><!--[end].col-xs-2-->
  </div><!--[end].row .form-group-->
  <div class="row setup-content" id="step-2">
    <div class="col-xs-2">
    </div><!--[end].col-xs-2-->
    <div class="col-xs-8" well">
      <h1 class="text-center">入力内容の確認</h1>
      <!-- ここから入力フォーム -->
      <form method="POST" action="check.php" class="form-horizontal" role="form">

        <!-- プロフィール写真 -->
        <!-- http://bootsnipp.com/snippets/featured/profile-picture-with-badge -->
        <div class="profile-header-container col-xs-4">
          <div class="profile-header-img">
            <img class="img-circle" src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['picture_path']); ?>"/>
            <!-- badge -->
            <div class="rank-label-container">
              <span class="label label-default rank-label">photo</span>
            </div><!--[end].rank-label-container-->
          </div><!-- [end].profile-header-img -->
        </div><!-- [end].profile-header-container .col-xs-4 -->

        <div class="col-xs-8">
        <!-- 入力フォーム -->
        <!-- http://bootsnipp.com/snippets/featured/password-strength-popover -->
          <div class="form-group">
            <label>ユーザーネーム</label>
            <div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
              <input type="text" class="form-control" name="user_name" id="user_name" placeholder="例：山田　太郎" disabled="disabled" value="<?php echo htmlspecialchars($_SESSION['join']['user_name']); ?>">
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

          <div class="form-group">
            <label>メールアドレス</label>
            <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
             <input type="email" class="form-control" name="email" id="email" placeholder="例：bookrus.book.com" disabled="disabled" value="<?php echo htmlspecialchars($_SESSION['join']['email']); ?>">
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

          <div class="form-group">
            <label>パスワード</label>
            <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
            <input type="password" class="form-control" name="password" id="password" placeholder="●●●●" disabled="disabled value="●●●●">
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

          <div class="form-group">
            <label>カテゴリー選択</label>
            <div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-check"></span></span>

              <!-- チェックボックス -->
              <!-- http://bootsnipp.com/snippets/pjKqe -->
              <div class="col-xs-12">
                <div class="checkbox">
                  <?php $count = count($_SESSION['join']['category_id']); ?>
                  <?php for ($i=0; $i < $count; $i++): ?>
                    <label>
                      <!-- <input type="text" disabled="disabled" value=""> -->
                      <!-- <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> -->
                      <?php $sc_id = $_SESSION['join']['category_id'][$i] - 1; ?>
                      <?php echo $categories[$sc_id]['name'];?>
                    </label>
                  <?php endfor; ?>
                </div><!--[end].checkbox-->
              </div><!--[end].col-xs-12-->
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

        </div><!--[end].col-xs-8-->
        <div class="btn_btn text-center">
          <a href="index.php?action=rewrite" id="activate-step-2" class="btn btn-primary btn-lg">前に戻る</a>
          <input type="hidden" name="sample" value="sample"><!-- ダミーinputボタン設置 -->
          <button type="submit" id="activate-step-2" class="btn btn-primary btn-lg">登録完了へ</button>
        </div><!--[end].btn_btn text-center -->
      </form>
    </div><!--[end].col-xs-8 .well -->
    <div class="col-xs-2">
    </div><!--[end].col-xs-2-->
  </div><!--[end]row setup-content #step-2-->
</div><!--[end].container #wrapper-->

  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <script src="../assets/js/step_wizard_working.js"></script>
  <script src="../assets/js/password-strength-popover.js"></script>

</body>
</html>