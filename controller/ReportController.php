<?php

use Phalcon\Mvc\Controller;

class ReportController extends Controller {
    
    public function getAction () {
        echo 'get';
    }
    
    public function postAction() {
        $form = $this->request->getPost();
        error_log(print_r($form, true));
        $record = new Record();
        $record->name = $form['name'];
        $record->state = $form['state'];
        $record->output = $form['output'];
        $record->type = $form['type'];

        // 对数据做完整性验证
        if (!$record->validation()) {
            echo print_r($record->getMessages(), true);
            return;
        }

        if ($record->save()) {
            echo 'saved';
        }
        else {
            $messages = $record->getMessages();
            echo print_r($messages, true);
        }
    }

}