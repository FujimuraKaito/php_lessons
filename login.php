<?php
session_start();
require('dbconnect.php');

if ($_COOKIE['email'] !== '') {
  // クッキーの値を使ってログインしたとき
  $email = $_COOKIE['email'];
}

if($_POST['email'] === ''){
  // name属性がemailであるものを取ってくる感じ
  $error['email'] = 'blank';
}

if($_POST['password'] === ''){
  // name属性がpasswordであるものを取ってくる感じ
  $error['password'] = 'blank';
}

if (!empty($_POST)){
  $email = $_POST['email'];
  //もしクッキーに登録されていたメールアドレスと違うものでログインしようとしたときにクッキーに保存してある値が$emailに保存してあるのでそれを上書きする

  if($_POST['email'] !== '' && $_POST['password'] !== ''){
    $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
    $login->execute(array(
      $_POST['email'],
      sha1($_POST['password'])
      // データベースにあるのは暗号化されたものなのでそれと比べるためにここも暗号化させる
      // sha1は強力な暗号化なので管理者であってもパスワードを推測することができない
    ));
    $member = $login->fetch();
    // ログインを試している

    if($member) {
      // ログインに成功した場合
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();
      // sessionにはパスワードを保存しないようにする

      if($_POST['save'] === 'on') {
        // チェックボタンがオンになっていれば
        setcookie('email', $_POST['email'], time() + 60 * 60 * 24 * 14);
        // クッキーに値を保存する
        // 第２引数は現在からどれだけ保存するか
      }

      header('Location: index.php');
      exit();
    }else{
      // ログインに失敗した場合
        $error['login'] = 'failed';
      }
  }else{
    // emailかpasswordのどちらかが空である場合
    $error['login'] = 'blank';
  }
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<title>ログインする</title>
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ログインする</h1>
  </div>
  <div id="content">
    <div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
      <p>入会手続きがまだの方はこちらからどうぞ</p>
      <p>&raquo;<a href="join/">入会手続きをする</a></p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($email, ENT_QUOTES)); ?>" />
          <?php if($error['login'] === 'blank'): ?>
          <p class="error">メールアドレスとパスワードをご記入ください</p>
          <?php endif; ?>
          <?php if($error['email'] === 'blank'): ?>
					<p class="error">※メールアドレスを入力してください</p>
					<?php endif; ?>
          <?php if($error['login'] === 'failed'): ?>
          <p class="error">ログインに失敗しました。正しくご記入ください</p>
          <?php endif; ?>
        </dd>
        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
          <?php if($error['password'] === 'blank'):?>
					<p class="error">※パスワードを入力してください</p>
					<?php endif; ?>
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
        <input type="submit" value="ログインする" />
      </div>
    </form>
  </div>
  <div id="foot">
    <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
  </div>
</div>
</body>
</html>