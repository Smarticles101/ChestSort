<?php

// TODO:
//      Ascending sort
//      Sorting collections of chests
//      Sorting by item damadge/defense?
//      Sorting items alphabetically
//      Trash?

namespace me\logans\ChestSort;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\tile\Chest;
use pocketmine\Player;
use pocketmine\block\BlockIds;

class Main extends PluginBase implements Listener {
    private $executions;

    public function onEnable() {
        $this->executions = [];
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if($sender instanceof Player) {
            $this->executions[$sender->getName()] = true;
            $sender->sendMessage("§aTouch any chest to sort it!");
        } else {
            $sender->sendMessage("Cannot run command on server");
        }
        return true;
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        if(isset($this->executions[$event->getPlayer()->getName()])) {
            if($event->getBlock()->getID() == BlockIds::CHEST){
                $tile = $event->getPlayer()->getLevel()->getTile(new Vector3($event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z));
                if($tile instanceof Chest) {
                    $inv = $tile->getInventory();
                    $maxItem = PHP_INT_MIN;
                    $indexOfMax = -1;
                    for ($i = 0; $i < 27; $i++) {
                        for($j = $i; $j < 27; $j++) {
                            if ($inv->getItem($j)->getId() !== 0 && $inv->getItem($j)->getId()>$maxItem) {
                                //echo $j;
                                $maxItem = $inv->getItem($j)->getId();
                                $indexOfMax = $j;
                            }
                        }
                        if($indexOfMax!==-1) {
                            $t = $inv->getItem($indexOfMax);
                            //echo $t;
                            $inv->setItem($indexOfMax, $inv->getItem($i));
                            $inv->setItem($i, $t);
                            $indexOfMax = -1;
                            $maxItem = PHP_INT_MIN;
                        }
                    }

                    $event->getPlayer()->sendMessage("§aChest sorted in descending order based off of item id.");
                    unset($this->executions[$event->getPlayer()->getName()]);
                }
            } else {
                $event->getPlayer()->sendMessage("§4Please touch a chest!");
            }
        }
    }
}
