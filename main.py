import logging
import os.path
import modules.utils as utils
from flask import Flask, render_template

# create the web service
app = Flask(import_name="mario-kart-tournament", static_folder=os.path.join(utils.DIR_PATH, 'web', 'static'),
            template_folder=os.path.join(utils.DIR_PATH, 'web', 'templates'))

# generate random number for session secret key
app.secret_key = os.urandom(24)


@app.route('/', methods=["GET"])
def index():
    return render_template("index.html")


# run the web app
logging.info("Welcome to Mario Kart!")
logging.info("Starting web service")
app.run(debug=False, host='0.0.0.0', port=5000, use_reloader=False)
