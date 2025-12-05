USE vaiicko_db;

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

-- Odstránenie starých tabuliek (poradie je OK)
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS Bezec;
DROP TABLE IF EXISTS Stanovisko;
DROP TABLE IF EXISTS `rokKonania`;

-- Tabuľka rokKonania
CREATE TABLE `rokKonania`
(
    `ID_roka` INT NOT NULL AUTO_INCREMENT,
    `rok` INT NOT NULL,
    `datum_konania` DATE NOT NULL,
    `pocet_ucastnikov` INT NOT NULL,
    PRIMARY KEY (`ID_roka`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


-- ────────────────────────────────────────────────
-- Tabuľka STANOVISKO (1:N k rokKonania)
-- ────────────────────────────────────────────────
CREATE TABLE Stanovisko (
                            ID_stanoviska INT NOT NULL AUTO_INCREMENT,
                            nazov VARCHAR(100) NOT NULL,
                            poloha VARCHAR(255),
                            popis TEXT,
                            ID_roka INT NOT NULL,
                            PRIMARY KEY (ID_stanoviska),
                            CONSTRAINT fk_stanovisko_rok
                                FOREIGN KEY (ID_roka)
                                    REFERENCES rokKonania(ID_roka)
                                    ON DELETE CASCADE
                                    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4;


-- ────────────────────────────────────────────────
-- Tabuľka BEZEC (1:N k rokKonania)
-- ────────────────────────────────────────────────
CREATE TABLE `Bezec`
(
    `ID_bezca` INT NOT NULL AUTO_INCREMENT,
    `meno` VARCHAR(50) NOT NULL,
    `priezvisko` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `pohlavie` ENUM('M', 'Ž') NOT NULL,
    `ID_roka` INT NOT NULL,
    PRIMARY KEY (`ID_bezca`),
    CONSTRAINT `fk_bezec_rok`
        FOREIGN KEY (`ID_roka`)
            REFERENCES `rokKonania` (`ID_roka`)
            ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4;


SET foreign_key_checks = 1;

-- Príklad vloženia údajov
INSERT INTO `rokKonania` (`ID_roka`,`rok`, `datum_konania`, `pocet_ucastnikov`)
VALUES (2023,2023, '2024-06-15', 103),
       (2024,2024, '2024-06-15', 120),
       (2025,2023, '2023-06-10', 110);

INSERT INTO `Stanovisko` (`ID_stanoviska`, `nazov`, `poloha`, `popis`, `ID_roka`)
VALUES (1, 'Horská chata', 'Nízke Tatry', 'Chata sa nachádza v srdci Nízkych Tatier a ponúka útulné ubytovanie pre bežcov.', 2023),
       (2, 'Lesný kemp', 'Malá Fatra', 'Kemp je obklopený hustým lesom a poskytuje ideálne miesto na oddych po behu.', 2024),
       (3, 'Jazerná pláž', 'Oravská priehrada', 'Pláž pri jazere je skvelým miestom na regeneráciu a relaxáciu po náročnom behu.', 2025);

INSERT INTO `Bezec` (`ID_bezca`, `meno`, `priezvisko`,`pohlavie`, `email`, `ID_roka`)
VALUES (1, 'Ján', 'Novák','M', 'novak@gmail.com', 2023),
       (2, 'Mária', 'Kováčová','M', 'kovac@gmial.com',  2024),
       (3, 'Peter', 'Horváth','M', 'hornak@gmail.sk',  2024);