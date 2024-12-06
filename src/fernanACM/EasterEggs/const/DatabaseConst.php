<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\const;

final class DatabaseConst{
    
    public const INIT = "eastereggs.init";

    public const RESET = "eastereggs.reset";

    public const GET_PLAYER_DATA = "eastereggs.get_player_data";
    public const PLAYER_EXISTS = "eastereggs.player_exists";
    public const CREATE_PLAYER = "eastereggs.create_player";
    public const UPDATE_EVENT = "eastereggs.update_event";
    public const SET_COMPLETED = "eastereggs.set_completed";

    public const GET_EGGS = "eastereggs.get_eggs";
    public const UPDATE_EGGS = "eastereggs.update_eggs";
}
