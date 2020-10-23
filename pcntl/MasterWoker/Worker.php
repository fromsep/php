<?php
/**
 * Created by PhpStorm.
 * User: wcm
 * Date: 2020/10/23
 * Time: 16:13
 */

class Worker {
    /**
     * @var Closure
     */
    private $callback;

    private $workerId;


    public function __construct($workerId, Closure $callback) {
        $this->workerId = $workerId;
        $this->callback = $callback;
    }

    public function run() {
        return ($this->callback)();
    }

    /**
     * @return mixed
     */
    public function getWorkerId() {
        return $this->workerId;
    }
}