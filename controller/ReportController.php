<?php

use Phalcon\Mvc\Controller;

class ReportController extends Controller {
    
    public function getAction () {
        echo 'get';
    }
    
    public function postAction() {
        error_log(print_r($this->request->getPost(), true));
        echo 'post';
    }

}