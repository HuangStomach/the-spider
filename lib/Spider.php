<?php
use Phalcon\DI;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Mvc\Application as BaseApplication;
use Phalcon\Mvc\Router\Annotations as Annotations;

class Spider extends BaseApplication {

    public function init () {
        $this->services();
        $config = new ConfigIni(APP_PATH . 'config/application.ini');
        $loader = new Loader();
        $loader->registerDirs([
            APP_PATH . $config->application->controller,
            APP_PATH . $config->application->model
        ])->register();
    }

    protected function services () {
        $di = new DI();

        $di->set('router', function () {
            return new Router();
        });
        
        $di->set('dispatcher', function () {
            return new Dispatcher();
        });
        
        $di->set('response', function () {
            return new Response();
        });
        
        $di->set('request', function () {
            return new Request();
        });
        
        // 注册一个view吧虽然用不到
        $di->set('view', function () {
            $view = new View();
            $config = new ConfigIni(APP_PATH . 'config/application.ini');
            $view->setViewsDir(APP_PATH . $config->application->view);
            return $view;
        });

        $this->setDI($di);
    }

}