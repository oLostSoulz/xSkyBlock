<?php

declare(strict_types=1);

/**
 * Copyright 2020 Fadhel
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Fadhel\xSkyBlock\utils;

use pocketmine\network\mcpe\protocol\{RemoveObjectivePacket, SetDisplayObjectivePacket, SetScorePacket};
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use pocketmine\Server;

class Scoreboard
{
    const MAX_LINES = 16;
    const CREATE = 0;
    const MODIFY = 1;
    const ASCENDING = 0;
    const DESCENDING = 1;
    const LIST = "list";
    const SIDEBAR = "sidebar";
    const BELOWNAME = "belowname";

    /**
     * @var string
     */
    private $objectiveName;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $displaySlot;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @var int
     */
    private $scoreboardId;

    /** @var array */
    private $entries;

    /** @var array */
    private $scoreboards;

    /** @var array */
    private $displaySlots;

    /** @var array */
    private $sortOrders;

    /** @var array */
    private $ids;

    /** @var array */
    private $viewers;

    public function __construct(string $title, int $action)
    {
        $this->displayName = $title;
        if ($action === self::CREATE && is_null($this->getId($title))) {
            $this->objectiveName = uniqid();

            return;
        }
        $this->objectiveName = $this->getId($title);
        $this->displaySlot = $this->getDisplaySlot($this->objectiveName);
        $this->sortOrder = $this->getSortOrder($this->objectiveName);
        $this->scoreboardId = $this->getScoreboardId($this->objectiveName);
    }

    /**
     * @param $player
     */
    public function addDisplay(Player $player)
    {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = $this->displaySlot;
        $pk->objectiveName = $this->objectiveName;
        $pk->displayName = $this->displayName;
        $pk->criteriaName = "dummy";
        $pk->sortOrder = $this->sortOrder;
        $player->sendDataPacket($pk);
        $this->addViewer($this->objectiveName, $player->getName());
    }

    /**
     * @param $player
     */
    public function removeDisplay(Player $player)
    {
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $this->objectiveName;
        $player->sendDataPacket($pk);

        $this->removeViewer($this->objectiveName, $player->getName());
    }

    /**
     * @param Player $player
     * @param int $line
     * @param string $message
     */
    public function setLine(Player $player, int $line, string $message)
    {
        $pk = new SetScorePacket();
        $pk->type = SetScorePacket::TYPE_REMOVE;
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $this->objectiveName;
        $entry->score = self::MAX_LINES - $line;
        $entry->scoreboardId = ($this->scoreboardId + $line);
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
        $pk = new SetScorePacket();
        $pk->type = SetScorePacket::TYPE_CHANGE;
        if (!$this->entryExist($this->objectiveName, ($line - 2)) && $line !== 1) {
            for ($i = 1; $i <= ($line - 1); $i++) {
                if (!$this->entryExist($this->objectiveName, ($i - 1))) {
                    $entry = new ScorePacketEntry();
                    $entry->objectiveName = $this->objectiveName;
                    $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
                    $entry->customName = str_repeat(" ", $i); //You can't send two lines with the same message
                    $entry->score = self::MAX_LINES - $i;
                    $entry->scoreboardId = ($this->scoreboardId + $i - 1);
                    $pk->entries[] = $entry;
                    $this->addEntry($this->objectiveName, ($i - 1), $entry);
                }
            }
        }
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $this->objectiveName;
        $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $entry->customName = $message;
        $entry->score = self::MAX_LINES - $line;
        $entry->scoreboardId = ($this->scoreboardId + $line);
        $pk->entries[] = $entry;
        $this->addEntry($this->objectiveName, ($line - 1), $entry);
        $player->sendDataPacket($pk);
    }

    /**
     * @param Player $player
     * @param int $line
     */
    public function removeLine(Player $player, int $line)
    {
        $pk = new SetScorePacket();
        $pk->type = SetScorePacket::TYPE_REMOVE;
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $this->objectiveName;
        $entry->score = self::MAX_LINES - $line;
        $entry->scoreboardId = ($this->scoreboardId + $line);
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
        $this->removeEntry($this->objectiveName, $line);
    }

    /**
     * @param string $displaySlot
     * @param int $sortOrder
     */
    public function create(string $displaySlot, int $sortOrder)
    {
        $this->displaySlot = $displaySlot;
        $this->sortOrder = $sortOrder;
        $this->scoreboardId = mt_rand(1, 100000);
        $this->registerScoreboard($this->objectiveName, $this->displayName, $this->displaySlot, $this->sortOrder, $this->scoreboardId);
    }

    public function delete()
    {
        $this->unregisterScoreboard($this->objectiveName, $this->displayName);
    }

    /**
     * @param string $newName
     */
    public function rename(string $newName)
    {
        $this->scoreboards[$newName] = $this->scoreboards[$this->displayName];
        unset($this->scoreboards[$this->displayName]);
        $this->displayName = $newName;
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $this->objectiveName;
        $pk2 = new SetDisplayObjectivePacket();
        $pk2->displaySlot = $this->displaySlot;
        $pk2->objectiveName = $this->objectiveName;
        $pk2->displayName = $this->displayName;
        $pk2->criteriaName = "dummy";
        $pk2->sortOrder = $this->sortOrder;
        $pk3 = new SetScorePacket();
        $pk3->type = SetScorePacket::TYPE_CHANGE;
        foreach ($this->getEntries($this->objectiveName) as $index => $entry) {
            $pk3->entries[$index] = $entry;
        }
        foreach ($this->getViewers($this->objectiveName) as $name) {
            $p = Server::getInstance()->getPlayer($name);
            $p->sendDataPacket($pk);
            $p->sendDataPacket($pk2);
            $p->sendDataPacket($pk3);
        }
    }

    /**
     * @param string $objectiveName
     * @param int $line
     * @param ScorePacketEntry $entry
     */
    public function addEntry(string $objectiveName, int $line, ScorePacketEntry $entry)
    {
        $this->entries[$objectiveName][$line] = $entry;
    }

    /**
     * @param string $objectiveName The identification of the scoreboard
     * @param int $line The line of the scoreboard
     */
    public function removeEntry(string $objectiveName, int $line)
    {
        unset($this->entries[$objectiveName][$line]);
    }

    public function registerScoreboard(string $objectiveName, string $displayName, string $displaySlot, int $sortOrder, int $scoreboardId)
    {
        $this->entries[$objectiveName] = null;
        $this->scoreboards[$displayName] = $objectiveName;
        $this->displaySlots[$objectiveName] = $displaySlot;
        $this->sortOrders[$objectiveName] = $sortOrder;
        $this->ids[$objectiveName] = $scoreboardId;
        $this->viewers[$objectiveName] = [];
    }

    public function unregisterScoreboard(string $objectiveName, string $displayName)
    {
        unset($this->entries[$objectiveName]);
        unset($this->scoreboards[$displayName]);
        unset($this->displaySlots[$objectiveName]);
        unset($this->sortOrders[$objectiveName]);
        unset($this->ids[$objectiveName]);
        unset($this->viewers[$objectiveName]);
    }

    public function getEntries(string $objectiveName): array
    {
        return $this->entries[$objectiveName];
    }

    public function entryExist(string $objectiveName, int $line): bool
    {
        return isset($this->entries[$objectiveName][$line]);
    }

    public function getId(string $displayName)
    {
        return $this->scoreboards[$displayName] ?? null;
    }

    public function getDisplaySlot(string $objectiveName): string
    {
        return $this->displaySlots[$objectiveName];
    }

    public function getSortOrder(string $objectiveName): int
    {
        return $this->sortOrders[$objectiveName];
    }

    public function getScoreboardId(string $objectiveName): int
    {
        return $this->ids[$objectiveName];
    }

    public function addViewer(string $objectiveName, string $playerName)
    {
        if (!in_array($playerName, $this->viewers[$objectiveName])) {
            array_push($this->viewers[$objectiveName], $playerName);
        }
    }

    public function removeViewer(string $objectiveName, string $playerName)
    {
        if (in_array($playerName, $this->viewers[$objectiveName])) {
            if (($key = array_search($playerName, $this->viewers[$objectiveName])) !== false) {
                unset($this->viewers[$objectiveName][$key]);
            }
        }
    }

    public function getViewers(string $objectiveName): ?array
    {
        return $this->viewers[$objectiveName] ?? null;
    }

    public function removePotentialViewer(string $playerName)
    {
        foreach ($this->viewers as $name => $data) {
            if (in_array($playerName, $data)) {
                if (($key = array_search($playerName, $data)) !== false) {
                    unset($this->viewers[$name][$key]);
                }
            }
        }
    }

    public function getScoreboardName(string $displayName): ?string
    {
        return $this->scoreboards[$displayName] ?? null;
    }
}