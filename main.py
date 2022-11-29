import argparse
import logging
import os.path
import math
import random
import sys
import modules.utils as utils
import modules.database as db
from flask import Flask, session, jsonify, render_template, request, url_for, flash, redirect

# create the web service
app = Flask(import_name="mario-kart-tournament", static_folder=os.path.join(utils.DIR_PATH, 'web', 'static'),
            template_folder=os.path.join(utils.DIR_PATH, 'web', 'templates'))

# generate random number for session secret key
app.secret_key = os.urandom(24)


@app.route('/', methods=["GET"])
def index():
    settings = db.load_settings()

    if(settings['tournament_active'] == 'true'):
        # redirect to scoreboard page
        return redirect(url_for('scoreboard'))
    else:
        return render_template("index.html", drivers=db.execute_query("select * from drivers order by name asc"), settings=settings)


@app.route('/tournaments/rules', methods=['GET'])
def rules():
    return render_template('rules.html', active_page='rules', settings=db.load_settings(), active_game=db.find_active_game())


@app.route('/tournaments/add_driver', methods=["GET", "POST"])
def add_driver():
    # check if submitting the form
    if request.method == "POST":
        phone = request.form['phone'] if 'phone' in request.form else ""

        if not request.form['name']:
            flash("Please fill in your name", "error")
        elif 'image' not in request.form:
            flash("Please select an avatar image")
        else:
            # save the new driver
            flash(f"Driver { request.form['name'] } saved")
            db.execute_update("insert into drivers (name, phone, image) values (?, ?, ?)",
                              [request.form['name'], phone, request.form['image']])
            return redirect(url_for('index'))

    return render_template("add_driver.html", avatars=utils.AVATARS, settings=db.load_settings())


@app.route('/tournaments/driver/<id>', methods=['GET'])
def view_driver(id):
    return render_template('driver_info.html', driver=db.find_driver(id))


@app.route('/tournaments/scoreboard', methods=['GET'])
def scoreboard():
    template = 'base.html' if 'fullscreen' not in request.args.keys() else "fullscreen.html"
    return render_template('scoreboard.html', scoreboard_template=template,
                           settings=db.load_settings(), active_game=db.find_active_game())


@app.route('/tournaments/current_match', methods=['GET'])
def current_match():
    settings = db.load_settings()

    # load the current match
    match = db.execute_query("select * from matches where bracket_level = ? and match_num = ?", [settings['active_level'], settings['active_match']])

    return jsonify({'player1': db.find_driver(settings['player_1']), 'player2': db.find_driver(settings['player_2']),
                   'match': match, 'settings': settings})


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
        # update settings, reset game mode to none
        db.update_setting('admin_pin', request.form['admin_pin'])
        db.update_setting('active_game', request.form['active_game'])
        db.update_setting('send_sms', request.form['send_sms'])

        # reset game mode to none if game has changed
        if(request.form['old_active_game'] != request.form['active_game']):
            db.update_setting('game_mode', 'none')
        else:
            db.update_setting('game_mode', request.form['game_mode'])

        # logout the admin user if pin changed
        if(request.form['admin_pin'] != request.form['old_admin_pin']):
            session.pop('is_admin', None)

        flash("Settings Updated")

    settings = db.load_settings()

    if('is_admin' not in session):
        return redirect(url_for('login'))

    if(settings['tournament_active'] == 'false'):
        # send the settings, the currently active game, and a list of games
        active_game = db.find_active_game()
        return render_template('admin.html', active_page='setup', active_game=active_game,
                               settings=settings, games=db.execute_query("select * from games order by name asc"),
                               modes=db.execute_query(utils.FIND_DIFFICULTY_QUERY, [active_game.get_id()]))
    else:
        # load the current match
        match = db.execute_query("select * from matches where bracket_level = ? and match_num = ?",
                                 [settings['active_level'], settings['active_match']])

        return render_template('admin.html', active_page='setup', active_game=db.find_active_game(), settings=settings,
                               player1=db.find_driver(settings['player_1']), player2=db.find_driver(settings['player_2']), match=match)


