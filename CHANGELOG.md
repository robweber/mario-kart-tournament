# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)

## Version 2.3

### Added

- added template service file and instructions to run app as a systemd service

### Changed

- moved Python lib requirement files to `install` directory

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
