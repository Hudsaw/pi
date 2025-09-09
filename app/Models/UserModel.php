<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function autenticar($cpf, $password)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE cpf = ? AND ativo = 1");
        $stmt->execute([$cpf]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $this->password_verify($password, $user['senha'])) {
            unset($user['senha']);
            return $user;
        }
        
        return false;
    }

    public function criarUser(array $data)
{
    try {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios
            (nome, cpf, email, telefone, cep, logradouro, complemento, cidade, tipo_chave_pix, chave_pix, senha, especialidade_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        $success = $stmt->execute([
            $data['nome'],
            $data['cpf'],
            $data['email'],
            $data['telefone'],
            $data['cep'],
            $data['logradouro'],
            $data['complemento'],
            $data['cidade'],
            $data['tipo_chave_pix'],
            $data['chave_pix'],
            $data['senha'],
        ]);

        return $success ? $this->pdo->lastInsertId() : false;
    } catch (PDOException $e) {
        error_log("Error creating user: " . $e->getMessage());
        return false;
    }
}

    public function atualizarUser($userId, $data)
{
    try {
        $campos = [
            'nome'           => $data['nome'],
            'telefone'       => $data['telefone'],
            'email'          => $data['email'],
            'cpf'            => $data['cpf'],
            'cep'            => $data['cep'],
            'logradouro'     => $data['logradouro'],
            'complemento'    => $data['complemento'],
            'cidade'         => $data['cidade'],
            'tipo_chave_pix' => $data['tipo_chave_pix'],
            'chave_pix'      => $data['chave_pix'],
        ];

        // Adicionar senha se fornecida
        if (!empty($data['senha'])) {
            $campos['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }

        $setParts = [];
        $params = [];
        
        foreach ($campos as $campo => $value) {
            $setParts[] = "{$campo} = ?";
            $params[] = $value;
        }
        
        $params[] = $userId;
        
        $query = "UPDATE usuarios SET " . implode(', ', $setParts) . " WHERE id = ?";
        
        // Executar a atualização
        $stmt = $this->pdo->prepare($query);
        $success = $stmt->execute($params);

        return $success;
    } catch (PDOException $e) {
        error_log("Error updating user: " . $e->getMessage());
        return false;
    }
}

    public function removerUser($userId)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    public function getUserPeloId($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, e.nome as especialidade
            FROM usuarios u
            LEFT JOIN especialidade e ON u.especialidade_id = e.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTodosUser()
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, e.nome as especialidade
            FROM usuarios u
            LEFT JOIN especialidade e ON u.especialidade_id = e.id
            WHERE u.ativo = 1
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserPeloEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExiste($email)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    public function cpfExiste($cpf)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch() !== false;
    }

    public function getEspecialidade()
    {
        $stmt = $this->pdo->query("SELECT id, nome FROM especialidade ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNivel(): array
    {
        return $this->pdo->query("SELECT * FROM nivel")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function password_verify($senha, $senhaInformada) {
        return $senha === $senhaInformada;
    }

    public function createPasswordResetToken($userId, $token, $expiry)
{
    try {
        // Limpa tokens antigos primeiro
        $this->pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")
                 ->execute([$userId]);
        
        // Insere o novo token com timestamp correto
        $stmt = $this->pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $token, $expiry]);
    } catch (PDOException $e) {
        error_log("Erro ao criar token: " . $e->getMessage());
        return false;
    }
}

public function verifyPasswordResetToken($token)
{
    try {
        $stmt = $this->pdo->prepare("
            SELECT user_id, expires_at 
            FROM password_resets 
            WHERE token = ? 
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        $valido = strtotime($result['expires_at']) > time();

        return $valido;
    } catch (PDOException $e) {
        error_log("Erro na verificação: " . $e->getMessage());
        return false;
    }
}

public function getUserIdByResetToken($token)
{
    try {
        $stmt = $this->pdo->prepare("
            SELECT user_id 
            FROM password_resets 
            WHERE token = ? AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        return (int)$result['user_id'];
    } catch (PDOException $e) {
        error_log("Erro ao buscar ID por token: " . $e->getMessage());
        return false;
    }
}

public function invalidateResetToken($token)
{
    try {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        return $stmt->execute([$token]);
    } catch (PDOException $e) {
        error_log("Erro ao invalidar token: " . $e->getMessage());
        return false;
    }
}

public function updatePassword($userId, $passwordHash)
{
    try {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        return $stmt->execute([$passwordHash, $userId]);
    } catch (PDOException $e) {
        error_log("Erro ao atualizar senha: " . $e->getMessage());
        return false;
    }
}
}
