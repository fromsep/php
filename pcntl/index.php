<?php

if(function_exists('pcntl_fork') == false) {
    exit('pcntl functions not found!');
}


cli_set_process_title('php-Master');


$sonProcesses = [];

$i = 0;
while($i < 5) {
    $i ++;

    $sonPid = pcntl_fork();

    if($sonPid == -1) {
        die('Fork fail!');
    } elseif($sonPid == 0) {
        // 子进程
        cli_set_process_title("php-Son[{$i}]");

        for ($j = 0; $j < 10; $j++) {
            echo "[{$i}]run task {$j}\n";
            sleep(1);
        }

        exit();
    } else {

        $sonProcesses[$sonPid] = $sonPid;

        // 父进程
        echo "Parent: Child pid is {$sonPid}\n";
//        pcntl_wait($status, WNOHANG);
    }
}



while(count($sonProcesses) > 0) {
    $exitPid = pcntl_wait($status, WNOHANG);
    unset($sonProcesses[$exitPid]);
    sleep(10);
}

