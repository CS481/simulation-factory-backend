#!/usr/bin/python3
import re
import json
import os
import tempfile
from argparse import ArgumentParser
from collections import defaultdict

import pyreadstat
from pandas import DataFrame
from pymongo import MongoClient

def normalize(record):
    """Normalizes the data in the record, removes unnecessary fields, and returns the result"""
    del record['_id']
    del record['simulation_id']
    # Keep a copy of this dict, in case it has a key that would otherwise override it
    resources = record['resources']
    for key, value in resources.items():
        key = re.sub('[^\w]+', '', key)
        record[key] = value
    del record['resources']
    return record

def main():
    """ Converts some ResponseRecords into an SPSS .sav file. A temporary file is created,
        and the path to file is returned. It is the caller's responsibility to delete the
        file when they are done with it. IT is the caller's responsibility to ensure the
        user is authenticated to perform this action.

        Note that this script is not generic with respect to the database implementation. That will need to be fixed in the future.
    """
    parser = ArgumentParser(description='Converts some ResponseRecords into an SPSS .sav file')
    parser.add_argument('connection', type=str, help='The mongodb connection string')
    parser.add_argument('database', type=str, help='The mongodb database')
    parser.add_argument('simulation_id', type=str, help='The id of the simulation to dump')

    args = parser.parse_args()

    client = MongoClient(args.connection)
    db = client[args.database]
    records_list = []
    for record in db.ResponseRecords.find({'simulation_id': args.simulation_id}):
        norm_record = normalize(record)
        records_list.append(norm_record)
    # records_list = [normalize(record) for record in db.ResponseRecords.find({'simulation_id': args.simulation_id})]
    records_df = DataFrame(records_list)

    records_sav = os.path.join('/tmp', f'{os.urandom(24).hex()}.sav')
    pyreadstat.write_sav(records_df, records_sav)
    print(records_sav)

if __name__ == "__main__":
    main()
