<?php

class pdoe extends \pdo {


    public string $error = '';
    public string $errorInfo = '';

    public object $conn;

    # public int $num_rows = 0;


    public function __construct(string $dsn, string $user = null, ?string $password = null, ?array $options = null)
    {

        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->options = $options;

        # parent::__construct($this->dsn, $this->user, $this->password, $this->options);

        try {

            parent::__construct($this->dsn, $this->user, $this->password, $this->options);

        }
        catch (\PDOException $e) {

            $this->error = 'The database is unavailable.'; # База недоступна.
            $this->errorInfo = $e->getMessage();

        }

    }


    # получить список таблиц базы
    public function tables($tableName = ''): array
    {

        $query = $tableName ? 'show tables from ' . $tableName : 'show tables';

        $li = [];

        try {

            $r = $this->query($query);
            while ($row = $r->fetch(\PDO::FETCH_NUM)) {
                $li[$row[0]] = $row[0];
            }

        }
        catch (\PDOException $e) {

            $this->error = 'Error when trying to read the database. Probably such a database does not exist..';
            $this->errorInfo = $e->getMessage();

        }

        return $li;
    }



    # получает массив имен полей таблицы со свойствами
    public function tableFields(string $table, bool $details = false): array
    {

        $li = [];

        try {

            $r = $this->query("SHOW COLUMNS FROM ".$table);

            while($col = $r->fetch()){
                $li[$col['Field']] = $details ? $col : $col['Field'];
            }

        }
        catch (\PDOException $e) {

            $this->error = 'Unable to get list of fields.';
            $this->errorInfo = $e->getMessage();

        }

        return($li);
    }



    # единый метод для работы с запросами
    public function action(string $query, array $prepare = []): mixed
    {
        $ACTION = mb_strtoupper(strtok(trim($query), ' '));

        try {

            switch ($ACTION) {
                case 'CREATE':
                case 'INSERT':
                case 'UPDATE':
                case 'DELETE':
                    if (count($prepare)) {
                        $stmt = $this->prepare($query);
                        $stmt->execute($prepare);
                        return $stmt->rowCount() ?: 0;
                    }
                    else {
                        $result = $this->exec($query);
                        return $result ?: 0;
                    }
                break;
                case 'SELECT':
                default:
                    if (count($prepare)) {
                        $stmt = $this->prepare($query);
                        $stmt->execute($prepare);
                        return $stmt;
                    }
                    else {
                        return $this->query($query);
                    }
            }

        }
        catch (\PDOException $e) {

            $this->error = 'Unable to get list of fields.';
            $this->errorInfo = $e->getMessage();

        }

    }


    # **
    public function optimizeTable(array $tables = []): mixed
    {

        if (!count($tables)) {
            $tables = $this->tables();

        }

        $tables = implode(', ', $tables);

        $query = 'OPTIMIZE TABLE ' . $tables;
        $result = $this->query($query);

        return $result;
    }


}





