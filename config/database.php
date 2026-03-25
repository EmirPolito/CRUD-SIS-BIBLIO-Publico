<?php
class DBConnection
{
    private $host = 'localhost';
    private $db = 'crud_biblioteca';
    private $user = 'root';
    private $pass = 'tu-contraseña-de-tu-mysql';

    public function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new PDO($dsn, $this->user, $this->pass, $options);
        }
        catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>
