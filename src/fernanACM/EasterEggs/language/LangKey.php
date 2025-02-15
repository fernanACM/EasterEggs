<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\language;

final class LangKey{

    // ERROR
    public const ERROR_NO_PERMISSION = "Messages.error.no-permission";

    public const ERROR_FULL_INVENTORY = "Messages.error.full-inventory";
    public const ERROR_LACK_OF_DATA = "Messages.error.lack-of-data";
    public const ERROR_CHARACTERES_LIMIT = "Messages.error.characteres-limit";

    public const ERROR_NO_EXISTING_EVENTS = "Messages.error.no-existing-events";
    public const ERROR_EXISTING_EVENT = "Messages.error.existing-event";
    public const ERROR_EVENT_NOT_FOUND = "Messages.error.event-not-found";

    public const ERROR_EGGS_ARE_MISSING = "Messages.error.eggs-are-missing";
    public const ERROR_IT_IS_COMPLETED = "Messages.error.it-is-completed";
    public const ERROR_IT_IS_CLAIMED = "Messages.error.it-is-claimed";
    public const ERROR_REACHED_THE_LIMIT = "Messages.error.reached-the-limit";
    public const ERROR_UNCLAIMED_EGG = "Messages.error.unclaimed-egg";

    public const ERROR_EXISTING_EGG = "Messages.error.existing-egg";
    public const ERROR_NO_EXISTING_EGGS = "Messages.error.no-existing-eggs";

    // SUCCESS
    public const SUCCESS_SAVED_POSITION = "Messages.success.saved-position";
    public const SUCCESS_REMOVED_POSITION = "Messages.success.removed-position";

    public const SUCCESS_ENTER_SETUP_MODE = "Messages.success.enter-setup-mode";
    public const SUCCESS_EXIT_SETUP_MODE = "Messages.success.exit-setup-mode";

    public const SUCCESS_EVENT_CREATED = "Messages.success.event-created";
    public const SUCCESS_EVENT_REMOVED = "Messages.success.event-removed";
    public const SUCCESS_EVENT_ESTABLISHED = "Messages.success.event-established";

    public const SUCCESS_SETUP_COMPLETED = "Messages.success.setup-completed";
    public const SUCCESS_SETUP_RESET_DATA = "Messages.success.setup-reset-data";
    public const SUCCESS_SETUP_PROGRESS = "Messages.success.setup-progress";
    public const SUCCESS_SETUP_SPAWN_ENTITY = "Messages.success.setup-spawn-entity";
    public const SUCCESS_SETUP_DESPAWN_ENTITY = "Messages.success.setup-despawn-entity";

    public const SUCCESS_ADDED_POINT = "Messages.success.added-point";
    public const SUCCESS_POINT_REMOVED = "Messages.success.point-removed";
    public const SUCCESS_REWARD_RECEIVED = "Messages.success.reward-received";

    // GENERAL - FORM [SETUP]
    public const SETUP_FORM_CONTENT = "Form.setup.content";
    public const SETUP_FORM_TOGGLE1_BUTTON = "Form.setup.toggle1-button"; // ENABLE
    public const SETUP_FORM_TOGGLE2_BUTTON = "Form.setup.toggle2-button"; // DISABLE
    public const SETUP_FORM_REMOVE_BUTTON = "Form.setup.remove-button";
    public const SETUP_FORM_LOCATIONS_BUTTON = "Form.setup.locations-button";
    public const SETUP_FORM_ENTITY_BUTTON = "Form.setup.entity-button";
    public const SETUP_FORM_RESET_DATA_BUTTON = "Form.setup.reset-data-button";
    public const SETUP_FORM_CLOSE_BUTTON = "Form.setup.close-button";

    public const SETUP_FORM_CONTENT_LOCATIONS = "Form.setup.locations.content";

    public const SETUP_FORM_CONTENT_ENTITIES = "Form.setup.entities.content";
    public const SETUP_FORM_ENTITIES_BUTTON1 = "Form.setup.entities.button1"; // SPAWN
    public const SETUP_FORM_ENTITIES_BUTTON2 = "Form.setup.entities.button2"; // DESPAWN

    public const SETUP_FORM_CONFIRM_CONTENT = "Form.setup.data.confirm-content";
    public const SETUP_FORM_CONFIRM_BUTTON1 = "Form.setup.data.button1"; // YES
    public const SETUP_FORM_CONFIRM_BUTTON2 = "Form.setup.data.button2"; // NO

    public const EVENT_FORM_MAIN_CONTENT = "Form.event.content";
    public const EVENT_FORM_MAIN_CREATOR_BUTTON = "Form.event.creator-button";
    public const EVENT_FORM_MAIN_REMOVER_BUTTON = "Form.event.remover-button";
    public const EVENT_FORM_MAIN_APPLY_BUTTON = "Form.event.apply-button";
    public const EVENT_FORM_MAIN_LIST_BUTTON = "Form.event.list-button";
    public const EVENT_FORM_MAIN_CLOSE_BUTTON = "Form.event.close-button";

    public const EVENT_FORM_CREATOR_CONTENT = "Form.event.creator.content";
    public const EVENT_FORM_CREATOR_INPUT = "Form.event.creator.input";

    public const EVENT_FORM_REMOVER_CONTENT = "Form.event.remover-content";

    public const EVENT_FORM_APPLY_CONTENT = "Form.event.apply.content";
    public const EVENT_FORM_CONFIRM_CONTENT = "Form.event.apply.confirm-content";
    public const EVENT_FORM_CONFIRM_BUTTON1 = "Form.event.apply.button1"; // YES
    public const EVENT_FORM_CONFIRM_BUTTON2 = "Form.event.apply.button2"; // NO

    // ITEM
    public const ITEM_NAME = "Item.name";

    // LOOT
    public const SAVED_INVENTORY = "Item.saved-inventory";

    public const REWARD_TITLE = "Reward.player-title";
    public const REWARD_SUBTITLE = "Reward.player-subtitle";

    public const REWARD_BROADCAST = "Reward.broadcast-message";
}