<?php

namespace Gini\Controller\CGI\Base;

class Rest extends \Gini\Controller\REST {

    protected $form;

    private $errors = [
        400 => 'Bad Request',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'NotImplemented',
    ];

	/* function __preAction ($action, &$params) {
        switch ($this->env['method']) {
            case 'get':
                $form = $this->form('get');
                break;
            case 'post':
                $form = $this->form('post');
                break;
            case 'patch':
            case 'delete':
            case 'put':
                if ($this->form('put')) {
                    $form = $this->form('put');
                }else {
                    $content = file_get_contents('php://input');
                    $form = json_decode($content, true);
                    if (!$form) {
                        $form = [];
                        parse_str($content, $form);
                    }
                }
                break;
        }
    } */

    protected function error ($code = 500, $message = '') {
        $code = array_key_exists($code, $this->errors) ? $code : 500;
        $message = $message or $this->errors[$code];

        return [
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ];
    }

    private function getClassName($str) {
		list(, , , $name) = explode('/', str_replace('\\', '/', strtolower($str)), 4);
		return $name;
	}

}
