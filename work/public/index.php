<?php

// __DIR__ : 絶対パス表記の現在のファイルが存在するディレクトリ名。これを指定することで読み込みエラーを防ぐため。マジック定数
require_once(__DIR__ . '/../app/config.php');

// 画面を表示した時点でトークンが作成されセッションに保存される
createToken();

$pdo = getPdoInstance();


// todoが入力されPOSTされたら以下が発動
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateToken();

    $action = filter_input(INPUT_GET, 'action');
    pr($action);
    switch ($action) {
        case 'add':
            addTodo($pdo);
            break;
        case 'toggle':
            toggleTodo($pdo);
            break;
        case 'delete':
            deleteTodo($pdo);
            break;
        default;
            exit;
    }
    // todo登録後に更新すると再度todoが追加されるのを防ぐためのリダイレクト
    header('Location: ' . SITE_URL);
    exit;
}

// ページ読み込み時todoを全件取得して変数に代入
$todos = getTodos($pdo);


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>My Todos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <main>
        <h1>Todos</h1>

        <form action="?action=add" method="post">
            <input type="text" name="title" placeholder="Type new todo.">
            <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
        </form>

        <ul>
            <?php foreach ($todos as $todo) : ?>
                <li>
                    <form action="?action=toggle" method="post">
                        <input type="checkbox" <?= $todo->is_done ? 'checked' : ''; ?>>
                        <input type="hidden" name="id" value="<?= h($todo->id); ?>">
                        <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
                    </form>
                    <span class="<?= $todo->is_done ? 'done' : ''; ?>">
                        <?= h($todo->title); ?>
                    </span>

                    <form action="?action=delete" method="post" class="delete-form">
                        <span class="delete">x</span>
                        <input type="hidden" name="id" value="<?= h($todo->id); ?>">
                        <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
                    </form>

                </li>
            <?php endforeach; ?>
        </ul>
    </main>

    <script src="js/main.js"></script>
</body>

</html>