@app.route('/admin/pin', methods=['GET', 'POST'])
def login():
    # check the admin pin
    settings = db.load_settings()

    if(settings['admin_pin'].strip() == ''):
        # ignore the PIN and proceed to the admin page
        session['is_admin'] = True
        return redirect(url_for('admin'))

    if request.method == 'POST':
        if(request.form['admin_pin'] == settings['admin_pin']):
            session['is_admin'] = True
            return redirect(url_for('admin'))
        else:
            flash('Incorrect PIN', 'error')

    # if login fails display the pin page again
    return render_template('pin.html', active_page='none')


@app.route('/admin/start_tournament', methods=['GET'])
def start_tournament():
    if('is_admin' not in session):
        return redirect(url_for('login'))

    # first update the settings
    db.update_setting('tournament_active', 'true')
    db.update_setting('game_over', 'false')

    # update the driver stats and reset all matches
    db.execute_update('update drivers set wins = 0, losses = 0, active = "false"')
    db.execute_update('delete from matches')

    # get the active game
    active_game = db.find_active_game()

    # get all the drivers in a random order
    drivers = db.execute_query('select * from drivers order by RANDOM()')
    match_count = len(drivers)

    # first round matches must be a power of 2
    while not math.log2(match_count).is_integer():
        match_count = match_count + 1

    top_next = True
    got_two = False
    top_bracket = 1
    bottom_bracket = match_count / 2  # equals total matches
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
        driver_id = -1
        if i < len(drivers):
            driver_id = drivers[i]['id']

        # add the information to the matches bracket
        db.execute_update(utils.CREATE_MATCH_STATEMENT, [driver_id, 1, match_num])
        i = i + 1

    # create the rest of the matches (unknown drivers)
    games_left = len(drivers)
    current_level = 1
    if games_left % 2 == 1:
        # account for bye
        games_left = games_left + 1

    games_left = games_left / 2
    while games_left > 1:
        current_level = current_level + 1

        if games_left % 2 == 1:
            # account for bye
            games_left = games_left + 1

        i = 1
        while i <= games_left / 2:
            # make 2 for each match
            db.execute_update(utils.CREATE_MATCH_STATEMENT, [-1, current_level, i])
            db.execute_update(utils.CREATE_MATCH_STATEMENT, [-1, current_level, i])

            i = i + 1

        games_left = games_left / 2

    # get the first two drivers
    match1 = db.execute_query("select * from matches where bracket_level = ? and match_num = ?", [1, 1])

    # update the active info in the DB
    update_active_match(match1[0]['driver_id'], match1[1]['driver_id'], 1, 1, active_game)

    flash('Tournament Started')

    return redirect(url_for("admin"))


@app.route('/admin/stop_tournament', methods=['GET'])
def stop_tournament():
    if('is_admin' not in session):
        return redirect(url_for('login'))

    # stop the tournament
    db.execute_update("update settings set value=? where name = ?", ['false', 'tournament_active'])
    db.execute_update("delete from matches")

    flash('Tournament Stopped')

    return redirect(url_for("admin"))


@app.route('/admin/winner/<winner>', methods=['GET'])
def advance_tournament(winner):
    if('is_admin' not in session):
        return redirect(url_for('login'))

    settings = db.load_settings()

    # load both drivers
    player1 = db.find_driver(settings['player_1'])
    player2 = db.find_driver(settings['player_2'])

    # score of 1 is a WIN, 0 is a LOSS
    player1_score = 1 if int(winner) == player1['id'] else 0
    player2_score = 1 if int(winner) == player2['id'] else 0

    # update the match
    db.execute_update("update matches set score = ? where driver_id = ? and bracket_level = ? and match_num = ?",
                      [player1_score, player1['id'], settings['active_level'], settings['active_match']])
    db.execute_update("update matches set score = ? where driver_id = ? and bracket_level = ? and match_num = ?",
                      [player2_score, player2['id'], settings['active_level'], settings['active_match']])

    # determine who moves on
    if(player1_score > player2_score):
        # advance this player
        advance_player(player1['id'], int(settings['active_level']), int(settings['active_match']))

        # update wins/losses
        db.execute_update("update drivers set wins = ? where id = ?", [player1['wins'] + 1, player1['id']])
        db.execute_update("update drivers set losses = ? where id = ?", [player2['losses'] + 1, player2['id']])
    else:
        # advance this player
        advance_player(player2['id'], int(settings['active_level']), int(settings['active_match']))

        # update wins/losses
        db.execute_update("update drivers set losses = ? where id = ?", [player1['losses'] + 1, player1['id']])
        db.execute_update("update drivers set wins = ? where id = ?", [player2['wins'] + 1, player2['id']])

    # find the next matchup
    next_match = find_next_match(int(settings['active_level']), int(settings['active_match']))

    if(next_match is not None):
        update_active_match(next_match[0]['driver_id'], next_match[1]['driver_id'],
                            next_match[0]['bracket_level'], next_match[0]['match_num'], db.find_active_game())
    else:
        # Game over
        db.update_setting("game_over", "true")

    flash("Next Race!")
    return redirect(url_for("admin"))


