import os


# full path to the running directory of the program
DIR_PATH = os.path.dirname(os.path.dirname(os.path.realpath(__file__)))
DATABASE_PATH = os.path.join(DIR_PATH, 'mario-kart-tournament.live.db')

# list of driver avatars
AVATARS = ['baby_daisy', 'baby_luigi', 'baby_mario', 'baby_peach', 'baby_rosalina', 'bowser', 'daisy', 'donkey',
           'koopa', 'ludwig', 'luigi', 'mario', 'mario2', 'peach', 'rosalina', 'shy_guy', 'toad', 'toadette', 'roy',
           'waluigi', 'wario', 'yoshi']

# some pre-canned queries to avoid re-typeing
ACTIVE_GAME_QUERY = "select id, name from games where id = (select value from settings where name = ?)"
CREATE_MATCH_STATEMENT = "insert into matches (driver_id, bracket_level, match_num, score) values (?, ?, ?, -1)"
FIND_CUPS_QUERY = "select cups.name as name from cups join game_cup on cups.id = game_cup.cup_id join games on games.id = game_cup.game_id where games.id = ?"  # noqa
FIND_DIFFICULTY_QUERY = "select difficulty_modes.name as name from difficulty_modes join game_difficulty on difficulty_modes.id = game_difficulty.mode_id join games on games.id = game_difficulty.game_id where games.id = ?"  # noqa


class ActiveGame:

    def __init__(self, game_obj, game_mode):
        self.game_obj = game_obj
        self.game_mode = game_mode

    def get_id(self):
        return self.game_obj['id']

    def get_name(self):
        return self.game_obj['name']

    def get_description(self):
        result = self.game_obj['name']

        # append the game mode if applicable
        if(self.game_mode != "none"):
            result = f"{result} ({self.game_mode})"

        return result
