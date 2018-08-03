<?php
declare(strict_types=1);

require_once __DIR__."/vendor/autoload.php";

use UCRM\Data\MAPPER;

$host = "ucrm.dev.mvqn.net";
$port = 5432;
$name = "ucrm";
$user = "ucrm";
$pass = "PHwUv2P1vldQwUn68Vwv9FT4s2SUQAqlmvK36gA6GONQZSUU";

$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$pdo = new PDO(
    "pgsql:host=$host;port=$port;dbname=$name;",
    $user,
    $pass,
    $options
);

\UCRM\Data\Database::connect($pdo);

MAPPER::create($pdo, "country");

$results = $pdo->query("SELECT * FROM country WHERE name = 'United States'");
$usa = $results->fetchObject(\UCRM\Data\Models\Country::class);
echo $usa."\n";



MAPPER::create($pdo, "app_key");
MAPPER::create($pdo, "plugin");

$results = $pdo->query("SELECT * FROM app_key WHERE key_id = 5");
/** @var \UCRM\Data\Models\AppKey $app */
$app = $results->fetchObject(\UCRM\Data\Models\AppKey::class);
echo $app."\n";

echo $app->getPlugin()."\n";

$appKeys = (new \UCRM\Data\Models\AppKey())->select(["name", "key"]);

foreach($appKeys as $appKey)
    echo $appKey."\r\n";

$countries = \UCRM\Data\Models\Country::where("name", "United States");

foreach($countries as $country)
    echo $country."\r\n";

$countries = \UCRM\Data\Models\Country::like("name", "United%");

foreach($countries as $country)
    echo $country."\r\n";