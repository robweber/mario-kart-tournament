import argparse
import logging
import os.path
import random
import sys
import modules.utils as utils
import modules.database as db
from flask import Flask, jsonify, render_template, request, url_for, flash, redirect

# create the web service
app = Flask(import_name="mario-kart-tournament", static_folder=os.path.join(utils.DIR_PATH, 'web', 'static'),
            template_folder=os.path.join(utils.DIR_PATH, 'web', 'templates'))

# generate random number for session secret key
app.secret_key = os.urandom(24)


@app.route('/', methods=["GET"])
def index():
    settings=load_settings()

    if(settings['tournament_active'] == 'true'):
        # redirect to scoreboard page
        return redirect(url_for('scoreboard'))
    else:
        return render_template("index.html", drivers=db.execute_query("select * from drivers"), settings=settings)

@app.route('/tournaments/rules', methods=['GET'])
def rules():
    return render_template('rules.html', active_page='rules', active_game=find_active_game())

@app.route('/tournaments/add_driver', methods=["GET", "POST"])
def add_driver():
    # check if submitting the form
    if request.method == "POST":
        if not request.form['name']:
            flash("Please fill in your name", "error")
        elif 'image' not in request.form:
            flash("Please select an avatar image")
        else:
            # save the new driver
            flash(f"Driver { request.form['name'] } saved")
            db.execute_update("insert into drivers (name, phone, image) values (?, ?, ?)",
                              [request.form['name'], request.form['phone'], request.form['image']])
            return redirect(url_for('index'))

    return render_template("add_driver.html", avatars=utils.AVATARS)

@app.route('/tournaments/driver/<id>', methods=['GET'])
def view_driver(id):
    return render_template('driver_info.html', driver=find_driver(id))

@app.route('/tournaments/scoreboard', methods=['GET'])
def scoreboard():

    drivers = db.execute_query("select * from drivers")
    settings = load_settings()

    # load the current match
    match = db.execute_query("select * from matches where bracket_level = ? and match_num = ?", [settings['active_level'], settings['active_match']])

    return render_template('scoreboard.html', drivers=drivers, active_game=find_active_game(), player1=find_driver(settings['player_1']),
                           player2=find_driver(settings['player_2']), match=match, settings=settings)

@app.route("/tournaments/bracket", methods=['GET'])
def bracket():
    matches = db.execute_query("select * from matches order by bracket_level asc, match_num asc, id asc")

    # final output arrays
    teams = []
    scores = []

    current_level = 0
    current_match = 0
    current_team = []
    level_wrapper = []
    current_scores = []

    for aMatch in matches:
        if current_level == 0:
            current_level = aMatch['bracket_level']

        if current_match == 0:
            current_match = aMatch['match_num']

	    # only build teams on first level
        if aMatch['bracket_level'] == 1:
            driver_name = None
            if aMatch['driver_id'] != -1:
                driver_name = db.execute_query("select * from drivers where id = ?", [aMatch['driver_id']], True)

    		# add to team
            if current_match == aMatch['match_num']:
                current_team.append(driver_name)
            else:
    			# save team
                teams.append(current_team)
                current_team = [driver_name]
        else:
            if len(current_team) != 2:
    			# we have a bye
                current_team.append(None)
                current_scores.append(None)
        if aMatch['bracket_level'] != current_level:
    		# add entire level to scores array
            current_level = aMatch['bracket_level']
            current_match = aMatch['match_num']

            level_wrapper.append(current_scores)

    		# catch for uneven brackets
            if current_level == 2 and len(level_wrapper) % 2 == 1:
                level_wrapper.append([None, None])

            if current_level == 2:
    			# add the last team
                teams.append(current_team)

            scores.append(level_wrapper)

            level_wrapper = []
            current_scores = []

        # check if we have a new match
        if aMatch['match_num'] != current_match:
            current_match = aMatch['match_num']
            level_wrapper.append(current_scores)
            current_scores = []

        if aMatch['score'] == -1:
            current_scores.append(None)
        else:
            current_scores.append(aMatch['score'])

    # add last score
    level_wrapper.append(current_scores)
    scores.append(level_wrapper)
    results = {'teams': teams, 'results': [scores]}

    return jsonify(results)

@app.route('/admin', methods=['GET', 'POST'])
def admin():
    # check if updating the tournament setup
    if request.method == 'POST':
        # update the sms and active game settings
        db.execute_update("update settings set value = ? where name = ?", [request.form['active_game'], 'active_game'])
        db.execute_update("update settings set value = ? where name = ?", [request.form['send_sms'], 'send_sms'])

        flash("Settings Updated")

    # send the settings, the currently active game, and a list of games
    return render_template('admin.html', active_page='setup', active_game=find_active_game(),
                            settings=load_settings(), games=db.execute_query("select * from games order by name asc"))

