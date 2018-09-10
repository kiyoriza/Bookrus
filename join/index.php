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


$user_name = '';
$email = '';
$password = '';
$password_check = '';
$category_id = '';

if (!empty($_POST)) {
	$user_name = $_POST['user_name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
  $password_check = $_POST['password_check'];

  if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id']; // 配列
  } else {
    $category_id = array();
  }

  var_dump($category_id);
  $selected_count = count($category_id);

	// フォームのバリデーション
	$error = array();

	// ニックネーム入力チェック
	if ($_POST['user_name'] == '') {
		$error['user_name'] = 'blank';
	}

	// メールアドレス入力チェック
	if ($_POST['email'] == '') {
		$error['email'] = 'blank';
	}

	// パスワード入力チェック
	if ($_POST['password'] == '') {
		$error['password'] = 'blank';
	} elseif (strlen($_POST['password']) < 4) {
		$error['password'] = 'length';
	}

  // パスワード確認チェック
  if ($_POST['password_check'] == '') {
    $error['password_check'] = 'blank';
  } elseif ($_POST['password_check'] != $_POST['password']) {
    $error['password_check'] = 'incorrect';
  }

  // $category_id配列の要素数が0じゃないか
  if ($selected_count > 0) {
    //
  } else {
    $error['category_id'] = 'blank';
  }

	// 画像入力チェック
	if ($_FILES['picture_path']['name'] == '') {
		$_FILES['picture_path']['name'] = 'blank';
	}

	// プロフィール画像の拡張子チェック
	$fileName = $_FILES['picture_path']['name'];


	if(!empty($fileName)) {
		$ext = substr($fileName, -3);
		if ($ext != 'jpg' && $ext !='png' && $ext != 'gif') {
			$error['picture_path'] = 'type';
		}
	}

	// メール重複チェック
	if (empty($error)) {
    echo 'hoge1';
		$sql = 'SELECT COUNT(*) AS `cnt` FROM `users` WHERE `email`=?';
		$data = array($email);
		$stmt =$dbh->prepare($sql);
		$stmt->execute($data);

		$record =$stmt->fetch(PDO::FETCH_ASSOC);
    echo $record['cnt'];
		if ($record['cnt'] > 0) {
      echo 'hoge2';
			$error['email'] = 'duplicate';
		}
	}

  // エラーがなかったとき
  if(empty($error)){
    $picture_path = date('YmdHis') . $_FILES['picture_path']['name'];
    move_uploaded_file($_FILES['picture_path']['tmp_name'],'../member_picture/' . $picture_path);


    // セッションに値を保持する
    $_SESSION['join'] = $_POST;
    $_SESSION['join']['picture_path'] = $picture_path;
    // $_SESSION['']


    // 確認画面へ遷移
    header('Location: check.php');
    exit();
  }
}

