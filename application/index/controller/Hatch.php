<?php
namespace app\index\controller;

class Hatch extends Common{

    public function index() {
        return $this->view->fetch('index');
    }

}