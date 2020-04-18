<?php 
session_start();
require('dbconnect.php');

if(!isset($_SESSION['icon'])) {
  // sessionに値が保存されていなかった場合にこのこの後の処理をする
  // ブラウザから直接このページを呼び出した場合は戻ってもらう
  header('Location: changeicon.php');
  exit();
}
if(!empty($_POST)){
	$statement2 = $db->prepare('UPDATE members SET picture=?, created=NOW()');
	echo $statement2->execute(array(
    $_SESSION['icon']['reimage']
	));
	unset($_SESSION['icon']);
	// session1から値を削除する。こうしないとデータベースに重複して値が保存されることがある

	header('Location: index.php');
	exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>画像変更チェック</title>

  <form action="" method="post">
  <input type="hidden" name="action" value="submit"/>
  <!-- 送信していることをフォームの隠し要素で送信している
       このページにはsubmitボタンがないのでこれで代用する -->
       <!-- これがいる理由がいまいちわからん -->
  <dl>
  <dd>
    <?php if($_SESSION['icon']['reimage'] !== ''): ?>
		  <img src="/member_picture/<?php print(htmlspecialchars($_SESSION['icon']['reimage'], ENT_QUOTES)); ?>">
		<?php endif; ?>
  </dd>
  </dl>
  <div><a href="changeicon.php?action=rewrite">&laquo;&nbsp;選び直す</a> | <input type="submit" value="登録する" /></div>
  </form>



