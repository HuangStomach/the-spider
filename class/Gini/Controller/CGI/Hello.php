<?php
namespace Gini\Controller\CGI;

class Hello extends Base\Rest
{
    public function getDefault ($ref = 0) {
        $response = ['hello' => 'world'];
        return \Gini\IoC::construct('\Gini\CGI\Response\Json', $response);
    }
}
