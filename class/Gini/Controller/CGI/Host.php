<?php
namespace Gini\Controller\CGI;

class Host extends Base\Rest
{
    public function postDefault () {
        $form = $this->form('post');
        $last = $form['lasthostcheck'];
        $name = $form['hostname'];
        $address = $form['hostaddress'];
        $state = $form['hoststate'];
        $attempt = $form['hostattempt']; // 插件检测
        $type = $form['hoststatetype']; // 主机状态
        $runtime = $form['hostexecutiontime']; // 检测主机状态所用的时间
        $output = $form['hostoutput']; // 主机输出
        $perf = $form['hostperfdata']; // 插件返回的额外数据
    }
}
