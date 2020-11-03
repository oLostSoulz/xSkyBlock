<?php

namespace Fadhel\xSkyBlock\commands;

use Fadhel\xSkyBlock\Main;
use Fadhel\xSkyBlock\utils\form\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Warp extends Command
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("warp", "Warps list", "", ["warps"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) $this->mainUI($sender);
    }

    public function mainUI(Player $player): void
    {
        $form = new SimpleForm(function (Player $event, $data) {
            $player = $event->getPlayer();
            if($data === null){
                return;
            }
            switch ($data){
                case 0:
                    $player->teleport($this->plugin->getServer()->getLevelByName("PvP")->getSpawnLocation());
                    break;
                case 1:
                    $player->teleport($this->plugin->getServer()->getLevelByName("Hub")->getSpawnLocation());
            }
        });
        $form->setTitle("Warps list");
        $form->addButton("PvP", 0, "textures/items/diamond_sword");
        $form->addButton("Spawn", 0, "textures/items/paper");
        $form->sendToPlayer($player);
    }
}