<?php

$this->result = [
    'php' => phpversion(),
    'zend_version' => zend_version()
];

$this->result['methos'] = $_SERVER["REQUEST_METHOD"];

foreach ($this->r as $key => $value) {
    $this->result[$key] = $value;
    unset($key, $value);
}

foreach ($_GET as $key => $value) {
    $this->result[$key] = $value;
    unset($key, $value);
}

$this->http->responseCode(200);
print json_encode($this->result);
exit;