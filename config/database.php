<?php
class Database {
    public $conn;

    public function connect() {
        $this->conn = null;
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '3306';
        $db_name = getenv('DB_NAME') ?: 'pos_system';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];

            if (!in_array(strtolower($host), ['localhost', '127.0.0.1', '::1'], true)) {
                $configuredCaFile = getenv('DB_SSL_CA');
                $caFiles = $configuredCaFile
                    ? [$configuredCaFile]
                    : [
                        '/etc/ssl/certs/ca-certificates.crt',
                        '/etc/ssl/cert.pem',
                        '/etc/pki/tls/certs/ca-bundle.crt',
                    ];
                $caFile = null;
                foreach ($caFiles as $candidate) {
                    if (is_readable($candidate)) {
                        $caFile = $candidate;
                        break;
                    }
                }

                if ($caFile === null) {
                    throw new RuntimeException('The configured database CA certificate file is not readable.');
                }

                $sslCaAttribute = defined('Pdo\\Mysql::ATTR_SSL_CA')
                    ? constant('Pdo\\Mysql::ATTR_SSL_CA')
                    : PDO::MYSQL_ATTR_SSL_CA;
                $verifyServerAttribute = defined('Pdo\\Mysql::ATTR_SSL_VERIFY_SERVER_CERT')
                    ? constant('Pdo\\Mysql::ATTR_SSL_VERIFY_SERVER_CERT')
                    : PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT;

                $options[$sslCaAttribute] = $caFile;
                $options[$verifyServerAttribute] = true;
            }

            $this->conn = new PDO(
                "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4",
                $username,
                $password,
                $options
            );
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die("ERROR: Unable to connect to the database. Please contact the system administrator.");
        } catch (RuntimeException $e) {
            error_log('Database TLS configuration failed: ' . $e->getMessage());
            die("ERROR: Unable to connect to the database. Please contact the system administrator.");
        }
        return $this->conn;
    }
}