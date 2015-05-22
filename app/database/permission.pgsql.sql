-- Creator:       MySQL Workbench 5.2.37/ExportPostgres plugin 2009.12.02
-- Author:        Administrador
-- Caption:       New Model
-- Project:       Name of the project
-- Changed:       2014-03-15 23:44
-- Created:       2014-03-15 23:37

-- Schema: mydb
BEGIN;
CREATE TABLE system_group(
  id INTEGER PRIMARY KEY NOT NULL,
  name VARCHAR(100)
);
CREATE TABLE system_program(
  id INTEGER PRIMARY KEY NOT NULL,
  name VARCHAR(100),
  controller VARCHAR(100)
);
CREATE TABLE system_user(
  id INTEGER PRIMARY KEY NOT NULL,
  name VARCHAR(100),
  login VARCHAR(100),
  password VARCHAR(100),
  email VARCHAR(100),
  frontpage_id INTEGER NOT NULL,
  CONSTRAINT fk_system_user_system_program1
    FOREIGN KEY(frontpage_id)
    REFERENCES system_program(id)
);
CREATE INDEX system_user_fk_system_user_system_program1 ON system_user(frontpage_id);
CREATE TABLE system_user_group(
  id INTEGER PRIMARY KEY NOT NULL,
  system_user_id INTEGER NOT NULL,
  system_group_id INTEGER NOT NULL,
  CONSTRAINT fk_system_user_has_system_group_system_user
    FOREIGN KEY(system_user_id)
    REFERENCES system_user(id),
  CONSTRAINT fk_system_user_has_system_group_system_group1
    FOREIGN KEY(system_group_id)
    REFERENCES system_group(id)
);
CREATE INDEX system_user_group_fk_system_user_has_system_group_system_group1 ON system_user_group(system_group_id);
CREATE INDEX system_user_group_fk_system_user_has_system_group_system_user ON system_user_group(system_user_id);
CREATE TABLE system_group_program(
  id INTEGER PRIMARY KEY NOT NULL,
  system_group_id INTEGER NOT NULL,
  system_program_id INTEGER NOT NULL,
  CONSTRAINT fk_system_group_has_system_program_system_group1
    FOREIGN KEY(system_group_id)
    REFERENCES system_group(id),
  CONSTRAINT fk_system_group_has_system_program_system_program1
    FOREIGN KEY(system_program_id)
    REFERENCES system_program(id)
);
CREATE INDEX system_group_program_fk_system_group_has_system_program1 ON system_group_program(system_program_id);
CREATE INDEX system_group_program_fk_system_group_has_system_group1 ON system_group_program(system_group_id);
CREATE TABLE system_user_program(
  id INTEGER PRIMARY KEY NOT NULL,
  system_user_id INTEGER NOT NULL,
  system_program_id INTEGER NOT NULL,
  CONSTRAINT fk_system_user_has_system_program_system_user1
    FOREIGN KEY(system_user_id)
    REFERENCES system_user(id),
  CONSTRAINT fk_system_user_has_system_program_system_program1
    FOREIGN KEY(system_program_id)
    REFERENCES system_program(id)
);
CREATE INDEX system_user_program_fk_system_user_has_system_program_system_program1 ON system_user_program(system_program_id);
CREATE INDEX system_user_program_fk_system_user_has_system_program_system_user1 ON system_user_program(system_user_id);

INSERT INTO system_group VALUES(1,'ADMIN');
INSERT INTO system_program VALUES(1,'System Group Form','SystemGroupForm');
INSERT INTO system_program VALUES(2,'System Group List','SystemGroupList');
INSERT INTO system_program VALUES(3,'System Program Form','SystemProgramForm');
INSERT INTO system_program VALUES(4,'System Program List','SystemProgramList');
INSERT INTO system_program VALUES(5,'System User Form','SystemUserForm');
INSERT INTO system_program VALUES(6,'System User List','SystemUserList');
INSERT INTO system_program VALUES(7,'Common Page','CommonPage');

INSERT INTO system_user VALUES(1,'Administrator','admin','21232f297a57a5a743894a0e4a801fc3','admin@admin.net',6);
INSERT INTO system_user VALUES(2,'User','user','ee11cbb19052e40b07aac0ca060c23ee','user@user.net',7);

INSERT INTO system_user_group VALUES(1,1,1);
INSERT INTO system_group_program VALUES(1,1,1);
INSERT INTO system_group_program VALUES(2,1,2);
INSERT INTO system_group_program VALUES(3,1,3);
INSERT INTO system_group_program VALUES(4,1,4);
INSERT INTO system_group_program VALUES(5,1,5);
INSERT INTO system_group_program VALUES(6,1,6);
INSERT INTO system_user_program VALUES(1,2,7);
COMMIT;
