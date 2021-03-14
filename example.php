<?php

require_once __DIR__ . '/vendor/autoload.php';

use Classes\Blacklists;

$dsn = 'mysql:dbname=advertising;host=127.0.0.1';
$user = 'someuser';
$password = 'zaq1xsw2cde3';

try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $bl = new Blacklists($db);

    $advertiserId = 1;;

    $bl->save("s111, p1, p2, s111", $advertiserId);

    $advertiserBl = $bl->get($advertiserId);
    print_r("Чёрный Список рекламодателя {$advertiserId}: {$advertiserBl}");
} catch (PDOException $e) {
    die ('DB Error. Code: ' . $e->getCode() . '. Text: ' . $e->getMessage());
} catch (Throwable $t) {
    die ($t->getMessage());
}
