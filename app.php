<?php
class App {
    private $params = array();

    // 생성자 (CORS 처리)
    public function __construct(){
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        // OPTIONS 요청일때
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    // json 타입으로 결과 뿌리는 함수
    public function print($arr, $code=200) {
        http_response_code($code);
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 파라미터 가져오기 (post, patch, put... 모두 이걸로 얻어옴)
    public function getData() {
        return json_decode(file_get_contents('php://input'), true);
    }

    // URL 파라미터 구해오기
    public function getParams() {
        return $this->params;
    }

    // URL 분석
    private function route($pat) {

        $pat = '~^'.$pat.'$~i';

        $request = $_SERVER['REQUEST_URI'];
        $ret = preg_match($pat, $request, $mat);
        if (count($mat) > 1) {
            array_shift($mat);
            $this->params = $mat;
        }
        return $ret;
    }

    // METHOD 체크
    private function checkMethod($method) {
        $_method = strtolower($_SERVER["REQUEST_METHOD"]);
        if ($_method !== $method) return false;
        return true;
    }

    // GET 요청 분석
    public function get($pat) {
        if(!$this->checkMethod('get')) return false;
        return $this->route($pat);
    }

    // POST 요청 분석
    public function post($pat) {
        if(!$this->checkMethod('post')) return false;
        return $this->route($pat);
    }

    // PUT 요청 분석
    public function put($pat) {
        if(!$this->checkMethod('put')) return false;
        return $this->route($pat);
    }

    // PATCH 요청 분석
    public function patch($pat) {
        if(!$this->checkMethod('patch')) return false;
        return $this->route($pat);
    }

    // DELETE 요청 분석
    public function delete($pat) {
        if(!$this->checkMethod('delete')) return false;
        return $this->route($pat);
    }
}

?>