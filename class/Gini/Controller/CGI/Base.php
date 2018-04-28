<?php

namespace Gini\Controller\CGI\Base;

class Base extends \Gini\Controller\REST {

    protected function range(\Gini\Those $objects, $key, $array) {
        $op = $array['op'];
        $max = $array['max'];
        $min = $array['min'];

        switch ($op) {
            case 'lt':
                $objects->whose($key)->isLessThan($min);
            break;
            case 'gt':
                $objects->whose($key)->isGreaterThen($max);
            break;
            case 'bt':
                $objects->whose($key)->isBetween($min, $max);
            break;
        }
    }

}
