#!/usr/bin/python3
""" This script creates a hardcoded version of Druen's simulation using the api exposed to frontend.
A random user is created to facilitate the simulation.
Run it with `./create_sim.py`.
"""
import json
import random
import string
from functools import partial

import requests
from simplejson.errors import JSONDecodeError

host = 'http://98.235.235.188/api'

to_json = partial(json.dumps, indent=2, sort_keys=True)

def post(resource, data, message="Posting"): 
    json_data = to_json(data)
    print(f'{message} with the following data:\n{json_data}')
    response = requests.post(f'{host}/{resource}.php', data=json_data)
    if (response.status_code != 200):
        raise Exception(f'Failed to post! Status code {response.status_code}')
    else:
        try:
            response_json = response.json()
        except JSONDecodeError:
            response_json = 'none'
        print(f'Post succesful, response = {response_json}')
        return response_json

def random_string(num_chars):
    return ''.join(random.choices(string.ascii_letters + string.digits, k=num_chars))

def num_range(start, stop, step):
    return [i for i in range(start, stop+step, step)]

def main():
    num_chars=16
    username = random_string(num_chars)
    password = random_string(num_chars)
    user = {"username": username, "password": password}
    generic_user = {"user": user}
    post('CreateUser', generic_user, 'Creating user')

    result = post('SimulationInitialization', generic_user, 'Initializing sim')
    sim_id = result['simulation_id']

    resource_dict = {'player1_cash': 250000.0, 'player2_cash': 250000.0, 'environment': 500000.0}
    sim_modification = {'simulation_id': sim_id, 'user': user, 'name': "Professor Druen's awesome simulation",
                        'resources': resource_dict}
    post('SimulationModification', sim_modification, "Modifying sim")

    frame_initialization = {'simulation_id': sim_id, 'user': user}
    result = post('FrameInitialization', frame_initialization, "Initializing frame")
    frame_id = result['frame_id']

    effects_dict = {'player1_cash': [num_range(0, -6, -1), num_range(2, -4, -1), num_range(4, -2, -1),
                                     num_range(6, 0, -1), num_range(8, 2, -1), num_range(10, 4, -1),
                                     num_range(12, 6, -1)],
                    'player2_cash': [num_range(0, 12, 2), num_range(-1, 11, 2), num_range(-2, 10, 2),
                                     num_range(-3, 9, 2), num_range(-4, 8, 2), num_range(-5, 7, 2),
                                     num_range(-6, 6, 2)],
                    'environment': [num_range(-60, 0, 10), num_range(-50, 10, 10), num_range(-40, 20, 10),
                                    num_range(-30, 30, 10), num_range(-20, 40, 10), num_range(-10, 50, 10),
                                    num_range(0, 60, 10)]
                   }
    for name, resource in effects_dict.items():
        for i, row in enumerate(resource):
            for j, value in enumerate(row):
                effects_dict[name][i][j] = value/100.0
    frame_modification = {'frame_id': frame_id,'simulation_id': sim_id, 'user': user,
                          'rounds': [i for i in range(10)],
                          'prompt': "The environment level is at ${resources.environment}. "
                                    "Player1's cash is ${resources.player1_cash}. "
                                    "Player2's cash is ${resources.player2_cash}. "
                                    "How would you like to affect your production?",
                          'responses': ['15', '10', '5', '0', '-5', '-10', '-15'], 'effects': effects_dict
                         }
    post('FrameModification', frame_modification, "Modifying frame")

    print(f"Your sim id is {sim_id}")
if __name__ == '__main__':
    main();
