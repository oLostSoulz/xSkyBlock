<?php

namespace Fadhel\xSkyBlock\commands;

use Fadhel\xSkyBlock\Main;
use Fadhel\xSkyBlock\utils\form\CustomForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Grant extends Command
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("grant", "Ranks management", "", [""]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player and $sender->isOp()) $this->mainUI($sender);
    }

    public function mainUI(Player $player): void
    {
        $form = new CustomForm(function (Player $event, $data) {
            $player = $event->getPlayer();
            if ($data === null) {
                return;
            }
            if ($data[0] && $data[1]) {
                $ranks = array("Default", "YouTuber", "VIP", "MVP", "God", "Builder", "Trainee", "Admin", "Owner");
                if(!in_array($data[1], $ranks)) {
                    $player->sendMessage("§cInvalid rank.");
                    return;
                }
                $kid = $this->plugin->getServer()->getPlayer($data[0]);
                if ($kid !== null) {
                    $this->plugin->setRank($kid, $data[1]);
                    $player->sendMessage("§6Success, Player: " . $data[0] . " Rank: " . $data[1]);
                } else {
                    $player->sendMessage("§cPlayer not online");
                }
            } else {
                $player->sendMessage("§cProvide all information boomer.");
            }
        });
        $form->setTitle("Grant players");
        $form->addInput("Player", "Fadhel");
        $form->addInput("Rank", "Developer");
        $form->sendToPlayer($player);
    }
}