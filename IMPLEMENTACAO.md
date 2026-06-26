# Sistema de Billing Completo - Implementado

## ✅ O que foi implementado

### 1. Sistema de Roles e Permissões
- **Enum UserRole** (`app/Enums/UserRole.php`)
  - USER (usuário comum)
  - ADMIN (administrador)
  - SUPERADMIN (super administrador)
  
- **Migration** para adicionar roles aos usuários
  - Campo `role` (padrão: 'user')
  - Campo `is_active` (controle de status)
  - Campo `last_login_at`

- **Model User** atualizado com métodos:
  - `isAdmin()`, `isSuperAdmin()`, `isUser()`

### 2. Sistema de Billing Completo

#### Enums criados:
- `InvoiceStatus` - Status das faturas (draft, unpaid, paid, cancelled, refunded, overdue)
- `OrderStatus` - Status dos pedidos (pending, active, suspended, cancelled, completed)
- `SubscriptionStatus` - Status das assinaturas (active, paused, cancelled, expired)
- `ProductType` - Tipos de produtos (hosting, vps, dedicated, domain, ssl, addon, other)
- `BillingCycle` - Ciclos de cobrança (monthly, quarterly, semiannually, annually, etc)

#### Models criados:
- **Product** - Produtos/Serviços
  - Preço, taxa de setup, ciclo de cobrança
  - Controle de estoque
  - Features em JSON
  - Soft deletes

- **Invoice** - Faturas
  - Número automático (INV-AAAAMM-0001)
  - Subtotal, tax, discount, total
  - Múltiplos status
  - Data de vencimento
  - Métodos: `markAsPaid()`, `markAsOverdue()`, `calculateTotal()`

- **InvoiceItem** - Itens da fatura
  - Descrição, quantidade, preço unitário
  - Cálculo automático do amount
  - Atualiza total da fatura automaticamente

- **Order** - Pedidos
  - Número automático (ORD-AAAA-000001)
  - Ligado a produto e usuário
  - Ciclo de cobrança, próxima data de vencimento
  - Métodos: `activate()`, `suspend()`, `cancel()`

- **Subscription** - Assinaturas
  - Gerenciamento de assinaturas recorrentes
  - Data de início, próxima cobrança, expiração
  - Métodos: `pause()`, `resume()`, `cancel()`, `renew()`

### 3. Middleware de Autenticação
- `EnsureUserIsAdmin` - Bloqueia acesso de não-admins
- `EnsureUserIsSuperAdmin` - Apenas super admins
- Registrados no `bootstrap/app.php` com aliases `admin` e `superadmin`

### 4. Rotas Administrativas
Arquivo `routes/admin.php` com rotas protegidas:
- `/admin/dashboard` - Dashboard administrativo
- `/admin/users` - Gerenciamento de usuários
- `/admin/products` - Gerenciamento de produtos
- `/admin/orders` - Gerenciamento de pedidos (activate, suspend, cancel)
- `/admin/invoices` - Gerenciamento de faturas (mark-paid, send)
- `/admin/subscriptions` - Gerenciamento de assinaturas (pause, resume, cancel)

### 5. Controllers Administrativos
Todos em `app/Http/Controllers/Admin/`:
- `AdminDashboardController` - Estatísticas gerais
- `AdminUserController` - CRUD de usuários
- `AdminProductController` - CRUD de produtos
- `AdminOrderController` - Gerenciamento completo de pedidos
- `AdminInvoiceController` - Gerenciamento completo de faturas
- `AdminSubscriptionController` - Gerenciamento completo de assinaturas

## 📋 Próximos passos

1. **Iniciar MySQL/Laragon** e rodar as migrations:
   ```bash
   php artisan migrate
   ```

2. **Criar um usuário admin** via Tinker:
   ```bash
   php artisan tinker
   ```
   ```php
   $user = User::create([
       'name' => 'Admin',
       'email' => 'admin@example.com',
       'password' => bcrypt('password'),
       'role' => 'admin',
       'is_active' => true,
   ]);
   ```

3. **Criar views Inertia** para as páginas administrativas em `resources/js/Pages/Admin/`

4. **Implementar área do usuário** (cliente) para:
   - Ver seus produtos/serviços
   - Ver e pagar faturas
   - Gerenciar assinaturas
   - Fazer novos pedidos

5. **Sistema de pagamentos**:
   - Integrar com gateway de pagamento (Stripe, PagSeguro, Mercado Pago, etc)
   - Webhooks para atualização automática de status

6. **Sistema de notificações**:
   - Emails de fatura vencendo
   - Confirmação de pagamento
   - Status de pedido alterado

## 🏗️ Arquitetura implementada

```
Usuários (com roles)
    ↓
Produtos (catálogo)
    ↓
Orders (pedidos dos clientes)
    ↓
Subscriptions (renovações automáticas)
    ↓
Invoices (cobranças) → InvoiceItems
    ↓
Payments (futura integração)
```

## 🔒 Separação Admin/Usuário

- **Admin**: Acessa `/admin/*` com middleware `admin`
- **Usuário**: Acessa `/dashboard` e rotas normais
- **Login separado**: Mesmo sistema de auth, mas direcionamento diferente baseado em role

## 📂 Estrutura de arquivos criada

```
app/
├── Enums/
│   ├── UserRole.php
│   ├── InvoiceStatus.php
│   ├── OrderStatus.php
│   ├── SubscriptionStatus.php
│   ├── ProductType.php
│   └── BillingCycle.php
├── Models/
│   ├── Product.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   ├── Order.php
│   └── Subscription.php
├── Http/
│   ├── Controllers/Admin/
│   │   ├── AdminDashboardController.php
│   │   ├── AdminUserController.php
│   │   ├── AdminProductController.php
│   │   ├── AdminOrderController.php
│   │   ├── AdminInvoiceController.php
│   │   └── AdminSubscriptionController.php
│   └── Middleware/
│       ├── EnsureUserIsAdmin.php
│       └── EnsureUserIsSuperAdmin.php
routes/
└── admin.php
database/migrations/
├── 2026_06_25_180143_add_role_and_status_to_users_table.php
├── 2026_06_25_180243_create_products_table.php
├── 2026_06_25_180243_create_invoices_table.php
├── 2026_06_25_180244_create_orders_table.php
├── 2026_06_25_180244_create_invoice_items_table.php
└── 2026_06_25_180245_create_subscriptions_table.php
```

Sistema completo de billing inspirado no WHMCS, mas nativo em Laravel com arquitetura moderna!
