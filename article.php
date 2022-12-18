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

// 특정 유저 게시글 private 제외 조회
if($app->get('/articles/non-user/([a-zA-Z0-9_]*)')){
    $params = $app->getParams();
    $sql = "SELECT article_id, title, thumbnail, preview, content, date, type_id FROM preview where article_id in (select article_id from article where user_id = '".$params[0]."')";

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


// 특정 게시글 조회(조회하는 유저와 글쓴 유저가 동일할 경우)
if($app->get('/article/user/([0-9]*)')){
    $params = $app->getParams();
    $sql = "select article_id, title, thumbnail, content, date, user_id from article where article_id = ".$params[0];

    $stmt = $conn->prepare($sql);

    if($stmt->execute()) {
        $result = $stmt->fetch();
        $data = array(
            'article_id' => $result['article_id'],
            'title' => $result['title'],
            'a_thumbnail' => $result['thumbnail'],
            'a_content' => $result['content'],
            'a_date' => $result['date'],
            'a_user' => $result['user_id']
        );

        $sql2 = "select article_id, title from article where article_id in (
                 (select article_id from article where article_id > ".$params[0]." and user_id = '".$data['a_user']."' order by article_id limit 1), 
                 (select article_id from article where article_id < ".$params[0]." and user_id = '".$data['a_user']."' order by article_id desc limit 1)); ";

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

// 특정 게시글 조회(조회하는 유저와 글쓴 유저가 다를 때, private 보이지 않게 제거)
if($app->get('/article/non-user/([0-9]*)')){
    $params = $app->getParams();
    $sql = "select article_id, title, thumbnail, content, date, user_id from preview where article_id = ".$params[0];

    $stmt = $conn->prepare($sql);

    if($stmt->execute()) {
        $result = $stmt->fetch();
        $data = array(
            'article_id' => $result['article_id'],
            'title' => $result['title'],
            'a_thumbnail' => $result['thumbnail'],
            'a_content' => $result['content'],
            'a_date' => $result['date'],
            'a_user' => $result['user_id']
        );

        $sql2 = "select article_id, title from preview where article_id in (
                 (select article_id from preview where article_id > ".$params[0]." and user_id = '".$data['a_user']."' order by article_id limit 1), 
                 (select article_id from preview where article_id < ".$params[0]." and user_id = '".$data['a_user']."' order by article_id desc limit 1)); ";

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

if ($app->put('/article/update/([0-9]*)')){
    $params = $app->getParams();

    $article = $app->getData();

    // $sql = "UPDATE article SET title ='" . $article['title'] . "', thumbnail = '" . $article['thumbnail'] . "', content = '" . $article['content'] . "', is_public = " . $article['is_public'] . ", type_id = " . $article['type_id'] . " WHERE article_id = " . $params[0];
    $sql = "UPDATE article SET title = :title, thumbnail = :thumbnail, content = :content, is_public = :is_public, type_id = :type_id, date = null WHERE article_id = " . $params[0];
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':title', $article['title']);
    $stmt->bindParam(':thumbnail', $article['thumbnail']);
    $stmt->bindParam(':content', $article['content']);
    $stmt->bindParam(':is_public', $article['is_public']);
    $stmt->bindParam(':type_id', $article['type_id']);

    $stmt->execute();

    if($stmt->execute()){
        $response = ['status' => 200, 'message' => 'comment update successfully.', 'response' => $data[0]];
    } else{
        $response = ['status' => 500, 'message' => 'Failed to update comment.'];
    }
    $app->print($response);

}

if($app->delete('/article/delete/([0-9]*)')){
    $article = $app->getParams();

    $sql = "DELETE from article where article_id = ".$article[0];
    $stmt = $conn->prepare($sql);

    if($stmt->execute()){
        $response = ['status' => 200, 'message' => 'comment deleted successfully'];
    } else {
        $response = ['status' => 500, 'message' => 'failed to delete comment'];
    }
}

// main
if ($app->get('/articles/([0-9]*)/user/([a-zA-Z0-9_]*)')) {
    $params = $app->getParams();

    $sql = "SELECT article_id, title, thumbnail, preview, user_id, type_id FROM preview WHERE type_id =" . strval($params[0]);
    $stmt = $conn->prepare($sql);

    $stmt->execute();

    if($stmt->execute()) {
        $data = [];
        while($result = $stmt->fetch()){
            array_push($data, array(
                        'article_id' => $result['article_id'],
                        'thumbnail' => $result['thumbnail'],
                        'title' => $result['title'],
                        'preview' => $result['preview'],
                        'user_id' => $result['user_id']
            ));
        }

        $sql2 = "SELECT article_id, title, thumbnail, if (length(content) > 50, concat(substr(content, 1, 50), ' ...'), content) as preview, user_id, type_id FROM article WHERE type_id =" . strval($params[0])." and is_public = 0 and user_id ='" .$params[1]."'";
        $stmt2 = $conn->prepare($sql2);

        $stmt2->execute();

        while ($result2 = $stmt2->fetch()){
            array_push($data, array(
                'article_id' => $result2['article_id'],
                'thumbnail' => $result2['thumbnail'],
                'title' => $result2['title'],
                'preview' => $result2['preview'],
                'user_id' => $result2['user_id']
            ));
        }

        $response = ['status' => 200, 'message' => 'Record created successfully.','response' => $data];
        $app->print($response);

    } else {
        $response = ['status' => 500, 'message' => 'Failed to create record.'];
        $app->print($response, 500);
    }
}

// search
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

// history
if ($app->get('/articles/history/([a-zA-Z0-9_]*)')) {
    $params = $app->getParams();

    $sql = "SELECT * FROM history WHERE user_id = '" . strval($params[0]) . "' order by date desc";
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

if ($app->post('/articles/history/post')) {
    $history = $app->getData();

    // 일단 넣고
    $sql = "INSERT INTO users_view_articles(user_id, article_id) VALUE(:user_id, :article_id)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':user_id', $history['user_id']);
    $stmt->bindParam(':article_id', $history['article_id']);

    $data = array(
        'user_id' => $history['user_id'],
        'article_id' => $history['article_id']
    );

    try {
        $stmt->execute();
        $response = ['status' => 200, 'message' => 'Create History successfully.',
            'response' => $data
        ];
        echo json_encode($response);

    }  catch (Exception $ex) {
        // 안 넣어지고 에러가 중복키 때문이면 update 로 바꾸게 post 함수 탈출
        if($ex->getCode() === "23000"){
            $response = ['status' => 501, 'message' => '이미 히스토리에 존재합니다'];
        }
        return $app->print($response, 500);
    }

    // 마지막에 얼마나 들어가는지 확인하고,
    $sqlSelect = "select * from users_view_articles where user_id = '".$history['user_id']."' order by date_viewed";
    $stmtSelect = $conn->prepare($sqlSelect);

    try {
        $stmtSelect->execute();
        $resultSelect = $stmtSelect->fetchAll();

        // 6개 이상(7개)이면 제일 옛날 기록 하나 삭제 해서 6개로 유지
        if(count($resultSelect) >= 6){
            try {
                $sqlDelete = "Delete from users_view_articles where article_id = '".$resultSelect[0][1]."' and user_id = '".$resultSelect[0][0]."' ";

                $stmtDelete = $conn->prepare($sqlDelete);
                $stmtDelete->execute();

            } catch (Exception $ex){
                $response = ['status' => 502, 'message' => 'failed to drop history'];
                $app->print($response, 500);
            }
        }

    } catch (Exception $ex) {
        $response = ['status' => 500, 'message' => 'failed to create history'];
        $app->print($response, 500);
    }
}

if ($app->put('/articles/history/update/([0-9]*)')){
    $params = $app->getParams();

    $history = $app->getData();

    $sql = "UPDATE users_view_articles SET date_viewed = null WHERE user_id = :user_id AND article_id = " . $params[0];
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':user_id', $history['user_id']);

    $stmt->execute();

    if($stmt->execute()){
        $response = ['status' => 200, 'message' => 'comment update successfully.'];
    } else{
        $response = ['status' => 500, 'message' => 'Failed to update comment.'];
    }
    $app->print($response);

}

?>