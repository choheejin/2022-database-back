<?php

$objDb = new DbConnect;
$conn = $objDb->connect();

include_once 'app.php';

echo "Testing2";
$app = new App();   


if ($app->post('/2022-database-back/signup')) {
    echo "Testing3";
    // URL 파라미터 구해오기(정규식부분에서 괄호 부분 순서대로 가져옴)image.png
    $params = $app->getParams();

    // POST, PUT 등에서 보내온 데이타
    $user = $app->getData(); //user

    // // 결과출력
    // $app->print(array(
    //     'id' => $data['id'],
    //     'pw' => $data['pw'],
    //     'name' => $data['name'],
    //     'gender' => $data['gender'],
    //     'email' => $data['email']
    // ));

    echo "Testing5";

    // $id = $data['id'];
    // $pw = $data['pw'];
    // $name = $data['name'];
    // $gender = $data['gender'];
    // $email = $data['email'];

    $sql = "INSERT INTO user (code, id, pw, name, gender, email) VALUES(null, :id, :pw, :name, :gender, :email)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':id', $user['id']);
    $stmt->bindParam(':pw', $user['pw']);
    $stmt->bindParam(':name', $user['name']);
    $stmt->bindParam(':gender', $user['gender']);
    $stmt->bindParam(':email', $user['email']);
      echo $sql;

    if($stmt->execute()) {
        $response = ['status' => 1, 'message' => 'Record created successfully.'];
    } else {
        $response = ['status' => 0, 'message' => 'Failed to create record.'];
    }
    echo json_encode($response);
}
?>
