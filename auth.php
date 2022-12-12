<?php
include_once 'dbconfig.php';
include_once 'app.php';


$app = new App();
if ($app->post('/signup')) {

    // URL 파라미터 구해오기(정규식부분에서 괄호 부분 순서대로 가져옴)
    $params = $app->getParams();

    // POST, PUT 등에서 보내온 데이타
    $data = $app->getData();

    // 결과출력
    $app->print(array(
        'user_id' => $data['user_id'],
        'password' => $data['password']
    ));

    // sql 쿼리
}
?>
