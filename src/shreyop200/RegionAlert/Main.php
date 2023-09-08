<?php

namespace shreyop200\RegionAlert;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    protected $config;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, []);
    }

    public function onPlayerMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();
        $position = $player->getPosition();
        $config = $this->config;

        if (!$player->hasPermission("regionalert.use")) {
            return; 
        }

        $regions = $config->getAll();

        foreach ($regions as $regionKey => $regionData) {
            if (is_array($regionData) &&
                isset($regionData["world"]) &&
                isset($regionData["x"]) &&
                isset($regionData["y"]) &&
                isset($regionData["z"]) &&
                isset($regionData["msg"])) {
                
                $regionWorld = $regionData["world"];
                $regionX = $regionData["x"];
                $regionY = $regionData["y"];
                $regionZ = $regionData["z"];
                $regionMsg = $regionData["msg"];

                if ($world === $regionWorld &&
                    $position->getX() === $regionX &&
                    $position->getY() === $regionY &&
                    $position->getZ() === $regionZ) {
                    $player->sendTitle($regionMsg);
                    break;
                }
            } else {
                $this->getLogger()->error("Invalid configuration for region key: $regionKey. Check if world, x, y, z, and msg are defined.");
            }
        }
    }
}
