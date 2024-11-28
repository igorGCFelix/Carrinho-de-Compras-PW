create database loja_felix;

use loja_felix;

create table usuario(
nome varchar(255) not null,
senha varchar(255) not null,
id int auto_increment primary key
);

create table produtos(
    id_produto int auto_increment primary KEY,
    imagem LONGTEXT NOT NULL,
    nome VARCHAR(50),
    marca VARCHAR(50),
    preco double
    
);

INSERT INTO usuario (nome, senha) values ('admin', 'admin');

INSERT INTO produtos (imagem,nome,marca,preco) VALUES
 ('imgs/img1.jpg','M30','8BitDo','300'), 
 ('imgs/img2.jpg','Micro','8BitDo','250'), 
 ('imgs/img3.jpg','M30 Bluetooth','8BitDo','233.67'), 
 ('imgs/img4.jpg','Lite SE','8BitDo','367'), 
 ('imgs/img5.jpg','SN30 Pro','8BitDo','400');