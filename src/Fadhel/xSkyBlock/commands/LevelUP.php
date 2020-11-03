<?php

namespace Fadhel\xSkyBlock\commands;

use Fadhel\xSkyBlock\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class LevelUP extends Command
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("levelup", "Upgrade your island level", "", ["lp"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($this->plugin->getMoney($sender) >= 50000) {
                $this->plugin->reduceMoney($sender, 50000);
                $this->plugin->addLevel($sender);
                $sender->sendMessage("§8§l(§a!§8)§r §aYour island has been upgraded to §f" . $this->plugin->getLevel($sender) . "§a.");
            } else {
                $sender->sendMessage("§8§l(§c!§8)§r §cYou don't have enough money to upgrade your island's level, You need §f$50,000§c.");
            }
        }
    }
}