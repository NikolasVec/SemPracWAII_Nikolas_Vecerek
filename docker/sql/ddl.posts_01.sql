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
DROP TABLE IF EXISTS `sponsors`;

-- Tabuľka rokKonania
CREATE TABLE `rokKonania`
(
    `ID_roka` INT NOT NULL AUTO_INCREMENT,
    `rok` INT NOT NULL,
    `datum_konania` DATE NOT NULL,
    `dlzka_behu` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `pocet_ucastnikov` INT NOT NULL DEFAULT 0,
    `pocet_stanovisk` INT NOT NULL DEFAULT 0,
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
                            mapa_odkaz VARCHAR(1000) DEFAULT NULL,
                            obrazok_odkaz VARCHAR(1000) DEFAULT NULL,
                            x_pos DECIMAL(9,6) NULL,
                            y_pos DECIMAL(9,6) NULL,
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
-- Tabuľka POUZIVATELIA (users) - created before Bezec so FK can reference email
-- ────────────────────────────────────────────────
CREATE TABLE `Pouzivatelia` (
    `ID_pouzivatela` INT NOT NULL AUTO_INCREMENT,
    `meno` VARCHAR(50) NOT NULL,
    `priezvisko` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `heslo` VARCHAR(255) NOT NULL,
    `pohlavie` VARCHAR(10) DEFAULT 'M',
    `datum_narodenia` DATE DEFAULT NULL,
    `zabehnute_kilometre` DOUBLE DEFAULT 0,
    `vypite_piva` INT DEFAULT 0,
    `admin` BOOLEAN NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID_pouzivatela`)
)  ENGINE=InnoDB
   DEFAULT CHARSET = utf8mb4;

-- ────────────────────────────────────────────────
-- Tabuľka BEZEC (1:N k rokKonania) - now references Pouzivatelia.email
-- Ak neexistuje používateľ s daným emailom, bežec vložený nebude môcť existovať
-- ────────────────────────────────────────────────
CREATE TABLE `Bezec`
(
    `ID_bezca` INT NOT NULL AUTO_INCREMENT,
    `meno` VARCHAR(50) NOT NULL,
    `priezvisko` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `pohlavie` ENUM('M', 'Ž') NOT NULL,
    `cas_dobehnutia` TIME DEFAULT NULL,
    `ID_roka` INT NOT NULL,
    PRIMARY KEY (`ID_bezca`),
    -- Prevent duplicate registrations: same email cannot be used for the same year
    UNIQUE KEY `uniq_bezec_email_roka` (`email`, `ID_roka`),
    CONSTRAINT `fk_bezec_rok`
        FOREIGN KEY (`ID_roka`)
            REFERENCES `rokKonania` (`ID_roka`)
            ON DELETE CASCADE,
    -- Foreign key linking runner to a registered user by email
    CONSTRAINT `fk_bezec_pouzivatel_email`
        FOREIGN KEY (`email`)
            REFERENCES `Pouzivatelia` (`email`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4;

-- Tabuľka sponzorov (footer)
CREATE TABLE `sponsors` (
    `ID_sponsor` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `contact_email` VARCHAR(255) DEFAULT NULL,
    `contact_phone` VARCHAR(100) DEFAULT NULL,
    `logo` VARCHAR(255) DEFAULT NULL,
    `url` VARCHAR(1000) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID_sponsor`)
) ENGINE=InnoDB
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


