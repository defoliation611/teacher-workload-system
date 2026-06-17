<?php
/**
 * 数据库操作类
 */

namespace Library;

class Database
{
    private $conn;
    private $config;
    private $table;

    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * 连接数据库
     */
    private function connect()
    {
        try {
            $dsn = 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['database'] . ';charset=' . $this->config['charset'];
            $this->conn = new \PDO($dsn, $this->config['user'], $this->config['password']);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die('数据库连接失败: ' . $e->getMessage());
        }
    }

    /**
     * 选择表
     */
    public function table($table)
    {
        $this->table = $this->config['prefix'] . $table;
        return $this;
    }

    /**
     * 查询所有
     */
    public function select($columns = '*')
    {
        $sql = 'SELECT ' . $columns . ' FROM ' . $this->table;
        return $this->query($sql);
    }

    /**
     * 条件查询
     */
    public function where($field, $operator = '=', $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $field . ' ' . $operator . ' ?';
        return $this->query($sql, [$value]);
    }

    /**
     * 单条查询
     */
    public function find($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';
        $result = $this->query($sql, [$id]);
        return count($result) > 0 ? $result[0] : null;
    }

    /**
     * 插入数据
     */
    public function insert($data)
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';

        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . $placeholders . ')';
        return $this->execute($sql, $values);
    }

    /**
     * 批量插入
     */
    public function insertBatch($dataArray)
    {
        if (empty($dataArray)) return false;
        
        $fields = array_keys($dataArray[0]);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . $placeholders . ')';
        
        foreach ($dataArray as $data) {
            $this->execute($sql, array_values($data));
        }
        return true;
    }

    /**
     * 更新数据
     */
    public function update($data, $id = null)
    {
        $sets = [];
        $values = [];
        foreach ($data as $field => $value) {
            $sets[] = $field . ' = ?';
            $values[] = $value;
        }
        
        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $sets);
        if ($id !== null) {
            $sql .= ' WHERE id = ?';
            $values[] = $id;
        }
        
        return $this->execute($sql, $values);
    }

    /**
     * 删除数据
     */
    public function delete($id = null)
    {
        $sql = 'DELETE FROM ' . $this->table;
        if ($id !== null) {
            $sql .= ' WHERE id = ?';
            return $this->execute($sql, [$id]);
        }
        return $this->execute($sql, []);
    }

    /**
     * 执行查询
     */
    private function query($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception('查询失败: ' . $e->getMessage());
        }
    }

    /**
     * 执行操作
     */
    private function execute($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception('执行失败: ' . $e->getMessage());
        }
    }

    /**
     * 获取最后插入ID
     */
    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    /**
     * 统计记录数
     */
    public function count()
    {
        $sql = 'SELECT COUNT(*) as total FROM ' . $this->table;
        $result = $this->query($sql);
        return $result[0]['total'] ?? 0;
    }

    /**
     * 分页查询
     */
    public function paginate($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT * FROM ' . $this->table . ' LIMIT ' . $perPage . ' OFFSET ' . $offset;
        return $this->query($sql);
    }
}
