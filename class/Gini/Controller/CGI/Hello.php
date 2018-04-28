<?php
namespace Gini\Controller\CGI;

class Hello extends \Gini\Controller\CGI
{
    public function get() {
        $response = ['hello' => 'world'];
        return \Gini\IoC::construct('\Gini\CGI\Response\Json', $response);
    }
}
