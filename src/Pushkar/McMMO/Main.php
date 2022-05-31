<?php

//███╗░░░███╗░█████╗░░██████╗░██╗░█████╗░░██████╗░░█████╗░███╗░░░███╗███████╗░██████╗
//████╗░████║██╔══██╗██╔════╝░██║██╔══██╗██╔════╝░██╔══██╗████╗░████║██╔════╝██╔════╝
//██╔████╔██║███████║██║░░██╗░██║██║░░╚═╝██║░░██╗░███████║██╔████╔██║█████╗░░╚█████╗░
//██║╚██╔╝██║██╔══██║██║░░╚██╗██║██║░░██╗██║░░╚██╗██╔══██║██║╚██╔╝██║██╔══╝░░░╚═══██╗
//██║░╚═╝░██║██║░░██║╚██████╔╝██║╚█████╔╝╚██████╔╝██║░░██║██║░╚═╝░██║███████╗██████╔╝
//╚═╝░░░░░╚═╝╚═╝░░╚═╝░╚═════╝░╚═╝░╚════╝░░╚═════╝░╚═╝░░╚═╝╚═╝░░░░░╚═╝╚══════╝╚═════╝░

namespace Pushkar\McMMO;

use pocketmine\block\Opaque;
use pocketmine\player\Player;
use Stats\player\MagicPlayer;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use _64FF00\PurePerms\PurePerms;
use pocketmine\plugin\PluginBase;
use Pushkar\McMMO\form\McmmoForm;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\CommandSender;
use Pushkar\McMMO\entity\FloatingText;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\block\BlockLegacyIds as BlockIds;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class Main extends PluginBase implements Listener
{
    public const LUMBERJACK = 0;
    public const FARMER = 1;
    public const MINER = 3;
    public const EXCAVATION = 2;
    public const COMBAT = 5;
    public const KILLER = 4;
    public const BUILDER = 6;
    public const CONSUMER = 7;
    public const ARCHER = 8;
    public const LAWN_MOWER = 9;

    public array $database;

    public static Main $instance;
    public EconomyAPI $eco;

    public function onEnable(): void
    {
        $this->saveResource("database.yml");
        $contents = file_get_contents($this->getDataFolder() . "database.yml");
        if (!$contents) {
            $this->getLogger()->error("Could not load database.yml");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        $this->database = yaml_parse($contents);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        //EntityFactory::register(FloatingText::class, true);
        self::$instance = $this;

        $this->eco = EconomyAPI::getInstance();
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args): bool
    {
        switch ($command->getName()) {
            case "skills":
                if ($sender instanceof Player) {
                    (new McmmoForm($this))->init($sender);
                } else {
                    $sender->sendMessage("Use this command in-game");
                    return true;
                }
                break;
                /*case "mcmmoadmin":
                if(!$sender instanceof Player) {
                    $sender->sendMessage("Please use command in-game");
                    return true;
                }
                if(!$sender->isOp()) {
                    $sender->sendMessage("You not op on this server");
                    return true;
                }
                $a = ["lumberjack", "farmer", "excavation", "miner", "killer", "combat", "builder", "consumer", "archer", "lawnmower"];
                if(count($args) === 0) {
                    $sender->sendMessage("Usage: /mcmmoadmin setup ".implode("/" , $a)."> (to spawn floating text) | /mcmmoadmin remove (to remove nearly floating text)");
                    return true;
                }
                if($args[0] === "remove") {
                    $maxDistance = 3;
                    $g = 0;
                    foreach($sender->getLevel()->getNearbyEntities($sender->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance)) as $entity){
                        if($entity instanceof FloatingText) {
                            $g++;
                            $entity->close();
                        }
                    }
                    $sender->sendMessage("Removed ".$g." floating text");
                    return true;
                }
                if($args[0] === "setup") {
                    if(!isset($args[1])) {
                        $sender->sendMessage("Usage: /mcmmoadmin setup ".implode("/" , $a)."> (to spawn floating text)");
                        return true;
                    }
                    if(!in_array($args[1], $a)) {
                        $sender->sendMessage("Usage: /mcmmoadmin setup ".implode("/" , $a)."> (to spawn floating text)");
                        return true;
                    }
                    $nbt = Entity::createBaseNBT($sender->asVector3(), null, $sender->yaw, $sender->pitch);
                    $sender->saveNBT();
                    $nbt->setTag(clone $sender->namedtag->getCompoundTag("Skin"));
                    $a = ["lumberjack" => 0, "farmer" => 1, "excavation" => 2, "miner" => 3, "killer" => 4, "combat" => 5, "builder" => 6, "consumer" => 7, "archer" => 8, "lawnmower" => 9];
                    $nbt->setInt("type", $a[$args[1]]);
                    $entity = new FloatingText($sender->level, $nbt);
                    $entity->spawnToAll();
                }
            break;*/
        }
        return true;
    }

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function onDisable(): void
    {
        file_put_contents($this->getDataFolder() . "database.yml", yaml_emit($this->database));
        // this is not a delay. sleep runs on the main thread and will freeze the server; not delay it.
        //sleep(3); // save database delay
    }

    public function getXp(int $type, Player $player): int
    {
        return $this->database["xp"][$type][strtolower($player->getName())];
    }

    public function getLevel(int $type, Player $player): int
    {
        return $this->database["level"][$type][strtolower($player->getName())];
    }

    public function addXp(int $type, Player $player): void
    {
        $this->database["xp"][$type][strtolower($player->getName())]++;
        if ($this->database["xp"][$type][strtolower($player->getName())] >= ($this->getLevel($type, $player) * 100)) {
            $this->database["xp"][$type][strtolower($player->getName())] = 0;
            $this->addLevel($type, $player);
        }
        $a = ["Lumberjack ", "Farmer ", "Excavation ", "Miner ", "Killer ", "Combat ", "Builder ", "Consumer ", "Archer ", "Lawn Mower "];
        $player->sendTip("§b+1 §d" . $a[$type] . " §7(§a" . $this->getXp($type, $player) . "§7)");
    }

    public function addLevel(int $type, Player $player): void
    {
        $this->database["level"][$type][strtolower($player->getName())]++;
        $a = ["Lumberjack", "Farmer", "Excavation", "Miner", "Killer", "Combat", "Builder", "Consumer", "Archer", "Lawn Mower"];
        $health = mt_rand(1, 8);
        $defense = mt_rand(1, 8);
        $player->sendMessage("§3§l========================\n §l§bSKILL LEVEL UP§r§3 " . $a[$type] . "\n\n §l§aREWARDS§r\n   §e" . $a[$type] . " " . $this->getLevel($type, $player) . "\n   §8+§6" . $this->getLevel($type, $player) * 1000 . " §7Coins\n   §8+§c  $health Health\n   §8+§a  $defense Defense\n   §8+§4  1 Damage\n§3§l========================");

        /** @var PurePerms $purePerms */
        $purePerms = $this->getServer()->getPluginManager()->getPlugin('PurePerms');

        // avoiding switches in switches
        switch ($type) {
            case self::FARMER:
                if ($this->getLevel(self::FARMER, $player) == 2) {
                    $purePerms->getUserDataMgr()->setPermission($player, 'portalspe.farm');
                    $player->sendMessage("§r§l§eBONUS\n §r§b Farming Island Portal Is Now Unlocked\n§3§l========================");
                    break;
                }
                if ($this->getLevel(self::FARMER, $player) == 3) {
                    $purePerms->getUserDataMgr()->setPermission($player, 'portalspe.mushroom');
                    $player->sendMessage("§r§l§eBONUS\n §r§b Mushroom Island Portal Is Now Unlocked\n§3§l========================");
                }
                break;
            case self::LUMBERJACK:
                if ($this->getLevel(self::LUMBERJACK, $player) == 2) {
                    $purePerms->getUserDataMgr()->setPermission($player, 'portalspe.forest');
                    $player->sendMessage("§r§l§eBONUS\n §r§b Forest Island Portal Is Now Unlocked\n§3§l========================");
                }
                break;
            case self::MINER:
                if ($this->getLevel(self::MINER, $player) == 2) {
                    $purePerms->getUserDataMgr()->setPermission($player, 'lapis.teleport');
                    $player->sendMessage("§r§l§eBONUS\n §r§b Lapis And Redstone Lift Is Now Unlocked\n§3§l========================");
                    break;
                }
                if ($this->getLevel(self::MINER, $player) == 3) {
                    $purePerms->getUserDataMgr()->setPermission($player, 'diamond.teleport');
                    $player->sendMessage("§r§l§eBONUS\n §r§b Diamond And Emerald Lift Is Now Unlocked\n§3§l========================");
                    break;
                }
                if ($this->getLevel(self::MINER, $player) == 4) {
                    $purePerms->getUserDataMgr()->setPermission($player, 'obsidian.teleport');
                    $player->sendMessage("§r§l§eBONUS\n §r§b Sanctuary Lift Is Now Unlocked\n§3§l========================");
                }
                break;
        }

        $player->sendTitle("§6Level Up ", "§e$a[$type]",);
        $cost = ($this->getLevel($type, $player) * 1000);
        $this->eco->addMoney($player, $cost);
        if ($player instanceof MagicPlayer) {
            $maxheal = $player->getMaxHealth();
            $adefense = $player->getDefense();
            $adamage = $player->getDamage();
            $x = ($maxheal + $health);
            $y = ($adefense + $defense);
            $z = ($adamage + 1);

            $player->setMaxHealth($x);
            $player->setStats("Defense", $y);
            $player->setStats("Damage", $z);
        }
    }

    public function getAll(int $type): array
    {
        return $this->database["level"][$type];
    }

    public function onLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        if (!isset($this->database["xp"][0][strtolower($player->getName())])) {
            for ($i = 0; $i < 10; $i++) {
                $this->database["xp"][$i][strtolower($player->getName())] = 0;
                $this->database["level"][$i][strtolower($player->getName())] = 1;
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onBreak(BlockBreakEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }
        
        $player = $event->getPlayer();
        $block = $event->getBlock();
        switch ($block->getId()) {
            case BlockIds::WHEAT_BLOCK:
            case BlockIds::BEETROOT_BLOCK:
            case BlockIds::PUMPKIN_STEM:
            case BlockIds::PUMPKIN:
            case BlockIds::MELON_STEM:
            case BlockIds::MELON_BLOCK:
            case BlockIds::CARROT_BLOCK:
            case BlockIds::POTATO_BLOCK:
            case BlockIds::SUGARCANE_BLOCK:
                $this->addXp(self::FARMER, $player);
                break;
            case BlockIds::STONE:
            case BlockIds::DIAMOND_ORE:
            case BlockIds::GOLD_ORE;
            case BlockIds::REDSTONE_ORE:
            case BlockIds::IRON_ORE:
            case BlockIds::COAL_ORE:
            case BlockIds::EMERALD_ORE:
            case BlockIds::OBSIDIAN:
                $this->addXp(self::MINER, $player);
                break;
            case BlockIds::LOG:
            case BlockIds::LOG2:
            case BlockIds::LEAVES:
            case BlockIds::LEAVES2:
                $this->addXp(self::LUMBERJACK, $player);
                break;
            case BlockIds::DIRT:
            case BlockIds::GRASS:
            case BlockIds::GRASS_PATH:
            case BlockIds::FARMLAND:
            case BlockIds::SAND:
            case BlockIds::GRAVEL:
                $this->addXp(self::EXCAVATION, $player);
                break;
            case BlockIds::TALL_GRASS:
            case BlockIds::YELLOW_FLOWER:
            case BlockIds::RED_FLOWER:
            case BlockIds::CHORUS_FLOWER:
                $this->addXp(self::LAWN_MOWER, $player);
                break;
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlace(BlockPlaceEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (!$block instanceof Opaque) {
            $this->addXp(self::BUILDER, $player);
            return;
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onDamage(EntityDamageEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }
        
        if ($event->getEntity() instanceof FloatingText) {
            $event->cancel();
            return;
        }
        if ($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            if (!$entity instanceof Player) return;
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                if (($entity->getHealth() - $event->getFinalDamage()) <= 0) {
                    $this->addXp(self::KILLER, $damager);
                }
                $this->addXp(self::COMBAT, $damager);
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onShootBow(EntityShootBowEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $this->addXp(self::ARCHER, $entity);
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onItemConsume(PlayerItemConsumeEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        if ($event->getPlayer()->getHungerManager()->getFood() < $event->getPlayer()->getHungerManager()->getMaxFood()) {
            $this->addXp(self::CONSUMER, $event->getPlayer());
        }
    }
}
