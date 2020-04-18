<?php
session_start();
require('dbconnect.php');

// いきなりこのページにわたってきた時のための処理
if(isset($_SESSION)) {
  $_SESSION['time'] = time();
  // 時間の更新
  $newname = $_SESSION['name'];
  $names = $db->prepare('UPDATE members SET name=?, modified=NOW() WHERE id=? ');
  echo $names->execute(array(
    $newname,
    $_SESSION['id']
  ));
  // ここは変数に置き換えているので$newnameにしたいところ
} else {
  header('Location: changename.php');
  exit();
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF=8">
<meta name="viewport" content="width=device-width, initial-scale=1,0">
<meta http-equiv="X-UA=Compatible" content="ie=edge">
<title>アイコンの変更</title>

<link rel="stylesheet" href="style.css" />
</head>
<body>
<h1><?php print($newname);?>に変更しました</h1>
<a href="index.php">投稿画面に戻る</a>




</body>