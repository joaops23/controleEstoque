create table status (
    stt_id tinyint(1) primary key,
    stt_desc varchar(100) not null
);

insert into status values (1, 'Ativo');
insert into status values (2, 'Inativo');
insert into status values (3, 'Bloqueado');
insert into status values (4, 'Pré Cadastrado');
insert into status values(5, 'Excluído');

alter table usuario add column usu_stt tinyint(1) default "1" after usu_senha;
alter table usuario add constraint usu_stt_fk foreign key usuario('usu_stt') references status('stt_id');