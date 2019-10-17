<?php

class Controller {

    public function model($model) {
        $expModel = explode('\\', $model);
        $expModel = end($expModel);
        
        require_once '../src/models/' . $expModel .'.php';
        return new $model;
    }
}