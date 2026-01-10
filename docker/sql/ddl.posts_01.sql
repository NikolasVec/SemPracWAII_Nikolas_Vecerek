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
DROP TABLE IF EXISTS `Pouzivatelia`;

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

-- Tabuľka pre používateľov
CREATE TABLE `Pouzivatelia` (
    `ID_pouzivatela` INT NOT NULL AUTO_INCREMENT,
    `meno` VARCHAR(50) NOT NULL,
    `priezvisko` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `heslo` VARCHAR(255) NOT NULL,
    `datum_narodenia` DATE NOT NULL,
    `pohlavie` ENUM('M', 'Z') NOT NULL,
    `zabehnute_kilometre` INT DEFAULT 0,
    `vypite_piva` INT DEFAULT 0,
    PRIMARY KEY (`ID_pouzivatela`)
)  ENGINE=InnoDB
   DEFAULT CHARSET = utf8mb4;

SET foreign_key_checks = 1;

-- Príklad vloženia údajov
INSERT INTO `rokKonania` (`ID_roka`,`rok`, `datum_konania`)
VALUES (2020,2020, '2020-06-17'),
       (2021,2021, '2021-06-10'),
       (2022,2022, '2022-06-14'),
       (2023,2023, '2023-06-15'),
       (2024,2024, '2024-06-15'),
       (2025,2025, '2025-06-10');


INSERT INTO `Stanovisko` (`ID_stanoviska`, `nazov`, `poloha`, `popis`, `ID_roka`)
VALUES (1, 'Horská chata', 'Nízke Tatry', 'Chata sa nachádza v srdci Nízkych Tatier a ponúka útulné ubytovanie pre bežcov.', 2023),
       (2, 'Lesný kemp', 'Malá Fatra', 'Kemp je obklopený hustým lesom a poskytuje ideálne miesto na oddych po behu.', 2024),
       (3, 'Jazerná pláž', 'Oravská priehrada', 'Pláž pri jazere je skvelým miestom na regeneráciu a relaxáciu po náročnom behu.', 2025),
         (4, 'Mestský park', 'Bratislava', 'Park v centre mesta ponúka zelené prostredie pre bežcov a ich rodiny.', 2023),
            (5, 'Horský prameň', 'Vysoké Tatry', 'Prameň s čerstvou horskou vodou je ideálny na osvieženie počas behu.', 2024),
                (6, 'Riečna oáza', 'Dunajská Streda', 'Oáza pri rieke poskytuje krásne výhľady a pokojné prostredie pre bežcov.', 2025),
                     (7, 'Kultúrne centrum', 'Košice', 'Centrum ponúka kultúrne zážitky a oddych po behu v mestskom prostredí.', 2023);

INSERT INTO `Bezec` (`ID_bezca`, `meno`, `priezvisko`,`pohlavie`, `email`, `ID_roka`)
VALUES (1, 'Ján', 'Novák','M', 'novak@gmail.com', 2023),
       (2, 'Mária', 'Kováčová','M', 'kovac@gmial.com',  2024),
       (3, 'Peter', 'Horváth','M', 'hornak@gmail.sk',  2024),
         (4, 'Anna', 'Vargová','Ž', 'vargova@gmail.com', 2025),
       (5, 'Lucia', 'Bieliková','Ž', 'bielikova@gmail.com', 2025),
         (6, 'Martin', 'Farkaš','M', 'farkas@gmail.com', 2025),
         (7, 'Zuzana', 'Mlynarčíková','Ž', 'mlynarová@gmail.com', 2023),
            (8, 'Tomáš', 'Kučera','M', 'kučora@gmail.com', 2023);

-- Vloženie administrátora do tabuľky Pouzivatelia
INSERT INTO `Pouzivatelia` (`meno`, `priezvisko`, `email`, `heslo`, `datum_narodenia`, `pohlavie`, `zabehnute_kilometre`, `vypite_piva`)
VALUES ('Admin', 'Admin', 'admin@example.com', '$2y$10$GRA8D27bvZZw8b85CAwRee9NH5nj4CQA6PDFMc90pN9Wi4VAWq3yq', '2000-01-01', 'M', 0, 0);

-- Trigger na automatickú aktualizáciu počtu účastníkov v rokKonania
DELIMITER //
CREATE TRIGGER update_pocet_ucastnikov_after_bezec_insert
AFTER INSERT ON Bezec
FOR EACH ROW
BEGIN
    UPDATE rokKonania
    SET pocet_ucastnikov = (
        SELECT COUNT(*) FROM Bezec WHERE ID_roka = NEW.ID_roka
    )
    WHERE ID_roka = NEW.ID_roka;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER update_pocet_ucastnikov_after_bezec_delete
AFTER DELETE ON Bezec
FOR EACH ROW
BEGIN
    UPDATE rokKonania
    SET pocet_ucastnikov = (
        SELECT COUNT(*) FROM Bezec WHERE ID_roka = OLD.ID_roka
    )
    WHERE ID_roka = OLD.ID_roka;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER update_pocet_ucastnikov_after_bezec_update
AFTER UPDATE ON Bezec
FOR EACH ROW
BEGIN
    -- Aktualizácia pre starý rok
    UPDATE rokKonania
    SET pocet_ucastnikov = (
        SELECT COUNT(*) FROM Bezec WHERE ID_roka = OLD.ID_roka
    )
    WHERE ID_roka = OLD.ID_roka;
    -- Aktualizácia pre nový rok
    UPDATE rokKonania
    SET pocet_ucastnikov = (
        SELECT COUNT(*) FROM Bezec WHERE ID_roka = NEW.ID_roka
    )
    WHERE ID_roka = NEW.ID_roka;
END;//
DELIMITER ;

-- Po vložení bežcov nastav správny počet účastníkov v rokKonania (bezpečne)
UPDATE rokKonania rk
SET pocet_ucastnikov = (
    SELECT COUNT(*) FROM Bezec b WHERE b.ID_roka = rk.ID_roka
)
WHERE rk.ID_roka IN (SELECT DISTINCT ID_roka FROM Bezec);
