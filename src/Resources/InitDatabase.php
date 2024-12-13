<?php
namespace Resources;

use Database\Connector;

class InitDatabase {
    private \PDO $conn;
    public function __construct()
    {
        $this->conn = (new Connector())->getConnection();

        $this->execute();
    }


    private function execute(): void
    {
        $this->setValidaExistenciaTabelas();
    }

    private function setValidaExistenciaTabelas()
    {
        $consulta = $this->conn->query("show tables");
        // Criar as tabelas caso ainda não estejam criadas
        $sql = file_get_contents(__DIR__ . '/sql/CreateFullTables.sql');
        $tables = explode("-", $sql);
        foreach($tables as $table) {
            $this->conn->query($table);
        }
        
    }
}
// Finalizar
