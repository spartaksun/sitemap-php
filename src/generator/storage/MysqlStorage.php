<?php

namespace spartaksun\sitemap\generator\storage;


class MysqlStorage implements UniqueValueStorageInterface
{

    /**
     * @var \Closure
     */
    public $onAddCallback;

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
    public function setKey($storageKey)
    {
        $this->storageKey = $storageKey;
    }

    /**
     * Drop current table
     */
    public function deInit()
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
                `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `url` VARCHAR(1000) NOT NULL,
                `level` INT UNSIGNED DEFAULT 0
            ) ENGINE=MyIsam AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
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
    public function add(array $values, $level)
    {
        $unique = $this->filterUnique($values);
        $countUniqueValues = count($unique);

        if($countUniqueValues > 0) {
            $paramsStr = implode(",", array_fill(0, $countUniqueValues, '(?, '.(int) $level.')'));
            $insertSql = "INSERT INTO `{$this->tableName()}` (`url`, `level`) VALUES {$paramsStr}; ";
            $st = $this->getConnection()
                ->prepare($insertSql);

            if ($st->execute($unique)) {
                if ($this->onAddCallback instanceof \Closure) {
                    call_user_func($this->onAddCallback, $countUniqueValues);
                }

                return $unique;
            } else {
                throw new StorageException('PDO: ' . serialize($st->errorInfo()));
            }
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function onAdd(\Closure $callback)
    {
        $this->onAddCallback = $callback;
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
        $exists = $st->fetchAll(\PDO::FETCH_COLUMN);

        $callback = function ($value) use ($exists) {
            if (in_array($value, $exists)) {
                return false;
            }
            return true;
        };

        $unique = array_values(
            array_unique(
                array_filter($values, $callback)
            )
        );

        return $unique;
    }

    /**
     * @inheritdoc
     */
    public function total()
    {
        $st = $this->getConnection()->prepare("SELECT COUNT(id) FROM `{$this->tableName()}`;");
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
        if (empty($this->db['dsn']) || empty($this->db['username'])
            || !isset($this->db['password'])
        ) {
            throw new StorageException("Incorrect db config ". serialize($this->db));
        }

        static $connection = null;
        if (empty($connection)) {
            try {
                $connection = new \PDO(
                    $this->db['dsn'],
                    $this->db['username'],
                    $this->db['password']
                );
            } catch (\PDOException $e) {
                throw new StorageException("DB error: " . $e->getTraceAsString());
            }
        }

        return $connection;
    }
}
