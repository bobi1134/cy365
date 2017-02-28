<?php
namespace app\index\controller;

class Education extends Common{

    public function index() {
        return $this->view->fetch('index');
    }

}