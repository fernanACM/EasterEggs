[![](https://poggit.pmmp.io/shield.state/EasterEggs)](https://poggit.pmmp.io/p/EasterEggs)

[![](https://poggit.pmmp.io/shield.api/EasterEggs)](https://poggit.pmmp.io/p/EasterEggs)

# EasterEggs

EasterEggs addon with customizable locations, world objectives, and unique rewards. Make your players enjoy searching and competing!

![image](https://github.com/user-attachments/assets/be799da7-2e28-40a4-bbd6-f92df924008a)

<a href="https://discord.gg/YyE9XFckqb"><img src="https://img.shields.io/discord/837701868649709568?label=discord&color=7289DA&logo=discord" alt="Discord" /></a>

### 🌍 Wiki
* Check our plugin [wiki](https://github.com/fernanACM/EasterEggs/wiki) for features and secrets in the...

### 💡 Implementations
* [X] Configuration
* [x] ScoreHud support
* [x] Keys in config.yml
* [x] Reward system

### 💾 Config 
```yaml
#  _____                 _                   _____                       
# | ____|   __ _   ___  | |_    ___   _ __  | ____|   __ _    __ _   ___ 
# |  _|    / _` | / __| | __|  / _ \ | '__| |  _|    / _` |  / _` | / __|
# | |___  | (_| | \__ \ | |_  |  __/ | |    | |___  | (_| | | (_| | \__ \
# |_____|  \__,_| |___/  \__|  \___| |_|    |_____|  \__, |  \__, | |___/
#                                                    |___/   |___/
#               by fernanACM

# EasterEggs for PocketMine-MP 5.0 servers. Find the Eggs and get rewards 
# by completing the Eggs goal.

# DO NOT TOUCH!!
config-version: "1.0.0"

# Languages
# "eng", // English
# "spa", // Spanish
# "ger", // German
# "indo", // Indonesian
# "vie" // Vietnamese
language: eng

# Prefix plugin
Prefix: "&l&f[&aEasterEggs&f]&8»&r "

# ====(SETIINGS)====
Settings:
  Form:
    # Set the character limit to create an event
    characteres: 16
  EasterEgg:
    # EasterEggs limit
    egg-limit: 10

    Reward:
      # Use "true" or "false" to activate/deactivate this option
      broadcast: true
      # Use "true" or "false" to activate/deactivate this option
      player-titles: true

      Commands:
        # Use "true" or "false" to activate/deactivate this option
        execute: true
        # Use "{PLAYER}" to run commands with the player name
        list:
          - give "{PLAYER}" apple 22
      Items:
        # Use "true" or "false" to activate/deactivate this option
        receive: true
        # Set the minimum number of items to receive
        min: 5
        # Set the maximum number of items to receive
        max: 8
    Entity:
      # Use "true" or "false" to activate/deactivate this option
      particles: true
      # Sets the particle color for the egg. Use this page to get the RGB code:
      # https://htmlcolorcodes.com/es/
      particle-color: [16, 230, 227]
```
### 🔒 Permissions
| Permission | Description |
|---------|-------------|
| ```eastereggs.cmd.acm``` | Executing the command |
| ```eastereggs.events.cmd.acm``` | Events |
| ```eastereggs.loot.cmd.acm``` | Loot |
| ```eastereggs.setup.cmd.acm``` | Setup |
| ```astereggs.enter.setupmode.acm``` | Enter in setup mode |
| ```eastereggs.use.remover.wand.acm``` | Use the wand to remove eggs |

### 🍟ScoreHud
| Permission | Description |
|---------|-------------|
| ```{eastereggs.eggs}``` | Eggs obtained |
| ```{eastereggs.goal}``` | Egg goal |
| ```{eastereggs.event-name}``` | Current event |

### 🌐 MultiLanguage
| Language | Translated by |
|----------|---------------|
| English | [fernanACM](https://github.com/fernanACM) |
| Spanish | [fernanACM](https://github.com/fernanACM) |
| Indonesian | EasterEggs |
| German | EasterEggs |
| Vietnamese | EasterEggs |

### 📢 Report bug
* If you find any bugs in this plugin, please let me know via: [issues](https://github.com/fernanACM/EasterEggs/issues)

### 📞 Contact
| Redes | Tag | Link |
|-------|-------------|------|
| YouTube | fernanACM | [YouTube](https://www.youtube.com/channel/UC-M5iTrCItYQBg5GMuX5ySw) | 
| Discord | fernanACM#5078 | [Discord](https://discord.gg/YyE9XFckqb) |
| GitHub | fernanACM | [GitHub](https://github.com/fernanACM)
| Poggit | fernanACM | [Poggit](https://poggit.pmmp.io/ci/fernanACM)
****

### ✔ Credits
| Authors | Github | Lib |
|---------|--------|-----|
| Vecnavium | [Vecnavium](https://github.com/Vecnavium) | [FormsUI](https://github.com/Vecnavium/FormsUI/tree/master/) |
| CortexPE | [CortexPE](https://github.com/CortexPE) | [Commando](https://github.com/CortexPE/Commando/tree/master/) |
| Muqsit | [Muqsit](https://github.com/Muqsit) | [SimplePacketHandler](https://github.com/Muqsit/SimplePacketHandler) |
| Muqsit | [Muqsit](https://github.com/Muqsit) | [InvMenu](https://github.com/Muqsit/InvMenu) |
| DaPigGuy | [DaPigGuy](https://github.com/DaPigGuy) | [libPiggyUpdateChecker](https://github.com/DaPigGuy/libPiggyUpdateChecker) |
****
