<?php

$fixtures = array (
  'symmetric_headers' =>
  array (
    0 => 'header_a',
    1 => 'header_b',
    2 => 'header_c',
    3 => 'header_d',
    4 => 'header_e',
  ),
  'rows_from_one_row_only' => array (
    array (
      0 => '1',
      1 => '2',
      2 => '3',
    ),
  ),
  'rows_from_one_row_only_plus_one_filled_with_num_1' => array (
    array (
      0 => '1',
      1 => '1',
      2 => '1',
    ),
  ),
  'rows_from_one_row_only_plus_one_filled_with_str_hello' => array (
    array (
      0 => 'hello',
      1 => 'hello',
      2 => 'hello',
    ),
  ),
  'rows_from_one_row_only_plus_one_filled_with_arr_abc' => array (
    array (
      0 => 'a',
      1 => 'b',
      2 => 'c',
    ),
  ),
  'symmetric_raw_data_with_second_column_removed' =>
  array (
    0 =>
    array (
      0 => 'header_a',
      1 => 'header_c',
      2 => 'header_d',
      3 => 'header_e',
    ),
    1 =>
    array (
      0 => '1aa',
      1 => '1cc',
      2 => '1dd',
      3 => '1ee',
    ),
    2 =>
    array (
      0 => '2aa',
      1 => '2cc',
      2 => '2dd',
      3 => '2ee',
    ),
    3 =>
    array (
      0 => '3aa',
      1 => '3cc',
      2 => '3dd',
      3 => '3ee',
    ),
    4 =>
    array (
      0 => '4aa',
      1 => '4cc',
      2 => '4dd',
      3 => '4ee',
    ),
    5 =>
    array (
      0 => '5aa',
      1 => '5cc',
      2 => '5dd',
      3 => '5ee',
    ),
    6 =>
    array (
      0 => '6aa',
      1 => '6cc',
      2 => '6dd',
      3 => '6ee',
    ),
    7 =>
    array (
      0 => '7aa',
      1 => '7cc',
      2 => '7dd',
      3 => '7ee',
    ),
    8 =>
    array (
      0 => '8aa',
      1 => '8cc',
      2 => '8dd',
      3 => '8ee',
    ),
    9 =>
    array (
      0 => '9aa',
      1 => '9cc',
      2 => '9dd',
      3 => '9ee',
    )
  ),
  'symmetric_raw_data_with_last_colum_removed' =>
  array (
    0 =>
    array (
      0 => 'header_a',
      1 => 'header_b',
      2 => 'header_c',
      3 => 'header_d',
    ),
    1 =>
    array (
      0 => '1aa',
      1 => '1bb',
      2 => '1cc',
      3 => '1dd',
    ),
    2 =>
    array (
      0 => '2aa',
      1 => '2bb',
      2 => '2cc',
      3 => '2dd',
    ),
    3 =>
    array (
      0 => '3aa',
      1 => '3bb',
      2 => '3cc',
      3 => '3dd',
    ),
    4 =>
    array (
      0 => '4aa',
      1 => '4bb',
      2 => '4cc',
      3 => '4dd',
    ),
    5 =>
    array (
      0 => '5aa',
      1 => '5bb',
      2 => '5cc',
      3 => '5dd',
    ),
    6 =>
    array (
      0 => '6aa',
      1 => '6bb',
      2 => '6cc',
      3 => '6dd',
    ),
    7 =>
    array (
      0 => '7aa',
      1 => '7bb',
      2 => '7cc',
      3 => '7dd',
    ),
    8 =>
    array (
      0 => '8aa',
      1 => '8bb',
      2 => '8cc',
      3 => '8dd',
    ),
    9 =>
    array (
      0 => '9aa',
      1 => '9bb',
      2 => '9cc',
      3 => '9dd',
    ),
  ),

  'first_symmetric_header' => 'header_a',
  'first_symmetric_cell' => '1aa',

  'symmetric_extra_header' =>
  array (
    0 => 'header_a',
    1 => 'header_b',
    2 => 'header_c',
    3 => 'header_d',
    4 => 'header_e',
    5 => 'extra',
  ),
  'first_row_from_symmetric' =>
  array (
    0 => '1aa',
    1 => '1bb',
    2 => '1cc',
    3 => '1dd',
    4 => '1ee',
  ),
  'eighth_row_from_symmetric' =>
  array (
    0 => '9aa',
    1 => '9bb',
    2 => '9cc',
    3 => '9dd',
    4 => '9ee',
  ),
  'valid_files' =>
  array (
    'empty.csv' => 'emtpy csv file',
    'uppercased.CSV' => 'upper cased extension',
    'multcased.CsV' => 'multiple cased extension',
    'symmetric.csv' => 'symmetric data',
    'asymmetric.csv' => 'asymmetric data',
    'escape_ok.csv' => 'valid escape syntax file',
    'escape_ok.csv' => 'valid escape syntax file',
    'non_csv_extension.txt' => 'non csv-extension file',
  ),
  'expected_headers' =>
  array (
    0 => 'COL_1',
    1 => 'COL_2',
    2 => 'COL_3',
    3 => 'COL_4',
    4 => 'COL_5',
  ),
  'original_headers' =>
  array (
    0 => 'header_a',
    1 => 'header_b',
    2 => 'header_c',
    3 => 'header_d',
    4 => 'header_e',
  ),
  'symmetric_connection' =>
  array (
    0 =>
    array (
      'header_a' => '1aa',
      'header_b' => '1bb',
      'header_c' => '1cc',
      'header_d' => '1dd',
      'header_e' => '1ee',
    ),
    1 =>
    array (
      'header_a' => '2aa',
      'header_b' => '2bb',
      'header_c' => '2cc',
      'header_d' => '2dd',
      'header_e' => '2ee',
    ),
    2 =>
    array (
      'header_a' => '3aa',
      'header_b' => '3bb',
      'header_c' => '3cc',
      'header_d' => '3dd',
      'header_e' => '3ee',
    ),
    3 =>
    array (
      'header_a' => '4aa',
      'header_b' => '4bb',
      'header_c' => '4cc',
      'header_d' => '4dd',
      'header_e' => '4ee',
    ),
    4 =>
    array (
      'header_a' => '5aa',
      'header_b' => '5bb',
      'header_c' => '5cc',
      'header_d' => '5dd',
      'header_e' => '5ee',
    ),
    5 =>
    array (
      'header_a' => '6aa',
      'header_b' => '6bb',
      'header_c' => '6cc',
      'header_d' => '6dd',
      'header_e' => '6ee',
    ),
    6 =>
    array (
      'header_a' => '7aa',
      'header_b' => '7bb',
      'header_c' => '7cc',
      'header_d' => '7dd',
      'header_e' => '7ee',
    ),
    7 =>
    array (
      'header_a' => '8aa',
      'header_b' => '8bb',
      'header_c' => '8cc',
      'header_d' => '8dd',
      'header_e' => '8ee',
    ),
    8 =>
    array (
      'header_a' => '9aa',
      'header_b' => '9bb',
      'header_c' => '9cc',
      'header_d' => '9dd',
      'header_e' => '9ee',
    ),
  ),
  'asymmetric_rows' =>
  array (
    0 =>
    array (
      0 => '5aa',
      1 => '5bb',
      2 => '5cc',
      3 => '5dd',
      4 => '5ee',
      5 => 'extra1',
    ),
    1 =>
    array (
      0 => '8aa',
      1 => '8bb',
      2 => '8cc',
      3 => '8dd',
      4 => '8ee',
      5 => 'extra2',
    ),
  ),
  'empty_column' =>
   array (
     0 => '',
     1 => '',
     2 => '',
     3 => '',
     4 => '',
     5 => '',
     6 => '',
     7 => '',
     8 => '',
  ),
  'expected_column' =>
  array (
    0 => '1cc',
    1 => '2cc',
    2 => '3cc',
    3 => '4cc',
    4 => '5cc',
    5 => '6cc',
    6 => '7cc',
    7 => '8cc',
    8 => '9cc',
  ),
  'symmetric_rows_without_first_row' =>
  array (
    0 =>
    array (
      0 => '2aa',
      1 => '2bb',
      2 => '2cc',
      3 => '2dd',
      4 => '2ee',
    ),
    1 =>
    array (
      0 => '3aa',
      1 => '3bb',
      2 => '3cc',
      3 => '3dd',
      4 => '3ee',
    ),
    2 =>
    array (
      0 => '4aa',
      1 => '4bb',
      2 => '4cc',
      3 => '4dd',
      4 => '4ee',
    ),
    3 =>
    array (
      0 => '5aa',
      1 => '5bb',
      2 => '5cc',
      3 => '5dd',
      4 => '5ee',
    ),
    4 =>
    array (
      0 => '6aa',
      1 => '6bb',
      2 => '6cc',
      3 => '6dd',
      4 => '6ee',
    ),
    5 =>
    array (
      0 => '7aa',
      1 => '7bb',
      2 => '7cc',
      3 => '7dd',
      4 => '7ee',
    ),
    6 =>
    array (
      0 => '8aa',
      1 => '8bb',
      2 => '8cc',
      3 => '8dd',
      4 => '8ee',
    ),
    7 =>
    array (
      0 => '9aa',
      1 => '9bb',
      2 => '9cc',
      3 => '9dd',
      4 => '9ee',
    ),
  ),
  'symmetric_rows_without_third_row' =>
  array (
    0 =>
    array (
      0 => '1aa',
      1 => '1bb',
      2 => '1cc',
      3 => '1dd',
      4 => '1ee',
    ),
    1 =>
    array (
      0 => '2aa',
      1 => '2bb',
      2 => '2cc',
      3 => '2dd',
      4 => '2ee',
    ),
    2 =>
    array (
      0 => '4aa',
      1 => '4bb',
      2 => '4cc',
      3 => '4dd',
      4 => '4ee',
    ),
    3 =>
    array (
      0 => '5aa',
      1 => '5bb',
      2 => '5cc',
      3 => '5dd',
      4 => '5ee',
    ),
    4 =>
    array (
      0 => '6aa',
      1 => '6bb',
      2 => '6cc',
      3 => '6dd',
      4 => '6ee',
    ),
    5 =>
    array (
      0 => '7aa',
      1 => '7bb',
      2 => '7cc',
      3 => '7dd',
      4 => '7ee',
    ),
    6 =>
    array (
      0 => '8aa',
      1 => '8bb',
      2 => '8cc',
      3 => '8dd',
      4 => '8ee',
    ),
    7 =>
    array (
      0 => '9aa',
      1 => '9bb',
      2 => '9cc',
      3 => '9dd',
      4 => '9ee',
    ),
  ),
  'one_row_for_symmetric' => array (
      0 => '10aa',
      1 => '10bb',
      2 => '10cc',
      3 => '10dd',
      4 => '10ee',
    ),
  'symmetric_rows_plus_one' => // contains 'one_row_for_symmetric'
  array (
    0 =>
    array (
      0 => '1aa',
      1 => '1bb',
      2 => '1cc',
      3 => '1dd',
      4 => '1ee',
    ),
    1 =>
    array (
      0 => '2aa',
      1 => '2bb',
      2 => '2cc',
      3 => '2dd',
      4 => '2ee',
    ),
    2 =>
    array (
      0 => '3aa',
      1 => '3bb',
      2 => '3cc',
      3 => '3dd',
      4 => '3ee',
    ),
    3 =>
    array (
      0 => '4aa',
      1 => '4bb',
      2 => '4cc',
      3 => '4dd',
      4 => '4ee',
    ),
    4 =>
    array (
      0 => '5aa',
      1 => '5bb',
      2 => '5cc',
      3 => '5dd',
      4 => '5ee',
    ),
    5 =>
    array (
      0 => '6aa',
      1 => '6bb',
      2 => '6cc',
      3 => '6dd',
      4 => '6ee',
    ),
    6 =>
    array (
      0 => '7aa',
      1 => '7bb',
      2 => '7cc',
      3 => '7dd',
      4 => '7ee',
    ),
    7 =>
    array (
      0 => '8aa',
      1 => '8bb',
      2 => '8cc',
      3 => '8dd',
      4 => '8ee',
    ),
    8 =>
    array (
      0 => '9aa',
      1 => '9bb',
      2 => '9cc',
      3 => '9dd',
      4 => '9ee',
    ),
    9 =>
    array (
      0 => '10aa',
      1 => '10bb',
      2 => '10cc',
      3 => '10dd',
      4 => '10ee',
    ),
  ),
  'symmetric_rows_empty' =>
  array (
    0 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    1 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    2 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    3 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    4 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    5 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    6 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    7 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
    8 =>
    array (
      0 => '',
      1 => '',
      2 => '',
      3 => '',
      4 => '',
    ),
  ),
  'symmetric_rows' =>
  array (
    0 =>
    array (
      0 => '1aa',
      1 => '1bb',
      2 => '1cc',
      3 => '1dd',
      4 => '1ee',
    ),
    1 =>
    array (
      0 => '2aa',
      1 => '2bb',
      2 => '2cc',
      3 => '2dd',
      4 => '2ee',
    ),
    2 =>
    array (
      0 => '3aa',
      1 => '3bb',
      2 => '3cc',
      3 => '3dd',
      4 => '3ee',
    ),
    3 =>
    array (
      0 => '4aa',
      1 => '4bb',
      2 => '4cc',
      3 => '4dd',
      4 => '4ee',
    ),
    4 =>
    array (
      0 => '5aa',
      1 => '5bb',
      2 => '5cc',
      3 => '5dd',
      4 => '5ee',
    ),
    5 =>
    array (
      0 => '6aa',
      1 => '6bb',
      2 => '6cc',
      3 => '6dd',
      4 => '6ee',
    ),
    6 =>
    array (
      0 => '7aa',
      1 => '7bb',
      2 => '7cc',
      3 => '7dd',
      4 => '7ee',
    ),
    7 =>
    array (
      0 => '8aa',
      1 => '8bb',
      2 => '8cc',
      3 => '8dd',
      4 => '8ee',
    ),
    8 =>
    array (
      0 => '9aa',
      1 => '9bb',
      2 => '9cc',
      3 => '9dd',
      4 => '9ee',
    ),
  ),
  'symmetric_raw_data' =>
  array (
    0 =>
    array (
      0 => 'header_a',
      1 => 'header_b',
      2 => 'header_c',
      3 => 'header_d',
      4 => 'header_e',
    ),
    1 =>
    array (
      0 => '1aa',
      1 => '1bb',
      2 => '1cc',
      3 => '1dd',
      4 => '1ee',
    ),
    2 =>
    array (
      0 => '2aa',
      1 => '2bb',
      2 => '2cc',
      3 => '2dd',
      4 => '2ee',
    ),
    3 =>
    array (
      0 => '3aa',
      1 => '3bb',
      2 => '3cc',
      3 => '3dd',
      4 => '3ee',
    ),
    4 =>
    array (
      0 => '4aa',
      1 => '4bb',
      2 => '4cc',
      3 => '4dd',
      4 => '4ee',
    ),
    5 =>
    array (
      0 => '5aa',
      1 => '5bb',
      2 => '5cc',
      3 => '5dd',
      4 => '5ee',
    ),
    6 =>
    array (
      0 => '6aa',
      1 => '6bb',
      2 => '6cc',
      3 => '6dd',
      4 => '6ee',
    ),
    7 =>
    array (
      0 => '7aa',
      1 => '7bb',
      2 => '7cc',
      3 => '7dd',
      4 => '7ee',
    ),
    8 =>
    array (
      0 => '8aa',
      1 => '8bb',
      2 => '8cc',
      3 => '8dd',
      4 => '8ee',
    ),
    9 =>
    array (
      0 => '9aa',
      1 => '9bb',
      2 => '9cc',
      3 => '9dd',
      4 => '9ee',
    ),
  ),
  'expected_raw' =>
  array (
    0 =>
    array (
      0 => 'h_one',
      1 => 'h_two',
      2 => 'h_three',
    ),
    1 =>
    array (
      0 => 'v_1one',
      1 => 'v_1two',
      2 => 'v_1three',
    ),
    2 =>
    array (
      0 => 'v_2one',
      1 => 'v_2two',
      2 => 'v_2three',
    ),
    3 =>
    array (
      0 => 'v_3one',
      1 => 'v_3two',
      2 => 'v_3three',
    ),
  ),
  'expected_escaped' =>
  array (
    0 =>
    array (
      'one' => 'thie,',
      'two' => 'adn',
      'three' => 'thei',
    ),
    1 =>
    array (
      'one' => 'thie',
      'two' => 'adn',
      'three' => 'thei',
    ),
  ),
  'header_a_connection' =>
  array (
    0 =>
    array (
      'header_a' => '1aa',
    ),
    1 =>
    array (
      'header_a' => '2aa',
    ),
    2 =>
    array (
      'header_a' => '3aa',
    ),
    3 =>
    array (
      'header_a' => '4aa',
    ),
    4 =>
    array (
      'header_a' => '5aa',
    ),
    5 =>
    array (
      'header_a' => '6aa',
    ),
    6 =>
    array (
      'header_a' => '7aa',
    ),
    7 =>
    array (
      'header_a' => '8aa',
    ),
    8 =>
    array (
      'header_a' => '9aa',
    ),
  ),
  'symmetric_queries' =>
  array (
    0 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'1aa\', \'1bb\', \'1cc\', \'1dd\', \'1ee\')',
    1 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'2aa\', \'2bb\', \'2cc\', \'2dd\', \'2ee\')',
    2 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'3aa\', \'3bb\', \'3cc\', \'3dd\', \'3ee\')',
    3 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'4aa\', \'4bb\', \'4cc\', \'4dd\', \'4ee\')',
    4 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'5aa\', \'5bb\', \'5cc\', \'5dd\', \'5ee\')',
    5 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'6aa\', \'6bb\', \'6cc\', \'6dd\', \'6ee\')',
    6 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'7aa\', \'7bb\', \'7cc\', \'7dd\', \'7ee\')',
    7 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'8aa\', \'8bb\', \'8cc\', \'8dd\', \'8ee\')',
    8 => 'INSERT INTO test_table (header_a, header_b, header_c, header_d, header_e) VALUES (\'9aa\', \'9bb\', \'9cc\', \'9dd\', \'9ee\')',
  ),
  'alternated_header_queries' =>
  array (
    0 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'1aa\', \'1cc\')',
    1 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'2aa\', \'2cc\')',
    2 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'3aa\', \'3cc\')',
    3 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'4aa\', \'4cc\')',
    4 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'5aa\', \'5cc\')',
    5 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'6aa\', \'6cc\')',
    6 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'7aa\', \'7cc\')',
    7 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'8aa\', \'8cc\')',
    8 => 'INSERT INTO test_table (header_a, header_c) VALUES (\'9aa\', \'9cc\')',
  ),
  'symmetric_range_of_rows' =>
  array (
    0 =>
    array (
      0 => '2aa',
      1 => '2bb',
      2 => '2cc',
      3 => '2dd',
      4 => '2ee',
    ),
    1 =>
    array (
      0 => '3aa',
      1 => '3bb',
      2 => '3cc',
      3 => '3dd',
      4 => '3ee',
    ),
  ),
  'longer_headers' =>
  array (
    0 =>
    array (
      'one' => '1',
      'two' => '2',
      'three' => '3',
      'four' => '',
      'five' => '',
      'six' => '',
    ),
    1 =>
    array (
      'one' => '1',
      'two' => '2',
      'three' => '3',
      'four' => '4',
      'five' => '',
      'six' => '',
    ),
    2 =>
    array (
      'one' => '',
      'two' => '2',
      'three' => '3',
      'four' => '4',
      'five' => '',
      'six' => '',
    ),
  ),

);

function fix($key) {
    global $fixtures;
    if (!array_key_exists($key, $fixtures)) {
        throw new Exception("Fixture not found: '$key' ");
    }
    return $fixtures[$key];
}

function path($file)
{
    return '../tests/data/' . $file;
}

function callback($value)
{
    return 1;
}

function callback2($value)
{
    return '';
}

?>
