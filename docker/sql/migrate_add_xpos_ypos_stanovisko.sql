-- Migration deprecated: functionality moved into docker/sql/ddl.posts_01.sql
-- Original file: migrate_add_xpos_ypos_stanovisko.sql
-- Reason: columns x_pos and y_pos are now created as part of the main DDL in ddl.posts_01.sql
-- The normalization UPDATEs (setting empty-string coordinates to NULL) were also moved there.
-- Keep this file as a record; do NOT run this script.

/*
Original content (moved):
ALTER TABLE `Stanovisko`
  ADD COLUMN `x_pos` DECIMAL(9,6) NULL AFTER `obrazok_odkaz`,
  ADD COLUMN `y_pos` DECIMAL(9,6) NULL AFTER `x_pos`;

UPDATE `Stanovisko` SET x_pos = NULL WHERE x_pos = '';
UPDATE `Stanovisko` SET y_pos = NULL WHERE y_pos = '';
*/
