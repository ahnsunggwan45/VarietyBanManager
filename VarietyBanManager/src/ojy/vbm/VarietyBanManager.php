<?php

namespace ojy\vbm;

use ojy\vbm\command\CommandBanCommand;
use ojy\vbm\command\CraftBanCommand;
use ojy\vbm\command\ItemBanCommand;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class VarietyBanManager extends PluginBase
{

    public const PREFIX = '§l§b[알림] §r§7';
    /** @var VarietyBanManager */
    private static $instance;

    /** @var CraftBanManager */
    private $craftBanManager;
    /** @var CommandBanManager */
    private $commandBanManager;
    /** @var ItemBanManager */
    private $itemBanManager;

    /**
     *
     */
    public function onLoad()
    {
        self::$instance = $this;
    }

    /**
     * @return VarietyBanManager
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    /**
     *
     */
    public function onEnable()
    {
        $this->craftBanManager = new CraftBanManager();
        $this->commandBanManager = new CommandBanManager();
        $this->itemBanManager = new ItemBanManager();

        foreach ([CommandBanCommand::class, CraftBanCommand::class, ItemBanCommand::class] as $c) {
            Server::getInstance()->getCommandMap()->register("VarietyBanManager", new $c());
        }
    }

    /**
     * @param Item $item
     * @return string
     */
    public static function hash(Item $item)
    {
        return $item->getId() . ":" . $item->getDamage();
    }

    /**
     *
     */
    public function onDisable()
    {
        $this->craftBanManager->save();
        $this->commandBanManager->save();
        $this->itemBanManager->save();
    }

    /**
     * @return CraftBanManager
     */
    public function getCraftBanManager(): CraftBanManager
    {
        return $this->craftBanManager;
    }

    /**
     * @return CommandBanManager
     */
    public function getCommandBanManager(): CommandBanManager
    {
        return $this->commandBanManager;
    }

    /**
     * @return ItemBanManager
     */
    public function getItemBanManager(): ItemBanManager
    {
        return $this->itemBanManager;
    }
}