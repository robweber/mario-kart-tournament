import os


# full path to the running directory of the program
DIR_PATH = os.path.dirname(os.path.dirname(os.path.realpath(__file__)))
DATABASE_PATH = os.path.join(DIR_PATH, 'mario-kart-tournament.live.db')

# list of driver avatars
AVATARS = ['baby_daisy', 'baby_luigi', 'baby_mario', 'baby_peach', 'bowser', 'daisy', 'donkey',
           'koopa', 'luigi', 'mario', 'mario2', 'peach', 'waluigi', 'wario', 'yoshi']

# some pre-canned queries to avoid re-typeing
FIND_CUPS_QUERY = "select cups.name as name from cups join game_cup on cups.id = game_cup.cup_id join games on games.id = game_cup.game_id where games.id = ?"
