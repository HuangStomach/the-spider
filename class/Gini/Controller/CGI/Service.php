<?php
namespace Gini\Controller\CGI;

use \Gini\CGI\Response;

class Service extends Base\Rest
{
    public function postDefault () {
        $form = $this->form('post');
        $service = $form['servicedesc'];
        // 目前这里接收所有的插件传递的信息
        // TODO: 考虑今后进行拆分到不同的接口
        $res = Event::tigger("service.post.{$service}", $this->env['swoole'], $form);
        
        return new Response\Json($res);
    }
}
