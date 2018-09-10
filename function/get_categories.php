<?php 
//カテゴリー全件取得
$sql = 'SELECT * FROM `categories`';
$all_category_stmt = $dbh->prepare($sql);
$all_category_stmt->execute();
$all = array();
$categories = array();
 while ($all_category = $all_category_stmt->fetch(PDO::FETCH_ASSOC)) {
    $all[] = $all_category['category_id'];
    $categories[] = array('category_id'=>$all_category['category_id'], 'name'=>$all_category['name']);
 }

$c1 = count($all);
$c2 = count($selected_categories);
for ($i=0; $i < $c1; $i++) {
  for ($j=0; $j < $c2; $j++) {
    if ($all[$i] == $selected_categories[$j]) {
      unset($categories[$i]);
    }
  }
}
// echo'<pre>';
// var_dump($categories);
// echo'</pre>';
// 選んだカテゴリ以外のカテゴリ
$unselected_categories = array_merge($categories);
// echo'<pre>';
// var_dump($unselected_categories);
// echo'</pre>';
$b = count($categories);

?>