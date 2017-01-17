<?php

/**
 * 数据库抽象类
 * 
 * 主要函数及使用方法:
 * 
 * $db = new db('mysql:host=localhost;dbname=aohe','root','123');
 * $db->connect();
 * 
 * 查询并处理每行数据
 * $data = $db->query('SELECT username,time FROM user ');
 * while($row = $data->fetch()) 
 * {
 * 		$row['time'] = date('Y-m-d',$row['time']);
 * 		......
 * 		$result[] = $row;
 * }
 * 
 * 查询并一次返回全部数据
 * $data = $db->fetchAll('SELECT username,time FROM user');
 * 
 * 插入一行记录
 * $data = array(
 * 				'username'=>'张三',
 * 				'password'=>md5($password),
 * 			);
 * $db->insert('user',$data);
 * 
 * 更新记录
 * $data = array(
 * 				'username'=>'张三',
 * 				'email'=>'zhangsan@aohe.com',
 * 			);
 * $db->update('user',$data,'user_id=2');
 * 
 * 删除记录
 * $db->delete('user','user_id=2');
 * 
 * 返回第一行数据
 * $row = $db->fetchRow('SELECT * FROM user WHERE user_id = 2');
 * 
 * 返回第一行第一列数据
 * $count = $db->fetchColumn('SELECT COUNT(*) FROM user');
 * 
 * 
 * 绑定查询
 * $sql = 'SELECT COUNT(*) FROM user WHERE id = ? and fff = ?'
 * $data = array(1,4);
 * $type = 1; //注释:1,返回单行数据，其他：返回多行数据
 * $ret = $db->querybind($sql,$data,$type);
 * 
 * 返回最后插入的记录主键id
 * $id = $db->lastInsertId();
 * 
 */
class Db {

