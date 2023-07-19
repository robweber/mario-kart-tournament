BEGIN TRANSACTION;
DROP TABLE IF EXISTS "games";
CREATE TABLE IF NOT EXISTS "games" (
	"id"	INTEGER,
	"name"	TEXT NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "settings";
CREATE TABLE IF NOT EXISTS "settings" (
	"id"	INTEGER,
	"name"	TEXT NOT NULL,
	"value"	TEXT NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "drivers";
CREATE TABLE IF NOT EXISTS "drivers" (
	"id"	INTEGER,
	"name"	TEXT NOT NULL,
	"phone"	TEXT,
	"image"	TEXT NOT NULL,
	"wins"	INTEGER DEFAULT 0,
	"losses"	INTEGER DEFAULT 0,
	"active"	TEXT DEFAULT 'false',
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "matches";
CREATE TABLE IF NOT EXISTS "matches" (
	"id"	INTEGER,
	"driver_id"	INTEGER,
	"score"	INTEGER,
	"bracket_level"	INTEGER,
	"match_num"	INTEGER,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "game_cup";
CREATE TABLE IF NOT EXISTS "game_cup" (
	"game_id"	INTEGER,
	"cup_id"	INTEGER,
	PRIMARY KEY("game_id","cup_id")
);
DROP TABLE IF EXISTS "cups";
CREATE TABLE IF NOT EXISTS "cups" (
	"id"	INTEGER,
	"name"	TEXT NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "difficulty_modes";
CREATE TABLE IF NOT EXISTS "difficulty_modes" (
	"id"	INTEGER,
	"name"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "game_difficulty";
CREATE TABLE IF NOT EXISTS "game_difficulty" (
	"game_id"	INTEGER,
	"mode_id"	INTEGER,
	PRIMARY KEY("game_id","mode_id")
);
INSERT INTO "games" VALUES (1,'Mario Kart Wii'),
 (2,'Mario Kart 64'),
 (3,'Super Mario Kart - SNES'),
 (4,'Mario Kart: Double Dash!'),
 (6,'Mario Kart 8 Deluxe'),
 (7,'Mario Kart 8');
INSERT INTO "settings" VALUES (1,'tournament_active','false'),
 (2,'active_game','1'),
 (3,'player_1','14'),
 (4,'player_2','15'),
 (5,'active_cup','Shell'),
 (6,'game_over','false'),
 (7,'send_sms','false'),
 (8,'active_match','1'),
 (9,'active_level','1'),
 (10,'game_mode','50cc'),
 (11,'admin_pin',''),
 (12,'selected_cups','[1, 2, 4, 5, 6, 7, 8, 9]');
INSERT INTO "game_cup" VALUES (1,1),
 (1,2),
 (1,4),
 (1,5),
 (1,6),
 (1,7),
 (1,8),
 (1,9),
 (2,1),
 (2,2),
 (2,4),
 (2,5),
 (3,1),
 (3,2),
 (3,4),
 (3,5),
 (4,1),
 (4,2),
 (4,4),
 (4,5),
 (6,9),
 (6,8),
 (6,7),
 (6,6),
 (6,5),
 (6,4),
 (6,2),
 (6,1),
 (6,28),
 (6,27),
 (6,26),
 (6,25),
 (6,29),
 (6,30),
 (6,31),
 (6,32),
 (7,1),
 (7,2),
 (7,4),
 (7,5),
 (7,6),
 (7,7),
 (7,8),
 (7,9),
 (7,25),
 (7,26),
 (7,27),
 (7,28),
 (6,33),
 (6,34),
 (6,35),
 (6,36);
INSERT INTO "cups" VALUES (1,'Mushroom'),
 (2,'Flower'),
 (4,'Star'),
 (5,'Special'),
 (6,'Shell'),
 (7,'Banana'),
 (8,'Leaf'),
 (9,'Lightning'),
 (25,'Egg'),
 (26,'Triforce'),
 (27,'Crossing'),
 (28,'Bell'),
 (29,'Golden Dash'),
 (30,'Lucky Cat'),
 (31,'Turnip'),
 (32,'Propeller'),
 (33,'Rock'),
 (34,'Moon'),
 (35,'Fruit'),
 (36,'Boomerang');
INSERT INTO "difficulty_modes" VALUES (1,'50cc'),
 (2,'100cc'),
 (3,'150cc'),
 (4,'Mirror Mode'),
 (5,'200cc'),
 (6,'Extra');
INSERT INTO "game_difficulty" VALUES (1,1),
 (1,2),
 (1,3),
 (1,4),
 (2,1),
 (2,2),
 (2,3),
 (2,6),
 (3,1),
 (3,2),
 (3,3),
 (4,1),
 (4,2),
 (4,3),
 (4,4),
 (5,1),
 (5,2),
 (5,3),
 (5,4),
 (5,5),
 (6,1),
 (6,2),
 (6,3),
 (6,4),
 (6,5),
 (7,1),
 (7,2),
 (7,3),
 (7,4),
 (7,5);
COMMIT;