@app.route('/admin/start_tournament', methods=['GET'])
def start_tournament():
    # first update the settings
    update_setting('tournament_active', 'true')
    update_setting('game_over', 'false')

    # update the driver stats and reset all matches
    db.execute_update('update drivers set wins = 0, losses = 0, active = "false"')
    db.execute_update('delete from matches')

    # get the active game
    active_game = find_active_game()

    # get all the drivers in a random order
    drivers = db.execute_query('select * from drivers order by RANDOM()')
    match_count = len(drivers)

    # first round players are a multiple of 4
    while match_count % 4 != 0:
        match_count = match_count + 1

    top_next = True
    got_two = False
    top_bracket = 1
    bottom_bracket = match_count/2  # equals total matches
    i = 0

    # distribute the players in the bracket (alternate top/bottom)
    while i < match_count:
        match_num = -1
        if top_next:
            match_num = top_bracket

            if got_two:
                top_bracket = top_bracket + 1
                got_two = False
                top_next = False
            else:
                # will be true next time
                got_two = True
        else:
            match_num = bottom_bracket

            if got_two:
                bottom_bracket = bottom_bracket - 1
                got_two = False
                top_next = True
            else:
                # will be true next time
                got_two = True

        # use a real driver if we can
        driver_id = -1;
        if i < len(drivers):
            driver_id = drivers[i]['id']

        # add the information to the matches bracket
        db.execute_update("insert into matches (driver_id, bracket_level, match_num, score) values (?, ?, ?, 0)", [driver_id, 1, match_num])
        i = i + 1

    # create the rest of the matches (unknown drivers)
    games_left = len(drivers)
    current_level = 1;
    if games_left % 2 == 1:
        # account for bye
        games_left = games_left + 1

    games_left = games_left/2
    while games_left > 1:
        current_level = current_level + 1

        if games_left % 2 == 1:
            # account for bye
            games_left = games_left + 1

        i = 1
        while i <= games_left/2:
            # make 2 for each match

            db.execute_update("insert into matches (driver_id, bracket_level, match_num) values (?, ?, ?)", [-1, current_level, i])
            db.execute_update("insert into matches (driver_id, bracket_level, match_num) values (?, ?, ?)", [-1, current_level, i])

            i = i + 1

        games_left = games_left /2

    # get the first two drivers
    match1 = db.execute_query("select * from matches where bracket_level = ? and match_num = ?", [1,1])
    player1 = match1[0]
    player2 = match1[1]

    # set the active drivers
    db.execute_update("update drivers set active = ? where id = ?", ['true', player1['driver_id']])
    db.execute_update("update drivers set active = ? where id = ?", ['true', player2['driver_id']])

    update_setting('player_1', player1['driver_id'])
    update_setting('player_2', player2['driver_id'])

    # set the active match to the first one
    update_setting('active_match', 1)
    update_setting('active_level', 1)

    # get a cup for them to play
    cups = db.execute_query("select * from cups where game_id = ?", [active_game['id']])
    cup_id = random.randint(0,len(cups) - 1);
    update_setting('active_cup', cups[cup_id]['name'])

    flash('Tournament Started')

    return redirect(url_for("admin"))

@app.route('/admin/stop_tournament', methods=['GET'])
def stop_tournament():
    # stop the tournament
    db.execute_update("update settings set value=? where name = ?", ['false', 'tournament_active'])
    db.execute_update("delete from matches")

    flash('Tournament Stopped')

    return redirect(url_for("admin"))

@app.route('/admin/drivers', methods=['GET'])
def drivers():
    drivers = db.execute_query("select * from drivers order by name asc")
    return render_template('drivers.html', active_page='drivers', drivers=drivers)

@app.route('/admin/delete_driver/<id>', methods=['GET'])
def delete_driver(id):
    db.execute_update("delete from drivers where id = ?", id)
    flash('Driver Deleted')

    return redirect(url_for('drivers'))

def find_active_game():
    # return the current active game based on selected value
    return db.execute_query("select id, name from games where id = (select value from settings where name = ?)", ["active_game"], True)

def find_driver(id):
    return db.execute_query("select * from drivers where id = ?", [id], True)

def load_settings():
    """this is needed on almost every page load

    :returns: the settings in a dict {key:value}
    """
    result = {}
    # convert the settings list to a dict
    for s in db.execute_query("select * from settings"):
        result[s['name']] = s['value']

    return result

def update_setting(name, value):
    """updates a single setting in the database"""

    db.execute_update("update settings set value = ? where name = ?", [value, name])

# parse the CLI args
parser = argparse.ArgumentParser(description='Mario Kart Tournament')
parser.add_argument('-p', '--port', default=5000,
                    help="Port number to run the web server on, %(default)d by default")
parser.add_argument('-D', '--debug', action='store_true',
                    help='If the program should run in debug mode')

args = parser.parse_args()

# setup the logger
logLevel = 'INFO' if not args.debug else 'DEBUG'
logHandlers = [logging.StreamHandler(sys.stdout)]
logging.basicConfig(datefmt='%m/%d %H:%M:%S',
                    format="%(levelname)s %(asctime)s: %(message)s",
                    level=getattr(logging, logLevel),
                    handlers=logHandlers)

# set logging for Flask app
for h in logHandlers:
    app.logger.addHandler(h)
app.logger.setLevel(getattr(logging, logLevel))

# turn of web server logging if not in debug mode
if(not args.debug):
    werkzeug = logging.getLogger('werkzeug')
    werkzeug.disabled = True

# run the web app
logging.info("Welcome to Mario Kart!")
logging.info("Starting web service")
app.run(debug=args.debug, host='0.0.0.0', port=5000, use_reloader=False)
