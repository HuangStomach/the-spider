<?php
namespace Gini\Controller\CGI;

use \GraphQL\Type\Definition\ResolveInfo;
use \GraphQL\Type\Definition\ObjectType;
use \GraphQL\Type\Schema;

class GraphQL extends \Gini\Controller\CGI
{
    public function __index() {
        if ($_SERVER['CONTENT_TYPE'] != 'application/graphql'
        && $this->env['header']['content-type'] != 'application/graphql') {
            $output = [
                'errors' => [
                    'message' => 'Invalidate Content Type',
                    'category' => 'graphql',
                    'locations' => [
                        ['line' => 1, 'column' => 1]
                    ],
                ]
            ];
            goto output;
        }

        $raw = \Gini\CGI::content() ? : $this->env['raw'];
        
        try {
            $fields = new \ArrayIterator();

            if (isset($GLOBALS['gini.class_map'])) {
                array_walk($GLOBALS['gini.class_map'], function ($path, $class, $fields) {
                    $pos = strpos($class, 'gini/controller/graphql/'); // 只匹配graphql命名空间下的类
                    if ($pos === false) return false;

                    $class = str_replace('/', '\\', $class);
                    $o = \Gini\IoC::construct($class);
                    $o->app = \Gini\Core::app();
                    if (method_exists($class, 'query')) {
                        $callback = [$o, 'query'];
                    } elseif (function_exists($class . '\\query')) {
                        $callback = $class . '\\query';
                    }
                    
                    if (is_callable($callback)) {
                        call_user_func_array($callback, [$this->env, $fields]);
                    }
                }, $fields);
                // 需要将$fields引用传递 在内部做资源注入
            }
            
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => (array)$fields,
            ]);

            $schema = new Schema([
                'query' => $queryType
            ]);

            $input = json_decode($raw, true);
            $query = $input['query'];
            $variableValues = isset($input['variables']) ? $input['variables'] : null;
            $rootValue = ['prefix' => 'You said: '];
            $result = \GraphQL\GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        }
        catch (\Exception $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        output:
        return \Gini\IoC::construct('\Gini\CGI\Response\Json', $output);
    }
}
