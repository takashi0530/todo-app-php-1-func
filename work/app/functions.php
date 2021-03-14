<?php


// htmlspecialcharsメソッド
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// トークンを作成する関数
function createToken() {
    if (!isset($_SESSION['token'])) {
      // 推測されにくい文字列をトークンとして生成
    $_SESSION['token'] = bin2hex(random_bytes(32));
    }
}

// トークンをチェックする関数
function validateToken() {
    // 空もしくはセッションのトークンと送信されたトークンが一致していない場合
    if (empty($_SESSION['token']) || $_SESSION['token'] !== filter_input(INPUT_POST, 'token')) {
        exit('Invalid post request');
    }
}

// DBにアクセスしてpdoを返す変数
function getPdoInstance() {
    try {
        $pdo = new PDO(
            DSN,
            DB_USER,
            DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // オブジェクト形式で結果を取得するためのオプション
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            // 取得したデータの型をSQLで定義した型に合わせて取得したいときのオプション
            PDO::ATTR_EMULATE_PREPARES => false,
            ]

        );
        return $pdo;
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

// 【INSERT】todoを追加する
function addTodo($pdo) {
    // postされたデータ$_POST['title']が存在しなくても$titleが未定義エラーとならない（$titleが初期化される)
    $title = trim(filter_input(INPUT_POST, 'title'));
    // もしフィルターの結果、$titleが空だったら（todoが空だった場合）
    if ($title === '') {
        echo 'todoを入力してください';
        return;
    }
    $stmt = $pdo->prepare("INSERT INTO todos (title) VALUES (:title)");
    $stmt->bindValue('title', $title, PDO::PARAM_STR);
    // クエリを実行する
    $stmt->execute();
}

// 【UPDATE】todoを更新する
function toggleTodo($pdo) {
    $id = filter_input(INPUT_POST, 'id');
    if (empty($id)) {
        return;
    }
    // preateメソッドを使用しSQLの実行前準備をする。変数値をプレースホルダとして設定する  :id   is_done = NOT is_done とすることでtrueとfalseが入れ替わる
    $stmt = $pdo->prepare("UPDATE todos SET is_done = NOT is_done WHERE id = :id");
    // プレースホルダに値をバインドする bindValue(プレースホルダ名, バインドする値, 値のデータ型)
    $stmt->bindValue('id', $id, PDO::PARAM_INT);
    // execute()メソッド：値をバインドした結果のSQLを実行する
    $stmt->execute();
}

// 【DELETE】todoを削除する
function deleteTodo($pdo) {
    $id = filter_input(INPUT_POST, 'id');
    if (empty($id)) {
        return;
    }
    // preateメソッドを使用しSQLの実行前準備をする。変数値をプレースホルダとして設定する  :id   is_done = NOT is_done とすることでtrueとfalseが入れ替わる
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id");
    // プレースホルダに値をバインドする bindValue(プレースホルダ名, バインドする値, 値のデータ型)
    $stmt->bindValue('id', $id, PDO::PARAM_INT);
    // execute()メソッド：値をバインドした結果のSQLを実行する
    $stmt->execute();
}

// 【SELECT】全てののtodoを取得する
function getTodos($pdo) {
    // クエリを発行
    $stmt = $pdo->query("SELECT * FROM todos ORDER BY id DESC");
    // SQLの結果を取得 fetchAll()すべてのレコードを取得する。  fetch() 対象の１件のレコードを取得する。
    $todos = $stmt->fetchAll();
    return $todos;
}





















//PHP var_dump2() を見やすく整形する
function vd($var) {
    echo '<pre style="white-space:pre; font-family: monospace; font-size:12px; border:3px double #BED8E0;" margin:8px;&gt;<code>';
    var_dump($var);
    echo '</code></pre>';
    echo '<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/styles/default.min.css"/>';
    echo '<script src="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/highlight.min.js"></script>';
    echo '<script>hljs.initHighlightingOnLoad();</script>';
}

//PHP print_r()を見やすく整形する
function pr($var) {
    echo '<pre style="white-space:pre; font-family: monospace; font-size:12px; border:3px double #BED8E0;" margin:8px;&gt;<code>';
    print_r($var);
    echo '</code></pre>';
    echo '<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/styles/default.min.css"/>';
    echo '<script src="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.10/highlight.min.js"></script>';
    echo '<script>hljs.initHighlightingOnLoad();</script>';
}