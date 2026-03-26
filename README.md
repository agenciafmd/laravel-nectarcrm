# Laravel - Nectar CRM

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/laravel-nectarcrm.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/laravel-nectarcrm)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Integração com o Nectar CRM para projetos Laravel.

## Instalação

```bash
composer require agenciafmd/laravel-nectarcrm:v11.x-dev
```

## Configuração

Adicione as variáveis de ambiente ao seu arquivo `.env`:

```env
NECTARCRM_ACCESS_TOKEN=seu-token-aqui
NECTARCRM_ERROR_EMAIL=email-de-erro@exemplo.com
```
Obs: Gere ou solicite a geração do Access-Token em: [Integrações](https://app.nectarcrm.com.br/crm/crm/inicio#/configuracao/integracoes) > Gerar Token.

Caso queira customizar as configurações, publique o arquivo de configuração:

```bash
php artisan vendor:publish --tag=laravel-nectarcrm:config
```

## Uso

### Job de Conversão

O pacote fornece um Job para enviar conversões ao Nectar CRM de forma assíncrona.

```php
use Agenciafmd\Nectarcrm\Jobs\SendConversionsToNectarcrm;

$paylod = [
    'nome' => $data['enterprise'],
    'razaoSocial' => $data['enterprise'],
    'origem' => 'Site - ' . config('app.url'),
    'categoria' => 'Cliente em potencial',
    'constante' => 3, // 0 = cliente, 1 = prospect, 2 = suspect, 3 = lead, 5 = descartados
    'sigiloso' => false,
    'ativo' => true,
    'emails' => [
        $data['email'], // required
    ],
    'telefones' => [
        $data['phone'], // +5511999999999
    ],
    'responsavel' => [
        'login' => $postal->to,
        'nome' => $postal->to_name,
    ],
    'camposPersonalizados' => [ // verificar a existencia dos campos no Nectar CRM
        'Nome da empresa' => $data['enterprise'],
        'Quantidade de colaboradores' => $data['employees'],
        'utm_campaign' => Cookie::get('utm_campaign', ''),
        'utm_content' => Cookie::get('utm_content', ''),
        'utm_medium' => Cookie::get('utm_medium', ''),
        'utm_source' => Cookie::get('utm_source', ''),
        'utm_term' => Cookie::get('utm_term', ''),
    ],
    'contatos' => [
        [
            'nome' => $data['name'],
            'cargo' => $data['role'],
            'emails' => [
                $data['email'],
            ],
        ],
    ],
];

dispatch(new SendConversionsToNectarcrm($paylod))
    ->delay(5)
    ->onQueue('low');
```

Obs: verifique os campos obrigatórios em [Contatos](https://app.nectarcrm.com.br/crm/crm/inicio#/contato) > Organizar Campos. 

### Macro HTTP

Você também pode utilizar a macro `Http::nectarcrm()` para realizar outras requisições à API do Nectar CRM.

```php
use Illuminate\Support\Facades\Http;

$response = Http::nectarcrm()->get('contatos/', [
    'email' => 'joao.paulo@fmd.ag',
]);

if ($response->successful()) {
    $contato = $response->json();
}
```

## Licença

Licença MIT. [Clique aqui](LICENSE.md) para mais detalhes.
