<h1 style="center">Zoológico bagunceiro API</h1>

<p align="center">
<img src="http://img.shields.io/static/v1?label=STATUS&message=EM%20DESENVOLVIMENTO&color=GREEN&style=for-the-badge"/>
</p>

## Sobre o projeto

Projeto desenvolvido como desafio para a empresa Codificar. Esta API complementa o projeto <a href="https://github.com/GuilhermeWatanabe/zoologico-bagunceiro-frontend">Zoológico bagunceiro front-end</a>.

## Tecnologias

As seguintes ferramentas foram usadas na construção do projeto:

- [Laravel](https://laravel.com)

## Configuração do projeto

### Clone este repositório
```
git clone https://github.com/GuilhermeWatanabe/zoologico-api-laravel.git
```

### Navegue até a pasta do projeto
```
cd zoologico-api-laravel
```

### Instale todas as dependências
```
composer install
```

### Configuração do banco de dados
Faça uma cópia do arquivo '.env.example' que se encontra na raiz do projeto. Renomeie a cópia deste arquivo para '.env', abra o mesmo e nas variaveis abaixo mude conforme suas necessidades.

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=NOME_DO_SEU_BANCO_DE_DADOS
DB_USERNAME=NOME_DO_USUÁRIO
DB_PASSWORD=SENHA_DO_USUÁRIO
```

### Rodando as migrations
Para rodar as migrations é só digitar o comando baixo no terminal:
```
php artisan migrate
```

### Rodando a aplicação
Para subir a aplicação locamente, digite no terminal:
```
php artisan serve
```
Ele vai subir a aplicação na porta 8000(padrão)

### Populando o banco de dados
Para popular o banco de dados, use o Postman ou então os formulários da aplicação front-end.
