<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use PhpOffice\PhpSpreadsheet\IOFactory;

require 'vendor/autoload.php';

$inputFileName = __DIR__ . '/users_import_template.xlsx';
$spreadsheet = IOFactory::load($inputFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);