<?php

namespace Gini\Logger;

class Spider extends Handler
{
    private static $_LEVEL2PRIORITY = [
        Level::EMERGENCY => \LOG_EMERG,
        Level::ALERT => \LOG_ALERT,
        Level::CRITICAL => \LOG_CRIT,
        Level::ERROR => \LOG_ERR,
        Level::WARNING => \LOG_WARNING,
        Level::NOTICE => \LOG_NOTICE,
        Level::INFO => \LOG_INFO,
        Level::DEBUG => \LOG_DEBUG,
    ];

    public function log($level, $message, $context = [])
    {
        if (!$this->isLoggable($level)) return;

        $message = "[{ident}] [{autotime}] $message";
        $context['ident'] = $this->_name;
        $context['autotime'] = date('Y-m-d H:i:s');

        $replacements = [];
        $_fillReplacements = function (&$replacements, $context, $prefix = '') use (&$_fillReplacements) {
            foreach ($context as $key => $val) {
                if (is_array($val)) {
                    $_fillReplacements($replacements, $val, $prefix.$key.'.');
                } else {
                    $replacements['{'.$prefix.$key.'}'] = $val;
                }
            }
        };
        $_fillReplacements($replacements, $context);

        $message = strtr($message, $replacements);

        $logDir = APP_PATH . '/data/log';
        \Gini\File::ensureDir($logDir);
        file_put_contents($logDir . "/{$this->_name}.log", "{$this->levelColor($level)}$message\e[0m\n", FILE_APPEND);
    }

    public function levelColor($level) {
        $color = "\e[1;30m";
        switch ($this->levelValue($level)) {
            case $this->levelValue(Level::EMERGENCY):
            case $this->levelValue(Level::ALERT):
                $color = "\e[1;31m";
                break;
            case $this->levelValue(Level::CRITICAL):
            case $this->levelValue(Level::ERROR):
                $color = "\e[1;33m";
                break;
            case $this->levelValue(Level::WARNING):
                $color = "\e[1;34m";
                break;
            case $this->levelValue(Level::NOTICE):
                $color = "\e[1;32m";
                break;
            case $this->levelValue(Level::INFO):
                $color = "\e[1;35m";
                break;
            default:
                $color = "\e[1;37m";
                break;
        }
        return $color;
    }
}