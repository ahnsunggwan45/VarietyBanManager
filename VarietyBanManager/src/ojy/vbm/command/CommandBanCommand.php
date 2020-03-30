<?php

namespace ojy\vbm\command;

use ojy\vbm\VarietyBanManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;

class CommandBanCommand extends Command
{

    /**
     * CommandBanCommand constructor.
     */
    public function __construct()
    {
        parent::__construct("명령어밴", "명령어를 사용하지 못하게합니다.", "/명령어밴", []);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission($this->getPermission())) {
            if (!isset($args[0]))
                $args[0] = 'x';
            switch ($args[0]) {
                case "추가":
                    if (isset($args[1])) {
                        unset($args[0]);
                        $commandLine = implode(" ", $args);
                        if (VarietyBanManager::getInstance()->getCommandBanManager()->addCommandBan($commandLine)) {
                            $sender->sendMessage(VarietyBanManager::PREFIX . "\"/{$commandLine}\" 를 금지했습니다..");
                        } else {
                            $sender->sendMessage(VarietyBanManager::PREFIX . "이미 금지되어있는 명령어입니다.");
                        }
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "/명령어밴 추가 [명령어]");
                    }
                    break;

                case "목록":
                    $all = VarietyBanManager::getInstance()->getCommandBanManager()->getAll();
                    if (count($all) > 0) {
                        $list = implode(", ", $all);
                        $sender->sendMessage(VarietyBanManager::PREFIX . "명령어밴 목록을 출력합니다.");
                        $sender->sendMessage("§7: {$list}");
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "금지된 명령어가 없습니다.");
                    }
                    break;

                case "제거":
                    if (isset($args[1])) {
                        unset($args[0]);
                        $commandLine = implode(" ", $args);
                        if (VarietyBanManager::getInstance()->getCommandBanManager()->isBan($commandLine)) {
                            VarietyBanManager::getInstance()->getCommandBanManager()->removeCommandBan($commandLine);
                            $sender->sendMessage(VarietyBanManager::PREFIX . "\"/{$commandLine}\" 의 밴을 해제했습니다.");
                        } else {
                            $sender->sendMessage(VarietyBanManager::PREFIX . "금지되지 않은 명령어입니다.");
                        }

                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "/명령어밴 제거 [명령어]");
                    }
                    break;
                default:
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/명령어밴 추가 [명령어]");
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/명령어밴 목록");
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/명령어밴 제거 [명령어]");
                    break;
            }
        }
    }
}