// 書き直し処理
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'rewrite'){
  $user_name = $_SESSION['join']['user_name'];
  $email = $_SESSION['join']['email'];
  $category_id = $_SESSION['join']['category_id'];
  $error['rewrite'] = 'rewrite';
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
        <li class="active"><a href="#step-1" class="active_active">
          <h4 class="list-group-item-heading">会員情報の入力</h4>
        </a></li>
        <li class="disabled"><a href="#step-2" class="disabled_disabled">
          <h4 class="list-group-item-heading">入力内容の確認</h4>
        </a></li>
        <li class="disabled"><a href="#step-3" class="disabled_disabled">
          <h4 class="list-group-item-heading">登録完了</h4>
        </a></li>
      </ul>
    </div><!--[end].col-xs-8 -->
    <div class="col-xs-2">
    </div><!--[end].col-xs-2-->
  </div><!--[end].row .form-group-->
  <div class="row setup-content" id="step-1">
    <div class="col-xs-2">
    </div><!--[end].col-xs-2-->
    <div class="col-xs-8 well no-padding">
      <h1 class="text-center">会員情報の入力</h1>
      <!-- ここから入力フォーム -->
      <form method="POST" action="index.php" class="form-horizontal" role="form" enctype="multipart/form-data">

        <!-- プロフィール写真 -->
        <!-- http://bootsnipp.com/snippets/featured/profile-picture-with-badge -->
        <div class="profile-header-container col-xs-4">
          <div class="profile-header-img">
           <img class="img-circle" src="" />
                <!-- badge -->
              <div class="rank-label-container">
                <span class="label label-default rank-label">photo</span>
              </div><!-- [end].rank-label-container-->
            <input type="file" name="picture_path" class="form-control">
              <?php if(isset($error['picture_path']) && $error['picture_path'] == 'type'): ?>
                <p style="color: red; text-align: left;">プロフィール画像の拡張子は「jpg」「png」「gif」のデータを指定してください</p>
              <?php endif; ?>
              <?php if(!empty($error)) :?>
                <p style="color: red; text-align: left;">画像を再度指定してください</p>
              <?php endif; ?>
          </div><!-- [end].profile-header-img -->
        </div><!-- [end].profile-header-container -->

        <div class="col-xs-8">
        <!-- 入力フォーム -->
        <!-- http://bootsnipp.com/snippets/featured/password-strength-popover -->
          <div class="form-group">
            <label>ユーザーネーム</label>
            <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
              <input type="text" class="form-control" name="user_name" id="user_name" placeholder="例：山田　太郎" value="<?php echo $user_name; ?>">
              <?php if (isset($error['user_name']) && $error['user_name'] == 'blank') :?>
              <p style="color: red;">ユーザーネームを入力してください</p>
              <?php endif; ?>
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

          <div class="form-group">
            <label>メールアドレス</label>
            <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
              <input type="email" class="form-control" name="email" id="email" placeholder="例：bookrus.book.com" value="<?php echo $email; ?>">
              <?php if (isset($error['email']) && $error['email'] == 'blank'):?>
                <p style="color: red">メールアドレスを入力してください</p>
              <?php endif;?>
              <?php if (isset($error['email']) && $error['email'] == 'duplicate') :?>
                <p style="color: red">指定したメールアドレスは既に登録されています</p>
              <?php endif; ?>
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

          <div class="form-group">
            <label>パスワード</label>
            <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
              <input type="password" class="form-control" name="password" id="password" placeholder="半角英数4文字以上で入力してください"  value="<?php echo $password; ?>">
              <?php if (isset($error['password_check']) && $error['password_check'] == 'blank') :?>
                <p style="color: red;">パスワードを入力してください</p>
              <?php endif; ?>
              <?php if(isset($error['password']) && $error['password'] == 'lenge'): ?>
                <p style="color: red">パスワードは４文字以上で入力してください</p>
              <?php endif; ?>
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

          <div class="form-group">
            <label>パスワード確認</label>
            <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-resize-vertical"></span></span>
              <input type="password" class="form-control" name="password_check" id="password_check" placeholder="もう一度パスワードを入力してください" value="<?php echo $password_check; ?>">
              <?php if(isset($error['password_check']) && $error['password_check'] == 'blank') :?>
                <p style="color: red;">もう一度パスワードを入力してください</p>
              <?php endif; ?>
              <?php if (isset($error['password_check']) && $error['password_check'] == 'incorrect'): ?>
                <p style="color: red">入力したパスワードと一致しません</p>
              <?php endif; ?>

            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

          <div class="form-group">
            <label>カテゴリー選択</label>
            <div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-check"></span></span>
              <!-- チェックボックス -->
              <!-- http://bootsnipp.com/snippets/pjKqe -->
              <div class="col-xs-12">
                <div class="checkbox">
                  <?php for ($i=0; $i < $count; $i++): ?>
                    <label>
                      <!-- $_POST['category_id'] = array('小説', '歴史') -->
                      <input type="checkbox" name="category_id[]" value="<?php echo $categories[$i]['category_id']; ?>">
                      <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                      <?php echo $categories[$i]['name']; ?>
                    </label>
                  <?php endfor; ?>
                  <?php if (isset($error['category_id']) && $error['category_id'] == 'blank') : ?>
                    <p style="color: red;">カテゴリーを選択してください</p>
                  <?php endif; ?>
                </div><!--[end].checkbox-->
              </div><!--[end].col-xs-12-->
            </div><!--[end].input-group-->
          </div><!--[end].form-group-->

        </div><!--[end].col-xs-8-->
        <div class="btn_btn text-center">
          <button type="submit" id="activate-step-2" class="btn btn-primary btn-lg">入力内容の確認へ</button>
        </div><!--[end].btn_btn text-center -->
      </form>
    </div><!--[end].col-xs-8 .well-->
    <div class="col-xs-2">
    </div><!--[end].col-xs-2-->
  </div><!--[end]row setup-content #step-1-->
</div><!--[end].container #wrapper-->

  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <script src="../assets/js/step_wizard_working.js"></script>
  <script src="../assets/js/password-strength-popover.js"></script>

</body>
</html>
