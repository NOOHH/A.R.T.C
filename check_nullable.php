<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Create a simple connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'artc',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Check nullable fields
$desc = $capsule->connection()->select('DESCRIBE registrations');
$fieldsToCheck = ['firstname', 'student_school', 'street_address', 'state_province', 'city', 'zipcode', 'contact_number', 'Start_Date', 'disability_support'];

foreach($desc as $field) {
    if(in_array($field->Field, $fieldsToCheck)) {
        echo $field->Field . ' - Null: ' . $field->Null . PHP_EOL;
    }
}
