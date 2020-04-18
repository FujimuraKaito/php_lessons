<?php
session_start();
require('dbconnect.php');

if(!isset($_REQUEST['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_REQUEST['id']));
  $member = $members->fetch();
  // index.phpのところで名前が表示されなかったのでここを一回コメントにして試してみた→表示されなくなったのはここを消したせい？
  // 変数がかぶった可能性があるから2をつけて回避した
  // ここまででmembersテーブルのid=ログインした人、の内容をすべて受け取った
}

if(!empty($_POST)) {
  if($_POST['name'] === '') {
    $error['name'] = 'blank';
  }

  if(empty($error)) {
    $_SESSION['name'] = $_POST['name'];
    if(isset($_POST)) {
      print('postname OK');
      // ここは表示される→ポストはセットされている
    }
    $_SESSION['id'] = $_POST['id'];
    if(isset($_POST['id'])) {
      print('postid OK');
      // OKと表示される→postにセットされている
    }
    header('Location: changenamecheck.php');
    exit();
  }
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
  <div id="wrap">
    <div id="head">
      <h1>名前の変更画面</h1>
        <div id="content">
          <form action="" method="post">
            <dl>
              <dt>ニックネーム<span class="required">必須</span></dt>
              <dd>
                <input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>" />
                <!-- ニックネームをページがリフレッシュされる度に消えないようにするためにPOSTで送信している値を受け取る -->
                <!-- このPOSTで受け取った情報はcheck.phpからhiddenを使って送信されてきたもの -->
                <?php if($error['name'] === 'blank'):?>
                <!-- error配列の中身がblankの場合はエラーメッセージを表示する -->
                <p class="error">※ニックネームを入力してください</p>
                <?php endif; ?>

                <input type="hidden" name="id" value="<?php print(htmlspecialchars($_REQUEST['id'], ENT_QUOTES));?>">
                <!-- ここにhiddenでidを送信するしかないのか？→そうしてみる -->
              </dd>
            </dl>
            <div><input type="submit" value="この名前に変更する" /></div>
          </form>
        </div>
    </div>
  </div>
</body>
