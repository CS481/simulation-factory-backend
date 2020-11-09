#!/usr/bin/python3
""" This script creates a hardcoded version of Druen's simulation using the api exposed to frontend.
A random user is created to facilitate the simulation.
Run it with `./create_sim.py`.
"""
import json
import random
import string
from functools import partial
import os

import requests
from simplejson.errors import JSONDecodeError

host = os.environ['REACT_APP_SIMULATION_FACTORY_URL']

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

    resource_dict = {'infected': 1000.0, 'sacrificers': 50.0, 'food': 200.0}
    sim_modification = {'simulation_id': sim_id, 'user': user, 'name': "Zombie survival",
                        'resources': resource_dict, 'response_timeout': 180}
    post('SimulationModification', sim_modification, "Modifying sim")

    frame_initialization = {'simulation_id': sim_id, 'user': user}
    result = post('FrameInitialization', frame_initialization, "Initializing frame")
    frame_id = result['frame_id']

    effects_dict = {'infected': [[0.0, 0.05, 0.1],[0.05, 0.1, 0.15],[0.1, 0.15, 0.2]],
                    'sacrificers': [[0, 0, 0], [0, 0, 0], [0, 0, 0]],
                    'food': [[-0.4, -0.1, 0.0],[-0.3, -0.2, 0.2],[0.0, 0.2, 0.4]]
                   }
    frame_modification = {'frame_id': frame_id,'simulation_id': sim_id, 'user': user,
                          'rounds': [0],
                          'prompt': "You are the leader of a small country that is experiencing a zombie outbreak. "
                                    "There are ${resources.infected} infected, and you have ${resources.food} days of food stockpiled. "
                                    "How would you like to respond to the zombie outbreak?",
                          'responses': ['Arm the people', 'Vaccine Research', 'Gather food'], 'effects': effects_dict,
                          'default_action': 'Arm the people'
                         }
    post('FrameModification', frame_modification, "Modifying frame")

    result = post('FrameInitialization', frame_initialization, "Initializing frame")
    frame_id = result['frame_id']

    effects_dict = {'infected': [[0.0]],
                    'sacrificers': [[0.0]],
                    'food': [[0.0]]
                   }
    frame_modification = {'frame_id': frame_id,'simulation_id': sim_id, 'user': user,
                          'rounds': [1],
                          'prompt': "Oh no! A small cult has arisen. They believe that sacrificing healthy individuals "
                                    "to the zombie hordes will appease their god.",
                          'responses': ['Ok'], 'effects': effects_dict, 'default_action': 'Ok'
                         }
    post('FrameModification', frame_modification, "Modifying frame")

    result = post('FrameInitialization', frame_initialization, "Initializing frame")
    frame_id = result['frame_id']

    effects_dict = {'infected': [[0.0]],
                    'sacrificers': [[0.0]],
                    'food': [[0.0]]
                    }
    frame_modification = {'frame_id': frame_id,'simulation_id': sim_id, 'user': user,
                          'rounds': [2],
                          'prompt': "You are the leader of a small country that is experiencing a zombie outbreak. "
                                    "There are ${resources.infected} infected, and you have ${resources.food} days of food stockpiled. "
                                    "There are ${resources.sacrificers} cultists sacrificing your people. "
                                    "How would you like to respond to the zombie outbreak?",
                          'responses': ['Arm the people', 'Vaccine Research', 'Gather food', 'Give those cultists a taste of their own medicine'],
                          'effects': effects_dict, 'default_action': 'Arm the people'
                         }
    post('FrameModification', frame_modification, "Modifying frame")

    print(f"Your sim id is {sim_id}")
if __name__ == '__main__':
    main();
