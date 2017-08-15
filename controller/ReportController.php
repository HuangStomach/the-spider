<?php

use Phalcon\Mvc\Controller;

class ReportController extends Controller {
    
    public function getAction () {
        echo 'get';
    }
    
    public function postAction() {
        $form = $this->request->getPost();
        $record = new Record();
        $record->name = $form['name'];
        $record->state = $form['state'];
        $record->output = $form['output'];
        $record->type = $form['type'];
        echo 'post';
    }

}