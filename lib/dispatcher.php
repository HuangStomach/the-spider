<?php
use Phalcon\Config\Adapter\Ini as ConfigIni;

class Dispatcher {
    
    protected $req;
    protected $res;
    protected $loader;

    function __construct ($data) {
        $this->req = $data[0];
        $this->res = $data[1];
    }

    public function dispatch () {
        if (!$this->loader) {
            $config = new ConfigIni(APP_PATH . 'config/application.ini');
            $this->loader = new \Phalcon\Loader();
            $this->loader->registerDirs([
                APP_PATH . $config->application->controllerDir
            ])->register();
        }

        // trim();
    }

}