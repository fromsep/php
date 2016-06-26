<?php
/**
 * 将session数据写入mysql
 */

/**
 * 继承session处理接口
 */
class session2mysql implements SessionHandlerInterface
{
    // 保存mysql连接资源
    private $conn;
    // session过期时间
    private $expire = 10;

    public function __construct(){
        $this->initDb();
        session_set_save_handler ($this, true);
    }

    public function open($save_path, $name){      
        return true;
    }

    /**
     * [write 添加session]
     * @param  [type] $session_id   [description]
     * @param  [type] $session_data [description]
     * @return [type]               [description]
     */
    public function write($session_id, $session_data){
        $expire = time() + $this->expire;
        $sql = "INSERT INTO `session` VALUES('{$session_id}', '{$session_data}', $expire)";   
        mysql_query($sql, $this->conn);

        if(mysql_affected_rows($this->conn))
            return true;
        else
            return false;
    }

    /**
     * [destroy 删除session]
     * @param  [type] $session_id [description]
     * @return [type]             [description]
     */
    public function destroy($session_id){
        $sql = "DELETE FROM `session` where `id` ='{$session_id}'";
        mysql_query($sql, $this->conn);

        if(mysql_affected_rows($this->conn))
            return true;
        else
            return false;
    }

    /**
     * [read 读取session]
     * @param  [type] $session_id [description]
     * @return [type]             [description]
     */
    public function read($session_id){
        $sql = "SELECT `value` FROM `session` WHERE id='{$session_id}'";
        $rs = mysql_query($sql, $this->conn);
        if(mysql_num_rows($rs) == 1){
            $row = mysql_fetch_assoc($rs);
            return $row['value'];
        }else{
            return false;
        }
    }

    /**
     * [close 关闭此次session处理]
     * @return [type] [description]
     */
    public function close(){
        $expire = time();
        $sql = "DELETE FROM `session` where `expire` < {$expire}";
      
        mysql_query($sql, $this->conn);
        return true;
    }

    /**
     * [gc 自动销毁]
     * @param  [type] $maxlifetime [description]
     * @return [type]              [description]
     */
    public function gc($maxlifetime){
        $expire = time();
        $sql = "DELETE FROM `session` where `expire` < {$expire}";
        mysql_query($sql, $this->conn);
        return true;
    }

    /**
     * [initDb 初始化数据库]
     * @return [type] [description]
     */
    private function initDb(){
        $this->conn = @mysql_connect('127.0.0.1', 'root', '111111');
        mysql_select_db('test', $this->conn);
        mysql_set_charset('utf8');
    }
}

$handler = new session2mysql;

session_start();
 
// $_SESSION['foo'] = "bar123";

echo '<pre>';
print_r($_SESSION);
exit('');
