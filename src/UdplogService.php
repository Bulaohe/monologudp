<?php
namespace Bulaohe\Udplog;

use Bulaohe\Udplog\Logger;
use Monolog\Formatter\JsonFormatter;
use Bulaohe\Udplog\Handler\UdpHandler;

class UdplogService
{

    protected $name;
    protected $logger = null;
    protected $level = Logger::WARNING;
    
    public function __construct()
    {
        $this->logger(config('udplog.logger_name'));
        
        $this->setHandler();
        
        var_dump('testttt');
    }
    
    /**
     * get logger with given name
     *
     * @param string $name
     */
    public function logger($name = 'default')
    {
        $this->name = $name ?? 'default';
        $this->logger = app()->make(Logger::class, ['name'=>$this->name]);
        
    }
    
    /**
     * set log level
     *
     * @param int $level
     */
    public function level($level = Logger::WARNING)
    {
        $this->level = $level;
    }
    
    /**
     * set log handler
     */
    public function setHandler()
    {
        $host = '127.0.0.1';
        $port = '9502';
        
        $handler = app()->make(UdpHandler::class, [
            'host' => $host,
            'port' => $port,
            'level' => $this->level,
            'bubble' => true,
            'recordBufferMaxSize' => config('udplog.logger_buffer_size')
        ]);
        
        $this->logger->pushHandler($handler);
        $handler->setFormatter(new JsonFormatter(JsonFormatter::BATCH_MODE_NEWLINES, true));
    }
    
    /**
     * write the log info to file
     *
     * @param  string $message the description of the log
     * @param  array  $context the log details
     * @return boolean
     */
    public function write($context = [])
    {
        if(empty($context)){
            return false;
        }
        
        if(isset($_SERVER['SERVER_ADDR'])) {
            $context['ip'] = $_SERVER['SERVER_ADDR'];
        }else if(isset($_SERVER['HOSTNAME'])) {
            $context['ip'] = $_SERVER['HOSTNAME'];
        }else {
            $context['ip'] = gethostname();
        }
        
        try{
            $this->logger->addRecord($this->level, $context);
        } catch (\Throwable $e) {
            // log info
        }
        return true;
    }
    
    /**
     * the entrance of Log, write json format log
     * 
     * @param  string $message the description of the log
     * @param  array  $context the log details
     * @param  string $name    set logger with given name
     * @param  string $path    the log path
     * @param  int    $level   set log level
     * @return boolean
     */
    public function log($context = [], $level = Logger::WARNING)
    {
            $this->level($level);
            return $this->write($message, $context);
    }
}