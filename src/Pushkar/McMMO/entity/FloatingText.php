<?php

namespace Pushkar\McMMO\entity;

use Pushkar\McMMO\Main;
use pocketmine\entity\Human;
use pocketmine\player\Player;
use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class FloatingText extends Human
{
    public int $updateTick = 0;
    public int $type = 0;

    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt); // TODO: Change the autogenerated stub
        $this->setScale(0.0001);
        $this->updateTick = 0;

        $tag = $nbt->getTag("NameTag");
        if ($tag instanceof IntTag) {
            $this->type = $tag->getValue();
        }
    }

    public function updateMovement(bool $teleport = false): void
    {
    }

    public function onUpdate(int $currentTick): bool
    {
        parent::onUpdate($currentTick);
        $this->updateTick++;
        if ($this->updateTick == 20) {
            $this->updateTick = 0;
            $a = ["Lumberjack", "Farmer", "Excavation", "Miner", "Killer", "Combat", "Builder", "Consumer", "Archer", "Lawn Mower"];
            $l = "";
            $i = 0;
            $lead = Main::getInstance()->getAll($this->type);
            arsort($lead);
            foreach ($lead as $k => $o) {
                if ($i == 20) break;
                $i++;
                $l .= TextFormat::YELLOW . $i . ". " . TextFormat::WHITE . $k . TextFormat::GRAY . " : " . TextFormat::GREEN . "Lv. " . $o . "\n";
            }
            $this->setNameTag(TextFormat::BOLD . TextFormat::GOLD . "Leaderboard\n" . TextFormat::RESET . TextFormat::YELLOW . "MagicGames\n" . TextFormat::RESET . TextFormat::GREEN . $a[$this->type] . TextFormat::RESET . "\n\n" . $l);
            foreach ($this->getViewers() as $player) {
                $this->sendNameTag($player);
            }
        }
        return true;
    }

    public function sendNameTag(Player $player): void
    {
        $pk = new SetActorDataPacket();
        $pk->actorRuntimeId = $this->getId();
        $pk->metadata = [EntityMetadataProperties::NAMETAG => new StringMetadataProperty($this->getNameTag())];
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
