<?php
/**
 * Created by PhpStorm.
 * User: rspaeth
 * Date: 8/2/2018
 * Time: 10:55 AM
 */

use UCRM\Data\MAPPER;
use UCRM\Data\Database;
use PHPUnit\Framework\TestCase;

class MAPPERTest extends TestCase
{
    /** @var PDO */
    protected $pdo;


    protected function setUp()
    {
        $host = "ucrm.dev.mvqn.net";
        $port = 5432;
        $name = "ucrm";
        $user = "ucrm";
        $pass = "PHwUv2P1vldQwUn68Vwv9FT4s2SUQAqlmvK36gA6GONQZSUU";

        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $this->pdo = new PDO(
            "pgsql:host=$host;port=$port;dbname=$name;",
            $user,
            $pass,
            $options
        );

        Database::connect($this->pdo);
    }



    public function testCreate()
    {
        MAPPER::create($this->pdo, "country");


    }
}
