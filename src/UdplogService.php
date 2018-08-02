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
        $host = config('udplog.host');
        $port = config('udplog.port');
        
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
        if(empty($context) || !is_array($context)){
            return false;
        }
        
        if(isset($context['flow_'])) {
            $context['ip'] = $_SERVER['SERVER_ADDR'];
        }
        
        try{
            $this->logger->addRecord($this->level, '', $context);
        } catch (\Throwable $e) {
            // log info
        }
        return true;
    }
    
    /**
     * the entrance of Log, write json format log
     * 
     * @param  array  $context the log details
     * $context['payload'] 日志内容
     * $context['udplog_appid'] 日志来源
     * $context['udplog_scene'] 日志场景
     * $context['udplog_type'] 日志类型
     * $context['udplog_flow_code'] 流水编码
     * $context['udplog_channel'] 日志自定义通道－will bet bind in kibana
     * @param  int    $level   set log level
     * @return boolean
     */
    public function log($context = [], $level = Logger::WARNING)
    {
        if(empty($context) || !is_array($context) || !isset($context['payload'])){
            return false;
        }
        
        if(!isset($context['udplog_appid'])){
            $context['udplog_appid'] = 'udplog_default_appid';
        }
        
        if(!isset($context['udplog_scene'])){
            $context['udplog_scene'] = 'udplog_default_scene';
        }
        
        if(!isset($context['udplog_type'])){
            $context['udplog_type'] = 'udplog_default_type';
        }
        
        if(!isset($context['udplog_flow_code'])){
            $context['udplog_flow_code'] = 'code' . md5(microtime(true) . rand(0, 99999999) . rand(0, 99999999) . $this->name);
        }
        
        if(!isset($context['udplog_channel'])){
            $context['udplog_channel'] = $this->name;
        }
        
        $this->level($level);
        return $this->write($context);
    }
}