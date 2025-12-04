USE vaiicko_db;

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS `rokKonania`;
CREATE TABLE `rokKonania`
(
    `ID_roka` INT NOT NULL AUTO_INCREMENT,
    `rok` INT NOT NULL,
    `datum_konania` DATE NOT NULL,
    `pocet_ucastnikov` INT NOT NULL,
    PRIMARY KEY (`ID_roka`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- Príklad vloženia údajov
INSERT INTO `rokKonania` (`ID_roka`,`rok`, `datum_konania`, `pocet_ucastnikov`)
VALUES (2024,2024, '2024-06-15', 120),
       (2025,2023, '2023-06-10', 110);
