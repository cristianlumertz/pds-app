<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaignTitle }} — ShopLaravel</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1EFE8; color: #3D3D3A; font-family: Inter, Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; background-color: #F1EFE8; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; max-width: 600px; overflow: hidden; background-color: #FFFFFF; border-collapse: collapse; border-radius: 12px;">
                    <tr>
                        <td style="position: relative; overflow: hidden; padding: 32px 40px 50px; background-color: #185FA5; text-align: center;">
                            <div style="position: relative; z-index: 2; color: #FFFFFF; font-size: 27px; font-weight: 700; line-height: 1.2;">ShopLaravel</div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 70" preserveAspectRatio="none" width="600" height="70" style="position: absolute; bottom: -1px; left: 0; z-index: 1; display: block; width: 100%; height: 52px;">
                                <path d="M0 35 C100 5 200 65 300 35 C400 5 500 65 600 35 L600 70 L0 70 Z" fill="#FFFFFF" opacity="0.28"></path>
                                <path d="M0 48 C120 18 220 72 340 42 C450 15 530 55 600 40 L600 70 L0 70 Z" fill="#FFFFFF"></path>
                            </svg>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 22px 40px 40px;">
                            <h1 style="margin: 0 0 20px; color: #3D3D3A; font-size: 28px; font-weight: 700; line-height: 1.3; text-align: center;">
                                {{ $campaignTitle }}
                            </h1>

                            <p style="margin: 0 0 26px; color: #3D3D3A; font-size: 16px; line-height: 1.7; text-align: center;">
                                {{ $campaignBody }}
                            </p>

                            @if ($couponCode)
                                <div style="margin-bottom: 28px; padding: 20px; border: 2px dashed #1D9E75; border-radius: 10px; background-color: #FFFFFF; text-align: center;">
                                    <div style="color: #1D9E75; font-family: 'Courier New', Courier, monospace; font-size: 27px; font-weight: 700; letter-spacing: 2px; line-height: 1.3;">
                                        {{ $couponCode }}
                                    </div>

                                    @if ($couponDescription)
                                        <p style="margin: 8px 0 0; color: #3D3D3A; font-size: 14px; line-height: 1.5;">
                                            {{ $couponDescription }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                                <tr>
                                    <td width="33.33%" valign="top" style="padding: 0 4px; text-align: center;">
                                        <img src="https://via.placeholder.com/160x120" width="160" height="120" alt="Ferramentas em oferta" style="display: block; width: 100%; max-width: 160px; height: auto; margin: 0 auto; border: 0; border-radius: 8px;">
                                        <p style="margin: 10px 0 0; color: #3D3D3A; font-size: 13px; font-weight: 700; line-height: 1.4;">Ferramentas em oferta</p>
                                    </td>
                                    <td width="33.33%" valign="top" style="padding: 0 4px; text-align: center;">
                                        <img src="https://via.placeholder.com/160x120" width="160" height="120" alt="Tintas" style="display: block; width: 100%; max-width: 160px; height: auto; margin: 0 auto; border: 0; border-radius: 8px;">
                                        <p style="margin: 10px 0 0; color: #3D3D3A; font-size: 13px; font-weight: 700; line-height: 1.4;">Tintas</p>
                                    </td>
                                    <td width="33.33%" valign="top" style="padding: 0 4px; text-align: center;">
                                        <img src="https://via.placeholder.com/160x120" width="160" height="120" alt="Materiais básicos" style="display: block; width: 100%; max-width: 160px; height: auto; margin: 0 auto; border: 0; border-radius: 8px;">
                                        <p style="margin: 10px 0 0; color: #3D3D3A; font-size: 13px; font-weight: 700; line-height: 1.4;">Materiais básicos</p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; margin-top: 30px; border-collapse: collapse;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $ctaUrl }}" style="display: inline-block; padding: 15px 30px; background-color: #1D9E75; color: #FFFFFF; font-size: 16px; font-weight: 700; line-height: 1.2; text-decoration: none; border-radius: 8px;">
                                            {{ $ctaText }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 40px; border-top: 1px solid #F1EFE8; color: #3D3D3A; font-size: 12px; line-height: 1.7; text-align: center;">
                            <strong style="color: #185FA5;">ShopLaravel — Materiais de Construção</strong>
                            <br>
                            Você recebeu este e-mail pois se cadastrou em nosso site.
                            <br>
                            <a href="{{ config('app.url') }}/newsletter/cancelar?email={{ urlencode($notifiable->email ?? '') }}" style="color: #993C1D; text-decoration: underline;">
                                Cancelar inscrição
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
