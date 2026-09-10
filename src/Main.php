<?php

declare(strict_types=1);

namespace TestBetterLitePlugin;

use betterlite\event\player\PlayerJoinEvent;
use betterlite\event\Listener;
use betterlite\event\EventHandler;
use betterlite\plugin\BetterLitePlugin;

class Main extends BetterLitePlugin implements Listener{

	protected function onPluginEnable() : void{
		$this->getLogger()->info("[BetterLite API] Ciao dal plugin di test!");
	}

	#[EventHandler]
	public function onJoin(PlayerJoinEvent $e) : void{
		$e->getPlayer()->sendMessage("Benvenuto su BetterLite API 0.1.0!");
	}
}
