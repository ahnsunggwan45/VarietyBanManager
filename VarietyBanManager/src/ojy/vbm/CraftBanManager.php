<?php

namespace ojy\vbm;

use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\Config;

class CraftBanManager implements Listener
{

    /** @var Config */
    public static $data;
    /** @var array */
    public static $db;

    /**
     * CraftBanManager constructor.
     */
    public function __construct()
    {
        self::$data = new Config(VarietyBanManager::getInstance()->getDataFolder() . "CraftBan.yml", Config::YAML, ["ban" => []]);
        self::$db = self::$data->getAll();
        Server::getInstance()->getPluginManager()->registerEvents($this, VarietyBanManager::getInstance());
    }

    /**
     * @param CraftItemEvent $event
     */
    public function onCraftItem(CraftItemEvent $event)
    {
        foreach ($event->getOutputs() as $item) {
            if ($this->isBan($item)) {
                $event->setCancelled();
                $event->getPlayer()->sendMessage("§l§b[알림] §r§7조합이 불가능한 아이템입니다.");
            }
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isBan(Item $item)
    {
        return isset(self::$db["ban"][VarietyBanManager::hash($item)]);
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function addCraftBanByItem(Item $item): bool
    {
        return $this->addCraftBanById(VarietyBanManager::hash($item));
    }

    /**
     * @return array
     */
    public function getAll():array{
        return array_keys(self::$db["ban"]);
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function addCraftBanById(string $hash): bool
    {
        if (!isset(self::$db["ban"][$hash])) {
            self::$db["ban"][$hash] = true;
            return true;
        }
        return false;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function removeCraftBanByItem(Item $item): bool
    {
        return $this->removeCraftBanById(VarietyBanManager::hash($item));
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function removeCraftBanById(string $hash): bool
    {
        if (isset(self::$db["ban"][$hash])) {
            unset(self::$db["ban"][$hash]);
            return true;
        }
        return false;
    }

    /**
     * ANG
     */
    public function save()
    {
        self::$data->setAll(self::$db);
        self::$data->save();
    }
}