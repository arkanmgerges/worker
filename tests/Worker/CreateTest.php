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

class CreateTest extends BaseClass
{
    public function tearDown()
    {
        exec('rm -fr ' . realpath(__DIR__ . '/../../tmp'));
    }

    public function testCallClosure()
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

        $worker->start('from ', 'closure');
        sleep(2);
        $this->assertEquals('from closure', file_get_contents($this->resultFile));
        if (file_exists($this->successFile)) {
            $this->assertEquals('success', file_get_contents($this->successFile));
        }
        if (file_exists($this->errorFile)) {
            $this->assertEquals('error', file_get_contents($this->errorFile));
        }
    }

    public function testCallClassMethod()
    {
        $tmp = new Temp();

        $worker = new Worker(
            [$tmp, 'method']
        );

        $worker->start('from ', 'method');
        sleep(2);
        $this->assertEquals('from method', file_get_contents($this->resultFile));
    }
}
