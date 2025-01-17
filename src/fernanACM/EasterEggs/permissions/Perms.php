<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\permissions;

final class Perms{

    public const MAIN_COMMAND = "eastereggs.cmd.acm";

    public const SETUP_SUBCOMMAND = "eastereggs.setup.cmd.acm";
    public const EVENTS_SUBCOMMAND = "eastereggs.events.cmd.acm";
    public const LOOT_SUBCOMMAND = "eastereggs.loot.cmd.acm";

    public const ENTER_SETUP_MODE = "eastereggs.enter.setupmode.acm";
    public const USE_REMOVER_WAND = "eastereggs.use.remover.wand.acm";
}