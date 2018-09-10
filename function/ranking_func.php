<?php 
function showSelfUser_rank($ranking_users, $i) {
    echo $ranking_users[$i]['rank'] . '位';
}
function showSelfUser_name($ranking_users, $i) {
    echo $ranking_users[$i]['user_name'];
}
function showSelfUser_picture($ranking_users, $i) {
    echo $ranking_users[$i]['picture_path'];
}
function showSelfUser_like($ranking_users, $i) {
    echo $ranking_users[$i]['score_like'];
}
function showSelfUser_finish($ranking_users, $i) {
    echo $ranking_users[$i]['score_finish'];
}




function showAnotherUser_rank($ranking_users, $i) {
    echo $ranking_users[$i]['rank'] . '位';
}
function showAnotherUser_name($ranking_users, $i) {
    echo $ranking_users[$i]['user_name'];
}
function showAnotherUser_picture($ranking_users, $i) {
    echo $ranking_users[$i]['picture_path'];
}
function showAnotherUser_like($ranking_users, $i) {
    echo $ranking_users[$i]['score_like'];
}
function showAnotherUser_finish($ranking_users, $i) {
    echo $ranking_users[$i]['score_finish'];
}


function hideAnotherUser_rank($ranking_users, $i) {
    echo $ranking_users[$i]['rank'] . '位';
}
function hideAnotherUser_name($ranking_users, $i) {
    echo $ranking_users[$i]['user_name'];
}
function hideAnotherUser_picture($ranking_users, $i) {
    echo $ranking_users[$i]['picture_path'];
}
function hideAnotherUser_like($ranking_users, $i) {
    echo $ranking_users[$i]['score_like'];
}
function hideAnotherUser_finish($ranking_users, $i) {
    echo $ranking_users[$i]['score_finish'];
}


// 王冠の表示
function crown($ranking_users, $i,$rank){
    if ($rank <= 3) {
        if ($rank == 1) {
            // 金の王冠
            echo '<td class="crown">';
                      echo '<div class="panel-crown">';               
                        echo'<div class="g-crown">';
                          echo '<div class="g-crown-circle"></div>';
                        echo'</div>';
                      echo'</div>';
                    echo'</td>';
        }elseif ($rank == 2) {
            // 銀の王冠
            echo '<td class="crown">
                      <div class="panel-crown">
                        <div class="s-crown">
                          <div class="s-crown-circle"></div>
                        </div>
                      </div>
                    </td>';
        }elseif ($rank ==3 ) {
            // 銅の王冠
            echo '<td class="crown">
                      <div class="panel-crown">
                        <div class="c-crown">
                          <div class="c-crown-circle"></div>
                        </div>
                      </div>
                    </td>';
        }
    }else{
            echo '<td class="rank-size">';
            echo $ranking_users[$i]['rank'] . '位';
            echo '</td>';
        }
    
}


// 矢印
function arrow(){
    echo' <tr>
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
          </tr>';
}

function arrow2(){
    echo'   <tr>
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
            </tr>';
}


 ?>