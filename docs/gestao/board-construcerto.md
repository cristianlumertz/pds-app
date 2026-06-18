# Board ConstruCerto (TCC)

Data de geração: 08/05/2026

## Feito
- [x] C001 - Inicializar projeto Laravel + Livewire + Breeze
- [x] C002 - Criar migrations base (users, cache, jobs)
- [x] C003 - Criar migrations de domínio (categories, products, product_images, carts, cart_items, addresses, orders, order_items)
- [x] C004 - Implementar models das 9 entidades
- [x] C005 - Implementar middleware admin e proteger rotas /admin/*
- [x] C006 - Implementar catálogo público (home, produtos, categorias, detalhes)
- [x] C007 - Implementar Livewire ProductSearch
- [x] C008 - Implementar Livewire ProductFilter
- [x] C009 - Implementar carrinho reativo (AddToCart, CartPage, CartSummary, CartIcon)
- [x] C010 - Implementar serviço de carrinho com transações e lock de estoque
- [x] C011 - Implementar CRUD de endereços no checkout (etapa 1)
- [x] C012 - Implementar checkout etapa 2 (seleção de pagamento)
- [x] C013 - Implementar painel admin com CRUD de categorias
- [x] C014 - Implementar painel admin com CRUD de produtos e upload de imagens
- [x] C015 - Implementar seeders (usuário admin/cliente, categorias e produtos)
- [x] C016 - Implementar testes de auth, perfil e carrinho
- [x] C017 - Validar suíte atual de testes (33 testes passando)

## A Fazer (Prioridade Alta)
- [ ] C018 - Implementar checkout etapa 3 (revisão e confirmação)
- [ ] C019 - Criar OrderController@store com DB::transaction
- [ ] C020 - Criar OrderItems no fechamento do pedido
- [ ] C021 - Limpar carrinho ao finalizar pedido
- [ ] C022 - Ajustar política de estoque para baixar no checkout final (não no carrinho)
- [ ] C023 - Disparar SendOrderConfirmationEmail ao finalizar pedido
- [ ] C024 - Disparar GenerateBoleto quando payment_method=boleto
- [ ] C025 - Criar Jobs em app/Jobs (SendOrderConfirmationEmail, GenerateBoleto)
- [ ] C026 - Criar Mailable de confirmação de pedido (app/Mail)
- [ ] C027 - Criar tela Meus Pedidos (listagem)
- [ ] C028 - Criar tela Detalhe do Pedido para cliente
- [ ] C029 - Implementar tracking_number e visualização de rastreio
- [ ] C030 - Implementar cancelamento com regra canBeCancelled()
- [ ] C031 - Criar módulo admin de pedidos (listar, atualizar status, tracking)

## A Fazer (Correções de Domínio / Arquitetura)
- [ ] C032 - Ajustar status do pedido para pending/processing/shipped/delivered/cancelled
- [ ] C033 - Remover status paid e alinhar regras de transição
- [ ] C034 - Trocar trackingNumber para tracking_number (migration/model)
- [ ] C035 - Ajustar User::carts() para User::cart() (hasOne)
- [ ] C036 - Garantir unique(user_id) em carts
- [ ] C037 - Adicionar campo phone em users (migration/model/form/validação)
- [ ] C038 - Criar Rule CpfValido em app/Rules e usar em requests
- [ ] C039 - Implementar mutator/accessor de CPF (salvar sem máscara e exibir com máscara)
- [ ] C040 - Migrar validações inline para Form Requests (checkout/admin/pedidos)
- [ ] C041 - Substituir closures de checkout por controllers dedicados

## A Fazer (Brasil, Qualidade e Entrega)
- [ ] C042 - Configurar locale pt_BR e fallback pt_BR
- [ ] C043 - Configurar timezone America/Sao_Paulo
- [ ] C044 - Publicar traduções/lang pt-BR para auth/validação
- [ ] C045 - Implementar máscara/normalização de telefone e CEP em todas as telas
- [ ] C046 - Resolver duplicidade de QUEUE_CONNECTION no .env
- [ ] C047 - Migrar base de testes para PestPHP (tests/Pest.php)
- [ ] C048 - Criar testes de checkout completo (pedido/itens/estoque/jobs)
- [ ] C049 - Criar testes de máquina de estados de Order
- [ ] C050 - Criar testes de permissões admin x cliente em pedidos
- [ ] C051 - Criar documentação técnica do projeto (README de produto)
- [ ] C052 - Preparar documentação final do TCC (deploy + arquitetura + evidências)

## Organização sugerida por sprint
- Sprint 4: C018-C022, C032-C034
- Sprint 5: C023-C031
- Sprint 6: C038-C041, C047-C050
- Sprint 7: C042-C046, C051-C052
