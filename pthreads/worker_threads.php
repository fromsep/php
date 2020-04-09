<?php


class WorkerThread extends \Thread {
    private $number;

    public function __construct($number) {
        $this->number = $number;
    }

    public function run() {
        while (true) {
            echo "[{$this->number}]: yes\n";
            echo $this->getThreadId() . "\n";
            echo Thread::getCurrentThreadId() . "\n";
            echo "----------------------------\n";

            sleep(1);
        }
    }
}


$workers = [];
for ($i = 0; $i < 10; $i++) {
    $workers[$i] = new WorkerThread($i);
    $workers[$i]->start();
}

sleep(10);