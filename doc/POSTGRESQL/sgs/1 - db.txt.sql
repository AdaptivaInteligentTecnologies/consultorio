-- BANCO DE DADOS db_sgs - S.A.S. SISTEMA ADAPTIVA DE SEGUROS
--
-- BANCO DE DADOS (POSTGRESQL)
-- 
-- usuario.: sas
-- senha...: ^@D@pt53g$

DROP OWNED 		BY usr_sgs CASCADE;
DROP DATABASE 	IF EXISTS db_sgs;
DROP TABLESPACE IF EXISTS data_tblsp_sgs;
DROP TABLESPACE IF EXISTS indx_tblsp_sgs;
DROP ROLE 		IF EXISTS usr_sgs;
CREATE ROLE usr_sgs LOGIN SUPERUSER PASSWORD '^@D@pt53g$';

CREATE TABLESPACE data_tblsp_sgs	OWNER usr_sgs LOCATION 'c:/banco/data/dbs';
CREATE TABLESPACE indx_tblsp_sgs	OWNER usr_sgs LOCATION 'c:/banco/data/indexes';

CREATE SCHEMA IF NOT EXISTS sch_api_sgs_system	AUTHORIZATION usr_sgs;
CREATE SCHEMA IF NOT EXISTS sch_api_sgs_data 	AUTHORIZATION usr_sgs;
ALTER USER 					usr_sgs 			SET search_path TO sch_api_sgs_system; 

CREATE DATABASE db_sgs OWNER usr_sgs ENCODING 'UTF-8' TEMPLATE template0 TABLESPACE data_tblsp_sgs;