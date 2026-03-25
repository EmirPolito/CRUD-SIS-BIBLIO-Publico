<?php
class DBConnection
{
    private $host = 'tu-host';
    private $db = 'tu-base-de-datos';
    private $user = 'tu-usuario';
    private $port = 'tu-puerto';
    private $pass = 'tu-contraseña';

    public function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../ca.pem',
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];
            return new PDO($dsn, $this->user, trim($this->pass), $options);
        }
        catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>