INSERT INTO `Stanovisko` (`ID_stanoviska`, `nazov`, `poloha`, `popis`, `mapa_odkaz`, `obrazok_odkaz`,`x_pos`,`y_pos`, `ID_roka`)
VALUES (1, 'Pohostinstvo Sviečka', 'Ľatoveň', 'Štart behu.', 'https://www.google.com/maps/place/Pohostinstvo+SVIE%C4%8CKA/@49.0515116,18.9232402,17.5z/data=!4m6!3m5!1s0x4714feeb8bc2c9bd:0x28c42deccd2cf952!8m2!3d49.0517616!4d18.9244251!16s%2Fg%2F11cjg7rc3b?entry=ttu&g_ep=EgoyMDI2MDExMS4wIKXMDSoASAFQAw%3D%3D', 'https://lh3.googleusercontent.com/gps-cs-s/AG0ilSyEJm-8s7WR3P9_xI0_V1eqlmGTn-8mMKh5dolHRu1xKzglvIPSA24TnlqjDlaCKB7yL_6T8D2xaJqWzo433FQqSpEfL5ltf8uRgS6uzVaGeRSqAZP8Zr9gdNNrv5Nibz0tVBoA=w426-h240-k-no',0.490579, 0.904628, 2025),
        (2, 'Piváreň N', 'Centrum', '', 'https://www.google.com/maps/place/Piv%C3%A1re%C5%88+N,+U+Novansk%C3%A9ho/@49.0651289,18.9145018,17z/data=!3m1!4b1!4m6!3m5!1s0x4714ff00821e1545:0x1b3642e0a90b7e18!8m2!3d49.0651254!4d18.9170767!16s%2Fg%2F11s9cxyp7j?entry=ttu&g_ep=EgoyMDI2MDExMS4wIKXMDSoASAFQAw%3D%3D', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJd6Irbuen4i6wHuw9Pt-Pif7bwuhUcSlqww&s',0.402669, 0.649989, 2025),
         (3, 'Brept_divadlo', 'Centrum', '', 'https://www.google.com/maps/place/kaf%C3%A9+BREPT/@49.0653486,18.9190209,17z/data=!4m6!3m5!1s0x4714ffb37ab79b65:0x282fbbf50255de5e!8m2!3d49.0654858!4d18.9214564!16s%2Fg%2F11jkx5nd4c?entry=ttu&g_ep=EgoyMDI2MDExMS4wIKXMDSoASAFQAw%3D%3D', 'https://www.martin.sk/assets/Image.ashx?id_org=700031&id_obrazky=57001&datum=3%2F22%2F2014+2%3A15%3A49+PM',0.464849, 0.638672, 2025),
         (4, 'Kleofáš (911 Pub & Hub)', 'Centrum', '', 'https://www.google.com/maps/place/911+pub+%26+hub/@49.0701329,18.9254945,223a,35y,121.64h,45t/data=!3m1!1e3!4m9!1m2!2m1!1za2xlb2bDocWh!3m5!1s0x4714ff0027dc935f:0xd8ef3b98a1d7a3f8!8m2!3d49.0689626!4d18.9281972!16s%2Fg%2F11y5l6l6d9!5m1!1e4?entry=ttu&g_ep=EgoyMDI2MDExMS4wIKXMDSoASAFQAw%3D%3D', '',0.522741, 0.588499, 2025),
         (5, 'Piváreň u Sokola', 'Sever', '', 'https://www.google.com/maps/place/Apartm%C3%A1n+v+centre+mesta/@49.072775,18.9257025,256a,35y,102.2h/data=!3m1!1e3!4m9!3m8!1s0x4714ffefd9a75e25:0xa98794ae424de397!5m2!4m1!1i2!8m2!3d49.0721558!4d18.9257267!16s%2Fg%2F11k6g45sg7!5m1!1e4?entry=ttu&g_ep=EgoyMDI2MDExMS4wIKXMDSoASAFQAw%3D%3D', 'https://lh3.googleusercontent.com/gps-cs-s/AG0ilSx0LkoZfhpTrLOjSj6XD0xF1eKogSdlJn-LjCyc-Q35CuMYOZQZnHGX7J_5fgJRhgEsTCNT-wa4pCv6rQbLh27vLl2SdXrgNHCF7BaLiuAGv5kO1yfzNQe-FjRixqNLQEFvIqWT=w408-h306-k-no',0.507732, 0.487775, 2025),
         (6, 'Kocka Pub Sever', 'Sever', '', 'https://www.google.com/maps/place/Kocka+Musicbar/@49.0801532,18.9281265,147m/data=!3m1!1e3!4m6!3m5!1s0x4714ffce4c8dd5b9:0xbd153e2235ca6f78!8m2!3d49.0803697!4d18.9282671!16s%2Fg%2F11h60kb_jc!5m1!1e4?entry=ttu&g_ep=EgoyMDI2MDExMS4wIKXMDSoASAFQAw%3D%3D', 'https://www.uniglass.sk/sites/default/files/styles/600px/public/fotogaleria/realizacie/1406705029/mt_-_zabavne_centrum_kocka_2.jpg?itok=IZUO5225',0.505588, 0.397991, 2025),
         (7, 'Pivovar Martins', 'Záturčie', 'Ciel behu.', 'https://www.google.com/maps/place/Pivovar+Martins/@49.0897252,18.9261914,543m/data=!3m2!1e3!4b1!4m6!3m5!1s0x4714ff47f07e382b:0xee51cd17a7b5c740!8m2!3d49.0897217!4d18.9287663!16s%2Fg%2F11g6mrx7g7!5m1!1e4?entry=ttu&g_ep=EgoyMDI2MDExMS4wIKXMDSoASAFQAw%3D%3D', 'https://opive.sk/wp-content/uploads/2023/05/Pivovar-Martins-01.jpg',0.503444, 0.110532, 2025);


INSERT INTO `Pouzivatelia` (`meno`, `priezvisko`, `email`, `heslo`, `datum_narodenia`, `pohlavie`, `zabehnute_kilometre`, `vypite_piva`, `admin`)
VALUES ('Admin', 'Admin', 'admin@example.com', '$2y$10$GRA8D27bvZZw8b85CAwRee9NH5nj4CQA6PDFMc90pN9Wi4VAWq3yq', '2000-01-01', 'M', 0, 0, 1),
       ('Nikolas', 'Večerek', 'nikove17@gmail.com','$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyG89iSuyFK', '2003-10-07', 'M', 0, 0, 0),
       ('Ján', 'Novák', 'novak@gmail.com', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyG89iSuyFK', NULL, 'M', 0, 0, 0),
       ('Mária', 'Kováčová', 'kovac@gmial.com', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyG89iSuyFK', NULL, 'Ž', 0, 0, 0),
       ('Peter', 'Horváth', 'hornak@gmail.sk', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyG89iSuyFK', NULL, 'M', 0, 0, 0),
       ('Anna', 'Vargová', 'vargova@gmail.com', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyFK', NULL, 'Ž', 0, 0, 0),
       ('Lucia', 'Bieliková', 'bielikova@gmail.com', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyFK', NULL, 'Ž', 0, 0, 0),
       ('Martin', 'Farkaš', 'farkas@gmail.com', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyFK', NULL, 'M', 0, 0, 0),
       ('Zuzana', 'Mlynarčíková', 'mlynarova@gmail.com', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyFK', NULL, 'Ž', 0, 0, 0),
       ('Tomáš', 'Kučera', 'kucora@gmail.com', '$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyFK', NULL, 'M', 0, 0, 0);


INSERT INTO `Bezec` (`ID_bezca`, `meno`, `priezvisko`,`pohlavie`, `email`, `ID_roka`)
VALUES (1, 'Ján', 'Novák','M', 'novak@gmail.com', 2023),
       (2, 'Mária', 'Kováčová','Ž', 'kovac@gmial.com',  2024),
       (3, 'Peter', 'Horváth','M', 'hornak@gmail.sk',  2024),
         (4, 'Anna', 'Vargová','Ž', 'vargova@gmail.com', 2025),
       (5, 'Lucia', 'Bieliková','Ž', 'bielikova@gmail.com', 2025),
         (6, 'Martin', 'Farkaš','M', 'farkas@gmail.com', 2025),
         (7, 'Zuzana', 'Mlynarčíková','Ž', 'mlynarova@gmail.com', 2023),
            (8, 'Tomáš', 'Kučera','M', 'kucora@gmail.com', 2023);

-- Vloženie administrátora do tabuľky Pouzivatelia (duplicate admin row removed because already inserted above)
-- INSERT INTO `Pouzivatelia` (`meno`, `priezvisko`, `email`, `heslo`, `datum_narodenia`, `pohlavie`, `zabehnute_kilometre`, `vypite_piva`, `admin`)
-- VALUES ('Admin', 'Admin', 'admin@example.com', '$2y$10$GRA8D27bvZZw8b85CAwRee9NH5nj4CQA6PDFMc90pN9Wi4VAWq3yq', '2000-01-01', 'M', 0, 0, 1),
--        ('Nikolas', 'Večerek', 'nikove17@gmail.com','$2y$10$fPwteJOCC0jPTtAtJzWkpORDmVZYKaIBzWnY.bf56KuyG89iSuyFK', '2003-10-07', 'M', 0, 0, 0);

-- Example sponsor
INSERT INTO sponsors (name, contact_email, contact_phone, logo, url)
VALUES ('Príklad sponzora', 'kontakt@priklad.sk', '+421900000000', NULL, 'https://priklad.sk');

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

-- NOVÉ triggre: aktualizácia zabehnute_kilometre a vypite_piva v Pouzivatelia
DELIMITER //
CREATE TRIGGER bezec_after_insert_update_stats
AFTER INSERT ON Bezec
FOR EACH ROW
BEGIN
    DECLARE v_dlzka DECIMAL(7,2) DEFAULT 0.00;
    DECLARE v_stanovisk INT DEFAULT 0;

    -- len ak bežec má zaznamenaný čas dorazenia
    IF NEW.cas_dobehnutia IS NOT NULL THEN
        SELECT COALESCE(dlzka_behu, 0.00), COALESCE(pocet_stanovisk, 0)
        INTO v_dlzka, v_stanovisk
        FROM rokKonania
        WHERE ID_roka = NEW.ID_roka
        LIMIT 1;

        UPDATE Pouzivatelia
        SET zabehnute_kilometre = COALESCE(zabehnute_kilometre, 0) + v_dlzka,
            vypite_piva = COALESCE(vypite_piva, 0) + v_stanovisk
        WHERE email = NEW.email;
    END IF;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER bezec_after_delete_update_stats
AFTER DELETE ON Bezec
FOR EACH ROW
BEGIN
    DECLARE v_dlzka DECIMAL(7,2) DEFAULT 0.00;
    DECLARE v_stanovisk INT DEFAULT 0;

    -- iba ak vymazávaný bežec mal čas (inak sa nič nepripisovalo)
    IF OLD.cas_dobehnutia IS NOT NULL THEN
        SELECT COALESCE(dlzka_behu, 0.00), COALESCE(pocet_stanovisk, 0)
        INTO v_dlzka, v_stanovisk
        FROM rokKonania
        WHERE ID_roka = OLD.ID_roka
        LIMIT 1;

        UPDATE Pouzivatelia
        SET zabehnute_kilometre = GREATEST(COALESCE(zabehnute_kilometre, 0) - v_dlzka, 0),
            vypite_piva = GREATEST(COALESCE(vypite_piva, 0) - v_stanovisk, 0)
        WHERE email = OLD.email;
    END IF;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER bezec_after_update_update_stats
AFTER UPDATE ON Bezec
FOR EACH ROW
BEGIN
    DECLARE v_old_dlzka DECIMAL(7,2) DEFAULT 0.00;
    DECLARE v_old_stanovisk INT DEFAULT 0;
    DECLARE v_new_dlzka DECIMAL(7,2) DEFAULT 0.00;
    DECLARE v_new_stanovisk INT DEFAULT 0;

    -- staré hodnoty (pre OLD.ID_roka)
    IF OLD.cas_dobehnutia IS NOT NULL THEN
        SELECT COALESCE(dlzka_behu, 0.00), COALESCE(pocet_stanovisk, 0)
        INTO v_old_dlzka, v_old_stanovisk
        FROM rokKonania
        WHERE ID_roka = OLD.ID_roka
        LIMIT 1;
    END IF;

    -- nové hodnoty (pre NEW.ID_roka)
    IF NEW.cas_dobehnutia IS NOT NULL THEN
        SELECT COALESCE(dlzka_behu, 0.00), COALESCE(pocet_stanovisk, 0)
        INTO v_new_dlzka, v_new_stanovisk
        FROM rokKonania
        WHERE ID_roka = NEW.ID_roka
        LIMIT 1;
    END IF;

    -- Ak mal bežec starý čas, odpočítať staré štatistiky od pôvodného používateľa
    IF OLD.cas_dobehnutia IS NOT NULL THEN
        UPDATE Pouzivatelia
        SET zabehnute_kilometre = GREATEST(COALESCE(zabehnute_kilometre, 0) - v_old_dlzka, 0),
            vypite_piva = GREATEST(COALESCE(vypite_piva, 0) - v_old_stanovisk, 0)
        WHERE email = OLD.email;
    END IF;

    -- Ak má bežec nový čas, pridať nové štatistiky novému (alebo rovnakému) používateľovi
    IF NEW.cas_dobehnutia IS NOT NULL THEN
        UPDATE Pouzivatelia
        SET zabehnute_kilometre = COALESCE(zabehnute_kilometre, 0) + v_new_dlzka,
            vypite_piva = COALESCE(vypite_piva, 0) + v_new_stanovisk
        WHERE email = NEW.email;
    END IF;
END;//
DELIMITER ;

-- Po vložení bežcov nastav správny počet účastníkov v rokKonania (bezpečne)
UPDATE rokKonania rk
SET pocet_ucastnikov = (
    SELECT COUNT(*) FROM Bezec b WHERE b.ID_roka = rk.ID_roka
)
WHERE rk.ID_roka IN (SELECT DISTINCT ID_roka FROM Bezec);

-- Migration adjustments moved here from migrate_add_xpos_ypos_stanovisko.sql:
-- Ensure empty-string coordinates are normalized to NULL
UPDATE `Stanovisko` SET x_pos = NULL WHERE x_pos = '';
UPDATE `Stanovisko` SET y_pos = NULL WHERE y_pos = '';

DELIMITER //
CREATE TRIGGER rokKonania_after_update_resync_stats
AFTER UPDATE ON rokKonania
FOR EACH ROW
BEGIN
    DECLARE diff_dlzka DECIMAL(7,2) DEFAULT 0.00;
    DECLARE diff_stan INT DEFAULT 0;

    SET diff_dlzka = COALESCE(NEW.dlzka_behu, 0.00) - COALESCE(OLD.dlzka_behu, 0.00);
    SET diff_stan = COALESCE(NEW.pocet_stanovisk, 0) - COALESCE(OLD.pocet_stanovisk, 0);

    IF diff_dlzka <> 0 OR diff_stan <> 0 THEN
        -- Only apply to runners that actually have a recorded finish time
        UPDATE Pouzivatelia p
        SET p.zabehnute_kilometre = GREATEST(COALESCE(p.zabehnute_kilometre, 0) + diff_dlzka, 0),
            p.vypite_piva = GREATEST(COALESCE(p.vypite_piva, 0) + diff_stan, 0)
        WHERE p.email IN (
            SELECT b.email FROM Bezec b WHERE b.ID_roka = NEW.ID_roka AND b.cas_dobehnutia IS NOT NULL
        );
    END IF;
END;//
DELIMITER ;
