<!-- ログインしているユーザーのみだけが投稿できるようにする -->
<!-- login.phpにあるようにセッションにidと時間が保持されているのでセッション変数がセットされているかでログインしているかどうかがわかる -->
<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  // sessionに値が保存されていなければ直接このリンクに飛んだということ
  $_SESSION['time'] = time();
  // timeを更新すると何か行動を起こした後１時間はログインが保持されることになるので便利
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
  // ここまでで会員情報をすべて受け取っている
  // ここではprepareは情報を取得している
  // そうすることで名前などが後から表示しやすい
}else{
  header('Location: login.php');
  exit();
}

if(!empty($_POST)) {
  // 投稿ボタンが押され、テキストが送信されてとき
  if($_POST['message'] !== '') {
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, created=NOW()');
    $message->execute(array(
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']
      // ここではprepareは情報をデータベースに保存している
    ));
    // これはページを再読み込みするとその都度データベースに保存してしまう

    header('Location: index.php');
    // これは素の状態でページを呼ぶのでpostに保管した値は消えることになる
    exit();
  }
}

// ページネーション実装
$page = $_REQUEST['page'];
if($page === '') {
  $page = 1;
}
$page = max($page, 1);
// 大きい方が採用される
// 1より小さい数字は入らないようになった
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
// 投稿がいくつあるのかがわかった
$maxpage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxpage);
// 小さい方が採用される
// 最大のページより大きくならない
$start = ($page - 1) * 5;

$posts = $db->prepare('SELECT m.name, m.picture, p. * FROM members m, posts p WHERE
m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
// ?には数字を入れないといけないのでexecuteではなくPDOを使う
// executeは文字列としてわたってしまう
$posts->execute();

  // 返信の処理
if(isset($_REQUEST['res'])) {
  // Reというボタンがクリックされたときに
  $response = $db->prepare('SELECT m.name, m.picture, p. * FROM members m, posts p WHERE m.id = p.member_id AND p.id=?');
  $response->execute(array($_REQUEST['res']));
  // Reを押したメッセージがpostデータベースの何番目のメッセージなのかを取得しそれと同じ人の名前と画像を取ってくるという処理

  $table = $response->fetch();
  $message = '@' . $table['name'] . ' ' . $table['message'] . '→→→→';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <div style="text-align: right"><a href="changeicon.php">アイコンを変更する</a></div>
    <div style="text-align: right"><a href="changename.php?id=<?php print(htmlspecialchars($_SESSION['id'], ENT_QUOTES));?>">名前を変更する</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
          <input type="hidden" name="reply_post_id"
          value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>" />
          <!-- ここのhidden属性で送信されているがどのメッセージに対してなのかを判断するためにpost_idを送っている -->
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>


<?php foreach ($posts as $post): ?>
<!-- 配列の中身を繰り返しpostに入れる -->
    <div class="msg">
    <img src="member_picture/ <?php print(htmlspecialchars($post['pictere'], ENT_QUOTES)); ?>" width="48" height="48" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
    <!-- member_pictureはディレクトリが決まっているのでそこははじめに書いておく -->
    <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?>
    <span class="name">（<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>）</span>
    [<a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)) ?>">Re</a>]</p>
    <p class="day"><a href="view.php?id=<?php print(htmlspecialchars($post['id'])); ?><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>

  <?php if($post['reply_message_id'] > 0): ?>
    <a href="view.php?id=<?php print(htmlspecialchars($post['reply_message_id'], ENT_QUOTES)); ?>">
    <!-- これらは結局、この投稿の内容を詳しくみるか、または返信ものとのメッセージを見るかのどちらかなので -->
    返信元のメッセージ</a>
  <?php endif; ?>]

  <?php if($_SESSION['id'] === $post['member_id']): ?>
  <!-- 「削除できるのは本人だけ」の機能 -->
    [<a href="delete.php?id=<?php print(htmlspecialchars($post['id'])); ?>"
    style="color: #F33;">削除</a>]
  <?php endif; ?>
  </p>
  </div>
<?php endforeach; ?>

<ul class="paging">
<?php if($page > 1): ?>
  <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
<?php else: ?>
  <li>前のページへ</li>
<?php endif; ?>
<?php if($page < $maxpage): ?>
  <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
<?php else: ?>
  <li>次のページへ<li>
<?php endif; ?>
</ul>
  </div>
</div>
</body>
</html>
