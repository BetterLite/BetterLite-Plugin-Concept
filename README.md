# TestBetterLitePlugin

Test plugin for the **BetterLite API 0.1.0**.

This plugin exists to demonstrate the concept of BetterLite's new plugin API: a modern API, inspired by Bukkit/Spigot, but written in PHP.

> ⚠️ **Status**: concept / prototype. The API is at 0.1.0 and will change in future releases. For now it covers only a minimal subset of events and features — just enough to validate the idea.

---

## 🎯 What this plugin demonstrates

- ✅ How to write a plugin using the new BetterLite API
- ✅ How the event system works with PHP 8 `#[Attribute]` (instead of Bukkit's annotations)
- ✅ How a plugin is recognized and loaded by the `BetterLitePluginLoader`

---

## 📁 Structure

```
TestBetterLitePlugin/
├── plugin.yml
└── src/
    └── Main.php
```

### `plugin.yml`

```yaml
name: TestBetterLitePlugin
main: TestBetterLitePlugin\Main
version: 0.1.0
api:
  - 5.0.0
betterlite-api: 0.1.0
src-namespace-prefix: TestBetterLitePlugin
```

- `api: [5.0.0]` → PMMP 5.x compatibility (standard field, SemVer required)
- `betterlite-api: 0.1.0` → **custom** field that identifies BetterLite plugins. The `BetterLitePluginLoader` looks for this key to decide whether to load the plugin.
- `src-namespace-prefix` → PHP namespace prefix mapped to `src/`

### `src/Main.php`

```php
<?php

declare(strict_types=1);

namespace TestBetterLitePlugin;

use betterlite\event\player\PlayerJoinEvent;
use betterlite\event\Listener;
use betterlite\event\EventHandler;
use betterlite\plugin\BetterLitePlugin;

class Main extends BetterLitePlugin implements Listener{

    protected function onPluginEnable() : void{
        $this->getLogger()->info("[BetterLite API] Hello from the test plugin!");
    }

    #[EventHandler]
    public function onJoin(PlayerJoinEvent $e) : void{
        $e->getPlayer()->sendMessage("Welcome to BetterLite API 0.1.0!");
    }
}
```

Key points:
- `extends BetterLitePlugin` → base class of the new API
- `implements Listener` → marker interface indicating this class receives events
- `onPluginEnable()` → hook called on load (replaces PMMP's `onEnable()`)
- `#[EventHandler]` → PHP 8 attribute that automatically registers the method as a handler
- The `PlayerJoinEvent` parameter has a **type-hint** → the loader infers the corresponding PMMP event from the signature

---

## 🧠 How it works (under the hood)

```
Test plugin                          BetterLite Loader                     PMMP Core
─────────────                          ─────────────────                     ─────────
Main.php loaded
    │
    ├─ class_exists() OK
    │
    ├─ new Main() instantiated
    │
    ├─ onEnable() called
    │       │
    │       └─ BetterLitePluginLoader::registerListener($this)
    │               │
    │               └─ Scans methods with #[EventHandler]
    │                       │
    │                       └─ For each method: registerEvent(PM_PlayerJoinEvent, $closure)
    │                                                                              │
        (see below) ◄────────────────────────────────────────────────────────────┘
                │
                └─ When a player joins:
                        │
                        ├─ PM core emits pocketmine\event\player\PlayerJoinEvent
                        │
                        └─ PluginManager::registerEvent() calls the closure
                                │
                                └─ Closure wraps the PM event in betterlite\event\player\PlayerJoinEvent
                                        │
                                        └─ Invokes Main::onJoin($wrappedEvent)
                                                │
                                                └─ $e->getPlayer()->sendMessage("Welcome!")
```

The PMMP core knows nothing about BetterLite. The wrapping is completely transparent.

---

## ⚠️ Important notes

- **The API is not public yet** — it's an experiment to see if the concept works
- **Only `PlayerJoinEvent` is implemented** — for now
- **Wrappers are not explicitly thread-safe** — PMMP uses `pmmpthread` for AsyncTasks, but main-thread events are fine
- **Tested only on Windows + PHP 8.4 PM5** — other OSes might have surprises

---

**BetterLite** — a PocketMine-MP fork with a modern Bukkit-inspired API, but faithful to PHP and the original core.
