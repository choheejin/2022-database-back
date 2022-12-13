<?php
include_once 'app.php';

$objDb = new DbConnect;
$conn = $objDb->connect();

$app = new App();

if ($app->get('/comments/([a-zA-Z0-9_])')) {
    $params = $app->getParams();

    $sql = "SELECT * FROM comment where article_id = '" . $params[0] . "'";
    $stmt = $conn->prepare($sql);

    $stmt->execute();

    if($stmt->execute()) {
        $data = [];
        while($result = $stmt->fetch()){
            array_push($data, array(
                        'comment_id' => $result['comment_id'],
                        'content' => $result['content'],
                        'date' => $result['date'],
                        'user_id' => $result['user_id'],
                        'article_id' => $result['article_id']
            ));
        }

        $response = ['status' => 200, 'message' => 'Record created successfully.','response' => $data];
        $app->print($response);

    } else {
        $response = ['status' => 500, 'message' => 'Failed to create record.'];
        $app->print($response, 500);
    }
}

if ($app->post('/comment/post')) {
    // POST, PUT 등에서 보내온 데이타
    $comment = $app->getData();
    $sql = "INSERT INTO comment (content, date, user_id, article_id) VALUES(:content, DEFAULT, :user_id, :article_id)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':content', $comment['content']);
    $stmt->bindParam(':user_id', $comment['user_id']);
    $stmt->bindParam(':article_id', $comment['article_id']);

    $data = array('content' => $comment['content'], 'user_id' => $comment['user_id'], 'article_id' => $comment['article_id']);

    if($stmt->execute()) {
        $response = ['status' => 200, 'message' => 'Record created successfully.',
        'response' => $data
        ];
    } else {
        $response = ['status' => 500, 'message' => 'Failed to create record.'];
    }

    echo json_encode($response);
}


?>