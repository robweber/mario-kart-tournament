# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)

## Version 2.6

### Added

- added `install/database.sql` file. This contains the schema and setup for the default database.
- added Wave 5 DLC Tracks for Mario Kart 8 Deluxe Booster Pass
- added Mario Kart 7 game

### Changed

- the database file is now generated automatically at runtime if it doesn't exist

### Removed

- removed the master SQLite template file in favor of using an SQL commands file

## Version 2.5

### Added

- added Wave 4 DLC Tracks for Mario Kart 8 Deluxe Booster Pass

### Fixed

- link to Mario Kart 8 Booster pass in README

## Version 2.4

### Added

- more driver avatars
- added Rock and Moon cups from Mario Kart 8 Wave 3 release

### Fixed

- attempted fix for unbalanced brackets, could probably refine this further

## Version 2.3

### Added

- added template service file and instructions to run app as a systemd service
- button to delete all drivers in admin
- ability to select which cups are a part of the tournament per game
- added more character avatars
- screenshot to top of README

### Changed

- moved Python lib requirement files to `install` directory
- split Mario Kart 8 (Wii U version) from Mario Kart 8 Deluxe (Switch) since Cups are different

### Fixed

- secondary levels of tournament bracket were not calculating right with some seeds
- minor layout issues
- pin entry should be a password field

## Version 2.2

### Added

- can now set a PIN mode to enter Admin area - leave blank to not use
- link to Admin area in footer
- GPLv3 license file

### Fixed

- web server port argument was not used properly
- remove consolation round information
- fixed bracket sizing so it works properly for any number of drivers
- can't delete driver while tournament is active

## Version 2.1

### Added

- added difficulty mode setting for the tournament (50cc, 100cc, etc). Can be set to "not used" to ignore
- added fullscreen mode for scoreboard page

### Changed

- scoreboard page refreshes async via ajax calls instead of full page refresh

### Fixed

- don't need special CSS for avatar images
- fixed delete driver page

## Version 2.0

### Added

- converted PHP web app to Python app using Flask
- additional avatar pics

### Changed

- Converted DB to SQLite database
- minor UI improvements
- modified DB to use linked tables for game to cup associations
- advance bracket through admin not score input from player

### Removed

- CakePHP files and LAMP stack information
