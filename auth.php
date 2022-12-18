<?php

$objDb = new DbConnect;
$conn = $objDb->connect();

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
        $result = $stmt->fetch();
        
        if($result[0]){
            $sqlDate = "select attendance_point from point where date_format(attendance_date,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')and user_id like '$id'";
            $stmt2 = $conn->prepare($sqlDate);
            $stmt2->execute();
            $result2 = $stmt2->fetch();
        
            if($result2){
                $response = ['status' => 200, 'message' => 'Login successfully and have an attendance', 'response' => $id];
                $app->print($response);
            } else {
                $sqlAttend = "INSERT INTO point (attendance_point, user_id) VALUES(1, '".$id."')";
                $stmt3 = $conn->prepare($sqlAttend);
                if($stmt3->execute()){
                    // 오늘 출석일 가지고 오는 쿼리문
                    $sql3 = "select * from point where date_format(attendance_date,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')and user_id like '".$id."'";
                    $stmt4 = $conn->prepare($sql3);
                    if($stmt4->execute()){
                        $result3 = $stmt4->fetch();
                        
                        $data = array(
                            'point_id' => $result3['point_id'],
                            'user_id' => $result3['user_id'],
                            'attendance_date' => $result3['attendance_date'],
                            'attendance_point' => $result3['attendance_point']
                        );

                        $response = ['status' => 200, 'message' => 'Login successfully and make an attendance', 'response' => $data];
                        $app->print($response);
                    }
                }                
            }
        }
    }

}

if ($app->get('/my-page/([a-zA-Z0-9_]*)')) {
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

if ($app->get('/mystamp/([a-zA-Z0-9_]*)')) {
    // POST, PUT 등에서 보내온 데이타
    $params = $app->getParams();
    
    $sql4 = "SELECT attendance_date from point where user_id = '$params[0]' order by attendance_date;";
    $stmt = $conn->prepare($sql4);
    $stmt->execute();

    if($stmt->execute()) {
        $data = [];
        while($row = $stmt->fetch()){
            array_push($data, array(
                'attendance_date' => $row['attendance_date']
            ));
        }

        if(count($data) > 0) {
            $response = ['status' => 200, 'message' => 'mystemp successfully', 'response' => $data];
            $app->print($response);
        } else {
            $response = ['status' => 500, 'message' => 'getmystemp failed'];
            $app->print($response, 500);
        }

    } else {
        $response = ['status' => 500, 'message' => 'getmypage failed'];
        $app->print($response, 500);
}

}

if ($app->get('/mybadge/([a-zA-Z0-9_]*)')) {
    // POST, PUT 등에서 보내온 데이타
    $params = $app->getParams();
    
    $sql4 = "SELECT attendance_date from point where user_id ='$params[0]';";
    $stmt = $conn->prepare($sql4);
    $stmt->execute();

    if($stmt->execute()) {
        $data = [];
        while($row = $stmt->fetch()){
            array_push($data, array(
                'attendance_date' => $row['attendance_date']
            ));
        }

        if(count($data) > 0) {
            $response = ['status' => 200, 'message' => 'mystemp successfully', 'response' => $data];
            $app->print($response);
        } else {
            $response = ['status' => 500, 'message' => 'getmystemp failed'];
            $app->print($response, 500);
        }

    } else {
        $response = ['status' => 500, 'message' => 'getmypage failed'];
        $app->print($response, 500);
}

}

?>
