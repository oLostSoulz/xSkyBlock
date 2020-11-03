<?php

namespace Fadhel\xSkyBlock\commands;

use Fadhel\xSkyBlock\Main;
use Fadhel\xSkyBlock\utils\form\CustomForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;

class Key extends Command
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("key", "Key", "", [""]);
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
            if ($data[0]) {
                $keys = array("common", "rare", "epic", "legendary");
                if ($data[1]) {
                    if (in_array($data[1], $keys)) {
                        $kid = $this->plugin->getServer()->getPlayer($data[0]);
                        if ($kid !== null) {
                            $this->giveKey($kid, $data[1]);
                            $player->sendMessage("§eSuccessfully give §f " . $kid->getName() . " §6 " . $data[1]);
                        } else {
                            $player->sendMessage("§cPlayer not online");
                        }
                    } else {
                        $player->sendMessage("§cProvide a valid key: §fcommon, rare, epic, legendary");
                    }
                }else{
                    $player->sendMessage("§cProvide a key name.");
                }
            } else {
                $player->sendMessage("§cProvide a player name.");
            }
        });
        $form->setTitle("Key");
        $form->addInput("Player", "Fadhel");
        $form->addInput("Key", "Common");
        $form->sendToPlayer($player);
    }

    public function giveKey(Player $player, string $key): void
    {
        switch ($key) {
            case "common":
                $player->getInventory()->addItem(Item::get(Item::PAPER, 0, 1)->setCustomName("§r§aCommon Key"));
                break;
            case "rare":
                $player->getInventory()->addItem(Item::get(Item::PAPER, 0, 1)->setCustomName("§r§eRare Key"));
                break;
            case "epic":
                $player->getInventory()->addItem(Item::get(Item::PAPER, 0, 1)->setCustomName("§r§5Epic Key"));
                break;
            case "legendary":
                $player->getInventory()->addItem(Item::get(Item::PAPER, 0, 1)->setCustomName("§r§6Legendary Key"));
        }
    }
}