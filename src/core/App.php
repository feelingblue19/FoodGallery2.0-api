<?php

class App {

    protected $controller;
    protected $method;
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseURL();
        
        if (file_exists('../src/controllers/' . $url[0] . '.php') && isset($url[0])) {
            $this->controller = 'App\Controller\\' . $url[0];
            unset($url[0]);   
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode([
                'error' => 'not found'
            ]);
            return;
        }

        $expCtr = explode('\\', $this->controller);
        $expCtr = end($expCtr);

        require_once '../src/controllers/' . $expCtr . '.php';
        $this->controller = new $this->controller;

        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode([
                'error' => 'not found'
            ]);
            return;
        }

        if (!empty($url)) {
            $this->params = array_values(($url));
        }

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }

}