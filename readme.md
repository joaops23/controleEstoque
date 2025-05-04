## Api de Controle de Estoque

Esta é uma api que será responsável por gerenciar processos de estoque:

Módulos:
- CRUD de Produto
- Controle de Estoque (inclusão de estoque, retirada e histórico de estoque por produto)
- Controle de Usuário
- Relatorios (De estoque, De produto, Histórico de produto)


### Entidades

- produto: 
```
prd_id int(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
prd_descricao varchar(255) not null,
prd_valor decimal(7,2) defaut '00,00',
prd_status enum("ativo", "inativo") default 'ativo',
prd_data_inclusao datetime TIMESTAMP,
prd_data_ult_att datetime CURRENT_TIMESTAMP,
```

- usuario:
```
usu_id int(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
usu_nome varchar(100) NOT NULL, 
usu_cpf varchar(11) UNIQUE NOT NULL,
usu_email varchar(100) NOT NULL,
usu_senha varchar(50) not null,
usu_data_inclusao datetime TIMESTAMP,
usu_data_inclusao datetime CURRENT_TIMESTAMP,
```

- estoque:
```
est_id int(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
est_prd_id int(2) UNSIGNED not null,
est_usu_id int(1) UNSIGNED not null,
est_qtd int(255) UNSIGNED,
est_data_inclusao datetime TIMESTAMP,
est_data_ult_att datetime CURRENT_TIMESTAMP,

constraint FK_est_prd_id FOREIGN KEY (est_prd_id) REFERENCES produto(prd_id)
constraint FK_est_prd_id FOREIGN KEY (est_usu_id) REFERENCES usuario(usu_id)
```

- historico_estoque:
```
hest_id int(2) UNSIGNED not null AUTO_INCREMENT PRIMARY KEY,
hest_prd_id int(2) UNSIGNED not null,
hest_usu_id int(2) UNSIGNED not null COMMENT "coluna que controla o usuário que está realizando a operação",
hest_qtd int(10) UNSIGNED not null,
hest_operacao enum('add', 'sub') COMMENT "adição ou subtração do item",
hest_data datetime TIMESTAMP
```

### Arquitetura de diretórios

- public
    - index.php
    - .htaccess
- src
    - Config <span>-> Configuração de Database, docker, constantes etc.</span>
    - Routes
    - Controllers
    - Models
    - Resources


# Techs
- Docker
- PHP
    - versão 8
    - slim framework
    - PHP(OO)
- MySQL
- Nginx

## Features Desenvolvidas
- Dockerização
- Script de criação de banco
- Construção da base da api
- Módulo usuario ( CRUD )
- Módulo produto

## Próximas features
v1
- Implementação de Repository Patterns
- Refatoração das models já criadas (produto e usuario) 
- Módulo estoque (juntamente com controle do histórico)
- Tela interativa para controlar as features (possivelmente em um novo repo)

v2
- Documentação técnica
- Módulo de compra 
- Módulo de venda
- Dashboard


### Passo a passo

- Instalação dos containers e execução do projeto
```bash
docker-compose up -d --build
```

- Caso o usuário root não tenha sido criado, executar o script  `src/Resources/sql/createDefaultUser.sql` no banco de dados

#### Módulos

## Usuario
### Rotas - agrupadas em: /user

##  /login - POST
Rota de acesso de usuário, informado no payload o usuário e senha de acesso.

#### Payload
```javascript
{
    "data": {
        "login": "User12345",
        "password": "123432514"
    }
}
```

#### Retorno
```javascript
{
    data: {
        token: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IlJvZHJpZ28iLCJyb2xlIjoiYWRtaW4ifQ.vqoDiXce_zGG5I81wI2-gbzRq4k2B70UIr1NuZ08Z_4",
        timeout: 60, // dado apenas informativo do tempo de sessão do usuário
    }
}
```

##### HTTP Code
- 200 - ok ( Usuário validado e credenciais confirmadas)
- 403 - forbidden ( Acesso negado | Credenciais inválidas )


| /{$id} - GET

Rota para recuperar dados de um usuário específico


| /insertUpdate - POST

Rota para 

### Regras
- Apenas um usuário cadastrado pode criar um novo usuário
- Tempo máximo logado 1h

----------------------------------------------------------------------
## Fluxo de compra
1. Cadastro da compra.
1.1 - Cadastrar os produtos, suas quantidades e valores.
1.2 - Ao finalizado, o pedido de compra é cadastrado como "pedido realizado"

2. Efetivação
2.1 - Efetivar a compra.
2.2 - Incluir estoque doas produtos.
2.3 - Finalizar pedido de compra.

----------------------------------------------------------------------
## Fluxo de venda

1. Cadastro do pedido;
1.1 - Cadastrar pedido de venda, com o itens, valores (quantidade e valor de venda) e cliente;
1.2 - Aprovação de pedido;
1.3 - Geração de nota ou comprovante (ou relatório em PDF com informações do pedido);
1.4 - Efetivação da venda;
1.5 - Envio de email para o cliente com informações da venda e acompanhamento do pedido;

----------------------------------------------------------------------
## Tabela de status

1 - Pedido cadastrado;
2 - Pagamento não aprovado;
3 - pagamento aprovado;
4 - Pedido realizado;
5 - nota gerada;
5 - Em deslocamento;
6 - Entrega incompleta;
7 - Pedido Finalizado;
8 - Em rota de devolução;