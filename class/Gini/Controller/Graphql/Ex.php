<?php

namespace Gini\Controller\GraphQL;

use \GraphQL\Type\Definition\Type;

class Ex
{
    // 确保site存在
    public function query($env, $fields) {
        $fields['echo'] = [
            'type' => Type::string(),
            'args' => [
                'message' => ['type' => Type::string()],
            ],
            'resolve' => function ($root, $args) {
                return $root['prefix'] . $args['message'];
            }
        ];

        $data = ['trigger' => 'sth.hook'];
        $env['swoole']->task($data);
    }

}