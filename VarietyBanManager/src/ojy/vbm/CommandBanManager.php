<?php

namespace ojy\vbm;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\Server;
use pocketmine\utils\Config;

class CommandBanManager implements Listener
{

    /** @var Config */
    public static $data;

    /** @var array */
    public static $db;

    /** @var array */
    public static $banned = [];

    public function __construct()
    {
        self::$data = new Config(VarietyBanManager::getInstance()->getDataFolder() . "CommandBan.yml", Config::YAML, ["ban" => []]);
        self::$db = self::$data->getAll();
        Server::getInstance()->getPluginManager()->registerEvents($this, VarietyBanManager::getInstance());
    }

    /**
     * @handleCancelled true
     * @param PlayerCommandPreprocessEvent $event
     */
    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event)
    {
        if ($this->isBan(($msg = mb_substr($event->getMessage(), 1, null, "UTF-8"))) && !$event->getPlayer()->isOp()) {
            $event->getPlayer()->sendMessage("§l§b[알림] §r§7해당 명령어는 사용이 금지되었습니다.");
            $event->setCancelled();
        }
    }

    /**
     * @param string $msg
     * @return bool
     */
    public function isBan(string $msg): bool
    {
        $data = explode(" ", $msg);
        foreach (self::$db["ban"] as $commandBan => $bool) {
            if ($commandBan === $msg) {
                self::$banned[$msg] = true;
                return true;
            }
            $commandBanData = explode(" ", $commandBan);
            if (count($commandBanData) === 1) {
                if ($commandBanData[0] === $data[0]) {
                    return true;
                }
            }
            if ($commandBanData[0] === $data[0]) {
                if (count($data) >= count($commandBanData)) {
                    for ($i = 0; $i < count($commandBanData); $i++) {
                        if ($data[$i] !== $commandBanData[$i])
                            return false;
                    }
                    self::$banned[$msg] = true;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return array_keys(self::$db["ban"]);
    }

    /**
     * @param string $commandLine
     * @return bool
     */
    public function addCommandBan(string $commandLine): bool
    {
        if (!isset(self::$db["ban"][$commandLine])) {
            self::$db["ban"][$commandLine] = true;
            return true;
        }
        return false;
    }

    /**
     * @param string $commandLine
     * @return bool
     */
    public function removeCommandBan(string $commandLine): bool
    {
        if (isset(self::$db["ban"][$commandLine])) {
            unset(self::$db["ban"][$commandLine]);
            return true;
        }
        return false;
    }

    public function save()
    {
        self::$data->setAll(self::$db);
        self::$data->save();
    }
}