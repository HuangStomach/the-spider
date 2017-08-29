<?php

use Phalcon\Mvc\Controller;

class RecordController extends Controller {
    
    public function getAction () {
        echo 'get';
    }
    
    public function postAction() {
        $form = $this->request->getPost();
        
        $record = new Record();
        $record->name = $form['name'];
        $record->fqdn = $form['fqdn'];
        $record->state = $form['state'];
        $record->output = $form['output'];
        $record->type = $form['type'];

        // 对数据做完整性验证
        if (!$record->validation()) {
            echo print_r($record->getMessages(), true);
            return;
        }

        if ($record->save()) {
            $record->setServer($form);

            $this->server->task([
                'class' => 'status',
                'action' => 'http',
                'content' => $record,
            ]);

            echo 'saved';
        }
        else {
            $messages = $record->getMessages();
            echo print_r($messages, true);
        }
    }

}