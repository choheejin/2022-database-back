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

if ($app->get('/articles/([0-9]*)')) {
    $params = $app->getParams();

    $sql = "SELECT article_id, title, thumbnail, concat(substr(content, 1, 50), ' ...') AS preview, type_id FROM article WHERE is_public = 1 and type_id =" . strval($params[0]);
    $stmt = $conn->prepare($sql);

    $stmt->execute();

    if($stmt->execute()) {
        $data = [];
        while($result = $stmt->fetch()){
            array_push($data, array(
                        'article_id' => $result['article_id'],
                        'thumbnail' => $result['thumbnail'],
                        'title' => $result['title'],
                        'preview' => $result['preview']
            ));
        }

        $response = ['status' => 200, 'message' => 'Record created successfully.','response' => $data];
        $app->print($response);

    } else {
        $response = ['status' => 500, 'message' => 'Failed to create record.'];
        $app->print($response, 500);
    }
}

if ($app->get('/articles/([0-9]*)/search/([ㄱ-ㅎ|가-힣|a-z|A-Z|0-9|%]*)')) {
    $params = $app->getParams();

    $urlDecoded = urldecode($params[1]);

    // Error Code 1055
    $set = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
    $conn->query($set);

    $sql = "SELECT * FROM search WHERE (title LIKE '%" . strval($urlDecoded) . "%' OR content LIKE '%" . strval($urlDecoded) . "%') and type_id =" . strval($params[0]);
    // $sql = "SELECT * FROM search";
    // $sql = "SELECT * FROM search WHERE title LIKE '%" . strval($params[0]) . "%' OR content LIKE '%" . strval($params[0]) . "%'";
    $stmt = $conn->prepare($sql);

    $stmt->execute();

    if($stmt->execute()) {
        $data = [];
        while($result = $stmt->fetch()){
            array_push($data, array(
                        'article_id' => $result['article_id'],
                        'title' => $result['title'],
                        'thumbnail' => $result['thumbnail'],
                        'preview' => $result['preview'],
                        'content' => $result['content']
            ));
        }

        $response = ['status' => 200, 'message' => 'Record created successfully.','response' => $data];
        $app->print($response);

    } else {
        $response = ['status' => 500, 'message' => 'Failed to create record.'];
        $app->print($response, 500);
    }
}

?>