<?php

use Ayesh\PHP_Timer\Timer;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Queue.php';
require __DIR__ . '/Ws.php';
require __DIR__ . '/Data.php';

if (count($argv) < 4) usage();

list($type, $templateFile, $environmentName, $destinationName, $extraParams) = getRawConsoleParams($argv);

$destination = getDestination($type, $destinationName, $environmentName);
$data = new Data($templateFile, $extraParams);

startScript($destinationName, $environmentName, $destination->medium());

transfer($destination, $data);

endScript();



function transfer(Transferable $t, Data $data)
{
    $counter = 1;

    Timer::start('full');

    foreach ($data->next() as $dataToSend) {
        $t->send($dataToSend);

        echo " [SENDING...] " . $counter . " messages send               \r";
        $counter++;
    }

    Timer::stop('full');
    echo "\n " . $counter . ' Events send in ' . Timer::read('full', Timer::FORMAT_SECONDS) . 'seconds';
}

function getDestination($type, $destinationName, $environmentName)
{
    switch ($type) {
        case '-q':
            $destination = new Queue($destinationName, $environmentName);
            break;
        case '-w':
            $destination = new Ws($destinationName, $environmentName);
            break;
        default:
            echo "\n [ERROR] type must be -q (for queues) or -w (for webservices), given: " . $type . "\n\n";
            exit(1);
    }

    return $destination;
}

function getRawConsoleParams($args)
{
    array_shift($args);

    $type            = array_shift($args);
    $templateFile    = array_shift($args);
    $environmentName = array_shift($args);
    $queueName       = array_shift($args);

    $extraParams = $args;

    return [$type, $templateFile, $environmentName, $queueName, $extraParams];
}


function usage()
{
    echo "\n [USAGE] php shogun.php [type] [template-file] [env] [dest-name] [params]";
    echo "\n Params:";
    echo "\n\t(1) fieldName@range:1..300";
    echo "\n\t(2) fieldName@options:optionA,optionB";
    echo "\n\t(3) fieldName:fixedValue";
    echo "\n Example: php shogun.php -q sample.js dev queue-a field1@range:1..10 field2@options:A,B,C,D field3:value";
    echo "\n\n";

    exit(1);
}

function startScript($destinationName, $environmentName, $medium)
{
    echo "\n\n [START] Sending events to ". $medium ." [" . $destinationName . "] in environment [" . $environmentName . "]\n";
}

function endScript()
{
    echo "\n [END]\n\n";
    exit(0);
}
