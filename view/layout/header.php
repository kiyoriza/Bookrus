
<?php
require('../dbconnect.php');
require('../user_info.php');
?>

<header class="header">
<!-- http://bootsnipp.com/snippets/featured/rainbow-nav -->
<!-- Rainbow Nav バー -->
<div class="navbar-wrapper">
  <div class="container-fluid">
    <nav class="navbar navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="dashboard.php" style="font-size: 40px; padding-top: 20px;">Bookrus</a>
        </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li class="active"><a href="ranking.php" class="" style="padding-top: 25px;">ランキング</a></li>
          <li class="dropdown"><a href="mypage.php" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">マイページ</a>
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
            <img class="img-circle" src="../member_picture/<?php echo $login_user_info['picture_path']; ?>" />
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
                        <img src="../member_picture/<?php echo $login_user_info['picture_path']; ?>" width="100">
                        </p>
                      </div>
                      <div class="col-lg-8">
                        <p class="text-left"><strong><?php echo $login_user_info['user_name']; ?></strong></p>
                        <p class="text-left">
                          <a href="../function/logout.php" class="btn btn-primary btn-block btn-sm">ログアウト</a>
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
