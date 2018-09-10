<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="../assets/css/bootstrap.css" rel="stylesheet">
  <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <!-- Original css -->
  <link rel="stylesheet" href="../assets/css/join/thanks.css">
  <link rel="stylesheet" href="../assets/css/join/step_wizard_working"">
  <link rel="stylesheet" href="../assets/css/join/password-strength-popover"">

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
          <li class="disabled"><a href="#step-2" class="disabled_disabled">
            <h4 class="list-group-item-heading">入力内容の確認</h4>
          </a></li>
          <li class="active"><a href="#step-3" class="active_active">
            <h4 class="list-group-item-heading">登録完了</h4>
          </a></li>
        </ul>
      </div><!--[end].col-xs-8-->
      <div class="col-xs-2">
      </div><!--[end].col-xs-2-->
    </div><!--[end].row form-group-->
    <div class="row setup-content" id="step-3">
      <div class="col-xs-12">
        <div class="col-xs-2">
        </div><!--[end].col-xs-2-->
        <div class="col-md-8 well">
    	    <div class="completion">
            <h2 class="text-center">会員登録が完了しました</h2>
            <h5 class="text-center">ご登録いただき誠にありがとうございます。Booklus会員登録が完了いたしました。<br>
            ご登録いただきましたユーザーID・パスワードでログインが可能です。</h5>
          </div><!-- [end].completion -->
            <div class="btn_btn text-center">
              <a href="../view/top.php"><button id="activate-step-2" class="btn btn-primary btn-lg">トップページへ</button></a>
            </div><!--[end].btn_btn text-center-->
        </div><!--[end].col-md-8 .well -->
        <div class="col-xs-2">
        </div><!--[end].col-xs-2-->

      </div><!--[end].col-xs-12-->
    </div><!--[end]row setup-content #step-3-->
  </div><!--[end].container #wrapper-->

  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
</body>
</html>