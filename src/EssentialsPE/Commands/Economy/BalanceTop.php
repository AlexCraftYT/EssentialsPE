<?php
namespace EssentialsPE\Commands\Economy;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Balance extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "balancetop", "See the top money", null, true, ["topbalance", "topmoney", "moneytop"]);
        $this->setPermission("essentials.balancetop.use");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $alias, array $args): bool{
        if(!$this->testPermission($sender)){
            return false;
        }
        if(count($args) > 0){
            $this->sendUsage($sender, $alias);
            return false;
        }
        $this->getAPI()->getBalanceTop();
        $sender->sendMessage(TextFormat::GREEN . " --- Money top list ---");
        foreach($this->getAPI->numbers as $number) {
            $sender->sendMessage($number);
        }
        return true;
    }
}
