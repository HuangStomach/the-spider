<?php

namespace Gini\Controller\CGI;

class Restful extends \Gini\Controller\REST {

    private $operators = [
        'ne' => 'isNot',
        'in' => 'isIn',
        'ni' => 'isNotIn',
        'gt' => 'isGreaterThan',
        'ge' => 'isGreaterThanOrEqual',
        'lt' => 'isLessThan',
        'le' => 'isLessThanOrEqual',
        'bt' => 'isBetween',
    ];

    protected function query(\Gini\Those $objects, $key) {
        $form = $this->form('get');

        if (isset($form[$key])) {
            $value = $form[$key];
            if (is_array($value)) $this->filter($objects, $key, $value);
            else $objects->whose($key)->is($value);
        }
    }

    /**
     * 对restful 条件筛选所做的处理
     *
     * @param \Gini\Those $objects
     * @param string $key 需要查询的字段
     * @param array $array 筛选操作数组 ['操作', '数值A', '数值B', ...]
     * @return void
     */
    protected function filter(\Gini\Those $objects, $key, $array) {
        $op = current($array);
        if (!array_key_exists($op, $this->$operators)) return;
        $operator = $operators[$op];

        switch ($op) {
            case 'bt':
                $objects->whose($key)->isBetween($array[0], $array[1]);
                break;
            case 'in':
            case 'ni':
                array_shift($array);
                $objects->whose($key)->{$operator}($array);
                break;
            default:
                $objects->whose($key)->{$operator}($array[0]);
                break;
        }
    }

}
