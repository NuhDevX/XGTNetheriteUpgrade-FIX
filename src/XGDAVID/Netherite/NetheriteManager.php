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
#   (?) user: xgdav | 10/07/2024 7:07 PM
#

namespace XGDAVID\Netherite;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use XGDAVID\Main;
use XGDAVID\Netherite\events\ItemUpgradeEvent;
use XGDAVID\Netherite\events\NetheriteEvents;

class NetheriteManager
{

    public Main $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
        $main->getServer()->getPluginManager()->registerEvents(new NetheriteEvents($this), $main);
    }

    public function isDiamondItem(Item $item): bool
    {
        return $item->getTypeId() === VanillaItems::DIAMOND_SWORD()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_PICKAXE()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_AXE()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_SHOVEL()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_HOE()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_HELMET()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_CHESTPLATE()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_LEGGINGS()->getTypeId() ||
            $item->getTypeId() === VanillaItems::DIAMOND_BOOTS()->getTypeId();
    }

    public function handleUpgrade(Player $player, Item $diamondItem, int $diamondSlot, Inventory $diamondInventory, Item $netheriteIngot, int $netheriteSlot, Inventory $netheriteInventory): void
    {

        if (Main::$config->get("xp")) {
            if ($player->getXpManager()->getXpLevel() < Main::$config->get("xp-need")) {
                $player->sendMessage("§l§c>§r You dont have enough xp level to upgrade your §6" . $diamondItem->getVanillaName());
                return;
            }
            $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - Main::$config->get("xp-take"));
        }

        $netheriteItem = $this->getNetheriteItem($diamondItem->getTypeId());

        $event = new ItemUpgradeEvent($player, $diamondItem, $netheriteItem);
        $event->call();

        $netheriteIngot->pop();

        $netheriteItem->setCustomName("§b" . $netheriteItem->getVanillaName());
        $netheriteItem->setLore($diamondItem->getLore());

        foreach ($diamondItem->getEnchantments() as $enchantment) {
            $netheriteItem->addEnchantment($enchantment);
        }
        if ($diamondItem->keepOnDeath()) {
            $netheriteItem->setKeepOnDeath(true);
        }

        $diamondInventory->setItem($diamondSlot, VanillaItems::AIR());
        $netheriteInventory->setItem($netheriteSlot, $netheriteIngot->isNull() ? VanillaItems::AIR() : $netheriteIngot);


        $player->getInventory()->addItem($netheriteItem);

        $player->sendMessage("§l§a>§r Your §6" . $diamondItem->getVanillaName() . "§r has been upgraded to §6" . $netheriteItem->getVanillaName());
    }

    public function getNetheriteItem(int $diamondId): Item
    {
        return match ($diamondId) {
            VanillaItems::DIAMOND_SWORD()->getTypeId() => VanillaItems::NETHERITE_SWORD(),
            VanillaItems::DIAMOND_PICKAXE()->getTypeId() => VanillaItems::NETHERITE_PICKAXE(),
            VanillaItems::DIAMOND_AXE()->getTypeId() => VanillaItems::NETHERITE_AXE(),
            VanillaItems::DIAMOND_SHOVEL()->getTypeId() => VanillaItems::NETHERITE_SHOVEL(),
            VanillaItems::DIAMOND_HOE()->getTypeId() => VanillaItems::NETHERITE_HOE(),
            VanillaItems::DIAMOND_HELMET()->getTypeId() => VanillaItems::NETHERITE_HELMET(),
            VanillaItems::DIAMOND_CHESTPLATE()->getTypeId() => VanillaItems::NETHERITE_CHESTPLATE(),
            VanillaItems::DIAMOND_LEGGINGS()->getTypeId() => VanillaItems::NETHERITE_LEGGINGS(),
            VanillaItems::DIAMOND_BOOTS()->getTypeId() => VanillaItems::NETHERITE_BOOTS(),
            default => throw new \InvalidArgumentException("Invalid diamond item ID: $diamondId"),
        };
    }
}