@app.route('/admin/drivers', methods=['GET'])
def drivers():
    if('is_admin' not in session):
        return redirect(url_for('login'))

    drivers = db.execute_query("select * from drivers order by name asc")
    return render_template('drivers.html', active_page='drivers', drivers=drivers, settings=db.load_settings())


@app.route('/admin/delete_driver/<id>', methods=['GET'])
def delete_driver(id):
    if('is_admin' not in session):
        return redirect(url_for('login'))

    if(int(id) > 0):
        # delete a specific driver
        db.execute_update("delete from drivers where id = ?", [id])
        flash('Driver Deleted')
    else:
        # delete all the drivers
        db.execute_update('delete from drivers')
        flash('All Drivers Deleted')

    return redirect(url_for('drivers'))


def update_active_match(player1, player2, level, match, active_game):
    db.execute_update("update drivers set active = ?", ["false"])

    # set the active drivers
    db.execute_update("update drivers set active = ? where id = ?", ['true', player1])
    db.execute_update("update drivers set active = ? where id = ?", ['true', player2])

    db.update_setting('player_1', player1)
    db.update_setting('player_2', player2)

    # set the active match to the first one
    db.update_setting('active_match', match)
    db.update_setting('active_level', level)

    # get a cup for them to play
    cups = db.find_cups(active_game.get_id())
    cup_id = random.randint(0, len(cups) - 1)
    db.update_setting('active_cup', cups[cup_id]['name'])


def find_next_match(level, match):
    result = None

    # see if we can just increase the match
    while(db.find_count("select count(id) as total from matches where bracket_level = ? and match_num = ?",
                        [level, match + 1]) > 0 and result is None):
        # get the match and make sure it isn't a bye
        temp_match = db.execute_query("select * from matches where bracket_level= ? and match_num = ?",
                                      [level, match + 1])

        if(temp_match[0]['driver_id'] != -1 and temp_match[1]['driver_id'] != -1):
            # we're good to run this match
            result = temp_match
        else:
            match = match + 1

            # advance player if this is a bye
            if(temp_match[0]['driver_id'] != -1):
                advance_player(temp_match[0]['driver_id'], level, match)
            elif(temp_match[1]['driver_id'] != -1):
                advance_player(temp_match[1]['driver_id'], level, match)

    # if result still null go up one level
    if(result is None):
        if(db.find_count("select count(id) as total from matches where bracket_level = ? and match_num = ?",
                         [level + 1, 1])):
            result = db.execute_query("select * from matches where bracket_level = ? and match_num = ?",
                                      [level + 1, 1])

    return result


def advance_player(player, level, match):
    level = level + 1

    # match int needs to be even
    if(match % 2 != 0):
        match = match + 1
    match = match / 2  # represents next match in sequence

    # check if there is a next match
    if(db.find_count("select count(id) as total from matches where driver_id = -1 and bracket_level = ? and match_num = ? order by id asc",
                     [level, match]) > 0):
        next_match = db.execute_query("select * from matches where driver_id = -1 and bracket_level = ? and match_num = ?",
                                      [level, match], True)
        db.execute_update("update matches set driver_id = ? where id = ?",
                          [player, next_match['id']])


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
logging.info(f"Starting web service at http://IP:{args.port}")
logging.info(f"Admin page available at http://IP:{args.port}/admin")
app.run(debug=args.debug, host='0.0.0.0', port=args.port, use_reloader=False)
