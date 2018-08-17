<?php

namespace Gini\Module;

class TheSpider
{
    public static function setup() {
        \Gini\I18N::setup();
        date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');
        class_exists('\Gini\Those');
    }

    /**
     * 把一些必要的目录创建一下 因为data下面目录的东西如果是空的git是不让提交的
     *
     * @return void
     */
    static function cache() {
        $attachment = DATA_DIR . '/attachment/';
        \Gini\File::ensureDir($attachment, 0777);
        $log = DATA_DIR . '/log/';
        \Gini\File::ensureDir($log, 0777);
    }

    /**
     * 做路由的分发
     *
     * @param [\Gini\CGI\Router] $router
     * @return void
     */
    public static function cgiRoute($router) {
        $router->cleanUp();

        // v2版本的路由
        $router->any('/', function ($router) {
            $router
                ->get('hello', 'Hello@get')
                ->post('graphql', 'GraphQL@__index')
                ->get('site', 'Site@fetch')
                ->get('site/{id}', 'Site@get')
                ->put('site/{id}', 'Site@put')
                ->get('service/host/{id}', 'Service\\Host@get')
                ->get('service/host', 'Service\\Host@fetch')
                ->post('service/host', 'Service\\Host@post')
                ->get('service/http/{id}', 'Service\\Http@get')
                ->get('service/http', 'Service\\Http@fetch')
                ->post('service/http', 'Service\\Http@post')
                ->get('service/disk/{id}', 'Service\\Disk@get')
                ->get('service/disk', 'Service\\Disk@fetch')
                ->post('service/disk', 'Service\\Disk@post')
                ->get('service/load/{id}', 'Service\\Load@get')
                ->get('service/load', 'Service\\Load@fetch')
                ->post('service/load', 'Service\\Load@post')
                ->get('service/ntp/{id}', 'Service\\Ntp@get')
                ->get('service/ntp', 'Service\\Ntp@fetch')
                ->post('service/ntp', 'Service\\Ntp@post');
        }, ['classPrefix' => '\\Gini\\Controller\\CGI\\']);
    }
}

