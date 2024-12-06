-- #! sqlite

-- #{ eastereggs

-- #  { init
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player VARCHAR(255) UNIQUE NOT NULL,
    event_name VARCHAR(255) DEFAULT '',
    completed BOOLEAN DEFAULT 0,
    eggs TEXT DEFAULT '[]'
);
-- #  }

-- #  { create_player
-- #    :player string
-- #    :event_name string
-- #    :completed boolean
-- #    :eggs string
INSERT INTO players (player, event_name, completed, eggs) VALUES (:player, :event_name, :completed, :eggs);
-- #  }

-- #  { player_exists
-- #    :player string
SELECT COUNT(*) as count FROM players WHERE player = :player;
-- #  }

-- #  { get_player_data
-- #    :player string
SELECT * FROM players WHERE player = :player;
-- #  }

-- #  { update_event
-- #    :player string
-- #    :event_name string
UPDATE players SET event_name = :event_name WHERE player = :player;
-- #  }

-- #  { set_completed
-- #    :player string
-- #    :completed boolean
UPDATE players SET completed = :completed WHERE player = :player;
-- #  }

-- #  { get_eggs
-- #    :player string
SELECT eggs FROM players WHERE player = :player;
-- #  }

-- #  { update_eggs
-- #    :player string
-- #    :eggs string
UPDATE players SET eggs = :eggs WHERE player = :player;
-- #  }

-- #  { reset
UPDATE players SET event_name = "", completed = 0, eggs = "[]";
-- #  }

-- #}