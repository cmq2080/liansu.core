<?php

namespace liansu\core\traits;

trait TAppConfig
{
    private $configFiles = [];
    private $tmpConfigs = [];

    private function setConfigFiles($configFiles)
    {
        if (is_array($configFiles)) {
            $this->configFiles = $configFiles;
        } else if (is_callable($configFiles)) {
            $this->tmpConfigs = $configFiles();
        } else if (is_string($configFiles)) {
            $this->configFiles[] = $configFiles;
        } else {
            throw new \Exception('Invalid Configuration File');
        }
    }

    public function getConfigFiles()
    {
        return $this->configFiles;
    }

    public function getTmpConfigs()
    {
        return $this->tmpConfigs;
    }
}
