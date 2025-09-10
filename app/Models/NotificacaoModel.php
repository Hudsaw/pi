<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class NotificacaoModel
{
    private $pdo;
    private $userModel;

    public function __construct()
    {
        $this->pdo          = Database::getInstance();
        $this->userModel    = new UserModel($this->pdo);
    }

    public function enviarNotificacao(
        array $usuariosIds,
        string $titulo,
        string $mensagem,
        string $tipo = 'sistema',
        ?string $entidade = null,
        ?int $idEntidade = null
    ): bool {
        try {
            $this->pdo->beginTransaction();

            $sucesso = true;
            foreach ($usuariosIds as $usuarioId) {
                $registroOk = $this->criarNotificacao(
                    $usuarioId,
                    $titulo,
                    $mensagem,
                    $tipo,
                    $entidade,
                    $idEntidade
                );

                if (! $registroOk) {
                    $sucesso = false;
                    break;
                }

                // Envia e-mail se necessário
                if (in_array($tipo, ['email', 'ambos'])) {
                    $this->enviarEmailUnico($usuarioId, $titulo, $mensagem);
                }
            }

            if ($sucesso) {
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log("Erro ao enviar notificacao: " . $e->getMessage());
            return false;
        }
    }

    private function enviarEmail(int $usuarioId, string $titulo, string $mensagem): bool
    {
        $usuario = $this->userModel->gerenciarUsuarios($usuarioId);

        if (! $usuario || empty($usuario['email'])) {
            error_log("Usuário $usuarioId não encontrado ou sem e-mail");
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // Destinatário
            $mail->setFrom(SMTP_FROM, 'Sistema de Notificacao');
            $mail->addAddress($usuario['email'], $usuario['nome']);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = $titulo;
            $mail->Body    = nl2br($mensagem);
            $mail->AltBody = strip_tags($mensagem);

            return $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: " . $e->getMessage());
            return false;
        }
    }

    public function criarNotificacao(
        int $usuarioId,
        string $titulo,
        string $mensagem,
        string $tipo,
        ?string $entidade = null,
        ?int $idEntidade = null,
        ?string $url = null
    ): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notificacoes
                (usuario_id, titulo, mensagem, tipo, entidade, id_entidade, url)
                VALUES (:usuario_id, :titulo, :mensagem, :tipo, :entidade, :id_entidade, :url)
            ");

            return $stmt->execute([
                ':usuario_id'  => $usuarioId,
                ':titulo'      => $titulo,
                ':mensagem'    => $mensagem,
                ':tipo'        => $tipo,
                ':entidade'    => $entidade,
                ':id_entidade' => $idEntidade,
                ':url'         => $url,
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao criar notificacao: " . $e->getMessage());
            return false;
        }
    }

    public function contarNaoLidas(int $usuarioId): int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM notificacoes
                                       WHERE usuario_id = ? AND lida = 0");
            $stmt->execute([$usuarioId]);
            $result = $stmt->fetch();
            return (int) $result['total'];
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao contar notificacao: " . $e->getMessage());
        }
    }

    public function excluirNotificacoes(int $usuarioId, array $notificacoesIds): bool
    {
        try {
            $placeholders = implode(',', array_fill(0, count($notificacoesIds), '?'));

            $stmt = $this->pdo->prepare("
            DELETE FROM notificacoes
            WHERE id IN ($placeholders) AND usuario_id = ?
        ");

            $params = array_merge($notificacoesIds, [$usuarioId]);

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erro ao excluir notificações: " . $e->getMessage());
            return false;
        }
    }

    public function getNotificacoesPorUsuario(int $usuarioId, int $limit = 50): array
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT * FROM notificacoes
            WHERE usuario_id = :usuario_id
            ORDER BY data_criacao DESC
            LIMIT :limit
        ");
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar notificações do usuário: " . $e->getMessage());
            return [];
        }
    }

    public function enviarEmailResetSenha(string $email, string $resetUrl): bool
    {
        try {

            $mail = new PHPMailer(true);

            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // Remetente e destinatário
            $mail->setFrom(SMTP_FROM, 'Sistema de Redefinicao de Senha');
            $mail->addAddress($email);

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Redefinicao de Senha';
            $mail->Body    = "Clique no link para redefinir sua senha: <a href='{$resetUrl}'>{$resetUrl}</a><br><br>
                O link expira em 1 hora.<br>" .
                "Se você não solicitou isso, ignore este e-mail.";
            $mail->AltBody = "Clique no link para redefinir sua senha: $resetLink\n\n" .
                "O link expira em 1 hora.\n" .
                "Se você não solicitou isso, ignore este e-mail.";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de reset de senha: " . $e->getMessage());
            return false;
        }
    }

}