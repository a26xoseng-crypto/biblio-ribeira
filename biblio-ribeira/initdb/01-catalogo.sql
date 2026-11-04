-- Catálogo inicial das bibliotecas de Ribeira Alta
-- Este script execútase SÓ a primeira vez que arranca MariaDB cun volume baleiro.
-- Execútase sobre a base de datos indicada en MARIADB_DATABASE.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS libros (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titulo      VARCHAR(200) NOT NULL,
    autoria     VARCHAR(150) NOT NULL,
    ano         SMALLINT,
    biblioteca  VARCHAR(100) NOT NULL
) DEFAULT CHARSET = utf8mb4;

INSERT INTO libros (titulo, autoria, ano, biblioteca) VALUES
    ('Follas novas',              'Rosalía de Castro',                 1880, 'Biblioteca Central'),
    ('Cousas',                    'Alfonso Daniel Rodríguez Castelao', 1926, 'Biblioteca Central'),
    ('A esmorga',                 'Eduardo Blanco Amor',               1959, 'Biblioteca do Porto'),
    ('Memorias dun neno labrego', 'Xosé Neira Vilas',                  1961, 'Biblioteca do Porto'),
    ('O lapis do carpinteiro',    'Manuel Rivas',                      1998, 'Biblioteca da Ribeira');
