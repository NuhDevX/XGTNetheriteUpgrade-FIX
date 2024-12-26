<?php
#
# __   _______ _______ _____            __      _____
# \ \ / / ____|__   __/ ____|           \ \    / /__ \
#  \ V / |  __   | | | |     ___  _ __ __\ \  / /   ) |
#   > <| | |_ |  | | | |    / _ \| '__/ _ \ \/ /   / /
#  / . \ |__| |  | | | |___| (_) | | |  __/\  /   / /_
# /_/ \_\_____|  |_|  \_____\___/|_|  \___| \/   |____|
#
#   @author XGDAVID
#   Copyright (c) XGTeam & GCStaff - 2024
#   !file XGDAVID
#   (?) user: xgdav | 10/07/2024 7:17 PM
#

namespace XGDAVID\Netherite\events;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\VanillaItems;
use XGDAVID\Netherite\NetheriteManager;

class NetheriteEvents implements Listener
{
   private NetheriteManager $manager;

    public function __construct(NetheriteManager $manager)
    {
        $this->manager = $manager;
    }

    public function onInventoryAction(InventoryTransactionEvent $event): void
    {
        $actions = $event->getTransaction()->getActions();
        $player = $event->getTransaction()->getSource();

        if (count($actions) != 2) {
            return;
        }

        $netheriteIngot = null;
        $netheriteSlot = null;
        $netheriteInventory = null;

        $diamondItem = null;
        $diamondSlot = null;
        $diamondInventory = null;

        foreach ($actions as $action) {
            if ($action instanceof SlotChangeAction) {
                $eventItem = $action->getTargetItem();
                if ($eventItem->isNull()) {
                    $eventItem = $action->getSourceItem();
                }

                if ($eventItem->equals(VanillaItems::NETHERITE_INGOT())) {
                    $netheriteIngot = $eventItem;
                    $netheriteSlot = $action->getSlot();
                    $netheriteInventory = $action->getInventory();
                } elseif ($this->manager->isDiamondItem($eventItem)) {
                    $diamondItem = $eventItem;
                    $diamondSlot = $action->getSlot();
                    $diamondInventory = $action->getInventory();
                }
            }
        }

        if ($netheriteIngot !== null && $diamondItem !== null) {

            $this->manager->handleUpgrade($player, $diamondItem, $diamondSlot, $diamondInventory, $netheriteIngot, $netheriteSlot, $netheriteInventory);
            $event->cancel();
        }
    }
}
