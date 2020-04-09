<?php

class WebUtil {
    public static function get($url) {
        return file_get_contents($url);
    }
}

class WebWorker extends \Thread {
    protected $url;

    public function __construct($url) {
        $this->url = $url;
    }

    public function run() {
        $startTime = microtime(true);
        $content = WebUtil::get($this->url);
        $endTime = microtime(true);

        echo sprintf("[%d]content:%s\n", $this->getThreadId(), substr($content, 0, 5));
        echo sprintf("[%d]cost Time:%d\n", $this->getThreadId(),$endTime - $startTime);
        echo sprintf("[%d]current thread id:%d\n", $this->getThreadId(), Thread::getCurrentThreadId());
        echo "------------------------------------------\n";
    }
}



$workers = [];
for ($i=0;$i<10;$i++) {
    $workers[$i] = new WebWorker("http://www.baidu.com");
    $workers[$i]->start();
}


sleep(10);