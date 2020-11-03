<?php

declare(strict_types=1);

namespace Fadhel\xSkyBlock\utils;

use Fadhel\xSkyBlock\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class Status extends Task
{
    protected $player;
    protected $plugin;
    protected $score;

    public function __construct(Main $plugin, Player $player)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->score = new Scoreboard("§l§dSkyblock", Scoreboard::CREATE);
        $this->score->create(Scoreboard::SIDEBAR, Scoreboard::ASCENDING);
    }

    public function onRun(int $currentTick)
    {
        if ($this->player->isOnline()) {
            $x = round($this->player->getX());
            $y = round($this->player->getY());
            $z = round($this->player->getZ());
            $rank = $this->plugin->getRank($this->player);
            $level = $this->plugin->getLevel($this->player);
            $money = $this->plugin->getMoney($this->player);
            $broken = $this->plugin->getBroken($this->player);
            $placed = $this->plugin->getPlaced($this->player);
            $this->score->setLine($this->player, 9, " ");
            $this->score->setLine($this->player, 8, "§aRank: §7$rank");
            $this->score->setLine($this->player, 7, "§aLevel: §7$level");
            $this->score->setLine($this->player, 6, "§aMoney: §7$$money");
            $this->score->setLine($this->player, 5, "§aBlocks placed: §7$placed");
            $this->score->setLine($this->player, 4, "§aBlocks broken: §7$broken");
            $this->score->setLine($this->player, 3, "§aXYZ:§7 $x/$y/$z");
            $this->score->setLine($this->player, 2, "  ");
            $this->score->setLine($this->player, 1, "§dhexitmc.com");
            $this->score->addDisplay($this->player);
        }else{
            $this->getHandler()->cancel();
            $this->score->removeDisplay($this->player);
        }
    }
}