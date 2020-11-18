#!/usr/bin/python3
import os
import json
import tempfile
from argparse import ArgumentParser
from collections import defaultdict

import pyreadstat
from pandas import DataFrame

def main():
    """ Converts some ResponseRecords into an SPSS .sav file. A temporary file is created,
        and the path to file is returned. It is the caller's responsibility to delete the
        file when they are done with it.
        For now, this requires the ResponseRecords to be passed via stdin. The format is as follows:
            "{\"records\": [{...},{...}...]}"
        Keep in mind that all of the double quotes, except the outer pair, must be escaped.
        In the future, we should avoid loading all of the records into memory at once, by accessing
        the database directly from python, or writing the .sav directly from PHP
    """
    parser = ArgumentParser(description='Converts some ResponseRecords into an SPSS .sav file')
    parser.add_argument('records', type=str, help='The ResponseRecords to convert')
    args = parser.parse_args()
    records_dict = json.loads(args.records)
    records_df = DataFrame(records_dict['records'])

    records_sav = os.path.join('/tmp', f'{os.urandom(24).hex()}.sav')
    pyreadstat.write_sav(records_df, records_sav)
    print(records_sav)

if __name__ == "__main__":
    main()
