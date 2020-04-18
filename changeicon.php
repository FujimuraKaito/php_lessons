<?php
session_start();

echo __LINE__.PHP_EOL;
echo $_POST;
if(!empty($_POST)) {
  print('ポストが送信されました');
  // フォームを送信したときにだけエラーチェックをする
  echo __LINE__.PHP_EOL;
  if ($_POST['reimage'] === ''){
    $error['icon'] = 'blank';
  }
  echo __LINE__.PHP_EOL;
  // ここからはエラーチェック
  $fileName = $_FILES['reimage']['name'];
  //上の変数は結局file名を表す（name属性がreimageのやつでnameはその名前）
  echo __LINE__.PHP_EOL;
  if(!empty($fileName)){
    // 画像がアップロードされていれば
    $ext = substr($fileName, -3);
    // -3というのは後ろから３文字のこと
    echo __LINE__.PHP_EOL;
    if($ext !== 'jpg' && $ext !== 'gif' && $ext !== 'png' ){
      $error['icon'] = 'type';
    }
    echo __LINE__.PHP_EOL;
  }
  echo __LINE__.PHP_EOL;

  echo __LINE__.PHP_EOL;
  if(empty($error)) {
    print('エラーの中身は空でした。');
    //$errorの内容が空であるかを確認する
    $image = date('YmdHis') . $_FILES['reimage']['name'];
    move_uploaded_file($_FILES['reimage']['tmp_name'], 'member_picture/' . $image);
    // 日付を入れることでファイルが重複するのを避ける
    //  FILESというのはグローバル変数でfileから得られたもの
    // 二番目の引数が保存する場所を示す
    $_SESSION['icon'] = $_POST;
    // ポストで送信した内容をセッションに保存する
    $_SESSION['icon']['reimage'] = $image;
    // 後でデータベースに保存するためにセッションに保存しておく
    // その際に二次元配列でまだ指定していない['reimage']を二つ目の引数にしている
    var_dump($image);
    // header('Location: iconcheck.php');
    exit();
  }echo __LINE__.PHP_EOL;
}
echo __LINE__.PHP_EOL;

echo __LINE__.PHP_EOL;
if(!empty($error)) {
  header('Location: changeicon.php');
  exit();
}
echo __LINE__.PHP_EOL;

echo __LINE__.PHP_EOL;
if ($_REQUEST['action'] === 'rewrite' && isset($_SESSION['icon'])) {
  $_POST = $_SESSION['icon'];
  // 選び直すで呼ばれた場合はセッションに保存されている値をポストに入れ直す
}
echo __LINE__.PHP_EOL;
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
      <h1>アイコンの変更画面</h1>
        <div id="content">
        <form action="" method="post" enctype="multipart/form-data"><!-- ファイルを付け加える時にはこのenctypeの部分は必須項目 --> 
        <dl>
        <dt>写真など</dt>
		    <dd>
        	  <input type="file" name="reimage" size="35" value="test"  />
              <?php if($error['icon'] === 'type'):?>
					      <p class="error">[.jpg]  [.png]  [.gif]の画像を設定してください</p>
					    <?php endif; ?>
              <?php if($error['icon'] === 'blank'):?>
					      <p class="error">画像を選択してください</p>
					    <?php endif; ?>
					    <?php if(!empty($error)): ?>
						    <p class="error">恐れ入りますが画像を改めて選択ください</p>
					    <?php endif; ?>
          </dd>
          </dl>
            <div><input type="submit" value="画像を確認する"/></div>
        </form>
   </div>
  </div>
</body>
