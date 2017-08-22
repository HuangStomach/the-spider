<?php
use Phalcon\DI;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Application as BaseApplication;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Router\Annotations as Annotations;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;

class Spider extends BaseApplication {

    protected $server;
    
    public function __construct ($server = null) {
        $this->server = $server;
    }

    public function init () {
        $this->services();
        $config = new ConfigIni(CONFIG_PATH . 'application.ini');
        $loader = new Loader();
        $loader->registerDirs([
            APP_PATH . $config->application->controller,
            APP_PATH . $config->application->model
        ])->register();
    }

    protected function services () {
        $di = new DI();
        
        $di->set('server', $this->server);

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

        $di->set('modelsManager', function() {
            return new ModelsManager();
        });

        $di->set('modelsMetadata', function () {
            return new MetaDataAdapter();
        });
        
        // 注册一个view吧虽然用不到
        $di->set('view', function () {
            $view = new View();
            $config = new ConfigIni(CONFIG_PATH . 'application.ini');
            $view->setViewsDir(APP_PATH . $config->application->view);
            return $view;
        });

        $di->set('db', function () {
            $config = new ConfigIni(CONFIG_PATH . 'database.ini');
            $database['host'] = $config->database->host;
            $database['username'] = $config->database->username;
            if ($config->database->password) $database['password'] = $config->database->password;
            $database['dbname'] = $config->database->dbname;
            return new DbAdapter($database);
        });

        $this->setDI($di);
    }

    protected function broadcast () {
        
    }

}