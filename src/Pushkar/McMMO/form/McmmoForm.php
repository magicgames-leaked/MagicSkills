<?php

namespace Pushkar\McMMO\form;

use Pushkar\McMMO\Main;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;

class McmmoForm
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function init(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    $this->stats($player);
                    return;
                case 1:
                    $this->leaderboard($player);
                    return;
                case 2:
                    return;
            }
        });
        $form->setTitle("§l§4MagicGames Skills");
        $form->addButton("§d§lYour Skills\n§l§9»» §r§oTap to check", 0, "textures/ui/copy");
        $form->addButton("§d§lSkills Leaderboard\n§l§9»» §r§oTap to check", 0, "textures/items/chalkboard_large");
        $form->addButton("§cEXIT\n§r§8»» §r§oTap to exit", 0, "textures/ui/cancel");
        $player->sendForm($form);
    }

    public function stats(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data !== null) {
                $this->init($player);
            }
        });
        $form->setTitle("§e§lYour Skills");
        $content = [
            "§6Lumberjack: ",
            "   §eXP: " . $this->plugin->getXp(Main::LUMBERJACK, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::LUMBERJACK, $player),
            "§6Farmer: ",
            "   §eXP: " . $this->plugin->getXp(Main::FARMER, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::FARMER, $player),
            "§6Excavation: ",
            "   §eXP: " . $this->plugin->getXp(Main::EXCAVATION, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::EXCAVATION, $player),
            "§6Miner: ",
            "   §eXP: " . $this->plugin->getXp(Main::MINER, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::MINER, $player),
            "§6Killer: ",
            "   §eXP: " . $this->plugin->getXp(Main::KILLER, $player),
            "   §eLevel: " . $this->plugin->getLevel(Main::KILLER, $player),
            "§6Combat: ",
            "   §eXP: " . $this->plugin->getXp(Main::COMBAT, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::COMBAT, $player),
            "§6Builder: ",
            "   §eXP: " . $this->plugin->getXp(Main::BUILDER, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::BUILDER, $player),
            "§6Consumer: ",
            "   §eXP: " . $this->plugin->getXp(Main::CONSUMER, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::CONSUMER, $player),
            "§6Archer: ",
            "   §eXP: " . $this->plugin->getXp(Main::ARCHER, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::ARCHER, $player),
            "§6Lawn Mower: ",
            "   §eXP: " . $this->plugin->getXp(Main::LAWN_MOWER, $player),
            "   §aLevel: " . $this->plugin->getLevel(Main::LAWN_MOWER, $player)
        ];
        $form->setContent(implode("\n", $content));
        $form->addButton("§6§lBACK\n§r§8Tap to go back", 0, "textures/ui/icon_import");
        $player->sendForm($form);
    }

    public function leaderboard(Player $player): void
    {
        $a = ["Lumberjack", "Farmer", "Excavation", "Miner", "Killer", "Combat", "Builder", "Consumer", "Archer", "Lawn Mower"];
        $form = new SimpleForm(function (Player $player, $data) use ($a) {
            if ($data === null) {
                return;
            }
            if ($data === count($a)) {
                $this->init($player);
                return;
            }
            $this->leaderboards($player, $data);
        });
        $form->setTitle("§6§lSkills Leaderboard");
        $form->setContent("");
        foreach ($a as $as) {
            $form->addButton("$as", 0, "textures/ui/creative_icon");
        }
        $form->addButton("§6§lBACK\n§r§8Tap to go back", 0, "textures/ui/icon_import");
        $player->sendForm($form);
    }

    public function leaderboards(Player $player, int $type): void
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data !== null) {
                $this->leaderboard($player);
            }
        });
        $a = ["Lumberjack", "Farmer", "Excavation", "Miner", "Killer", "Combat", "Builder", "Consumer", "Archer", "Lawn Mower"];
        $form->setTitle("§6§lLeaderboard §8§l- §r§a" . $a[$type]);
        $content = "";
        $a = $this->plugin->getAll($type);
        arsort($a);
        $i = 1;
        foreach ($a as $key => $as) {
            if ($i == 20) break;
            $content .= "§e" . $i . ". §r" . $key . " §bLvl: §a" . $as . "\n";
            $i++;
        }
        $form->setContent("§eName §8§l- §r§bLevel\n\n§r" . $content);
        $form->addButton("§6§lBACK\n§r§8Tap to go back", 0, "textures/ui/icon_import");
        $player->sendForm($form);
    }
}
