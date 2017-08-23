<?php
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Config\Adapter\Ini as ConfigIni;

class Logger {

    protected $logger;
    
    public function __construct ($type = 'common') {
        $config = new ConfigIni(CONFIG_PATH . 'logger.ini');
        $path = APP_PATH . $config->logger->path;
        if (!is_dir($path)) mkdir($path, 0755, true);
        
        $logger = new FileAdapter("{$path}/{$type}.log");

        $level = strtoupper($config->logger->level);
        $reflection = new ReflectionClass('Phalcon\Logger');
        $logger->setLogLevel($reflection->getConstants()[$level]);
        $this->logger = $logger;
    }

    public function __call ($name, $args) {
        $this->logger->{$name}($args[0]);
    }

}