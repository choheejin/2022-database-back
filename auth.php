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

if ($app->post('/login')) {
    // POST, PUT 등에서 보내온 데이타
    $user = $app->getData(); //user
    $id = $user['id'];
    $pw = $user['pw'];

    $sql2 = "SELECT count(*) as 'cnt' from user where id ='$id' and pw ='$pw';";
    $stmt = $conn->prepare($sql2);
    $stmt->execute();

    if($stmt->execute()) {
        $result = $stmt->fetchAll();

        if($result[0][0] == 1) {
            $response = ['status' => 200, 'message' => 'Login successfully', 'response' => $id];
            $app->print($response);
        } else {
            $response = ['status' => 500, 'message' => 'Login failed'];
            $app->print($response, 500);
        }

    } else {
        $response = ['status' => 500, 'message' => 'Login failed'];
        $app->print($response, 500);
}
}

if ($app->get('/my-page/([a-zA-Z0-9_])')) {
    // POST, PUT 등에서 보내온 데이타

    $params = $app->getParams();
    
    $sql3 = "SELECT id, name, gender, email from user where id ='$params[0]';";
    $stmt = $conn->prepare($sql3);
    $stmt->execute();

    if($stmt->execute()) {
        $data = [];
        while($row = $stmt->fetch()){
            array_push($data, array(
                'id' => $row['id'],
                'name' => $row['name'],
                'gender' => $row['gender'],
                'email' => $row['email'],
            ));
        }

        if(count($data) > 0) {
            $response = ['status' => 200, 'message' => 'mypage successfully', 'response' => $data[0]];
            $app->print($response);
        } else {
            $response = ['status' => 500, 'message' => 'getmypage failed'];
            $app->print($response, 500);
        }

    } else {
        $response = ['status' => 500, 'message' => 'getmypage failed'];
        $app->print($response, 500);
}
}
?>
