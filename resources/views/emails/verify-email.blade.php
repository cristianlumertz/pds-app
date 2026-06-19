<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirme seu e-mail — ConstruCerto</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1EFE8; color: #3D3D3A; font-family: Inter, Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; background-color: #F1EFE8; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; max-width: 580px; background-color: #FFFFFF; border-collapse: collapse; border-radius: 12px;">
                    <tr>
                        <td style="padding: 36px 40px 16px; text-align: center;">
                            <div style="color: #185FA5; font-size: 26px; font-weight: 700; line-height: 1.2;">ShopLaravel</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 40px 40px;">
                            <h1 style="margin: 0 0 20px; color: #3D3D3A; font-size: 26px; font-weight: 700; line-height: 1.3; text-align: center;">
                                Confirme seu e-mail, {{ $userName }}!
                            </h1>

                            <p style="margin: 0 0 28px; color: #3D3D3A; font-size: 16px; line-height: 1.6; text-align: center;">
                                Clique no botão abaixo para ativar sua conta. O link expira em 60 minutos.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td align="center" style="padding: 0 0 28px;">
                                        <a href="{{ $url }}" style="display: inline-block; padding: 15px 28px; background-color: #1D9E75; color: #FFFFFF; font-size: 16px; font-weight: 700; line-height: 1.2; text-decoration: none; border-radius: 8px;">
                                            Confirmar meu e-mail
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; padding: 18px; background-color: #F1EFE8; color: #3D3D3A; font-size: 14px; line-height: 1.5; text-align: center; border-radius: 8px;">
                                Se não foi você quem criou a conta, ignore este e-mail.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 22px 40px; border-top: 1px solid #F1EFE8; color: #3D3D3A; font-size: 13px; line-height: 1.5; text-align: center;">
                            ConstruCerto — Materiais de Construção
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
