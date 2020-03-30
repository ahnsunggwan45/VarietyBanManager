<?php

namespace ojy\vbm\command;

use ojy\vbm\VarietyBanManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class CraftBanCommand extends Command
{

    /**
     * CraftBanCommand constructor.
     */
    public function __construct()
    {
        parent::__construct("조합밴", "아이템을 조합하지 못하게합니다.", "/조합밴", []);
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
                    if ($sender instanceof Player) {
                        $hand = $sender->getInventory()->getItemInHand();
                        if ($hand->getId() !== 0) {
                            if (VarietyBanManager::getInstance()->getCraftBanManager()->addCraftBanByItem($hand)) {
                                $sender->sendMessage(VarietyBanManager::PREFIX . "\"" . VarietyBanManager::hash($hand) . "\" 아이템을 조합밴 목록에 추가했습니다.");
                            } else {
                                $sender->sendMessage(VarietyBanManager::PREFIX . "이미 조합밴되어있는 아이템입니다.");
                            }
                        } else {
                            $sender->sendMessage(VarietyBanManager::PREFIX . "공기를 추가할 수는 없습니다.");
                        }
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "인게임에서 실행해주세요.");
                    }
                    break;

                case "목록":
                    $all = VarietyBanManager::getInstance()->getCraftBanManager()->getAll();
                    if (count($all) > 0) {
                        $list = implode(", ", $all);
                        $sender->sendMessage(VarietyBanManager::PREFIX . "조합밴 된 아이템목록을 출력합니다.");
                        $sender->sendMessage("§7: {$list}");
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "조합밴된 아이템이 없습니다.");
                    }
                    break;

                case "제거":
                    if ($sender instanceof Player) {
                        $hand = $sender->getInventory()->getItemInHand();
                        if ($hand->getId() !== 0) {
                            if (VarietyBanManager::getInstance()->getCraftBanManager()->isBan($hand)) {
                                VarietyBanManager::getInstance()->getCraftBanManager()->removeCraftBanByItem($hand);
                                $sender->sendMessage(VarietyBanManager::PREFIX . "\"" . VarietyBanManager::hash($hand) . "\" 아이템을 조합밴 목록에서 제거했습니다.");
                            } else {
                                $sender->sendMessage(VarietyBanManager::PREFIX . "조합밴 목록에 추가되어있지 않은 아이템입니다.");
                            }
                        }
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "인게임에서 실행해주세요.");
                    }
                    break;
                default:
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/조합밴 추가 | 손에 든 아이템으로 추가");
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/조합밴 목록");
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/조합밴 제거 | 손에 든 아이템으로 제거");
                    break;
            }
        }
    }
}