## Mario Kart Tournament

This is a simple web app that allows for the administration of a Mario Kart Tournament. The app consists of a page for end users to setup their driver and enter scores, a scoreboard to view the current racers and totals, and an admin page for administration. 

### Features

__Configurable Game__

Select which Mario Kart game you're using. This allows the app to randomly pick which cup the drivers will play as well as double check score entries against max possible score for that game. 

__Configurable Rounds__

Configure how many rounds each player will play when setting up the tournament. 

__Configurable Handicap__

You can set a handicap to seperate normal from advanced players. This will multiply the normal players score by what you enter. Put 1 here for no handicap. 

### Play

Drivers can only be created before the tournament is activated. Once the tournment is turned on in the Admin area you cannot add more drivers to the races. Turning the tournament off before it is completed will erase all scores. 

Once the tournament is turned on drivers will be randomly matched up for play. The app will also randomly select which cup in the Mario Kart game they will play against. Only advanced players can get "Special Cup" races. 

After the race is complete drivers can enter their score by using the driver URL to login as their player and enter the score. The scoreboard page refreshes every 10 seconds to keep current racers and scores up to date. Once both players have entered their score the next two racers and their cup are selected. If only one racer remains that driver will show a question mark as their opponent. This is meant to allow anyone to play against them with only the listed driver's score being entered. 

Once all rounds have been played for each driver the total is calculated and the winner displayed. 

### Endpoints

* Driver - / or /tournaments will allow you to setup a driver and interact with the tournament
* Scoreboard - /tournaments/scoreboard to see the scoreboard
* Admin - /admin

### Install

First install composer

```

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer

```

Then update the project from within the deployment directory

```

composer update

```