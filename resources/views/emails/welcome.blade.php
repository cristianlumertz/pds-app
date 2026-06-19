<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao ShopLaravel</title>
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
                            <h1 style="margin: 0 0 20px; color: #3D3D3A; font-size: 27px; font-weight: 700; line-height: 1.3; text-align: center;">
                                Seja bem-vindo, {{ $user->name }}! 🎉
                            </h1>

                            <p style="margin: 0 0 28px; color: #3D3D3A; font-size: 16px; line-height: 1.6; text-align: center;">
                                Sua conta foi verificada com sucesso. Agora você pode explorar o nosso catálogo de materiais de construção.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 14px 16px; background-color: #F1EFE8; border-bottom: 6px solid #FFFFFF; border-radius: 8px; color: #3D3D3A; font-size: 15px; line-height: 1.5;">
                                        <span style="display: inline-block; width: 32px; font-size: 22px; vertical-align: middle;">🔨</span>
                                        <span style="vertical-align: middle;">Catálogo completo de ferramentas e materiais</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 16px; background-color: #F1EFE8; border-bottom: 6px solid #FFFFFF; border-radius: 8px; color: #3D3D3A; font-size: 15px; line-height: 1.5;">
                                        <span style="display: inline-block; width: 32px; font-size: 22px; vertical-align: middle;">🚚</span>
                                        <span style="vertical-align: middle;">Frete grátis em compras acima de R$ 299</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 16px; background-color: #F1EFE8; border-radius: 8px; color: #3D3D3A; font-size: 15px; line-height: 1.5;">
                                        <span style="display: inline-block; width: 32px; font-size: 22px; vertical-align: middle;">📦</span>
                                        <span style="vertical-align: middle;">Acompanhe seus pedidos em tempo real</span>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; margin-top: 28px; border-collapse: collapse;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}/produtos" style="display: inline-block; padding: 15px 30px; background-color: #185FA5; color: #FFFFFF; font-size: 16px; font-weight: 700; line-height: 1.2; text-decoration: none; border-radius: 8px;">
                                            Explorar o catálogo
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <div style="margin-top: 28px; padding: 20px; border: 2px dashed #1D9E75; border-radius: 10px; text-align: center;">
                                <div style="margin-bottom: 8px; color: #1D9E75; font-size: 22px; font-weight: 700; letter-spacing: 2px;">BEMVINDO10</div>
                                <p style="margin: 0; color: #3D3D3A; font-size: 14px; line-height: 1.5;">
                                    Use o cupom <strong>BEMVINDO10</strong> para 10% de desconto na primeira compra
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 22px 40px; border-top: 1px solid #F1EFE8; color: #3D3D3A; font-size: 12px; line-height: 1.6; text-align: center;">
                            ShopLaravel — Materiais de Construção | Você recebeu este e-mail pois criou uma conta.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
