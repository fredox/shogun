This script is a tool to send multiple messages to a queue or a webservice with a predefined data configured.

Usage for queue:

php shogun.php -q [template-file] [env] [queue-name] [params]

Usage for webservice:

php shogun.php -w [template-file] [env] [endpoint-name] [params]

Where:

- template-file: Is the name of the template data file in templates folder. This file can be
filled with params following this format %param%. When call to script can include this params
in list of script params. This params will be replaced for the range or options or fixed value
via script call (explained bellow).

- env: Queue/Ws environment alias, described in queues.yaml

- queue/ws-name: Queue/ws name alias, described in queues.yaml/ws.yaml the queue/webservice destination of the generated events

- params: We can send all the necessary params, but must to be match the following formats:

    - range param: Formatted as fieldName@range:1..100 The string %fieldName% in template
    will be replaced by numbers from 1 to 100 in the successive calls.
    - options param: Formatted as fieldName@options:A,B,C The string %fieldName% in template
    will be replaced cyclically by these values.
    - single param: Formatted as fieldName:fixedValue The string %fieldName% in template
    will be replaced always by this value.
    