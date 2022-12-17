<?php
include_once 'app.php';

$objDb = new DbConnect;
$conn = $objDb->connect();

$app = new App();

// 특정 유저 게시글 전체 조회
if($app->get('/articles/user/([a-zA-Z0-9_]*)')){
    $params = $app->getParams();
    $sql = "SELECT article_id, title, thumbnail, if (length(content) > 50, concat(substr(content, 1, 50), ' ...'), content) as preview, content, date, type_id FROM article where article_id in (select article_id from article where user_id = '".$params[0]."')";

    $stmt = $conn->prepare($sql);

    if($stmt->execute()){
        $data = [];
        while ($result = $stmt->fetch()){
            $sql2 = "select count(comment_id) as cnt from article_detail where article_id = ".$result['article_id'];
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute();
            $result2 = $stmt2->fetch();

            array_push($data, array(
                'article_id' => $result['article_id'],
                'thumbnail' => $result['thumbnail'],
                'title' => $result['title'],
                'preview' => $result['preview'],
                'content' => $result['content'],
                'date' => $result['date'],
                'type_id' => $result['type_id'],
                'comment_cnt'=>$result2['cnt']
            ));
        }


        $response = ['status' => 200, 'message' => 'get user articles successfully.','response' => $data];
        $app->print($response);
    } else {
        $response = ['status' => 500, 'message' => 'Failed to get user articles.'];
        $app->print($response, 500);
    }
}

// 특정 게시글 조회
if($app->get('/article/([0-9]*)')){
    $params = $app->getParams();
    $sql = "select DISTINCT article_id, title,a_thumbnail, a_content, a_date, a_user from article_detail where article_id = ".$params[0];

    $stmt = $conn->prepare($sql);

    if($stmt->execute()) {

        $result = $stmt->fetch();

        $data = array(
            'article_id' => $result['article_id'],
            'title' => $result['title'],
            'a_thumbnail' => $result['a_thumbnail'],
            'a_content' => $result['a_content'],
            'a_date' => $result['a_date'],
            'a_user' => $result['a_user']
        );

        $sql2 = "select article_id, title from article where article_id in ((
                 select article_id from article where article_id > ".$params[0]." and user_id = '".$data['a_user']."' limit 1), (select article_id from article where article_id < ".$params[0]." and user_id = '".$data['a_user']."' limit 1)); ";

        $stmt2 = $conn->prepare($sql2);

        if($stmt2->execute()) {
            while($result2 = $stmt2->fetch()){
                if($result2['article_id']>$data['article_id']){
                    $data = array_merge($data, array(
                        'nxtArticle' => array('article_id' => $result2['article_id'], 'title' => $result2['title'])
                    ));
                }
                if($result2['article_id']<$data['article_id']){
                    $data = array_merge($data, array(
                        'preArticle' => array('article_id' => $result2['article_id'], 'title' => $result2['title'])
                    ));
                }
            }
        }

        $response = ['status' => 200, 'message' => 'get article successfully.','response' => $data];
        $app->print($response);

    } else {
        $response = ['status' => 500, 'message' => 'Failed to get article.'];
        $app->print($response, 500);
    }
}

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

    $sql = "SELECT article_id, title, thumbnail, preview, type_id FROM preview WHERE type_id =" . strval($params[0]);
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

    $sql = "SELECT * FROM preview WHERE (title LIKE '%" . strval($urlDecoded) . "%' OR content LIKE '%" . strval($urlDecoded) . "%') and type_id =" . strval($params[0]);
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