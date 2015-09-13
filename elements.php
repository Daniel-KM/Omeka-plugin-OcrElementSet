<?php
$elementSetMetadata = array(
    'name' => 'OCR',
    'description' => 'OCR metadata and text.',
    'record_type' => 'File',
);

// Note: only labels are used.
$elements = array(
    array(
        'name' => 'Text',
        'label' => 'Text',
        'old label' => 'Texte auto',
        'record_type' => 'File',
        'data_type' => 'Text',
        'description' => 'Text from the optical character recognition.',
    ),
    // Data in json for advanced processing (search, highlight, correction...).
    // The value is an array of each string, with its position (starting from 0,
    // and with count of spaces, as in the one line text), and an array for
    // content, position x, y, width, height, and eventually the quality (by
    // character). It the content is hyphenated, the values are in sub-arrays.
    /*
    {"String":{
        "0":["Omega",[
            ["Ome", 306,307,62,27,"010"],
            ["-",368,307,18,27,"1"],
            ["ga",130,343,45,27,"10"]
        ]],
        "6":["3",175,343,22,27,"1"],
        "7":["!",197,343,12,27,"1"]
    }}
    */
    // This array is the words "Omega 3!" cut at the end of a line "Ome- ga 3!".
    // The matching one line text is "Omega 3!". The position of the word "3"
    // is determined directly (6th character, starting from 0).
    //
    // Deprecated version:
    /*
    {"String":{
        "0":["Ome",306,307,62,27,"010","Omega"],
        "3":["-",368,307,18,27,"1"],
        "5":["ga",130,343,45,27,"10","Omega"],
        "8":["3",175,343,22,27,"1"],
        "9":["!",197,343,12,27,"1"]
    }}
    */
    // This array is the words "Omega 3!" cut at the end of a line "Ome- ga 3!".
    // The matching one line text is "Omega 3!". The position of the word "3"
    // is determined directly (8th character, starting from 0, minus one hyphen
    // and its following space).
    //
    // Warning: Data can be heavy and they are duplicated by default in the
    // search table of the base.
    array(
        'name' => 'Data',
        'label' => 'Data',
        'old label' => 'Mot-à-mot',
        'record_type' => 'File',
        'data_type' => 'Text',
        'description' => 'Detailled OCR in json for advanced processing (highlight, correction...).',
    ),
    array(
        'name' => 'Process',
        'label' => 'Process',
        'record_type' => 'File',
        'data_type' => 'String',
        'description' => 'Data about the OCR process (creator, software, statistic...).',
    ),
);
