<?php
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
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if($sender instanceof Player) {
            $this->executions[$sender->getName()] = true;
            $sender->sendMessage("Touch any chest to sort it!");
        } else {
            $sender->sendMessage("Cannot run command on server");
        }
        return true;
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        if(isset($this->executions[$event->getPlayer()->getName()]) && $event->getBlock()->getID() == BlockIds::CHEST){
            $tile = $event->getPlayer()->getLevel()->getTile(new Vector3($event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z));
            if($tile instanceof Chest) {
                $inv = $tile->getInventory();
                $maxItem = PHP_INT_MAX;
                $indexOfMax = -1;
                for ($i = 0; $i < 27; $i++) {
                    for($j = $i; $j < 27; $j++) {
                        if ($inv->getItem($j)->getId()>$maxItem) {
                            $maxItem = $inv->getItem($j)->getId();
                            $indexOfMax = $j;
                        }
                    }
                    $t = $inv->getItem($indexOfMax)->getId();
                    $inv->setItem($indexOfMax, $inv->getItem($i)->getId());
                    $inv->setItem($i, $t);
                }

                $event->getPlayer()->sendMessage("Chest sorted!");
                unset($this->executions[$event->getPlayer()->getName()]);
            }
        } else {
            $event->getPlayer()->sendMessage("Please touch a chest!");
        }
    }
}