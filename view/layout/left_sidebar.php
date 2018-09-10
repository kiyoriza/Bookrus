<div class="col-xs-2">
  <div class="login-container">
      <div id="output"></div>
      <a href=""><img src="../assets/img/content2.jpeg" width="120px" height="160px"></a>
      <div class="form-box">
        <h4>タイトル</h4><br>
        <h4>フレーズ</h4><br>
        <h4>著者名</h4>
      </div>
  </div>
         
  <div class="panel panel-default" id="panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">カテゴリ</h3>
    </div>
    <ul class="list-group">
      <?php for($i=0; $i < $b; $i++):?>
        <?php // for(初期化式(一回しか読み込まれない); 条件式; 変化式):?>
        <li class="list-group-item">
            <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                <a href="dashboard_category.php?category_id=<?php echo $unselected_categories[$i]['category_id']; ?>" style="color: black; text-decoration: none;"><?php echo $unselected_categories[$i]['name']; ?></a>
            </div>
        </li>
      <?php endfor; ?>
    </ul>
  </div>
</div><!-- col-xs-2 閉じタグ-->