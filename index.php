<?php

require_once __DIR__ . '/Utils/DiContainer.php';

use \Utils\DiContainer;

// #region DIコンテナ登録
$diContainer = DiContainer::GetInstance();
$pdoFactory = function(DiContainer $diContainer) : PDO {
    $dbcs = 'mysql:host=localhost;dbname=online_forum;charset=utf8';
    $dbUser = "root";
    $dbPass = "";
    return new PDO($dbcs, $dbUser, $dbPass);
};
$diContainer->Register(PDO::class, false, $pdoFactory);
// #endregion

// #region pathクエリパラメータ取得
$requestMethod = $_SERVER['REQUEST_METHOD'];
if ($requestMethod !== 'GET' && $requestMethod !== 'POST') {
    clientError("本システムではGET/POST以外対応していません。");
}

$path = null;
if ($requestMethod === 'GET') {
    $path = $_GET['path'] ?? null;
} else if ($requestMethod === 'POST') {
    $path = $_POST['path'] ?? null;
}
// #endregion

if (is_null($path)) {
    clientError("pathクエリ/パラメータが指定されていません。");
} else if ($path === "") {
    //コントローラのアクションを実行してください
}

function clientError($errorMsg) {
   //内部エラー: エラーページを表示させてください。
}