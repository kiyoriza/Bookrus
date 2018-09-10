<div class="hidden-sm col-md-2 col-lg-2" style="">

  <!-- http://bootsnipp.com/snippets/featured/simple-contact-form -->
  <!-- Simple Contact Form 本の登録 -->
  <div class="form-area" style="background-image: url(../assets/img/book2.jpg); background-size: cover; opacity: 0.9; height: 250px">
      <form role="form" class="form" method="POST" action="book_edit_new.php">
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