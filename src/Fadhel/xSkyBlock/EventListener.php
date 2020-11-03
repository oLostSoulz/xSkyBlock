<?php

declare(strict_types=1);

namespace Fadhel\xSkyBlock;

use Fadhel\xSkyBlock\utils\form\SimpleForm;
use Fadhel\xSkyBlock\utils\Status;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\math\Vector2;
use pocketmine\Player;
use pocketmine\utils\Config;

class EventListener implements Listener
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $this->plugin->getScheduler()->scheduleRepeatingTask(new Status($this->plugin, $player), 20);
        $player->addTitle("§dHexit", "§7SkyBlock");
        $player->setNameTag($this->getFormat($player) . $player->getName());
        $event->setJoinMessage("§7[§a+§7] §a" . $player->getName());
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $event->setQuitMessage("§7[§c-§7] §c" . $player->getName());
    }

    public function onLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player->hasPlayedBefore()) {
            $config = new Config($this->plugin->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
            $config->set("Rank", "Default");
            $config->set("Money", 1000);
            $config->set("Level", 1);
            $config->set("Placed", 0);
            $config->set("Broken", 0);
            $config->set("Starter", false);
            $config->set("Claimed", false);
            $config->save();
            $this->plugin->getServer()->broadcastMessage("§8§l(§e!§8) §r§eA new player joined the server §6" . $player->getName());
        }
    }

    public function getFormat(Player $player)
    {
        $rank = $this->plugin->getRank($player);
        switch ($rank):
            case "Owner":
                $format = "§8[§4Owner§8]§4 ";
                return $format;
                break;
            case "Admin":
                $format = "§8[§cAdmin§8]§c ";
                return $format;
                break;
            case "Moderator":
                $format = "§8[§5Moderator§8] ";
                return $format;
                break;
            case "Trainee":
                $format = "§8[§eTrainee§8]§e ";
                return $format;
            case "Builder":
                $format = "§8[§6Builder§8] ";
                return $format;
                break;
            case "God":
                $format = "§8[§bGOD§8]§b ";
                return $format;
                break;
            case "MVP":
                $format = "§8[§2MVP§8]§2 ";
                return $format;
                break;
            case "VIP":
                $format = "§8[§aVIP§8]§a ";
                return $format;
                break;
            case "YouTuber":
                $format = "§8[§cYouTube§8]§c ";
                return $format;
                break;
            case "Default":
                $format = "§8[§7Player§8]§7 ";
                return $format;
                break;
        endswitch;
        return true;
    }

    public function onChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        if ($event->isCancelled()) return;
        $event->setFormat($this->getFormat($player) . $player->getName() . " §f" . $event->getMessage());
    }

    public function sendUI(Player $player): void
    {
        $form = new SimpleForm(function (Player $event, $data) {
            $player = $event->getPlayer();
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
            }
        });
        $form->setTitle("Crates");
        $form->addButton("Common", 1, "https://thereeve.net/wp-content/uploads/2017/04/bronze-gold-diamond.png");
        $form->addButton("Rare", 1, "https://badblock.fr/archives/boutique_images/Cl%C3%A9s/iron-gold-diamond.png");
        $form->addButton("Epic", 1, "https://cdn.cloudprotected.net/rPPmDHlLQ1/57342f6b95854ad89e9c4088ab94adcf/5sfdnbr8mujl1vsc05xk6cdhg.png");
        $form->addButton("Legendary", 1, "https://i.ya-webdesign.com/images/crate-png-minecraft-3.png");
        $form->addButton("Exit");
        $form->sendToPlayer($player);
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($player->getLevel()->getFolderName() === "Hub" and $block->getId() === Block::CHEST) {
                $event->setCancelled(true);
                $this->sendUI($player);
            }
        }
    }

    public function onPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $worlds = array("Hub", "PvP", "Events");
        if (!in_array($player->getLevel()->getFolderName(), $worlds)) {
            $spawn = $player->getLevel()->getSpawnLocation();
            $vector = new Vector2($spawn->getX(), $spawn->getZ());
            $distance = $vector->distance($event->getBlock()->getX(), $event->getBlock()->getZ());
            $level = $this->plugin->getLevel($player);
            $y = $spawn->getY() + $this->getBorder($level);
            $test = $y - $event->getBlock()->getY();
            if ($distance >= $this->getBorder($level) || 0 >= $test) {
                $player->sendMessage("§l§8(§4!§8) §r§cPlease upgrade your island by /levelup for more space. Your current space is §fx/y: " . $this->getBorder($level) . " §cblocks.");
                $event->setCancelled(true);
            }
            if (!$event->isCancelled()) {
                $this->plugin->addPlaced($player);
            }
        } else {
            if (!$player->isOp()) {
                $event->setCancelled(true);
            }
        }
    }

    public function onBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $spawn = $player->getLevel()->getSpawnLocation();
        $vector = new Vector2($spawn->getX(), $spawn->getZ());
        $distance = $vector->distance($event->getBlock()->getX(), $event->getBlock()->getZ());
        $level = $this->plugin->getLevel($player);
        $worlds = array("Hub", "PvP", "Events");
        $y = $spawn->getY() + $this->getBorder($level);
        $test = $y - $event->getBlock()->getY();
        if (!in_array($player->getLevel()->getFolderName(), $worlds)) {
            if ($distance >= $this->getBorder($level) || 0 >= $test) {
                $player->sendMessage("§l§8(§4!§8) §r§cPlease upgrade your island by /levelup for more space. Your current space is §fx/y: " . $this->getBorder($level) . " §cblocks.");
                $event->setCancelled(true);
            }
            if (!$event->isCancelled()) {
                $this->plugin->addBroken($player);
                if (count($event->getDrops()) === 0) return;
                foreach ($event->getDrops() as $drop) {
                    if ($player->getInventory()->canAddItem($drop)) {
                        $player->addXp($event->getXpDropAmount());
                        $event->setXpDropAmount(0);
                        $player->getInventory()->addItem($drop);
                        $event->setDrops([]);
                    } else {
                        $player->addTitle("§l§8(§4!§8) §4Error", "§cYour inventory is full!");
                    }
                }
            }
        } else {
            if (!$player->isOp()) {
                $event->setCancelled(true);
            }
        }
    }

    public function getBorder(int $value): int
    {
        $border = 50;
        if ($value > 1) {
            $border = $border * $value;
        }
        return $border;
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $spawn = $player->getLevel()->getSpawnLocation();
        $vector = new Vector2($spawn->getX(), $spawn->getZ());
        $distance = $vector->distance($player->getX(), $player->getZ());
        $level = $this->plugin->getLevel($player);
        $worlds = array("Hub", "PvP", "Events");
        $y = $spawn->getY() + $this->getBorder($level);
        $test = $y - $player->getY();
        if (!in_array($player->getLevel()->getFolderName(), $worlds)) {
            if ($distance >= $this->getBorder($level) || 0 >= $test) {
                $player->sendMessage("§l§8(§4!§8) §r§cPlease upgrade your island by /levelup for more space. Your current space is §fx/y: " . $this->getBorder($level) . " §cblocks.");
                $event->setCancelled(true);
            }
        }
    }

    public function onEntityDanage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            if ($player->getLevel()->getFolderName() !== "PvP") {
                $event->setCancelled();
                if ($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
                    $player->teleport($player->getLevel()->getSpawnLocation());
                }
            }
        }
    }

    public function block(BlockFormEvent $event): void
    {
        $block = $event->getBlock();
        $cobbleInstance = BlockFactory::get(Block::COBBLESTONE);
        if ($event->getNewState() instanceof $cobbleInstance) {
            $event->setCancelled(true);
            $newBlock = null;
            $id = mt_rand(1, 8);
            switch ($id) {
                case 1:
                    $newBlock = BlockFactory::get(Block::COBBLESTONE);
                    break;
                case 2:
                    $newBlock = BlockFactory::get(Block::IRON_ORE);
                    break;
                case 3:
                    $newBlock = BlockFactory::get(Block::GOLD_ORE);
                    break;
                case 4:
                    $newBlock = BlockFactory::get(Block::EMERALD_ORE);
                    break;
                case 5:
                    $newBlock = BlockFactory::get(Block::COAL_ORE);
                    break;
                case 6:
                    $newBlock = BlockFactory::get(Block::REDSTONE_ORE);
                    break;
                case 7:
                    $newBlock = BlockFactory::get(Block::DIAMOND_ORE);
                    break;
                case 8:
                    $newBlock = BlockFactory::get(Block::LAPIS_ORE);
            }
            $block->getLevel()->setBlock($block, $newBlock, true, true);
        }
    }
}