<?php

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/Transferable.php';

/**
 * Created by PhpStorm.
 * User: alfredo.galiana
 * Date: 25/9/18
 * Time: 14:29
 */
class Queue implements Transferable
{
    const QUEUES_CONFIG_FILE = 'config/queues.yaml';

    public $config;
    public $name;
    public $connection;
    public $channel;

    public function __construct($queueName, $envName)
    {
        self::checkQueuesConfigFile();
        $config = self::getConfigFile();

        if (!array_key_exists($queueName, $config['queues'])) {
        self::error('queue name [' . $queueName . '] does not exist in config');
        }

        if (!array_key_exists($envName, $config['queue-envs'])) {
            self::error('Environment [' . $envName . '] does not exists');
        }


        $this->config = $config['queue-envs'][$envName];
        $this->name   = $config['queues'][$queueName]['name'];
        try {
            $this->connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['pass'],
                $this->config['vhost']
            );

            $this->channel = $this->connection->channel();
            $this->channel->queue_declare($this->name, false, true, false, false);
        } Catch (Exception $e) {
            self::error($e->getMessage());
        }
    }

    public function publish($msg)
    {
        try {
            $message = new \PhpAmqpLib\Message\AMQPMessage($msg);
            $this->channel->basic_publish($message, '', $this->name);
        } Catch (Exception $e) {
            self::error($e->getMessage());
        }
    }

    public static function checkQueuesConfigFile()
    {
        try {
            if (!is_file(self::QUEUES_CONFIG_FILE)) {
                throw new Exception('Queues config file: ' . self::QUEUES_CONFIG_FILE . ' does not exists', 100);
            }

            $config = self::getConfigFile();

            if (!array_key_exists('queue-envs', $config)) {
                throw new Exception('Yaml queue file config must contain queue-envs section');
            }

            if (!array_key_exists('queues', $config)) {
                throw new Exception('Yaml queue file config must contain names section');
            }

        } Catch (ParseException $e) {
            self::error('Yaml file: ' . self::QUEUES_CONFIG_FILE . ' Not Valid');
        } Catch (Exception $e) {
            self::error('Failed on read config file: ' . self::QUEUES_CONFIG_FILE . ' ' . $e->getMessage());
        }
    }

    public static function getConfigFile()
    {
        return Yaml::parse(file_get_contents(self::QUEUES_CONFIG_FILE));
    }

    public static function error($msg)
    {
        echo "\n [ERROR][QUEUE] " . $msg;
        exit(1);
    }

    public function send($msg)
    {
        $this->publish($msg);
    }

    public function medium()
    {
        return 'queue';
    }
}