<?php

Class Task extends \Threaded {
    protected $number;

    public function __construct($number) {
        $this->number = $number;
    }

    public function run() {
        sleep(2);
    }
}


class MyWorker extends \Worker {
}


Class WorkerPool extends \Pool {

    public function getSize() {
        return $this->size;
    }
    
    public function getWorkers() {
        return $this->workers;
    }

    public function getWorker($workerId) {
        if(isset($this->workers[$workerId])) {
            return $this->workers[$workerId];
        }
        return null;
    }
}


$pool = new \WorkerPool(10, MyWorker::class);

for ($i=0; $i<100; $i++) {
    $pool->submit(new Task($i));
}


while($pool->collect() > 0) {
    foreach ($pool->getWorkers() as $i => $worker) {
        echo sprintf("Worker[{$i}]:%d\n", $worker->getStacked());
    }
    echo "-------------------------\n";
    sleep(1);
}


echo 'Tasks all done';