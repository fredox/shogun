<?php

use Symfony\Component\Yaml\Yaml;

class Ws implements Transferable
{
    const WS_CONFIG_FILE = 'config/ws.yml';

    public $url;
    public $method;
    public $headers;
    public $saveInFile;
    public $file;

    public function __construct($destination, $environmentName)
    {
        $config = self::getConfigFile();

        $this->url     = $config['ws-envs'][$environmentName]['base-path'] . $config[$destination]['slug'];
        $this->method  = $config[$destination]['method'];
        $this->headers = $config[$destination]['headers'];

        $this->saveInFile = $config[$destination]['save-response'];
        $this->file       = $config[$destination]['save-response-in'];
    }

    public static function getConfigFile()
    {
        return Yaml::parse(file_get_contents(self::WS_CONFIG_FILE));
    }

    public function send($msg)
    {
        $output = $this->makeRequest($msg);

        if ($this->saveInFile) {
            echo "\n [SAVING IN FILE...]";
            file_put_contents($this->file, $output, FILE_APPEND);
        }
    }

    public function medium()
    {
        return "webservice";
    }

    function makeRequest($body)
    {
        $options = [];

        $options['http']['method'] = $this->method;
        $options['http']['header'] = '';

        foreach ($this->headers as $headerName => $headerValue) {
            $options['http']['header'] .= $headerName . ':' . $headerValue . "\r\n";
        }

        $options['http']['content'] = $body;

        $context = stream_context_create($options);

        $response = file_get_contents($this->url, false, $context);

        return $response;
    }
}