    public $pdoClass = 'PDO';
    public $connectionString;
    public $username;
    public $password;
    public $tablePrefix = ''; //数据表前缀
    public $driverName = 'mysql'; //默认使用mysql数据库
    public $_driverMap = array(
        'mysql' => 'pdo_mysql', // MySQL
    );
    private $_initOption = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //设置出错模式为异常处理模式
    );
    public $pdo;  //pdo实例
    public $statement; //PDOStatement实例

    public function __construct($dsn = '', $username = '', $password = '') {
        $this->driverName = $this->getDriverName($dsn);
        if (!extension_loaded($this->_driverMap[$this->driverName])) {
            throw new PDOException();
        }
        $this->connectionString = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect() {
        try {
            $this->pdo = new $this->pdoClass($this->connectionString, $this->username, $this->password, $this->_initOption);
        } catch (PDOException $e) {
            
        }
    }

    public function close() {
        $this->pdo = null;
    }

    /**
     * 重新连接数据库
     *
     */
    public function reconnect() {
        $this->close();
        $this->connect();
    }

    /**
     * 从数据库连接源名中取得数据库驱动名
     *
     * @param unknown_type $dsn
     * @return unknown
     */
    public function getDriverName($dsn) {
        if (($pos = strpos($dsn, ':')) !== false)
            return strtolower(substr($dsn, 0, $pos));
    }

    public function prepare($sql) {
        $this->statement = $this->pdo->prepare($sql);
        $this->statement->setFetchMode(PDO::FETCH_ASSOC); //设置默认的取值为关联数组
    }

    /**
     * 执行sql，如果失败则重新连接数据库再执行一次
     *
     */
    public function execute() {
        if (!$this->statement->execute()) {
            $this->reconnect();
            if (!$this->statement->execute()) {
                return false;
            }
        }
        return true;
    }

    public function query($sql) {
        $this->prepare($sql);
        return $this->execute();
    }

    /**
     * 从查询结果集中取下一行数据
     *
     * @return unknown
     */
    public function fetch() {
        return $this->statement->fetch();
    }

    /**
     * 取第一行的值
     *
     * @param unknown_type $sql
     * @return unknown
     */
    public function fetchRow($sql) {
        $this->query($sql);
        return $this->statement->fetch();
    }

    /**
     * 取第一列第一行的值
     *
     * @param unknown_type $sql
     * @return unknown
     */
    public function fetchColumn($sql) {
        $this->query($sql);
        return $this->statement->fetchColumn();
    }

    /**
     * 返回整个查询结果
     *
     * @param unknown_type $sql
     * @return Array
     */
    public function fetchAll($sql) {
        $this->query($sql);
        return $this->statement->fetchAll();
    }

    /**
     * 返回最后插入记录的主键id
     *
     * @return unknown
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * 往指定数据表中插入一条数据
     *
     * @param unknown_type $table
     * @param unknown_type $columns
     * @return unknown
     */
    public function insert($table, $columns) {
        $names = array();
        $placeholder = array();
        $params = array();

        foreach ($columns as $name => $value) {
            $names[] = $name;
            $placeholder[] = ':' . $name;
            $params[":$name"] = $value;
        }
        $columnstr = implode(', ', $names);
        $placeholderstr = implode(', ', $placeholder);
        $sql = "INSERT INTO `$table`($columnstr) VALUES($placeholderstr)";
        $this->statement = $this->pdo->prepare($sql);
        foreach ($params as $param => &$var) {
            $this->statement->bindParam($param, $var);
        }
        return $this->execute();
    }
    
    /**
     * 往指定数据表中插入批量数据
     *
     * @param unknown_type $table
     * @param unknown_type $columns
     * @return unknown
     */
    public function insertAll($table, $data) {
    	if(!is_array($data[0])) return false;
        $fields = array_keys($data[0]);
        foreach ($fields as $k => $v) {
        	$fields[$k] = "`$v`";
        }
		$values  =  array();
		foreach ($data as $columns) {
			$value  =  array();
			foreach ($columns as $name => $val) {
	            $value[] = "'$val'";
	        }
	        $values[]    = '('.implode(',', $value).')';
		}
        $columnstr = implode(',', $fields);
        $placeholderstr = implode(', ', $values);
        $sql = "INSERT INTO `$table`($columnstr) VALUES $placeholderstr";
        $this->statement = $this->pdo->prepare($sql);
        return $this->execute();
    }

    /**
     * 往指定数据表中更新一条记录
     *
     * @param unknown_type $table
     * @param unknown_type $columns
     * @return unknown
     */
    public function update($table, $columns, $condition) {
        $setstr = array();
        $params = array();

        foreach ($columns as $name => $value) {
            $setstr[] = "$name = :$name";
            $params[":$name"] = $value;
        }
        $setstr = implode(', ', $setstr);
        $sql = "UPDATE `$table` SET $setstr WHERE $condition";
        $this->statement = $this->pdo->prepare($sql);

        foreach ($params as $param => &$var) {
            $this->statement->bindParam($param, $var);
        }
        return $this->execute();
    }

    /**
     * 往指定数据表中增加某一字段的值
     * @param unknown_type $table
     * @param unknown_type $columns
     * @return unknown
     */
    public function cloumIncrease($table, $columns, $condition) {
        $setstr = array();
        $params = array();

        foreach ($columns as $name => $value) {
            $setstr[] = "$name = $name + :$name";
            $params[":$name"] = $value;
        }
        $setstr = implode(', ', $setstr);
        $sql = "UPDATE `$table` SET $setstr WHERE $condition";
        $this->statement = $this->pdo->prepare($sql);

        foreach ($params as $param => &$var) {
            $this->statement->bindParam($param, $var);
        }
        return $this->execute();
    }
    
    /**
     * 往指定数据表中删减某一字段的值
     * @param unknown_type $table
     * @param unknown_type $columns
     * @return unknown
     */
    public function cloumDec($table, $columns, $condition) {
        $setstr = array();
        $params = array();

        foreach ($columns as $name => $value) {
            $setstr[] = "$name = $name - :$name";
            $params[":$name"] = $value;
        }
        $setstr = implode(', ', $setstr);
        $sql = "UPDATE `$table` SET $setstr WHERE $condition";
        $this->statement = $this->pdo->prepare($sql);

        foreach ($params as $param => &$var) {
            $this->statement->bindParam($param, $var);
        }
        return $this->execute();
    }
    
    /**
     * 删除指定记录
     *
     * @param unknown_type $table
     * @param unknown_type $condition
     */
    public function delete($table, $condition) {
        $sql = "DELETE FROM `$table` WHERE $condition";
        $this->statement = $this->pdo->prepare($sql);
        return $this->execute();
    }

    /**
     * 往指定数据表中增加某一字段的值
     * @param string $sql
     * @param array $data 
     * @param type $type 1返回单行,其他返回多行,默认单行
     * @return unknown
     */
    public function querybind($sql, $data, $type = 1) {
        $this->statement = $this->pdo->prepare($sql);
        foreach ($data as $param => &$var) {
            $this->statement->bindParam($param + 1, $var);
        }
        $this->execute();
        if ($type == 1)
            return $this->statement->fetch();
        else
            return $this->statement->fetchAll();
    }

    /**
     * 开启事务
     * @return type 
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * 关闭事务
     * @return type 
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * 事务回滚
     * @return type 
     */
    public function rollback() {
        return $this->pdo->rollback();
    }

}
