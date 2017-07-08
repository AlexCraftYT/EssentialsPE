<?php

declare(strict_types = 1);

namespace EssentialsPE\Configurable;

use EssentialsPE\Loader;

class CommandSwitch extends ConfigurableDataHolder {

	/** @var string[] */
	private $availableCommands = [];
	/** @var string[] */
	private $disabledCommands = [];

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	/**
	 * @return array
	 */
	public function getAvailableCommands(): array {
		return $this->availableCommands;
	}

	/**
	 * @return array
	 */
	public function getDisabledCommands(): array {
		return $this->disabledCommands;
	}

	protected function check() {
		if(!file_exists($path = $this->getLoader()->getDataFolder() . "commands.yml")) {
			$this->getLoader()->saveResource("commands.yml");
		}
		$commands = yaml_parse_file($path);

		foreach($commands as $command => $enabled) {
			if($enabled === true) {
				$this->availableCommands[] = $command;
			} else {
				$this->disabledCommands[] = $command;
			}
		}
	}
}