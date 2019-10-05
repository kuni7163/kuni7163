<?php
//データベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS forum"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "datetime DATETIME"
	.");";
	$stmt = $pdo->query($sql);



//「編集対象番号」を送信した場合：「編集フォーム」
	if(isset($_POST["edit_number"])){
	//パスワードが一致した場合
	if($_POST["password"] === "pass"){
	//編集元のテキストを、投稿フォームに表示させるための処理
	//※post送信されたものは$sql内では認識できない
	$sql = 'SELECT id, name, comment FROM forum';//SELECT カラム名 FROM テーブル名;
	$stmt = $pdo->query($sql);
	//結果の読み込み
	$results = $stmt->fetchAll();
		foreach ($results as $row){
		//編集対象番号と一致した場合、名前・コメントのみ取得し、変数に代入
			if($row['id'] === $_POST['edit_number']){
			$edit_name = $row['name'];
			$edit_comment = $row['comment'];
			break;//条件がTRUEの時、処理終了
			}
		}
	}
	else{
	echo "※パスワードに誤りがあります"."<br />";
	}
	}
?>



<head>
  <meta charset="utf-8">
  <title>mission_5-1.php</title>
  <meta name="description" content="入力・削除・編集フォーム">
</head>
<body>
 <h1>美味しかった飲み物</h1>
 <h3>ジュース、お酒、流行りのタピオカドリンクetc. 何でもOK！</h3>
 <form action = "mission_5-1.php" method = "post">
  <p>名前：<br>
  <input
type = "text" name = "name" value = "<?php if(isset($edit_name)){echo $edit_name;} else{echo "";} ?>"> </p>
  <p>コメント：<br>
  <input
type = "text" name = "comment" value = "<?php if(isset($edit_comment)){echo $edit_comment;} else{echo "";} ?>"> </p>
  <p>パスワード：<br>
  <input
type = "password" name = "password" value = ""> </p>

 <form action = "mission_5-1.php" method = "post">
  <input
type = "hidden" name = "branch_number" value = ""> </p>

  <p><input type = "submit" value = "送信" /></p>
 </form>


 <form action = "mission_5-1.php" method = "post">
  <p>削除対象番号：<br>
  <input
type = "text" name = "delete_number" value = ""> </p>
  <p>パスワード：<br>
  <input
type = "password" name = "password" value = ""> </p>
  <p><input type = "submit" value = "削除" /></p>
 </form>

 <form action = "mission_5-1.php" method = "post">
  <p>編集対象番号：<br>
  <input
type = "text" name = "edit_number" value = ""> </p>
  <p>パスワード：<br>
  <input
type = "password" name = "password" value = ""> </p>
  <p><input type = "submit" value = "編集" /></p>
 </form>

  <p>――――――――――――――――――</p>
  <p>【　投稿一覧　】</p>
</body>
</html>


<?php
//以下、★で編集モード・新規投稿モードの分岐
//「名前」「コメント」が送信された場合（新規・編集のいずれか）
if(isset($_POST["name"], $_POST["comment"])){
//パスワードが一致した場合
if($_POST["password"] === "pass"){
//★編集対象番号が空欄の場合：「新規投稿フォーム」
	if($_POST["branch_number"] === ""){
//データベースへの登録
//POST送信の各値の変数を用意
	//bindParamの引数（:nameなど）はテーブル作成でどんな名前のカラムを設定したかで変える必要がある。
	$sql = $pdo -> prepare("INSERT INTO forum (name, comment, datetime) VALUES (:name, :comment, :datetime)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
	$name = $_POST["name"];
	$comment = $_POST["comment"];
//投稿日時を扱う変数を用意
	//タイムゾーン設定
	ini_set("date.timezone", "Asia/Tokyo");
	$datetime = date("Y/m/d H:i:s");
	$sql -> execute();
//テーブル内容の表示
	$sql = 'SELECT * FROM forum';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
		foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].'. ';
		echo $row['name'].' : ';
		echo $row['comment'].' (';
		echo $row['datetime'].')<br>';
		echo "<hr>";
		}
	}
		else{



//★編集対象番号が空欄ではない(＝数字が入力されている)場合：「編集フォーム」
		$id =  $_POST["edit_number"];
		$name = $_POST["name"];
		$comment = $_POST["comment"];
		$sql = 'update forum set name=:name,comment=:comment where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		echo $edit_name."さんの投稿「".$edit_comment."」を編集しました"."<br />";
		}
}
	else{
	echo "※パスワードに誤りがあります"."<br />";
}
}



//「削除対象番号」を送信した場合：「削除フォーム」
	elseif(isset($_POST["delete_number"])){
	//パスワードが一致した場合
	if($_POST["password"] === "pass"){
//削除処理をする
	$id =  $_POST["delete_number"];
	$sql = 'delete from forum where id=:id';//where 抽出条件;
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
//投稿削除のメッセージを表示
	echo "投稿番号：".$id."の投稿を削除しました"."<br />";
	}
	else{
	echo "※パスワードに誤りがあります"."<br />";
	}
	}
?>