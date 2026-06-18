## [C018] Implementar checkout etapa 3 revisão
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Checkout
Descrição: Criar tela de revisão final antes de confirmar pedido.

## [C019] Criar OrderController@store
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Checkout
Descrição: Finalizar pedido dentro de DB::transaction.

## [C020] Criar OrderItems no fechamento
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Checkout
Descrição: Gerar itens do pedido a partir do carrinho.

## [C021] Limpar carrinho após pedido
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Checkout
Descrição: Limpar itens e totais ao confirmar pedido.

## [C022] Ajustar baixa de estoque para checkout final
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Checkout
Descrição: Retirar baixa no carrinho e aplicar no fechamento do pedido.

## [C023] Disparar job de confirmação por e-mail
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S5
Área: Queue
Descrição: Dispatch de SendOrderConfirmationEmail após checkout.

## [C024] Disparar job de boleto
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S5
Área: Queue
Descrição: Dispatch de GenerateBoleto quando método for boleto.

## [C025] Criar jobs de fila
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S5
Área: Queue
Descrição: Criar classes SendOrderConfirmationEmail e GenerateBoleto.

## [C026] Criar mailable de confirmação
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S5
Área: Mail
Descrição: Criar template e mailable de confirmação do pedido.

## [C027] Criar tela Meus Pedidos
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Pedidos
Descrição: Listagem de pedidos do cliente com paginação e status.

## [C028] Criar tela Detalhe do Pedido
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Pedidos
Descrição: Exibir itens endereço pagamento e total do pedido.

## [C029] Implementar tracking_number
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S5
Área: Pedidos
Descrição: Salvar e exibir código de rastreio.

## [C030] Implementar cancelamento com regra de estado
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Pedidos
Descrição: Permitir cancelamento apenas em pending/processing.

## [C031] Criar admin de pedidos
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S5
Área: Admin
Descrição: Listar pedidos, atualizar status e tracking no admin.

## [C032] Ajustar enum de status do pedido
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Domínio
Descrição: Alinhar para pending processing shipped delivered cancelled.

## [C033] Remover status paid e ajustar transições
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Domínio
Descrição: Atualizar constantes e regras de cancelamento/transição.

## [C034] Renomear trackingNumber para tracking_number
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Domínio
Descrição: Ajustar migration model e usos no código.

## [C035] Ajustar User cart para hasOne
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S4
Área: Domínio
Descrição: Trocar relação de carts hasMany para cart hasOne.

## [C036] Garantir unique user_id em carts
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S4
Área: Banco
Descrição: Adicionar restrição única para um carrinho por usuário.

## [C037] Adicionar campo phone em users
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S2
Área: Auth
Descrição: Migration model form e validação para telefone.

## [C038] Criar Rule CpfValido reutilizável
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S2
Área: Validação
Descrição: Criar app/Rules/CpfValido e usar nos requests.

## [C039] Criar mutator e accessor de CPF
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S2
Área: Domínio
Descrição: Salvar sem máscara e exibir com máscara.

## [C040] Migrar validações inline para FormRequests
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S5
Área: Arquitetura
Descrição: Padronizar validações em requests dedicados.

## [C041] Substituir closures de checkout por controllers
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S4
Área: Arquitetura
Descrição: Organizar rotas de checkout em controllers.

## [C042] Configurar locale pt_BR
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S7
Área: Configuração
Descrição: Definir APP_LOCALE e fallback para pt_BR.

## [C043] Configurar timezone America Sao_Paulo
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S7
Área: Configuração
Descrição: Ajustar config/app.php e ambiente.

## [C044] Adicionar traduções pt-BR
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S7
Área: I18n
Descrição: Publicar arquivos lang e padronizar mensagens.

## [C045] Padronizar máscara de telefone e CEP
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S7
Área: UX BR
Descrição: Aplicar máscara no front e normalização no backend.

