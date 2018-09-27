<?php
namespace envtool;

class EnvTool {

    private $configFile;
    private $env;
    private $envConfigPath;
    private $exampleFile;
    private $errorMsg;
    private $envList = ['dev', 'test', 'beta', 'prod'];

    public function __construct($configFile = '.env', $exampleFile = '.env.example.example', $envConfigPath = '', $env = '') {
        $this->configFile = $configFile;
        $this->exampleFile = $exampleFile;
        $this->envConfigPath = $envConfigPath;
        $this->env = $env;
    }

    public function copy() {
        if (!file_exists($this->envConfigPath)) {
            $this->init();
        }
        $envFile = $this->envConfigPath.DIRECTORY_SEPARATOR.$this->env;
        if (!is_file($envFile)) {
            $this->errorMsg = 'environment config file not exists';
            return false;
        }
        $content = file_get_contents($envFile);
        $len = file_put_contents($this->configFile, $content);
        if ($len > 0) {
            return true;
        }
        $this->errorMsg = 'write config file failed';
        return false;
    }

    public function init() {
        if (!file_exists($this->envConfigPath)) {
            mkdir($this->envConfigPath);
        }
        $exampleContent = '';
        if (is_file($this->exampleFile)) {
            $exampleContent = file_get_contents($this->exampleFile);
        } else {
            echo 'example file not exists'.PHP_EOL;
        }
        foreach ($this->envList as $env) {
            $envFile = $this->envConfigPath.DIRECTORY_SEPARATOR.$env;
            if (!is_file($envFile)) {
                file_put_contents($envFile, $exampleContent);
            }
        }
    }

    public function sync($env = 'prod') {
        $defaultConfigFile = $this->envConfigPath.DIRECTORY_SEPARATOR.$env;
        if (!is_file($defaultConfigFile)) {
            $defaultConfigFile = $this->exampleFile;
            if (!is_file($defaultConfigFile)) {
                $this->errorMsg = 'example config file not exists';
                return false;
            }
        }
        $defaultConfigMap = self::configMap($defaultConfigFile);
        $status = false;
        foreach ($this->envList as $envName) {
            if ($envName != $env) {
                $envFile = $this->envConfigPath.DIRECTORY_SEPARATOR.$envName;
                $configMap = self::configMap($envFile);
                $configMap = self::compareMap($configMap, $defaultConfigMap);
                $writeStatus = self::writeFile($envFile, $configMap);
                if ($writeStatus) {
                    $status = true;
                } else {
                    $this->errorMsg = $this->errorMsg.';'.$envFile.'sync failed\n';
                }
            }
        }
        return $status;

    }

    public static function configMap($configFile) {
        $defaultContent = file($configFile);
        $configMap = [];
        $lineNum = 0;
        $commentNum = 0;
        foreach ($defaultContent as $line) {
            $line = str_replace("\n", "", $line);
            $tmp = explode('=', trim($line));
            if (count($tmp) == 1 && $tmp[0] == '') {
                $configMap['#blank'.$lineNum] = '';
                $lineNum ++;
                continue;
            }
            if (substr($line, 0, 1) == '#' ) {
                $configMap['#comment'.$commentNum] = $line;
                $commentNum ++;
                continue;
            }
            $configMap[$tmp[0]] = isset($tmp[1]) ? trim($tmp[1]) : NULL;
        }
        return $configMap;
    }

    public static function compareMap($sourceMap = [], $dstMap = []) {
        foreach ($dstMap as $k=>$v) {
            $sourceMap[$k] = $v;
        }
        return $sourceMap;
    }

    public static function writeFile($file, $contentMap) {
        $content = '';
        foreach ($contentMap as $k => $value) {
            if (substr($k, 0, 6) == '#blank' || substr(trim($k), 0, 8) == '#comment') {
                $content .= $value."\n";
            } else {
                $content .= $k.'='.$value."\n";
            }
        }
        return file_put_contents($file, $content);
    }

    public function setEnvList($envList = []) {
        $this->envList = $envList;
    }

    public function getErrorMsg() {
        return $this->errorMsg;
    }
}