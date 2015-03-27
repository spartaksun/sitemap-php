<?php

namespace spartaksun\sitemap\generator\storage;


class MysqlStorage implements UniqueValueStorage
{

    /**
     * DB connection config
     * @var array
     *      [
     *      'name' => 'db_name',
     *      'host' => 'db_host',
     *      'user' => 'db_user',
     *      'pass' => 'user_password',
     *      ]
     */
    public $db;

    /**
     * @var string
     */
    public $prefix = 'sitemap_';

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $storageKey;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;


    /**
     * @inheritdoc
     */
    public function __construct($storageKey)
    {
        $this->storageKey = $storageKey;
    }

    /**
     *
     */
    public function __destruct()
    {
        $sql = "DROP TABLE IF EXISTS `{$this->tableName()}`;";
        return $this->getConnection()
            ->prepare($sql)
            ->execute();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->connection = $this->getConnection();
        $sql = "DROP TABLE IF EXISTS `{$this->tableName()}`;
            CREATE TABLE `{$this->tableName()}`
            (
                id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
                url VARCHAR(255) NOT NULL
            );
            CREATE UNIQUE INDEX url ON `{$this->tableName()}` (url);";

        $init = $this->getConnection()
            ->prepare($sql)
            ->execute();

        if (!$init) {
            throw new StorageException('Can not init tables');
        }
    }

    /**
     * @return string
     */
    public function tableName()
    {
        return $this->prefix . $this->storageKey;
    }

    /**
     * @inheritdoc
     */
    public function add(array $values)
    {
        $unique = $this->filterUnique($values);
        $countUniqueValues = count($unique);

        if($countUniqueValues > 0) {
            $paramsStr = implode(",", array_fill(0, $countUniqueValues, '(?)'));
            $insertSql = "INSERT INTO `{$this->tableName()}` (url) VALUES {$paramsStr}; ";
            $st = $this->getConnection()
                ->prepare($insertSql);
            $result = $st->execute($unique);

            return $result;
        }

        return false;

    }

    /**
     * @param array $values
     * @return array
     * @throws StorageException
     */
    private function filterUnique(array $values)
    {
        $countValues = count($values);
        if($countValues == 0) {
            return [];
        }

        $paramsStr = implode(",", array_fill(0, $countValues, '?'));
        $existsSql = "SELECT url FROM `{$this->tableName()}` WHERE url IN ({$paramsStr}); ";

        $st = $this->getConnection()->prepare($existsSql);
        $st->execute($values);

        $exists = (array)$st->fetchColumn();
        $callback = function ($value) use ($exists) {
            if (in_array($value, $exists)) {
                return false;
            }
            return true;
        };

        $unique = array_filter($values, $callback);

        return $unique;
    }

    /**
     * @inheritdoc
     */
    public function total()
    {
        $st = $this->getConnection()->prepare("SELECT MAX(id) FROM `{$this->tableName()}`;");
        $st->execute();
        $rows = $st->fetch(\PDO::FETCH_NUM);

        return $rows[0];
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        $offset = (int)$this->offset;
        $limit = (int)$this->limit;

        $existsSql = "SELECT url FROM `{$this->tableName()}` ORDER BY id ASC LIMIT {$offset},{$limit}";

        $st = $this->getConnection()->prepare($existsSql);
        $st->execute();

        return $st->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @inheritdoc
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @inheritdoc
     */
    public function setOffset($offset)
    {
       $this->offset = $offset;
    }

    /**
     * @return \PDO
     * @throws StorageException
     */
    private function getConnection()
    {
        if (empty($this->db['name']) || empty($this->db['host'])
            || empty($this->db['user']) || !isset($this->db['pass'])
        ) {
            throw new StorageException("Incorrect db config ". serialize($this->db));
        }

        static $connection = null;
        if (empty($connection)) {
            try {
                $connection = new \PDO(
                    'mysql:dbname=' . $this->db['name'] . ';host=' . $this->db['host'],
                    $this->db['user'],
                    $this->db['pass']
                );
            } catch (\PDOException $e) {
                throw new StorageException("DB error: " . $e->getTraceAsString());
            }
        }

        return $connection;
    }
}