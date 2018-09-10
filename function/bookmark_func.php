<?php 
// ブックマーク機能

$sql = 'SELECT * FROM `bookmark` WHERE user_id=?';
$data = array($login_user_info['user_id']);
$stmt5 = $dbh->prepare($sql);
$stmt5->execute($data);
$bookmarks = array();
    while ($user_books = $stmt5->fetch(PDO::FETCH_ASSOC)){

      $sql = 'SELECT * FROM `books` WHERE book_id=?';
      $data = array($user_books['book_id']);
      $stmt4 = $dbh->prepare($sql);
      $stmt4->execute($data);
          while ($user_bookmarks = $stmt4->fetch(PDO::FETCH_ASSOC)){
                   $finish_reading_id = $user_bookmarks['book_id'];
                   $sql = 'SELECT COUNT(*) AS `cnt` FROM `finish_reading` WHERE book_id=?';
                   $data = array($finish_reading_id);
                   $finish_reading_stmt = $dbh->prepare($sql);
                   $finish_reading_stmt->execute($data);
                   $finish_reading = $finish_reading_stmt->fetch(PDO::FETCH_ASSOC);

                   $book_mark = $user_bookmarks['book_id'];
                   $sql = 'SELECT COUNT(*) AS `cnt` FROM `bookmark` WHERE book_id=?';
                   $data_book_mark = array($book_mark);
                   $book_mark_stmt = $dbh->prepare($sql);
                   $book_mark_stmt->execute($data_book_mark);
                   $book_mark = $book_mark_stmt->fetch(PDO::FETCH_ASSOC);



                   $bookmarks[] =  array(
                                'book_id' => $user_bookmarks['book_id'],
                                'title' => $user_bookmarks['title'],
                                'picture_url_API' => $user_bookmarks['picture_url_API'],
                                'author_API' => $user_bookmarks['author_API'],
                                'phrase' => $user_bookmarks['phrase'],
                                'category_id' => $user_bookmarks['category_id'],
                                'score_finish' => $finish_reading['cnt'],
                                'score_like' => $book_mark['cnt']
                              );
                 }
    }
 ?>