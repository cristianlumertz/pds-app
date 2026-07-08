# Diagrama do banco de dados

Este diagrama foi gerado a partir das migrations e models do projeto. Ele foca nas tabelas de dominio da loja: usuarios, catalogo, carrinho, pedidos, pagamentos, cupons e estoque.

```mermaid
erDiagram
    USERS ||--o{ ADDRESSES : possui
    USERS ||--o{ CARTS : possui
    USERS ||--o{ ORDERS : realiza
    USERS ||--o{ STOCK_MOVEMENTS : registra

    CATEGORIES ||--o{ CATEGORIES : agrupa
    CATEGORIES ||--o{ PRODUCTS : classifica
    PRODUCTS ||--o{ PRODUCT_IMAGES : possui
    PRODUCTS ||--o{ CART_ITEMS : aparece_em
    PRODUCTS ||--o{ ORDER_ITEMS : vendido_em
    PRODUCTS ||--o{ STOCK_MOVEMENTS : movimenta

    CARTS ||--o{ CART_ITEMS : contem

    ADDRESSES ||--o{ ORDERS : usado_em
    ORDERS ||--o{ ORDER_ITEMS : contem
    ORDERS ||--o{ PAYMENTS : possui
    ORDERS ||--o{ PAYMENT_EVENTS : recebe
    ORDERS ||--o{ ORDER_COUPONS : aplica
    ORDERS ||--o{ STOCK_MOVEMENTS : gera

    PAYMENTS ||--o{ PAYMENT_EVENTS : gera
    COUPONS ||--o{ ORDER_COUPONS : usado_em

    USERS {
        bigint id PK
        string name
        string email UK
        string cpf UK
        string phone
        timestamp email_verified_at
        string password
        boolean is_admin
        boolean newsletter_opt_in
        string status
        timestamp created_at
        timestamp updated_at
    }

    ADDRESSES {
        bigint id PK
        bigint user_id FK
        string street
        string number
        string complement
        string city
        string state
        string zip_code
        string country
        tinyint is_default
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        bigint parent_id FK
        string name
        string slug UK
        text description
        tinyint is_active
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTS {
        bigint id PK
        bigint category_id FK
        string name
        string slug UK
        text description
        string sku UK
        decimal price
        int stock
        tinyint is_active
        string image_url
        timestamp created_at
        timestamp updated_at
    }

    PRODUCT_IMAGES {
        bigint id PK
        bigint product_id FK
        string url
        string alt_text
        int order
        timestamp created_at
        timestamp updated_at
    }

    CARTS {
        bigint id PK
        bigint user_id FK
        decimal total_price
        int item_count
        timestamp created_at
        timestamp updated_at
    }

    CART_ITEMS {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        int quantity
        decimal price
        timestamp created_at
        timestamp updated_at
    }

    ORDERS {
        bigint id PK
        bigint user_id FK
        bigint address_id FK
        enum status
        decimal subtotal_amount
        decimal discount_amount
        decimal shipping_amount
        decimal total_amount
        string shipping_method
        string shipping_status
        string delivery_estimate
        string payment_method
        string payment_status
        string pagarme_payment_link_id
        string pagarme_checkout_url
        string tracking_number
        timestamp created_at
        timestamp updated_at
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        string product_name
        string product_sku
        int quantity
        decimal price
        timestamp created_at
        timestamp updated_at
    }

    PAYMENTS {
        bigint id PK
        bigint order_id FK
        string payment_method
        string status
        decimal amount
        string pagarme_payment_link_id
        string pagarme_checkout_url
        string pagarme_order_id
        string pagarme_charge_id
        string pagarme_transaction_id
        text pix_qr_code
        timestamp pix_expires_at
        string boleto_url
        string boleto_barcode
        timestamp paid_at
        timestamp cancelled_at
        timestamp refunded_at
        timestamp created_at
        timestamp updated_at
    }

    PAYMENT_EVENTS {
        bigint id PK
        bigint payment_id FK
        bigint order_id FK
        string pagarme_event_id
        string event_type
        json payload
        timestamp processed_at
        timestamp created_at
        timestamp updated_at
    }

    COUPONS {
        bigint id PK
        string code UK
        text description
        string discount_type
        decimal discount_value
        decimal min_order_amount
        timestamp starts_at
        timestamp expires_at
        int usage_limit
        int used_count
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    ORDER_COUPONS {
        bigint id PK
        bigint order_id FK
        bigint coupon_id FK
        decimal discount_amount
        timestamp created_at
        timestamp updated_at
    }

    STOCK_MOVEMENTS {
        bigint id PK
        bigint product_id FK
        bigint user_id FK
        bigint order_id FK
        string type
        int quantity
        text reason
        timestamp created_at
        timestamp updated_at
    }
```

## Observacoes

- `order_coupons` representa a relacao N:N entre `orders` e `coupons`, guardando tambem o desconto aplicado no pedido.
- `payment_events` pode apontar para `payments` e/ou `orders`; ambas as chaves sao nullable na migration.
- `stock_movements.user_id` e `stock_movements.order_id` tambem sao nullable, porque uma movimentacao pode nao estar ligada a usuario ou pedido.
- `categories.parent_id` permite hierarquia de categorias.
- `order_items.product_name` e `order_items.product_sku` sao snapshots do produto no momento da compra.

## Tabelas de infraestrutura Laravel

Estas tabelas existem no projeto, mas ficaram fora do ERD principal por nao fazerem parte direta do dominio da loja:

- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
