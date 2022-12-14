<?php
include_once 'app.php';

$objDb = new DbConnect;
$conn = $objDb->connect();

$app = new App();

if ($app->post('/article/post')) {
    $article = $app->getData();
    $sql = "INSERT INTO article (title, thumbnail, content, is_public, user_id, type_id) VALUES(:title, :thumbnail, :content, :is_public, :user_id, :type_id)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':title', $article['title']);
    $stmt->bindParam(':thumbnail', $article['thumbnail']);
    $stmt->bindParam(':content', $article['content']);
    $stmt->bindParam(':is_public', $article['is_public']);
    $stmt->bindParam(':user_id', $article['user_id']);
    $stmt->bindParam(':type_id', $article['type_id']);

    $data = array('title' => $article['title'], 'thumbnail' => $article['thumbnail'], 'content' => $article['content'], 'is_public' => $article['is_public'], 'user_id' => $article['user_id'], 'type_id' => $article['type_id']);

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