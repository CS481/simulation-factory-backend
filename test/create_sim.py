#!/usr/bin/python3
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

    effects_dict = {'player1_cash': [[2, 1, 0], [4, 3, 2], [6, 5, 4]],
                    'player2_cash': [[2, 4, 6], [1, 3, 5], [0, 2, 4]],
                    'environment_change': [[-20, -10, 0], [-10, 0, 10], [0, 10, 20]]
                   }
    for name, resource in effects_dict.items():
        for i, row in enumerate(resource):
            for j, value in enumerate(row):
                effects_dict[name][i][j] = value/100.0
    frame_modification = {'frame_id': frame_id,'simulation_id': sim_id, 'user': user,
                          'rounds': [i for i in range(10)],
                          'prompt': 'The environment level is at ${environment}. How would you like to affect your production?',
                          'responses': ['5, 0, -5'], 'effects': effects_dict
                         }
    post('FrameModification', frame_modification, "Modifying frame")

    print(f"Your sim id is {sim_id}")
if __name__ == '__main__':
    main();
