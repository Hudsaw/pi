<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class EmpresaModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function criarEmpresa($dados)
    {
        $sql = "INSERT INTO empresas (nome, cnpj, email, telefone, endereco, cidade, estado, cep, observacao, ativo) 
                VALUES (:nome, :cnpj, :email, :telefone, :endereco, :cidade, :estado, :cep, :observacao, 1)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':cnpj' => $dados['cnpj'],
            ':email' => $dados['email'],
            ':telefone' => $dados['telefone'],
            ':endereco' => $dados['endereco'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado'],
            ':cep' => $dados['cep'],
            ':observacao' => $dados['observacao'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getEmpresas($filtro = 'ativos')
    {
        $sql = "SELECT * FROM empresas WHERE 1=1";
        
        switch ($filtro) {
            case 'ativos':
                $sql .= " AND ativo = 1";
                break;
            case 'inativos':
                $sql .= " AND ativo = 0";
                break;
            // 'todos' não adiciona filtro
        }
        
        $sql .= " ORDER BY nome";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmpresaPorId($id)
    {
        $sql = "SELECT * FROM empresas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEmpresaPorCnpj($cnpj)
    {
        $sql = "SELECT * FROM empresas WHERE cnpj = :cnpj";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cnpj' => $cnpj]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarEmpresa($id, $dados)
    {
        $sql = "UPDATE empresas SET 
                nome = :nome, 
                cnpj = :cnpj, 
                email = :email, 
                telefone = :telefone, 
                endereco = :endereco, 
                cidade = :cidade, 
                estado = :estado, 
                cep = :cep, 
                observacao = :observacao,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $dados['nome'],
            ':cnpj' => $dados['cnpj'],
            ':email' => $dados['email'],
            ':telefone' => $dados['telefone'],
            ':endereco' => $dados['endereco'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado'],
            ':cep' => $dados['cep'],
            ':observacao' => $dados['observacao'] ?? null,
            ':id' => $id
        ]);
    }

    public function desativarEmpresa($id)
    {
        $sql = "UPDATE empresas SET ativo = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function reativarEmpresa($id)
    {
        $sql = "UPDATE empresas SET ativo = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function buscarEmpresas($termo)
    {
        $sql = "SELECT * FROM empresas 
                WHERE (nome LIKE :termo OR cnpj LIKE :termo OR email LIKE :termo) 
                AND ativo = 1 
                ORDER BY nome";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':termo' => '%' . $termo . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalEmpresas()
{
    $sql = "SELECT COUNT(*) as total FROM empresas WHERE ativo = 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
}
}