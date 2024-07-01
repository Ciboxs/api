<?php

declare(strict_types=1);

namespace Api\Engine\Classes;

abstract class Module {


    public $r               = []; # запрос
    public $section         = []; # модуль


    protected string $methodPath = 'methods' . SEP;


    public function __construct(array|object $params = []) {

        foreach ($params as $object=>$param) { $this->$object = $param; unset($object, $param); }

    }


    # **
    public function action(): void
    {

        if ($this->action == 'action') {
            $this->http->responseCode(404);
        }

        $action = $this->action??'' ? $this->action : 'example';
        $this->$action();

    }


    public function __call($name, $arguments)
    {

        # $methodFilePath = __DIR__ . '/' . $this->methodPath . $name . '.php';
        $methodFilePath = DIRAPI_MODULES . $this->section . SEP . $this->version . SEP . $this->methodPath . $name . '.php';

        if (file_exists($methodFilePath)) {

            require $methodFilePath;

        }
        else {

            $this->http->responseCode(404);

        }
    }


}