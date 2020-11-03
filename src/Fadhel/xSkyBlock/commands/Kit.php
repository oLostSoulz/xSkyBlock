<?php

namespace Fadhel\xSkyBlock\commands;

use Fadhel\xSkyBlock\Main;
use Fadhel\xSkyBlock\utils\form\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;

class Kit extends Command
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("kit", "Server Kits", "", ["kits"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) $this->mainUI($sender);
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
                    if (!$this->plugin->hasClaimed($player)) {
                        $this->plugin->setClaimed($player);
                        $sword = Item::get(Item::DIAMOND_SWORD, 0, 1);
                        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 3));
                        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));
                        $player->getInventory()->setItem(0, $sword);
                        $player->getInventory()->setItem(1, Item::get(Item::FISHING_ROD));
                        $bow = Item::get(Item::BOW, 0, 1);
                        $bow->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PUNCH), 1));
                        $bow->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::POWER), 1));
                        $bow->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));
                        $player->getInventory()->setItem(2, $bow);
                        $player->getInventory()->setItem(3, Item::get(Item::DIAMOND_PICKAXE));
                        $player->getInventory()->setItem(3, Item::get(Item::DIAMOND_AXE));
                        $player->getInventory()->addItem(Item::get(Item::GOLDEN_APPLE, 0, 5));
                        $player->getInventory()->addItem(Item::get(Item::BUCKET, Item::STILL_LAVA));
                        $player->getInventory()->addItem(Item::get(Item::BUCKET, Item::STILL_WATER));
                        $player->getInventory()->addItem(Item::get(Item::WOODEN_PLANKS, 0, 64));
                        $player->getInventory()->addItem(Item::get(Item::COBBLESTONE, 0, 64));
                        $helmet = Item::get(Item::DIAMOND_HELMET);
                        $helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));
                        $chestplate = Item::get(Item::DIAMOND_CHESTPLATE);
                        $chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));
                        $leggings = Item::get(Item::DIAMOND_LEGGINGS);
                        $leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));
                        $boots = Item::get(Item::DIAMOND_CHESTPLATE);
                        $boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 3));
                        $player->getArmorInventory()->setHelmet($helmet);
                        $player->getArmorInventory()->setChestplate($chestplate);
                        $player->getArmorInventory()->setLeggings($leggings);
                        $player->getArmorInventory()->setBoots($boots);
                        $player->sendMessage("§l§8(§a!§8) §r§aYou've claimed the Starter kit.");
                    } else {
                        $player->sendMessage("§l§8(§c!§8) §r§cYou've already claim this kit.");
                    }
            }
        });
        $form->setTitle("Kits");
        $form->addButton("Starter");
        $form->addButton("Exit");
        $form->sendToPlayer($player);
    }
}