## [C046] Resolver duplicidade QUEUE_CONNECTION
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S7
Área: Configuração
Descrição: Remover chave duplicada no .env e validar fila.

## [C047] Migrar testes para PestPHP
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S6
Área: Testes
Descrição: Criar tests/Pest.php e converter gradualmente testes.

## [C048] Testar checkout completo com jobs
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S6
Área: Testes
Descrição: Cobrir pedido itens estoque limpeza de carrinho e dispatch de jobs.

## [C049] Testar máquina de estados de Order
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S6
Área: Testes
Descrição: Cobrir transições e restrições de cancelamento/envio.

## [C050] Testar permissões de pedidos
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S6
Área: Testes
Descrição: Validar acessos cliente e admin em endpoints de pedido.

## [C051] Criar README técnico do produto
Lista: A Fazer
Status: Todo
Prioridade: Média
Sprint: S7
Área: Documentação
Descrição: Documentação funcional e técnica do sistema.

## [C052] Preparar documentação final do TCC
Lista: A Fazer
Status: Todo
Prioridade: Alta
Sprint: S7
Área: Documentação
Descrição: Checklist final de deploy, arquitetura e evidências.

## [C001] Inicializar projeto Laravel + Livewire + Breeze
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S1
Área: Infra
Descrição: Base do projeto configurada com autenticação Breeze e Livewire.

## [C002] Criar migrations base
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S1
Área: Banco
Descrição: Migrations de users, cache e jobs criadas.

## [C003] Criar migrations de domínio
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S1
Área: Banco
Descrição: Migrations de categories, products, product_images, carts, cart_items, addresses, orders e order_items.

## [C004] Implementar models das 9 entidades
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S1
Área: Domínio
Descrição: Models criados para todo o domínio principal.

## [C005] Implementar middleware admin
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S2
Área: Segurança
Descrição: Middleware admin criado e aplicado nas rotas administrativas.

## [C006] Implementar catálogo público
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S2
Área: Storefront
Descrição: Home, listagem de produtos, categorias e página de detalhe implementadas.

## [C007] Implementar ProductSearch Livewire
Lista: Feito
Status: Done
Prioridade: Média
Sprint: S2
Área: Livewire
Descrição: Busca reativa de produtos implementada.

## [C008] Implementar ProductFilter Livewire
Lista: Feito
Status: Done
Prioridade: Média
Sprint: S2
Área: Livewire
Descrição: Filtros reativos por categoria preço e status implementados.

## [C009] Implementar carrinho reativo Livewire
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S3
Área: Carrinho
Descrição: Componentes AddToCart, CartPage, CartSummary e CartIcon implementados.

## [C010] Implementar CartService com transações
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S3
Área: Carrinho
Descrição: Serviço com lock de estoque e recálculo de totais implementado.

## [C011] Implementar checkout etapa 1 endereço
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S4
Área: Checkout
Descrição: Seleção e CRUD de endereços funcionando.

## [C012] Implementar checkout etapa 2 pagamento
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S4
Área: Checkout
Descrição: Escolha de método de pagamento em UI implementada.

## [C013] Implementar CRUD admin de categorias
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S5
Área: Admin
Descrição: Painel e CRUD de categorias implementados.

## [C014] Implementar CRUD admin de produtos
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S5
Área: Admin
Descrição: Painel e CRUD de produtos com upload de imagens implementados.

## [C015] Implementar seeders iniciais
Lista: Feito
Status: Done
Prioridade: Média
Sprint: S1
Área: Dados
Descrição: Seeders de admin, cliente, categorias e produtos implementados.

## [C016] Implementar testes auth perfil carrinho
Lista: Feito
Status: Done
Prioridade: Alta
Sprint: S6
Área: Testes
Descrição: Testes de autenticação perfil e carrinho criados.

## [C017] Executar suíte de testes atual
Lista: Feito
Status: Done
Prioridade: Média
Sprint: S6
Área: Testes
Descrição: Suíte atual executada com sucesso 33 testes passando.

