<?php

namespace ojy\vbm\command;

use ojy\vbm\VarietyBanManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class ItemBanCommand extends Command
{

    /**
     * ItemBanCommand constructor.
     */
    public function __construct()
    {
        parent::__construct("아이템밴", "아이템을 사용하지 못하게합니다.", "/아이템밴", []);
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
                            if (VarietyBanManager::getInstance()->getItemBanManager()->addItemBanByItem($hand)) {
                                $sender->sendMessage(VarietyBanManager::PREFIX . "\"" . VarietyBanManager::hash($hand) . "\" 아이템의 사용을 금지시켰습니다.");
                            } else {
                                $sender->sendMessage(VarietyBanManager::PREFIX . "이미 금지되어있는 아이템입니다.");
                            }
                        } else {
                            $sender->sendMessage(VarietyBanManager::PREFIX . "공기를 추가할 수는 없습니다.");
                        }
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "인게임에서 실행해주세요.");
                    }
                    break;

                case "목록":
                    $all = VarietyBanManager::getInstance()->getItemBanManager()->getAll();
                    if (count($all) > 0) {
                        $list = implode(", ", $all);
                        $sender->sendMessage(VarietyBanManager::PREFIX . "아이템밴 목록을 출력합니다.");
                        $sender->sendMessage("§7: {$list}");
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "사용 금지된 아이템이 없습니다.");
                    }
                    break;

                case "제거":
                    if ($sender instanceof Player) {
                        $hand = $sender->getInventory()->getItemInHand();
                        if ($hand->getId() !== 0) {
                            if (VarietyBanManager::getInstance()->getItemBanManager()->isBan($hand)) {
                                VarietyBanManager::getInstance()->getItemBanManager()->removeItemBanByItem($hand);
                                $sender->sendMessage(VarietyBanManager::PREFIX . "\"" . VarietyBanManager::hash($hand) . "\" 아이템의 밴을 해제했습니다.");
                            } else {
                                $sender->sendMessage(VarietyBanManager::PREFIX . "금지되어있지 않은 아이템입니다.");
                            }
                        }
                    } else {
                        $sender->sendMessage(VarietyBanManager::PREFIX . "인게임에서 실행해주세요.");
                    }
                    break;
                default:
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/아이템밴 추가 | 손에 든 아이템으로 추가");
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/아이템밴 목록");
                    $sender->sendMessage(VarietyBanManager::PREFIX . "/아이템밴 제거 | 손에 든 아이템으로 제거");
                    break;
            }
        }
    }
}