<?php
session_start();
require('../dbconnect.php');
require('../function/login_check.php');
require('../function/get_categories.php');

if (!empty($_POST)) {
    $_SESSION['title']=$_POST['title'];
    $_SESSION['phrase']=$_POST['phrase'];
}

//検索機能
$id = array();
$title = array();
$author = array();
$image = array();

$query = $_SESSION['title'];
$data = 'https://www.googleapis.com/books/v1/volumes?q='.$query;
$json = file_get_contents($data); // APIからJSONデータを取得
$json_decode = json_decode($json); // PHPで使用できるフォーマットに変換

$posts = $json_decode->items;
$c = count($posts);
for ($i=0; $i < $c; $i++){
    if (isset($posts[$i]->id)) {
        $id[] = $posts[$i]->id;
    } else {
        $id[] = 'なし';
    }
    if (isset($posts[$i]->volumeInfo->title)) {
        $title[] = $posts[$i]->volumeInfo->title;
    } else {
        $title[] = 'なし';
    }
    if (isset($posts[$i]->volumeInfo->authors[0])) {
        $author[] = $posts[$i]->volumeInfo->authors[0];
    } else {
        $author[] = 'なし';
    }
    if (isset($posts[$i]->volumeInfo->imageLinks->thumbnail)) {
        $image[] = $posts[$i]->volumeInfo->imageLinks->thumbnail;
    } else {
        $image[] = '';
    }
}

     //新規登録が押されたとき
if (!empty($_POST['register'])) {
  if ($_POST['data'] != '' && $_POST['phrase'] != '') {
      echo'<pre>';
      var_dump($_POST);
      echo'</pre>';
      $data = explode('+',$_POST['data']);
      $sql = 'INSERT INTO `books` SET `title`=?, `phrase`=?, `user_id`=?, `author_API`=?, `API_id`=?, `picture_url_API`=?, `category_id`=?,`created`=NOW()';
      $data = array($_POST['title'], $_POST['phrase'], $_SESSION['id'], $data[0], $data[1], $data[2], $_POST['category_id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      // 下のheader関数はいつも手動でやっているURLを更新する処理をPHPでやっている処理
      header('Location: mypage.php?id='.$_SESSION['id']);
      exit();
  }
}

// var_dump($categories[]);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <!-- 共通リンク -->
  <?php require('layout/common_links.php'); ?>
  <!-- ユーザーアイコンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/user-detail -->
  <link href="../assets/css/book_user.css" rel="stylesheet">
  <!-- 大枠の本の編集のcss -->
  <link href="../assets/css/book_edit_body.css" rel="stylesheet">
  <!-- アクションボタンのcss -->
  <!-- http://bootsnipp.com/snippets/featured/circle-button -->
  <link href="../assets/css/book_action_button.css" rel="stylesheet">
  <!-- 本のスペースのcss -->
  <link href="../assets/css/space.css" rel="stylesheet">
  <!-- ようさんOriginal css -->
  <link href="../assets/css/main.css" rel="stylesheet">
 



  <title>Bookrus-知らない本と出会う-</title>
</head>
<body>
  
<?php require('layout/header.php'); ?>

<div class="container">
  <div class="row">
    <?php require('layout/left_sidebar.php') ?>

    <div class="col-sm-10 col-md-8 col-lg-8" style="margin-top: 70px; padding-left: 90px; padding-right: 70px; ">
      <!-- ユーザーアイコン -->
        <div class="media">
          <a class="pull-left" href="#">
          <!-- ログインユーザー画像表示 -->
          <img class="media-object dp img-circle" src="../member_picture/<?php echo $login_user['picture_path']; ?>" style="width: 100px;height:100px;">
          </a>
          <div class="media-body">
            <br>
            <br>
            <h4 class="media-heading"><?php echo $login_user['user_name']; ?>さんの本の新規登録</h4>
            <hr style="margin:8px auto">
          </div>
        </div>

      <form method="POST" action="" class="form-horizontal">
        <?php for ($i=0; $i < $c; $i++) :  ?>
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>以下の本でお間違いないですか？</h4>
            <br>
            <br>
            <!-- <div> -->
            <div class="col-lg-12 col-md-12">
            <?php if(!empty($image[$i])): ?>
              <img src="<?php echo $image[$i]; ?>" width="200px" height="300px" alt="" align="left" class="space">
            <?php else: ?>
              <img src="../assets/no_cover_thumb.gif"><br>
            <?php endif; ?>
              <br>
              <div class="title"><?php echo $title[$i]; ?></div>
              <div class="author">（著）<?php echo $author[$i]; ?></div>
              <div>この本を選択:
                <?php $data = $author[$i] . '+' . $id[$i] . '+' . $image[$i];?>
                <input type="radio" name="data" value="<?php echo $data; ?>">
                <br>
              </div>
              <br>
            </div>
          </div>
        </div>
        <?php endfor; ?>
        <div>この本を選択:
          <?php for($i=0; $i < $b; $i++):?>
            <?php echo $unselected_categories[$i]['name']; ?>
            <input type="radio" name="category_id" value="<?php echo $unselected_categories[$i]['category_id']; ?>">
          <?php endfor; ?>
        </div>
        <div class="col-lg-12 col-md-12">
          <br>
          <input type="hidden" name="register" value="本を登録">
          <input name="title" type="hidden" class="form-control" id="title" placeholder="タイトル" value="<?php echo $_SESSION['title']; ?>">
          <input name="phrase" type="hidden" class="form-control" id="phrase" placeholder="フレーズ" value="<?php echo $_SESSION['phrase']; ?>">
          <button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-check" aria-hidden="true"></i>登録</button>
        </div>
      </form>
      <!-- ここまでが本の編集のコード -->
    </div><!-- col-xs-8 閉じタグ-->

    <?php require('layout/right_sidebar.php') ?>

  </div><!-- row 閉じタグ -->
</div><!-- container 閉じタグ -->


  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
  <!--  本の編集のjs -->
  <script src="../assets/js/book_edit_body.js"></script>
  <!-- ダッシュボードのjs -->
  <script src="../assets/js/book_regi.js"></script>
  <script src="../assets/js/dashboard.js"></script>

</body>
</html>