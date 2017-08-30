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
            // TODO 错误信息回传待完善
            // 也需要做log处理
            echo print_r($record->getMessages(), true);
            return;
        }

        if ($record->save()) {
            // TODO 是否对server的设置一定要在这里做? 能否放到异步任务做处理?
            $record->setServer($form);

            // 异步处理任务 发送HTTP的广播以及记录
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