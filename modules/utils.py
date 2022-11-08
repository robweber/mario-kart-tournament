import os


# full path to the running directory of the program
DIR_PATH = os.path.dirname(os.path.dirname(os.path.realpath(__file__)))
DATABASE_PATH = os.path.join(DIR_PATH, 'mario-kart-tournament.db')

# list of driver avatars
AVATARS = ['baby_daisy', 'baby_luigi', 'baby_mario', 'baby_peach', 'bowser', 'daisy', 'donkey',
           'koopa', 'luigi', 'mario', 'mario2', 'peach', 'waluigi', 'wario', 'yoshi']
