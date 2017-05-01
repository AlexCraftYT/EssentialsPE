<?php

namespace EssentialsPE\EventHandlers\SpecialSigns;

use EssentialsPE\Loader;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as TF;

class TeleportSign extends BaseSign {

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function onSignChange(SignChangeEvent $signChangeEvent) {
		if(strtolower(TF::clean($signChangeEvent->getLine(0), true)) === "[teleport]" && $signChangeEvent->getPlayer()->hasPermission("essentials.sign.create.teleport")) {
			if(!is_numeric($signChangeEvent->getLine(1))) {
				$signChangeEvent->getPlayer()->sendMessage(TF::RED . "[Error] " /* TODO */);
				$signChangeEvent->setCancelled(true);
			} elseif(!is_numeric($signChangeEvent->getLine(2))) {
				$signChangeEvent->getPlayer()->sendMessage(TF::RED . "[Error] " /* TODO */);
				$signChangeEvent->setCancelled(true);
			} elseif(!is_numeric($signChangeEvent->getLine(3))) {
				$signChangeEvent->getPlayer()->sendMessage(TF::RED . "[Error] " /* TODO */);
				$signChangeEvent->setCancelled(true);
			} else {
				$signChangeEvent->getPlayer()->sendMessage(TF::GREEN . "" /* TODO */);
				$signChangeEvent->setLine(0, TF::AQUA . "[Teleport]");
				$signChangeEvent->setLine(1, $signChangeEvent->getLine(1));
				$signChangeEvent->setLine(2, $signChangeEvent->getLine(2));
				$signChangeEvent->setLine(3, $signChangeEvent->getLine(3));
			}
		}
	}

	public function onInteract(PlayerInteractEvent $interactEvent) {
		$tile = $interactEvent->getBlock()->getLevel()->getTile(new Vector3($interactEvent->getBlock()->getFloorX(), $interactEvent->getBlock()->getFloorY(), $interactEvent->getBlock()->getFloorZ()));
		if(!$tile instanceof Sign) {
			return;
		}
		if(TF::clean($tile->getText()[0], true) === "[Teleport]") {
			$interactEvent->setCancelled(true);
			if(!$interactEvent->getPlayer()->hasPermission("essentials.sign.use.teleport")) {
				$interactEvent->getPlayer()->sendMessage(TF::RED . "[Error] " /* TODO */);
			} else {
				$interactEvent->getPlayer()->teleport(new Vector3($x = $tile->getText()[1], $y = $tile->getText()[2], $z = $tile->getText()[3]));
				$interactEvent->getPlayer()->sendMessage(TF::GREEN . "Teleporting to " . TF::AQUA . $x . TF::GREEN . ", " . TF::AQUA . $y . TF::GREEN . ", " . TF::AQUA . $z);
			}
		}
	}
}