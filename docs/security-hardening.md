# Hardening HTTPS e Security Headers

Este projeto aplica uma camada inicial de hardening para reduzir downgrade HTTP, mixed content e ausência de headers de segurança.

## Headers Implementados

O middleware `App\Http\Middleware\SecurityHeaders` adiciona nas respostas web:

- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=(), bluetooth=(), accelerometer=(), gyroscope=()`
- `Content-Security-Policy`

A CSP de produção é compatível com Blade, Livewire, ViaCEP e Pagar.me:

```text
default-src 'self';
base-uri 'self';
object-src 'none';
frame-ancestors 'none';
form-action 'self' https://*.pagar.me https://*.pagarme.com.br;
script-src 'self' 'unsafe-inline' 'unsafe-eval' https:;
style-src 'self' 'unsafe-inline' https:;
img-src 'self' data: https:;
font-src 'self' data: https:;
connect-src 'self' https://*.pagar.me https://*.pagarme.com.br https://viacep.com.br;
upgrade-insecure-requests;
```

`unsafe-inline` e `unsafe-eval` continuam liberados nesta etapa para evitar quebra em Blade, Livewire e Vite. A próxima evolução recomendada é trocar isso por nonce/hash e remover permissões amplas depois de medir impacto em staging.

Em `local` e `testing`, a CSP não envia `upgrade-insecure-requests` e libera o Vite dev server em HTTP/WebSocket:

- `http://localhost:5173`
- `http://127.0.0.1:5173`
- `ws://localhost:5173`
- `ws://127.0.0.1:5173`

Se usar outra porta, configure:

```env
VITE_DEV_SERVER_URL=http://localhost:5173
```

Durante `npm run dev`, o middleware também lê o arquivo `public/hot` gerado pelo Laravel Vite. Assim, se a porta 5173 estiver ocupada e o Vite subir em outra porta local, a CSP local consegue liberar a origem ativa sem afetar produção.

## HSTS

`Strict-Transport-Security: max-age=31536000; includeSubDomains` é enviado apenas em `production`.

Não usamos `preload` nesta etapa. HSTS não deve ser ativado em local, testing ou staging porque o navegador passa a forçar HTTPS para o domínio durante o tempo do `max-age`, o que atrapalha ambientes temporários ou sem certificado válido.

## HTTPS em Produção

Em `production`, `AppServiceProvider` força geração de URLs com HTTPS:

```php
URL::forceScheme('https');
```

Em local/testing, o projeto continua funcionando em `http://localhost:8000`.

## Proxies

O Laravel está preparado para confiar em headers de proxy quando `TRUSTED_PROXIES` for configurado.

Use um valor explícito em produção, como IP/CIDR do proxy, ou `REMOTE_ADDR` quando houver apenas um reverse proxy confiável diretamente na frente da aplicação.

Exemplo:

```env
TRUSTED_PROXIES=REMOTE_ADDR
```

Não configure `*` sem necessidade. Confiar em qualquer origem pode permitir spoofing de headers como `X-Forwarded-Proto`.

## Nginx

Exemplo mínimo de redirecionamento HTTP para HTTPS:

```nginx
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name seudominio.com www.seudominio.com;

    root /var/www/construcerto/public;
    index index.php;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## Como Testar

Em produção ou staging HTTPS:

```bash
curl -I https://seudominio.com
```

Verifique a presença de:

- `Content-Security-Policy`
- `X-Frame-Options`
- `X-Content-Type-Options`
- `Referrer-Policy`
- `Permissions-Policy`
- `Strict-Transport-Security` somente em produção

Também abra o DevTools do navegador e confirme que não há avisos de mixed content.

## Webhook Local

Para testar retornos, webhooks e success URLs da Pagar.me em ambiente local, use uma URL pública HTTPS, por exemplo ngrok ou cloudflared:

```env
APP_URL=https://seu-link-ngrok.ngrok-free.app
PAGARME_SUCCESS_URL=https://seu-link-ngrok.ngrok-free.app/checkout/sucesso/{order_id}
```

## Mixed Content

Novas URLs externas de imagem manual de produto e CTA de newsletter são bloqueadas em produção quando usam `http://`.

Registros antigos no banco não são apagados automaticamente. Antes de colocar em produção, audite:

- `products.image_url`
- `product_images.url`
- `product_images.image_url`
- links de checkout Pagar.me salvos em `orders` e `payments`

Prefira uploads locais via `Storage::url()` ou URLs externas HTTPS.

## Checklist de Produção

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seudominio.com`
- `SESSION_SECURE_COOKIE=true`
- `SESSION_HTTP_ONLY=true`
- `SESSION_SAME_SITE=lax`
- `TRUSTED_PROXIES` configurado com proxy confiável
- Redirect HTTP para HTTPS no Nginx/Cloudflare/Load Balancer
- Certificado TLS válido
- Pagar.me usando endpoints HTTPS
- Success/cancel URLs públicas HTTPS quando configuradas
- Banco saneado contra imagens externas `http://`
- DevTools sem mixed content
- `curl -I` confirmando headers
