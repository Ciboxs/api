<?php

declare(strict_types=1);

namespace Api\Engine\Classes;

class Engine {


    # protected $namespaceModule = '\constructor\modules';

    public $r                  = [];

    public $version            = '';
    public $section            = '';

    public $action             = '';


    public function __construct() {

        $this->http = new Http();

        $this->parseUrl();

        if (!$this->version || !$this->section) {
            $this->http->responseCode(404);
        }


        # константа модуля по умолчанию
        define('SECTION', $this->section);


        $this->execute($this->section);

    }


    # подключает и отрабатывает компонент
    public function execute(string $section): void
    {

        $moduleDir = $_SERVER["DOCUMENT_ROOT"] . SEP . DIRAPI_MODULES . $section . '/' . $this->version . '/';

        if (!file_exists($moduleDir)) {

            $this->http->responseCode(404);

        }

        foreach (glob($moduleDir . '/*.php', GLOB_BRACE) as $path) {
            require_once $path;
        }

        $class = strstr($section, '.', true) ?: $section;

        # для передачи новому модулю его истенного имени
        $this->section = $class;

        # $className = $namespaceComponent . '\module' . ucfirst($class);
        $className = '\Api\Module\module' . ucfirst($class);


        if (class_exists($className, false)) {
            # return new $className($this);
            $o = new $className($this);
            if (method_exists($o, 'action')) {
                $o->action();
            }
        }

        $this->http->responseCode(404);

    }


    # разбираем ссылку
    public function parseUrl() {

        $params = explode('/',trim(parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH),'/'));

        $this->version = $params[0]??false;
        $this->section = $params[1]??false;

        $this->action  = $params[2]??false;

        if (isset($params[3])) {
            $this->http->responseCode(404);
        }

        $this->r = ($_SERVER["REQUEST_METHOD"]=='POST' ? $_POST : $_GET);

    return($this->r);
    }


}



?>