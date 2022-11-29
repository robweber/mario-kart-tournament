"""
Most of this adapted from the Flask SQLite documentation:
https://flask.palletsprojects.com/en/2.2.x/patterns/sqlite3/

"""
import sqlite3
from flask import g
from . import utils
from .utils import ActiveGame


def _get_db():
    # open a database connection if there isn't one already
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = sqlite3.connect(utils.DATABASE_PATH)
        db.row_factory = _make_dicts
    return db


def _make_dicts(cursor, row):
    """function to use the DB row names to create a dict from the results"""
    return dict((cursor.description[idx][0], value)
                for idx, value in enumerate(row))


def execute_update(statement, args=()):
    """executes the given statement (insert or update)"""
    cur = _get_db()
    cur.execute(statement, args)
    cur.commit()


def execute_query(query, args=(), one=False):
    """execute the given SQL query and return the results

    :param query: the sql query
    :param args: arguments to fill in "?" syntax in the query
    :param one: True/False value, if True it will return only the first result from the query
    """
    cur = _get_db().execute(query, args)
    rv = cur.fetchall()
    cur.close()
    return (rv[0] if rv else None) if one else rv


def find_count(query, args=()):
    """executes a query that will return an integer value representing a count

    :param query: sql query, should include a count() keyword aliasa to "total"
    :param args: arguments to fill in "?" syntax in the query

    :returns: either an integer or None if query doesn't contain a value
    """
    result = None
    # execute the query
    response = execute_query(query, args, True)

    if(response is not None and 'total' in response):
        result = response['total']

    return result


# These are all helper methods to make some common lookups easier
def load_settings():
    """this is needed on almost every page load

    :returns: the settings in a dict {key:value}
    """
    result = {}
    # convert the settings list to a dict
    for s in execute_query("select * from settings"):
        result[s['name']] = s['value']

    return result


def get_setting(name):
    """returns the value for a specific named setting"""
    setting = execute_query("select value from settings where name = ?", [name], True)

    return setting['value']


def update_setting(name, value):
    """updates a single setting in the database"""
    execute_update("update settings set value = ? where name = ?", [value, name])


def find_active_game():
    """returns an object representing the currently selected active game"""
    return ActiveGame(execute_query(utils.ACTIVE_GAME_QUERY, ["active_game"], True), get_setting('game_mode'), get_setting('selected_cups'))


def find_driver(id):
    return execute_query("select * from drivers where id = ?", [id], True)


def find_all_cups(game_id):
    return execute_query(utils.FIND_ALL_CUPS_QUERY, [game_id])

def find_selected_cups(game_id, cup_list):
    all_cups = find_all_cups(game_id)
    # filter the list by the cups we want
    return list(filter(lambda c: c['id'] in cup_list, all_cups))
