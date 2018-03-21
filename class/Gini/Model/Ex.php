<?php

namespace Gini\Model;

use \GraphQL\Type\Definition\Type;

class Ex
{
    // 确保site存在
    static function queryType($e, $fields) {
        $fields['echo'] = [
            'type' => Type::string(),
            'args' => [
                'message' => ['type' => Type::string()],
            ],
            'resolve' => function ($root, $args) {
                return $root['prefix'] . $args['message'];
            }
        ];
    }
}