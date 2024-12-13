CREATE TABLE IF NOT EXISTS usuario (
    usu_id int(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usu_nome varchar(100) NOT NULL, 
    usu_cpf varchar(11) UNIQUE NOT NULL,
    usu_email varchar(100) NOT NULL,
    usu_senha varchar(50) not null,
    usu_data_inclusao TIMESTAMP,
    usu_data_alteracao TIMESTAMP on update CURRENT_TIMESTAMP
);
-
CREATE TABLE IF NOT EXISTS produto (
    prd_id int(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prd_descricao varchar(255) not null,
    prd_valor decimal(7,2) default 0.00,
    prd_status enum("ativo", "inativo") default 'ativo',
    prd_data_inclusao TIMESTAMP,
    prd_data_ult_att TIMESTAMP on update CURRENT_TIMESTAMP
);
-
CREATE TABLE IF NOT EXISTS estoque (
    est_id int(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    est_prd_id int(2) UNSIGNED not null,
    est_usu_id int(1) UNSIGNED not null,
    est_qtd int(255) UNSIGNED,
    est_data_inclusao TIMESTAMP,
    est_data_ult_att TIMESTAMP on update CURRENT_TIMESTAMP,

    constraint FK_est_prd_id FOREIGN KEY (est_prd_id) REFERENCES produto(prd_id),
    constraint FK_est_usu_id FOREIGN KEY (est_usu_id) REFERENCES usuario(usu_id)
);
-
CREATE TABLE IF NOT EXISTS historico_estoque (
    hest_id int(2) UNSIGNED not null AUTO_INCREMENT PRIMARY KEY,
    hest_prd_id int(2) UNSIGNED not null,
    hest_usu_id int(2) UNSIGNED not null COMMENT "coluna que controla o usuário que está realizando a operação",
    hest_qtd int(10) UNSIGNED not null,
    hest_operacao enum('add', 'sub') COMMENT "adição ou subtração do item",
    hest_data TIMESTAMP
);