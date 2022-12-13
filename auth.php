<?php

$objDb = new DbConnect;
$conn = $objDb->connect();

include_once 'app.php';

$app = new App();


if ($app->post('/signup')) {
    // URL 파라미터 구해오기(정규식부분에서 괄호 부분 순서대로 가져옴)image.png
    $params = $app->getParams();

    // POST, PUT 등에서 보내온 데이타
    $user = $app->getData(); //user

    $sql = "INSERT INTO user (id, pw, name, gender, email) VALUES(:id, :pw, :name, :gender, :email)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':id', $user['id']);
    $stmt->bindParam(':pw', $user['pw']);
    $stmt->bindParam(':name', $user['name']);
    $stmt->bindParam(':gender', $user['gender']);
    $stmt->bindParam(':email', $user['email']);

//     echo $sql;

    if($stmt->execute()) {
        $response = ['status' => 200, 'message' => 'Record created successfully.'];
    } else {
        $response = ['status' => 500, 'message' => 'Failed to create record.'];
    }

    echo json_encode($response);
}
else if ($app->post('/login')) {
    // POST, PUT 등에서 보내온 데이타
    $user = $app->getData(); //user
    $id = $user['id'];

    $sql2 = "SELECT * from user where id='${id}'";
    $stmt = $conn->prepare($sql2);

    if($stmt->execute()) {
        $response = ['status' => 200, 'message' => 'Login successfully'];
    } else {
        $response = ['status' => 500, 'message' => 'Failed to login'];
    }
    echo json_encode($response);

}
?>
