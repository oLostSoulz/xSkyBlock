<?php

namespace Fadhel\xSkyBlock\commands;

use Fadhel\xSkyBlock\Main;
use Fadhel\xSkyBlock\utils\form\CustomForm;
use Fadhel\xSkyBlock\utils\form\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Island extends Command
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("manage", "Island management", "", ["isui"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) $this->mainUI($sender);
    }

    public function mainUI(Player $player): void
    {
        $form = new SimpleForm(function (Player $event, $data) {
            $player = $event->getPlayer();
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    $this->Manage($player);
                    break;
                case 1:
                    $this->plugin->getServer()->dispatchCommand($player, "is create");
            }
        });
        $form->setTitle("SkyBlock");
        $form->setContent("Select category:");
        $form->addButton("Manage Island");
        $form->addButton("Create Island");
        $form->sendToPlayer($player);
    }

    public function Manage(Player $player): void
    {
        $form = new SimpleForm(function (Player $event, $data) {
            $player = $event->getPlayer();
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    $this->plugin->getServer()->dispatchCommand($player, "is disband");
                    break;
                case 1:
                    $this->Invite($player);
                    break;
                case 2:
                    $this->Kick($player);
                    break;
                case 3:
                    $this->plugin->getServer()->dispatchCommand($player, "is members");
                    break;
                case 4:
                    $this->plugin->getServer()->dispatchCommand($player, "is setspawn");
                    break;
                case 5:
                    $this->plugin->getServer()->dispatchCommand($player, "is lock");
                    break;
                case 6:
                    $this->plugin->getServer()->dispatchCommand($player, "levelup");
                    break;
                case 7:
                    $this->mainUI($player);
            }
        });
        $form->setTitle("Manage your Island");
        $form->setContent("Select category:");
        $form->addButton("Disband", 0, "textures/ui/realms_red_x");
        $form->addButton("Invite", 0, "textures/ui/invite_base");
        $form->addButton("Kick", 0, "textures/ui/trash");
        $form->addButton("List", 0, "textures/ui/FriendsDiversity");
        $form->addButton("Update Spawn", 0, "textures/ui/magnifyingGlass");
        $form->addButton("Lock", 0, "textures/ui/ErrorGlyph");
        $form->addButton("Upgrade Island", 0, "textures/ui/plus");
        $form->addButton("Main menu");
        $form->sendToPlayer($player);
    }

    public function Kick(Player $player): void
    {
        $form = new CustomForm(function (Player $event, $data) {
            $player = $event->getPlayer();
            if ($data === null) {
                return;
            }
            if ($data[0]) {
                $this->plugin->getServer()->dispatchCommand($player, "is banish " . $data[0]);
            } else {
                $player->sendMessage("§cProvide a player name.");
            }
        });
        $form->setTitle("Kick");
        $form->addInput("Player", "Fadhel");
        $form->sendToPlayer($player);
    }

    public function Invite(Player $player): void
    {
        $form = new CustomForm(function (Player $event, $data) {
            $player = $event->getPlayer();
            if ($data === null) {
                return;
            }
            if ($data[0]) {
                $this->plugin->getServer()->dispatchCommand($player, "is invite " . $data[0]);
            } else {
                $player->sendMessage("§cProvide a player name.");
            }
        });
        $form->setTitle("Invite");
        $form->addInput("Player", "Fadhel");
        $form->sendToPlayer($player);
    }
}