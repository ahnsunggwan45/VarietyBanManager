<?php

namespace ojy\vbm;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\Config;

class ItemBanManager implements Listener
{

    /** @var Config */
    public static $data;
    /** @var array */
    public static $db;

    /**
     * ItemBanManager constructor.
     */
    public function __construct()
    {
        self::$data = new Config(VarietyBanManager::getInstance()->getDataFolder() . "ItemBan.yml", Config::YAML, ["ban" => []]);
        self::$db = self::$data->getAll();
        Server::getInstance()->getPluginManager()->registerEvents($this, VarietyBanManager::getInstance());
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $hand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($this->isBan($hand)) {
            $event->getPlayer()->sendPopup("§l§b[알림] §r§7이 아이템은 사용할 수 없습니다.");
            $event->setCancelled();
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event)
    {
        $hand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($this->isBan($hand)) {
            $event->getPlayer()->sendPopup("§l§b[알림] §r§7이 아이템은 사용할 수 없습니다.");
            $event->setCancelled();
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event)
    {
        $hand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($this->isBan($hand)) {
            $event->getPlayer()->sendPopup("§l§b[알림] §r§7이 아이템은 사용할 수 없습니다.");
            $event->setCancelled();
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
     * @return array
     */
    public function getAll(): array
    {
        return array_keys(self::$db["ban"]);
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function addItemBanByItem(Item $item): bool
    {
        return $this->addItemBanById(VarietyBanManager::hash($item));
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function addItemBanById(string $hash): bool
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
    public function removeItemBanByItem(Item $item): bool
    {
        return $this->removeItemBanById(VarietyBanManager::hash($item));
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function removeItemBanById(string $hash): bool
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