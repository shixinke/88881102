<?php
namespace envtool;
use envtool\EnvTool;

class EnvConsole {

    private $args = [];
    private $tool;
    public function __construct($args) {
        $this->args = $this->parseArgs($args);
        $this->tool = new \envtool\EnvTool($this->args['config'], $this->args['example'], $this->args['path'], $this->args['env']);
    }

    public function run() {
        switch ($this->args['command']) {
            case 'start':
                $this->start();
                break;
            case 'init':
                $this->init();
                break;
            case 'sync':
                $this->sync();
                break;
            case 'envList':
                $this->envList();
                break;
            default:
                $this->usage();
        }
    }

    public function start() {
        $status =  $this->tool->copy();
        if (!$status) {
            echo $this->tool->getErrorMsg().PHP_EOL;
        } else {
            echo "create env file success".PHP_EOL;
        }
    }

    public function init() {
        return $this->tool->init();
    }

    public function sync() {
        $status = $this->tool->sync($this->args['base']);
        if (!$status) {
            echo $this->tool->getErrorMsg().PHP_EOL;
        } else {
            echo "sync env file success".PHP_EOL;
        }
    }

    public function envList() {
        if (!empty($this->args['envList'])) {
            $this->tool->setEnvList($this->args['envList']);
        }
    }

    public function usage() {
        $usage =  "usage:".$this->args['script'].' [args] [command]'.PHP_EOL;
        $usage .= "args: --env envName --config configFileName --path envFilePath --example exampleFile --base defaultEnvName ".PHP_EOL;
        $usage .= "command: start | init | sync | envList ".PHP_EOL;
        echo $usage;
    }

    private function parseArgs($args) {
        $argc = count($args) - 1;
        $script = array_shift($args);
        $argsMap = [];
        $argsMap['script'] = $script;
        for($i = 0; $i < $argc; $i++) {
            $v = $args[$i];
            if (substr($v, 0, 2) == '--') {
                $key = substr($v, 2);
                $argsMap[$key] = '';
                if (isset($args[$i+1]) && (substr($args[$i+1], 0, 2) != '--')) {
                    $argsMap[$key] = $args[$i+1];
                    $i ++;
                }
            } else {
                $argsMap['command'] = $v;
            }
        }
        $argsMap['env'] = !isset($argsMap['env']) ? 'dev' : $argsMap['env'];
        $argsMap['config'] = !isset($argsMap['config']) ? '.dev' : $argsMap['config'];
        $argsMap['path'] = !isset($argsMap['path']) ? 'env' : $argsMap['path'];
        $argsMap['example'] = !isset($argsMap['example']) ? '.env.example' : $argsMap['example'];
        $argsMap['command'] = !isset($argsMap['command']) ? 'start' : $argsMap['command'];
        $argsMap['base'] = !isset($argsMap['base']) ? 'prod' : $argsMap['base'];
        $argsMap['envList'] =  !isset($argsMap['envList']) ? [] : explode(',', $argsMap['envList']);
        return $argsMap;
    }


}