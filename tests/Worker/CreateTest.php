<?php
namespace Test\Worker;

use Worker\Worker;

class Temp
{
    public function method($arg1, $arg2)
    {
        file_put_contents(realpath(__DIR__ . '/../../tmp') . '/result.txt', $arg1 . $arg2);
    }
};

class Temp2
{
    public static function method($arg1, $arg2)
    {
        file_put_contents(realpath(__DIR__ . '/../../tmp') . '/result.txt', $arg1 . $arg2);
    }
};

class CreateTest extends BaseClass
{
    public function tearDown()
    {
        exec('rm -fr ' . realpath(__DIR__ . '/../../tmp'));
    }

    public function testCallAnonymous()
    {
        $worker = new Worker(
            function($arg1 = '', $arg2 = '') {
                file_put_contents($this->resultFile, $arg1 . $arg2);
            },
            function() {
                file_put_contents($this->successFile, 'success');
            },
            function($e) {
                file_put_contents($this->errorFile, 'error');
            }
        );

        $worker->start('from', ' anonymous');
        sleep(2);
        $this->assertEquals('from anonymous', file_get_contents($this->resultFile));
        if (file_exists($this->successFile)) {
            $this->assertEquals('success', file_get_contents($this->successFile));
        }
        if (file_exists($this->errorFile)) {
            $this->assertEquals('error', file_get_contents($this->errorFile));
        }
    }

    public function testCallObjectMethod()
    {
        $tmp = new Temp();

        $worker = new Worker(
            [$tmp, 'method']
        );

        $worker->start('from', ' method');
        sleep(2);
        $this->assertEquals('from method', file_get_contents($this->resultFile));
    }

    public function testCallClassMethod()
    {
        $worker = new Worker(
            __NAMESPACE__ . '\Temp2::method'
        );

        $worker->start('from', ' class method');
        sleep(2);
        $this->assertEquals('from class method', file_get_contents($this->resultFile));
    }
}
