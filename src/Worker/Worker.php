<?php
namespace Worker;

/**
 * This worker class will fork a process and call the set callbacks
 *
 * @category Worker
 * @package  Worker
 * @author   Arkan M. Gerges <arkanmgerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Worker
{
    private $mainCallback    = null;
    private $successCallback = null;
    private $errorCallback   = null;

    /**
     * Constructor that will assign the callback methods
     *
     * @param mixed  $mainCallback     Main callback that will be called after start() method is called
     * @param mixed  $successCallback  If main callback has completed successfully, then this callback will be called
     * @param mixed  $errorCallback    If there is an exception then this callback will be called
     */
    public function __construct($mainCallback = null, $successCallback = null, $errorCallback = null)
    {
        $this->mainCallback    = $mainCallback;
        $this->successCallback = $successCallback;
        $this->errorCallback   = $errorCallback;
    }

    /**
     * Set main callback that will be called when start() method is called
     *
     * @param mixed $mainCallback  Main callback that will be set into this object
     *
     * @return void
     */
    public function setMainCallback($mainCallback)
    {
        $this->mainCallback = $mainCallback;
    }

    /**
     * Set success callback that will be called main callback is completed successfully (no exception has occurred)
     *
     * @param mixed $successCallback  Success callback that will be set into this object
     *
     * @return void
     */
    public function setSuccessCallback($successCallback)
    {
        $this->successCallback = $successCallback;
    }

    /**
     * Set error callback that will be called whenever there is an exception
     *
     * @param mixed $errorCallback  Error callback that will be set into this object
     *
     * @return void
     */
    public function setErrorCallback($errorCallback)
    {
        $this->errorCallback = $errorCallback;
    }

    /**
     * Start forking a child and running the main callback. This method accepts parameters
     *
     * @return void
     */
    public function start()
    {
        switch ($pid = pcntl_fork()) {
            case -1:
                // Error
                break;
            case 0:
                $this->doWork(func_get_args());
                // Exit the current child
                exit;
                break;
            default:
                // Parent do nothing
                break;
        }
    }

    /**
     * Run the work for the current process. This method accepts parameters
     *
     * @return void
     */
    private function doWork()
    {
        // Check if callback is callable
        if (is_callable($this->mainCallback, true)) {
            try {
                $isSucceeded = call_user_func_array($this->mainCallback, func_get_args()[0]);
                if ($isSucceeded === false) {
                    throw new \Exception('Callback syntax is not correct');
                }
                if (is_callable($this->successCallback, true)) {
                    call_user_func($this->successCallback);
                }
            }
            catch (\Exception $e) {
                $this->callErrorCallback($e->getMessage());
            }
        }
        // Callback is not callable
        else {
            $this->callErrorCallback('Callback is not callable');
        }
        return;
    }

    /**
     * Call error callback
     *
     * @param string  $arg  This argument is the error message that will be passed to the error callback
     *
     * @return void
     */
    private function callErrorCallback($arg)
    {
        if (is_callable($this->errorCallback, true)) {
            call_user_func($this->errorCallback, $arg);
        }
    }
}
