<?php
/**
 * Created by PhpStorm.
 * User: wcm
 * Date: 2020/10/23
 * Time: 16:13
 */

require 'Worker.php';


class Master {
    /**
     * @var int
     */
    private $maxWorkerAmount = 5;

    /**
     * @var Worker[] $workers
     */
    private $workers;

    private $workerId = 0;


    public function run() {
        $this->setProcessTitle("worker-master");

        for( $i = 0; $i < $this->maxWorkerAmount; $i++ ) {
            $worker = $this->getOneNewWorker();
            $this->forkWorkerProcess($worker, "worker-worker[{$worker->getWorkerId()}]");
        }

//        $this->registerWorkerWatcher();


        $this->wait();
    }

    /**
     * 获取一个新的worker对象
     * @return Worker
     */
    public function getOneNewWorker() {
        return new Worker($this->getNextWorkerId(), function () {
            $taskAmount = mt_rand(10, 20);
            for ($i = 0; $i < $taskAmount; $i ++) {
                $pid = posix_getpid();
                echo "[{$pid}] message {$i}\n";
                sleep(1);
            }
        });
    }

    public function getNextWorkerId() {
        return $this->workerId++;
    }

    public function setProcessTitle($title) {
        $isSuccess = cli_set_process_title($title);
        if ($isSuccess == false) {
            throw new Exception('Process title set fail!');
        }
    }

    public function forkWorkerProcess(Worker $worker, $workerProcessTitle) {
        $newWorkerPid = pcntl_fork();

        switch ($newWorkerPid) {
            case -1:
                throw new Exception("Master fork process fail!");
                break;
            case 0:
                $this->setProcessTitle($workerProcessTitle);
                $worker->run();
                exit();
                break;
            default:
                $this->workers[$newWorkerPid] = $newWorkerPid;
        }
    }

    public function registerWorkerWatcher() {
        pcntl_signal(SIGALRM, function () {
            $liveWorkerAmount  = count($this->workers);
            $reNewWorkerAmount = $this->maxWorkerAmount - $liveWorkerAmount;

            for( $i = 0; $i < $reNewWorkerAmount; $i++ ) {
                $newWorker = $this->getOneNewWorker();
                $this->forkWorkerProcess($newWorker, "worker-worker[{$newWorker->getWorkerId()}]");
            }
        }, true);
        pcntl_alarm(1);
    }
    

    public function wait() {
        $this->registerWorkerWatcher();

        while(true) {
            // 清理死亡的worker
            $exitPid = pcntl_wait($status);
            if($exitPid > 0) {
                unset($this->workers[$exitPid]);
            }

            $liveWorkerAmount  = count($this->workers);
            $reNewWorkerAmount = $this->maxWorkerAmount - $liveWorkerAmount;

            for( $i = 0; $i < $reNewWorkerAmount; $i++ ) {
                $newWorker = $this->getOneNewWorker();
                $this->forkWorkerProcess($newWorker, "worker-worker[{$newWorker->getWorkerId()}]");
            }
            
            sleep(1);
        }
    }
}


$master = new Master();

$master->run();

