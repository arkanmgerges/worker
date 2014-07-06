<?php
namespace Test\Worker;

class BaseClass extends \PHPUnit_Framework_TestCase
{
    protected $resultFile  = '';
    protected $successFile = '';
    protected $errorFile   = '';

    public function setUp()
    {
        if (!file_exists(realpath(__DIR__ . '/../..') . '/tmp')) {
            mkdir(realpath(__DIR__ . '/../..') . '/tmp', 0777);
        }
        $this->resultFile = realpath(__DIR__ . '/../../tmp') . '/result.txt';
        $this->successFile = realpath(__DIR__ . '/../../tmp') . '/success.txt';
        $this->errorFile = realpath(__DIR__ . '/../../tmp') . '/error.txt';

        // Set environment variable to test
        // putenv('APP_ENV=development');
        date_default_timezone_set("UTC");
    }
}
