<?php
/**
 * senha.php - Actions de recuperação e alteração de senha.
 * Rota via ?action=recuperar | validar | alterar
 */

session_start();

require_once __DIR__ . '/../functions/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'recuperar': action_recuperar(); break;
    case 'validar':   action_validar();   break;
    case 'alterar':   action_alterar();   break;
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
}

// ─── Handlers ────────────────────────────────────────────────────────────────

function action_recuperar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }

    $pdo     = db_connect();
    $usuario = trim($_POST['usuario'] ?? '');
    $email   = trim($_POST['email']   ?? '');

    if (empty($usuario) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos!']); return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'E-mail inválido.']); return;
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nome = :usuario AND email = :email LIMIT 1");
    $stmt->execute([':usuario' => $usuario, ':email' => $email]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário ou e-mail não encontrado.']); return;
    }

    $_SESSION['usuario_email'] = $email;

    $codigo = strtoupper(bin2hex(random_bytes(4)));
    $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);

    try {
        $pdo->prepare(
            "INSERT INTO recuperar_senha (usuario, email, codigo, criado_em) VALUES (:usuario, :email, :codigo, NOW())"
        )->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
    } catch (PDOException $e) {
        $pdo->prepare(
            "INSERT INTO recuperar_senha (usuario, email, codigo) VALUES (:usuario, :email, :codigo)"
        )->execute([':usuario' => $usuario, ':email' => $email, ':codigo' => $codigo]);
    }

    $subject  = mb_encode_mimeheader('Código de Recuperação de Senha — Hemodat', 'UTF-8', 'B', "\r\n");
    $ttl      = RESET_CODE_TTL;
    $message  = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#F1F5F9;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr><td align="center">
      <table width="520" cellpadding="0" cellspacing="0"
             style="background:#fff;border-radius:12px;box-shadow:0 2px 16px rgba(0,0,0,.08);overflow:hidden;">
        <!-- Header -->
        <tr>
          <td style="background:#DC2626;padding:28px 40px;text-align:center;">
            <span style="font-size:22px;font-weight:700;color:#fff;letter-spacing:1px;">🩸 HEMODAT</span>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:36px 40px;">
            <p style="margin:0 0 12px;font-size:16px;color:#1E293B;">Olá, <strong>{$usuario}</strong>!</p>
            <p style="margin:0 0 24px;font-size:14px;color:#475569;line-height:1.6;">
              Recebemos uma solicitação de recuperação de senha para a sua conta no Hemodat.
              Use o código abaixo para continuar:
            </p>
            <!-- Code box -->
            <div style="text-align:center;margin:0 0 24px;">
              <span style="display:inline-block;background:#FEF2F2;border:2px dashed #DC2626;
                           border-radius:10px;padding:18px 40px;font-size:34px;font-weight:700;
                           letter-spacing:8px;color:#DC2626;">{$codigo}</span>
            </div>
            <p style="margin:0 0 8px;font-size:13px;color:#94A3B8;text-align:center;">
              ⏱ Este código expira em <strong>{$ttl} minutos</strong>.
            </p>
            <hr style="border:none;border-top:1px solid #E2E8F0;margin:24px 0;">
            <p style="margin:0;font-size:12px;color:#94A3B8;line-height:1.6;">
              Se você não solicitou a recuperação de senha, ignore este e-mail.
              Sua senha permanece inalterada.
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#F8FAFC;padding:16px 40px;text-align:center;border-top:1px solid #E2E8F0;">
            <p style="margin:0;font-size:11px;color:#94A3B8;">
              © 2025 Hemodat &nbsp;·&nbsp;
              <a href="mailto:suporte@hemodatgp.com" style="color:#DC2626;text-decoration:none;">suporte@hemodatgp.com</a>
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    $headers  = "From: Hemodat <noreply@hemodatgp.com>\r\nReply-To: suporte@hemodatgp.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\nMIME-Version: 1.0\r\n";

    // Em ambiente local mail() não envia — expõe código no JSON para testes
    if (IS_LOCAL) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Código enviado com sucesso!',
            'debug'   => "[LOCAL] Código: $codigo",   // visível só no DevTools Network
        ]);
        return;
    }

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Código enviado com sucesso!',
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar o e-mail. Tente novamente.']);
    }
}

function action_validar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }
    if (!isset($_SESSION['usuario_email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']); return;
    }

    $pdo    = db_connect();
    $email  = trim($_SESSION['usuario_email']);
    $codigo = strtoupper(trim($_POST['code'] ?? ''));

    if (empty($codigo)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, insira o código.']); return;
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT id, criado_em FROM recuperar_senha WHERE email = :email AND codigo = :codigo LIMIT 1"
        );
        $stmt->execute([':email' => $email, ':codigo' => $codigo]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $stmt = $pdo->prepare("SELECT id FROM recuperar_senha WHERE email = :email AND codigo = :codigo LIMIT 1");
        $stmt->execute([':email' => $email, ':codigo' => $codigo]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$record) {
        echo json_encode(['status' => 'error', 'message' => 'Código inválido ou expirado.']); return;
    }

    if (!empty($record['criado_em'])) {
        $diff_min = (time() - strtotime($record['criado_em'])) / 60;
        if ($diff_min > RESET_CODE_TTL) {
            $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);
            echo json_encode(['status' => 'error', 'message' => 'Código expirado. Solicite um novo.']); return;
        }
    }

    $pdo->prepare("DELETE FROM recuperar_senha WHERE email = :email")->execute([':email' => $email]);
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Código validado!',
        'redirect' => BASE_URL . '/alterar_senha',
    ]);
}

function action_alterar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido.']); exit;
    }
    if (!isset($_SESSION['usuario_email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']); return;
    }

    $pdo          = db_connect();
    $email        = $_SESSION['usuario_email'];
    $senha        = trim($_POST['senha']         ?? '');
    $confirmSenha = trim($_POST['confirm-senha'] ?? '');

    if (empty($senha) || empty($confirmSenha)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos.']); return;
    }
    if ($senha !== $confirmSenha) {
        echo json_encode(['status' => 'error', 'message' => 'As senhas não coincidem.']); return;
    }
    if (strlen($senha) < 9) {
        echo json_encode(['status' => 'error', 'message' => 'A senha deve ter pelo menos 9 caracteres.']); return;
    }

    $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE email = :email")
        ->execute([':senha' => password_hash($senha, PASSWORD_BCRYPT), ':email' => $email]);

    session_unset();
    session_destroy();

    echo json_encode([
        'status'   => 'success',
        'message'  => 'Senha alterada! Faça login novamente.',
        'redirect' => BASE_URL . '/login',
    ]);
}
