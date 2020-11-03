<?php

namespace Fadhel\xSkyBlock;

use Fadhel\xSkyBlock\commands\Grant;
use Fadhel\xSkyBlock\commands\Island;
use Fadhel\xSkyBlock\commands\Key;
use Fadhel\xSkyBlock\commands\Kit;
use Fadhel\xSkyBlock\commands\Warp;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase
{
    /** @var self */
    public static $instance;

    protected $api;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder() . "players");
        $this->getServer()->getCommandMap()->register("", new Island($this));
        $this->getServer()->getCommandMap()->register("", new Warp($this));
        $this->getServer()->getCommandMap()->register("", new Kit($this));
        $this->getServer()->getCommandMap()->register("", new Grant($this));
        $this->getServer()->getCommandMap()->register("", new Key($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->notice("Enabled");
        $this->api = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    }

    public function onDisable(): void
    {
        $this->getLogger()->warning("Disabled");
    }

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function reduceMoney(Player $player, int $money): void
    {
        $this->api->reduceMoney($player, $money);
    }

    public function addLevel(Player $player, int $level = 1): void
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        $config->set("Level", $config->get("Level") + $level);
        $config->save();
    }

    public function addPlaced(Player $player, int $level = 1): void
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        $config->set("Placed", $config->get("Placed") + $level);
        $config->save();
    }

    public function addBroken(Player $player, int $level = 1): void
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        $config->set("Broken", $config->get("Broken") + $level);
        $config->save();
    }

    public function getMoney(Player $player): int
    {
        return $this->api->myMoney($player);
    }

    public function getLevel(Player $player): int
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        return $config->get("Level");
    }

    public function setRank(Player $player, string $rank): void
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        $config->set("Rank", $rank);
        $config->save();
    }

    public function setClaimed(Player $player): void
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        $config->set("Claimed", true);
        $config->save();
    }

    public function getRank(Player $player): string
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        return $config->get("Rank");
    }

    public function hasClaimed(Player $player): bool
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        return $config->get("Claimed");
    }

    public function getPlaced(Player $player): int
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        return $config->get("Placed");
    }

    public function getBroken(Player $player): int
    {
        $config = new Config($this->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
        return $config->get("Broken");
    }
}