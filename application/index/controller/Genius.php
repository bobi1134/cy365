<?php
namespace app\index\controller;

class Genius extends Common{

    public function index() {
        return $this->view->fetch('index');
    }

}