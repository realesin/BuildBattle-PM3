<?php

namespace BuildBattle;

use BuildBattle\GameSender;

Class ResetMap
{
    public function __construct(GameSender $plugin){
        $this->plugin = $plugin;
    }
    
    public function reload($lev)
    {
            $name = $lev->getFolderName();
            if ($this->plugin->getOwner()->getServer()->isLevelLoaded($name))
            {
                    $this->plugin->getOwner()->getServer()->unloadLevel($this->plugin->getOwner()->getServer()->getLevelByName($name));
            }
            $zip = new \ZipArchive;
            $zip->open($this->plugin->getOwner()->getDataFolder() . 'arenas/' . $name . '.zip');
            $zip->extractTo($this->plugin->getOwner()->getServer()->getDataPath() . 'worlds');
            $zip->close();
            unset($zip);
            $this->plugin->getOwner()->getServer()->loadLevel($name);
            return true;
    }
}