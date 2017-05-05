<?php

namespace EssentialsPEconomy\Providers;

use EssentialsPEconomy\EssentialsPEconomy;
use pocketmine\Player;

class SQLiteEconomyProvider extends BaseEconomyProvider {

	/** @var \SQLite3 $database */
	private $database;

	public function __construct(EssentialsPEconomy $loader) {
		parent::__construct($loader);
	}

	public function prepare() {
		if(!file_exists($path = $this->getLoader()->getDataFolder() . "economy.sqlite3")) {
			file_put_contents($path, "");
		}
		$this->database = new \SQLite3($path);
		$query = "CREATE TABLE IF NOT EXISTS Economy(Player VARCHAR(20) PRIMARY KEY, Balance INT);";
		$this->database->exec($query);
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getEconomyTop(int $limit = 10): array {
		$result = $this->database->query("SELECT * FROM Economy ORDER BY Balance DESC LIMIT 10;");
		$return = [];
		for($i = 0; $i <= $limit; $i++) {
			if($array = $result->fetchArray(SQLITE3_ASSOC)) {
				$return[$array[$i]] = $array["Balance"];
			}
		}
		var_dump($array);
		var_dump($result);
		var_dump($return);
		return $return;
	}

	/**
	 * @param Player $player
	 * @param int    $balance
	 *
	 * @return bool
	 */
	public function addPlayer(Player $player, int $balance = -1): bool {
		if($balance === -1) {
			$balance = $this->getLoader()->getConfiguration()->get("Default-Balance");
		}
		$lowerCaseName = strtolower($player->getName());

		if($this->playerExists($player)) {
			return false;
		}
		$this->database->exec("INSERT INTO Economy(Player, Balance) VALUES ('" . $this->escape($lowerCaseName) . "', $balance);");
		return true;
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function playerExists(Player $player): bool {
		$lowerCaseName = strtolower($player->getName());

		$result = $this->database->query("SELECT Balance FROM Economy WHERE Player = '" . $this->escape($lowerCaseName) . "';");
		return empty((array)$result);
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private function escape(string $string): string {
		return \SQLite3::escapeString($string);
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function removePlayer(Player $player): bool {
		$lowerCaseName = strtolower($player->getName());

		if(!$this->playerExists($player)) {
			return false;
		}
		if($this->database->exec("DELETE FROM Economy WHERE Player = '" . $this->escape($lowerCaseName) . "';")) {
			return true;
		}
		return false;
	}

	/**
	 * @param Player $player
	 * @param int    $amount
	 *
	 * @return bool
	 */
	public function setBalance(Player $player, int $amount): bool {
		$lowerCaseName = strtolower($player->getName());
		if(!$this->playerExists($player)) {
			return false;
		}
		if($amount < $this->getLoader()->getConfiguration()->get("Minimum-Balance")) {
			throw new \OutOfBoundsException("A Player's balance can't be below the minimum balance.");
		}
		if($amount > $this->getLoader()->getConfiguration()->get("Maximum-Balance")) {
			throw new \OutOfBoundsException("A Player's balance can't exceed the maximum balance.");
		}
		$result = $this->database->exec("UPDATE Economy SET Balance = $amount WHERE Player = '" . $this->escape($lowerCaseName) . "';");
		return $result !== false;
	}

	/**
	 * @param Player $player
	 * @param int    $amount
	 *
	 * @return bool
	 */
	public function subtractFromBalance(Player $player, int $amount): bool {
		if($this->getBalance($player) - $amount < $this->getLoader()->getConfiguration()->get("Minimum-Balance")) {
			throw new \OutOfBoundsException("A Player's balance can't be below the minimum balance.");
		}
		return $this->addToBalance($player, -$amount);
	}

	/**
	 * @param Player $player
	 *
	 * @return int|bool
	 */
	public function getBalance(Player $player) {
		$lowerCaseName = strtolower($player->getName());
		if(!$this->playerExists($player)) {
			return false;
		}
		$result = $this->database->query("SELECT Balance FROM Economy WHERE Player = '" . $this->escape($lowerCaseName) . "';");
		$ret = $result->fetchArray(SQLITE3_ASSOC)["Balance"];
		return $ret;
	}

	/**
	 * @param Player $player
	 * @param int    $amount
	 *
	 * @return bool
	 */
	public function addToBalance(Player $player, int $amount): bool {
		$lowerCaseName = strtolower($player->getName());
		if(!$this->playerExists($player)) {
			return false;
		}
		if($amount + $this->getBalance($player) > $this->getLoader()->getConfiguration()->get("Maximum-Balance")) {
			throw new \OutOfBoundsException("A Player's balance can't be above the maximum balance.");
		}
		$result = $this->database->exec("UPDATE Economy SET Balance = Balance + $amount WHERE Player = '" . $this->escape($lowerCaseName) . "';");
		return $result !== false;
	}

	/**
	 * @return bool
	 */
	public function closeDatabase(): bool {
		if($this->database instanceof \SQLite3) {
			$this->database->close();
			return true;
		}
		return false;
	}

	public function save() {

	}
}