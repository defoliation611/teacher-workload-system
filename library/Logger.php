<?php
/**
 * 日志记录类
 */

namespace Library;

class Logger
{
    private $logPath;
    private $logLevel = 'info';

    public function __construct($path = 'logs/', $level = 'info')
    {
        $this->logPath = $path;
        $this->logLevel = $level;
        
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }

    /**
     * 记录日志
     */
    public function log($message, $level = 'info')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message\n";
        
        $filename = $this->logPath . date('Y-m-d') . '.log';
        file_put_contents($filename, $logMessage, FILE_APPEND);
    }

    /**
     * 记录信息
     */
    public function info($message)
    {
        $this->log($message, 'INFO');
    }

    /**
     * 记录警告
     */
    public function warning($message)
    {
        $this->log($message, 'WARNING');
    }

    /**
     * 记录错误
     */
    public function error($message)
    {
        $this->log($message, 'ERROR');
    }

    /**
     * 记录调试信息
     */
    public function debug($message)
    {
        $this->log($message, 'DEBUG');
    }
}
