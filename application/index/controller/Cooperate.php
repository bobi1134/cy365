<?php
namespace app\index\controller;

class Cooperate extends Common{

    public function index() {
        return $this->view->fetch('index');
    }

}