<?php
namespace Gini\Controller\CGI;

use \GraphQL\Type\Definition\ObjectType;
use \GraphQL\Type\Schema;

class Graphql extends \Gini\Controller\CGI
{
    public function __index () {
        if ($this->env['header']['content-type'] != 'application/graphql') return false;

        try {
            $fields = new \ArrayIterator();
            \Gini\Event::trigger('graphql.queryType.fields', $fields);
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => (array)$fields,
            ]);

            $schema = new Schema([
                'query' => $queryType
            ]);

            $input = json_decode($this->env['raw'], true);
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

        return \Gini\IoC::construct('\Gini\CGI\Response\Json', $output);
    }
}
