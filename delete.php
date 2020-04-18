<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id'])) {
  $id = $_REQUEST['id'];
  // これはURLパラメータで送られてきた情報
  // ちなみにこれはsessionなので個人のidになる

  $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
  $messages->execute(array($id));
  $message = $messages->fetch();
  // SQLと組み合わせてpostsの内容をすべて受け取ったことになる

  if($message['member_id'] === $_SESSION['id']) {
    // データベースから取り出したidといまセッションに記録されているidが一致したときのみ削除できるようにする
    $del = $db->prepare('DELETE FROM posts WHERE id=?');
    $del->execute(array($id));
  }
}

header('Location: index.php');
exit();
?>
