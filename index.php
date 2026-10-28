<?php

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

if (is_null($path)) {
    clientError("pathクエリ/パラメータが指定されていません。");
} else if ($path === "") {
    //コントローラのアクションを実行してください
}

function clientError($errorMsg) {
   //内部エラー: エラーページを表示させてください。
}