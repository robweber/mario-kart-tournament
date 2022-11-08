import argparse
import logging
import os.path
import sys
import modules.utils as utils
import modules.database as db
from flask import Flask, render_template, request, url_for, flash, redirect

# create the web service
app = Flask(import_name="mario-kart-tournament", static_folder=os.path.join(utils.DIR_PATH, 'web', 'static'),
            template_folder=os.path.join(utils.DIR_PATH, 'web', 'templates'))

# generate random number for session secret key
app.secret_key = os.urandom(24)


@app.route('/', methods=["GET"])
def index():
    return render_template("index.html", drivers=db.execute_query("select * from drivers"), settings=load_settings())


@app.route('/tournaments/add_driver', methods=["GET", "POST"])
def add_driver():

    # check if submitting the form
    logging.debug(request.form)
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


def load_settings():
    """this is needed on almost every page load

    :returns: the settings in a dict {key:value}
    """
    result = {}
    # convert the settings list to a dict
    for s in db.execute_query("select * from settings"):
        result[s['name']] = s['value']

    return result


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
