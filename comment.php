<?php
include_once 'app.php';

$objDb = new DbConnect;
$conn = $objDb->connect();

$app = new App();

if ($app->get('/comments/([0-9]*)')) {
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

if ($app->put('/comment/update/([0-9]*)')){
    $params = $app->getParams();

    $comment = $app->getData();

    $sql = "update comment set content = '".$comment['content']."' where comment_id = ".$params[0];
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if($stmt->execute()){
        $data = [];
        $sql2 = "SELECT * FROM comment where comment_id = " .$params[0];

        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute();
        $result = $stmt2->fetch();

        array_push($data, array(
            'comment_id' => $result['comment_id'],
            'content' => $result['content'],
            'date' => $result['date'],
            'user_id' => $result['user_id'],
            'article_id' => $result['article_id']
        ));

        $response = ['status' => 200, 'message' => 'comment update successfully.', 'response' => $data[0]];
    } else{
        $response = ['status' => 500, 'message' => 'Failed to update comment.'];
    }
    $app->print($response);

}

if($app->delete('/comment/delete/([0-9]*)')){
    $comment = $app->getParams();

    $sql = "DELETE from comment where comment_id = ".$comment[0];
    $stmt = $conn->prepare($sql);

    if($stmt->execute()){
        $response = ['status' => 200, 'message' => 'comment deleted successfully'];
    } else {
        $response = ['status' => 500, 'message' => 'failed to delete comment'];
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
        $response = ['status' => 200, 'message' => 'comment posted successfully.',
        'response' => $data
        ];
    } else {
        $response = ['status' => 500, 'message' => 'Failed to post comment.'];
    }

    echo json_encode($response);
}


?>