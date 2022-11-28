# Mario Kart Tournament
[![PEP8](https://img.shields.io/badge/code%20style-pep8-orange.svg)](https://www.python.org/dev/peps/pep-0008/)

This is a simple web app that allows for the administration of a Mario Kart Tournament. The app consists of a page for end users to setup their driver, a scoreboard to view the current racer with a bracket, plus an admin page for administration.

## Install

Clone the repository and download the required libraries. Note that `sudo` is needed since we'll need root to bind to a socket later.

```
git clone https://github.com/robweber/mario-kart-tournament.git
cd mario-kart-tournament
sudo pip3 install -r requirements.txt
```

You'll also need to make a copy of the database to avoid messing the original version. The webservice expects to find it in a specific path.

```
cp mario-kart-tournment.db mario-kart-tournment.live.db
```

## Usage
Once the required features have been installed you can run the program with default options to start the web service on port 5000. Access it via a browser with `http://server_ip:5000/`.

```
sudo python3 main.py

```

To get a full list of command line arguments you can run:

```
python3 main.py -h
usage: main.py [-h] [-p PORT] [-D]

Mario Kart Tournament

optional arguments:
  -h, --help            show this help message and exit
  -p PORT, --port PORT  Port number to run the web server on, 5000 by default
  -D, --debug           If the program should run in debug mode
```

## Tournament Setup

To setup the tournament the Race Administrator can set the Mario Kart version to be played on the admin page at `http://server_ip:5000/admin`. Once this is set drivers can be added on the main page. Once the tournament is started drivers can no longer be added so only use this once you are ready to race.

To safeguard the admin page you can set a PIN that needs to be entered before entering the admin area. By default this is disabled but does help maintain control over the tournament if you're going to have others access the scoreboard and driver setup pages directly. 

### Adding a Drivers

Drivers can be added as long as the tournament is not started yet. When hitting the main page the currently added drivers will be shown along with a button to create new driver profiles. Once created a driver is ready to play in the tournament.

## Playing the Tournament

When the Race Administrator starts the tournament several things happen at once.

1. Creating new drivers is locked out.
2. Drivers are randomly seeded into a tournament bracket.
3. The first set of racers is selected and a Cup is selected from tracks from the active Mario Kart game.

On the scoreboard page racers can see who is up next and the overall status of the tournament via the bracket. Winning drivers are given a score of 1 and losses are a 0. Entering Fullscreen Mode removes the top nav bar for easier display on a large screen. The scoreboard page will automatically refresh data every 10 seconds.

### Advancing

On the `/admin` page the Race Administrator can select the winner from the currently active match. Selecting the winner will advance the tournament. This will update the scoreboard page within 10 seconds.

The winning driver will move on to the next race in the bracket and the losing player will drop off. Each driver's win/loss record will also be updated.

## Ending the Tournament

The tournament is over when the final match is played. The winning racer will be shown on both the scoreboard and admin pages. From here the Race Administrator can stop and reset the tournament.

__Note:__ stopping the tournament will cause a complete reset should it be started again. It is not possible to pause and resume.
