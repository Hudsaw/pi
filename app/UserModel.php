<?php
class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function autenticar($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['senha'])) {
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
            (nome, email, senha, tipo, cpf, telefone, cep, complemento, banco, agencia, conta, especialidade_id, nivel)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $success = $stmt->execute([
            $data['nome'],
            $data['email'],
            password_hash($data['senha'], PASSWORD_DEFAULT),
            'costureira',
            $data['cpf'],
            $data['telefone'],
            $data['cep'],
            $data['complemento'],
            $data['banco'],
            $data['agencia'],
            $data['conta'],
            $data['especialidade_id'] ?? null,
            'bronze'
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
            'nome'      => $data['nome'],
            'email'     => $data['email'],
            'telefone'  => $data['telefone'],
            'cpf'       => $data['cpf'],
            'cep'       => $data['cep'],
            'complemento' => $data['complemento'],
            'banco' => $data['banco'],
            'agencia'     => $data['agencia'],
            'conta' => $data['conta'],
            'especialidade_id' => $data['especialidade_id']
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

    public function getUserPeloId($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, e.nome as especialidade
            FROM usuarios u
            LEFT JOIN especialidade a ON u.especialidade_id = a.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

}
