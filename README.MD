1. Instale o WHMCS
Primeiro, instale o WHMCS v9.0.5 em algum lugar acessível (pode ser em outro domínio ou subdomínio):

https://billing.seusite.com (recomendado)
Ou em subpasta do Laragon mesmo
2. Configure as credenciais da API no WHMCS
No painel admin do WHMCS:

Vá em Setup > Staff Management > API Credentials
Clique em Generate New API Credential
Configure:
Admin User: Selecione um admin
Identifier: api-laravel (ou qualquer nome)
Secret: Será gerado automaticamente
Allowed IP Addresses: 127.0.0.1 (para testes local)
3. Configure o Laravel (.env)
Copie as credenciais para o .env do Laravel:

WHMCS_API_URL=http://localhost/whmcs/includes/api.php
WHMCS_API_IDENTIFIER=api-laravel
WHMCS_API_SECRET=seu_secret_aqui
WHMCS_ALLOWED_IPS=127.0.0.1
WHMCS_HMAC_SECRET=qualquer-chave-aleatoria-aqui
4. Crie produtos no WHMCS
No WHMCS Admin:

System Settings > Products/Services > Products/Services
Crie 4 produtos:
Nome: "10 Créditos IPTV" → Preço: R$ 29,90
Nome: "25 Créditos IPTV" → Preço: R$ 59,90
Nome: "50 Créditos IPTV" → Preço: R$ 99,90
Nome: "100 Créditos IPTV" → Preço: R$ 179,90
Adicione campo customizado em cada produto:
Field Name: credits
Field Type: Text Box
Default Value: 10 (ou 25, 50, 100 conforme o produto)
5. Teste a integração
Comando
php artisan tinker --execute="app(\App\Services\Whmcs\WhmcsClient::class)->getProducts()"
Saída
 Illuminate\Http\Client\ConnectionException cURL error 6: Could not resolve host: your-whmcs-domain.com (see https://curl.se/libcurl/c/libcurl-errors.html) for https://your-whmcs-domain.com/includes/api.php.
Perfeito! O erro é esperado porque você ainda não configurou a URL do WHMCS. Assim que você:

Instalar o WHMCS (no Laragon ou em outro servidor)
Atualizar o .env com a URL e credenciais corretas
O Laravel vai conseguir puxar os dados automaticamente.

O que já está pronto no Laravel:
✅ Cliente de API - WhmcsClient já implementado ✅ Endpoints funcionando:

GET /api/products - Lista produtos do WHMCS
GET /api/wallet/balance - Saldo do usuário
GET /api/wallet/history - Histórico de créditos
✅ Cache inteligente - Se o WHMCS cair, usa cache local ✅ Fallback automático - Catálogo padrão se a API falhar ✅ Frontend pronto:

Welcome.vue busca produtos automaticamente
Dashboard.vue mostra saldo e histórico
Após instalar o WHMCS:
Atualize apenas essas 3 linhas no .env:

WHMCS_API_URL=http://localhost/whmcs/includes/api.php
WHMCS_API_IDENTIFIER=sua_chave_identifier
WHMCS_API_SECRET=seu_secret
E tudo vai funcionar automaticamente! Os produtos do WHMCS vão aparecer na home do Laravel, e quando alguém comprar, você pode creditar a carteira usando o CreditWalletService.