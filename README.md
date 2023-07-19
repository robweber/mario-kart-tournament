
# Mario Kart Tournament
![alt text](https://github.com/robweber/mario-kart-tournament/raw/main/install/bracket.png "Bracket Example")
[![License](https://img.shields.io/github/license/robweber/mario-kart-tournament)](https://github.com/robweber/mario-kart-tournament/blob/main/LICENSE)
[![standard-readme compliant](https://img.shields.io/badge/readme%20style-standard-brightgreen.svg)](https://github.com/RichardLitt/standard-readme)
[![PEP8](https://img.shields.io/badge/code%20style-pep8-orange.svg)](https://www.python.org/dev/peps/pep-0008/)

Host your own Mario Kart Tournament with this web app. Features include the ability for participants to setup their driver and view the current race. A scoreboard page shows the tournament bracket with an admin interface to control the tournament flow. A database with various Mario Kart games is included to select the game you want to play along with which Cups are available for the competition.

## Table of Contents

- [Install](#install)
- [Usage](#usage)
- [Tournament Setup](#tournament-setup)
  - [Adding Drivers](#adding-drivers)
- [Playing The Tournament](#playing-the-tournament)
  - [Advancing](#advancing)
- [Ending The Tournament](#ending-the-tournament)
- [FAQ](#faq)
- [Thanks](#thanks)
- [Contributing](#contributing)
- [License](#license)

## Install

Clone the repository and download the required libraries. Note that `sudo` is needed since we'll need root to bind to a socket later. It's assumed you have a working version of Python 3 installed along with [Pip](https://pypi.org/project/pip/).

```
git clone https://github.com/robweber/mario-kart-tournament.git
cd mario-kart-tournament
sudo -H pip3 install -r install/requirements.txt
```

The database schema resides in `install/database.sql` and an SQLite database is automatically generated if it doesn't exist when the program loads.

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

### Install As Service

There is a template service file `install/mario-kart-tournament.service` that can be modified to run the app as a service on Linux systems that use systemd. First, modify the `WorkingDirectory` variable to point to the absolute path of the `mario-kart-tournament` folder. You can also modify the command line arguments, if needed, in the `ExecStart` command. Install the service with the following commands.

```
sudo cp install/mario-kart-tournament.service /etc/systemd/system/mario-kart-tournament.service
sudo chown root:root /etc/systemd/system/mario-kart-tournament.service
sudo systemctl enable mario-kart-tournament

# start the service
sudo systemctl start mario-kart-tournament

# stop the Service
sudo systemctl stop mario-kart-tournament
```

## Tournament Setup

To setup the tournament the Race Administrator can set the Mario Kart version to be played on the admin page at `http://server_ip:5000/admin`. The administrator can also determine what Cups to be used for the matches. By default all Cups for the selected game are available; however you can customize this on the setup page. Additionally the game mode (50cc, 100cc, etc) can also be set if desired.

Once configured drivers can be added on the main page. Once the tournament is started drivers can no longer be added so only start things once you are ready to race.

To safeguard the admin page you can set a PIN that needs to be entered before entering the admin area. By default this is disabled but does help maintain control over the tournament if you're going to have others access the scoreboard and driver setup pages directly.

### Adding Drivers

Drivers can be added as long as the tournament is not started yet. When hitting the main page the currently added drivers will be shown along with a button to create new driver profiles. Once created a driver is ready to play in the tournament.

## Playing The Tournament

When the Race Administrator starts the tournament several things happen at once.

1. Creating new drivers is locked out.
2. Drivers are randomly seeded into a tournament bracket.
3. The first set of racers is selected and a Cup is selected from tracks from the active Mario Kart game.

On the scoreboard page racers can see who is up next and the overall status of the tournament via the bracket. Winning drivers are given a score of 1 and losses are a 0. Entering Fullscreen Mode removes the top nav bar for easier display on a large screen. The scoreboard page will automatically refresh data every 10 seconds.

### Advancing Play

On the `/admin` page the Race Administrator can select the winner from the currently active match. Selecting the winner will advance the tournament. This will update the scoreboard page within 10 seconds.

The winning driver will move on to the next race in the bracket and the losing player will drop off. Each driver's win/loss record will also be updated.

## Ending The Tournament

The tournament is over when the final match is played. The winning racer will be shown on both the scoreboard and admin pages. From here the Race Administrator can stop and reset the tournament.

__Note:__ stopping the tournament will cause a complete reset should it be started again. It is not possible to pause and resume.

## FAQ

There are a few questions, caveats or _gotchas_ that made sense to document as FAQs below.

1. What kind of OS can this run on?

>I've been running this on Debian type versions of Linux but in theory anything you can get Python 3 and the dependencies up and running on should work. Small form factor computers, like a Raspberry Pi, should also work as long as you don't have a huge amount of web traffic to the web app.

2. Can I manually seed the bracket?

>In short, no. The bracket is seeded randomly at the start of the tournament. It would be possible to add this as a feature but not something I'm interested in doing. For my use case the random seeding was enough.

3. I really messed things up, can I recreate the database?

>Yes. Delete the `mario-kart-tournament.live.db` file and restart the program. Load the main page and the database will be recreated from scratch.

4. I selected a different Mario Kart version but the Cup listings are wrong.

>This is sort of a quirk in the system. Due partly to my own laziness in the setup area the Cup listing is pulled in after a page refresh, not loaded in the background. If you select a new Kart version and hit __Update Settings__ the correct Cups will be populated, as well as the proper Game Modes.

5. The Mario Kart version I wanted is not listed.

>Most console versions of Mario Kart are pre-loaded into the database. These include the following:

>* Mario Kart 64 (Nintendo 64 version)
>* Mario Kart 8 (Wii U version)
>* Mario Kart 8 Deluxe (Switch version)
>* Mario Kart Wii (Wii version)
>* Mario Kart: Double Dash (Gamecube version)
>* Super Mario Kart (SNES version)

>These were deemed the best for tournament play (by me) since they were available on multiplayer consoles. It is possible to add more games to the database as it's just an SQLite file. You can add it yourself (via SQL) or open an [Issue](https://github.com/robweber/mario-kart-tournament/issues) if you'd like to see one added that doesn't exist.

6. In Mario Kart 8 Deluxe I don't have all the Cups listed.

>Mario Kart 8 Deluxe for the Switch is a bit of an outlier in that it has additional "pay to play" type bonus tracks called the [Booster Course Pass](https://mariokart8.nintendo.com/booster-course-pass/). You have to either pay for these outright or subscribe to Nintendo's Online membership system. If you don't have these Cups available simply deselect them from play prior to starting the tournament.

## Thanks

Special thanks to the [JQuery Bracket Library](https://github.com/teijo/jquery-bracket). This is used to display the tournament provides a lot of the heavy lifting for the UI of this project.

## Contributing

If you want to expand on this feel free to fork the repo or submit pull requests. No guarantees anything will be merged in but features that enhance the usability, while not being overly specific to a single use case, will be considered. Mario Kart is a family favorite so this project was just something fun for family get togethers.

## License

[GPLv3](https://github.com/robweber/mario-kart-tournament/blob/main/LICENSE)
