<?php

namespace Gini\Controller\Graphql;

use \GraphQL\Type\Definition\Type;

class Ex
{
    // 确保site存在
    public function query($fields) {
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