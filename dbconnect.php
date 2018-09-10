<?php
  // データベースへ接続(例外処理＝エラー出たときの処理)
  // try catch文
  // try 部分に例外が起きそうな処理を
  // catch部分に例外が起きたときの処理を記述

  $dsn = 'mysql:dbname=Bookrus;host=localhost';
  $user = 'root';
  $password = 'root';


  try{

      $dbh = new PDO($dsn, $user, $password);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $dbh->query('SET NAMES utf8');
  }catch(PDOException $e){
    // POOExceptionは出たエラーを取得する
    // $eは$errorの略で、try部分で出たエラーを取得する
    echo 'データベース接続時エラー：' . $e->getMessage();
    exit();

  }
?>