# Portal Forest Ark Investiments

Laravel 13, PostgreSQL 18 e Tailwind 4. Conteúdo por blocos e multi-idioma, editado em `/admin`.

## Instalação

```
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm ci && npm run build
```

O seed cria o conteúdo inicial e exibe uma única vez a senha do primeiro administrador.

**Não rode `PortalContentSeeder` em produção depois da instalação:** ele substitui o conteúdo editado no admin.

## Atualizar a produção

No servidor, como root, na raiz do projeto:

```
bash deploy.sh
```

## Testes

Usam o banco `forestark_test` no PostgreSQL: `php artisan test`.
