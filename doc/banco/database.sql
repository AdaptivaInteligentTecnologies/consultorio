/*
NOTAS IMPORTANTES PARA INSTALAÇÃO
1- Lembrar de configurar o NTP
*/
DROP TABLE IF EXISTS agenda_pacientes			CASCADE;
DROP TABLE IF EXISTS agenda_profissional 		CASCADE;
DROP TABLE IF EXISTS bairros				CASCADE;
DROP TABLE IF EXISTS consultas				CASCADE;
DROP TABLE IF EXISTS convenios				CASCADE;
DROP TABLE IF EXISTS contatos_profissionais		CASCADE;
DROP TABLE IF EXISTS convenios_profissionais		CASCADE;
DROP TABLE IF EXISTS cid10				CASCADE;
DROP TABLE IF EXISTS cidades				CASCADE;
DROP TABLE IF EXISTS consultorios			CASCADE;
DROP TABLE IF EXISTS cores_racas			CASCADE;
DROP TABLE IF EXISTS contatos				CASCADE;
DROP TABLE IF EXISTS empresas				CASCADE;
DROP TABLE IF EXISTS especialidades_profissionais	CASCADE;
DROP TABLE IF EXISTS estados_federativos		CASCADE;
DROP TABLE IF EXISTS enderecos_pessoas			CASCADE;
DROP TABLE IF EXISTS estados_civis			CASCADE;
DROP TABLE IF EXISTS fichas_medicas			CASCADE;
DROP TABLE IF EXISTS graus_de_escolaridades		CASCADE;
DROP TABLE IF EXISTS logradouros			CASCADE;
DROP TABLE IF EXISTS necessidades_especiais		CASCADE;
DROP TABLE IF EXISTS pessoas_fisicas 			CASCADE;
DROP TABLE IF EXISTS pessoas_juridicas 			CASCADE;
DROP TABLE IF EXISTS pessoas 				CASCADE;
DROP TABLE IF EXISTS pessoas_tem_enderecos		CASCADE;
DROP TABLE IF EXISTS pais 				CASCADE;
DROP TABLE IF EXISTS profissionais			CASCADE;
DROP TABLE IF EXISTS profissionais_tem_especialidades	CASCADE;
DROP TABLE IF EXISTS profissionais_tem_agendas		CASCADE;
DROP TABLE IF EXISTS profissionais_tem_convenios	CASCADE;
DROP TABLE IF EXISTS pacientes				CASCADE;
DROP TABLE IF EXISTS procedimentos_profissionais 	CASCADE;
DROP TABLE IF EXISTS status_agendamento			CASCADE;
DROP TABLE IF EXISTS tipos_agendamentos			CASCADE;
DROP TABLE IF EXISTS tipos_contatos			CASCADE;
DROP TABLE IF EXISTS conselhos				CASCADE;



CREATE TABLE cores_racas
(
	crs_id serial,
	crs_descricao varchar(30)
);
ALTER TABLE cores_racas
	ALTER COLUMN crs_id SET NOT NULL,
	ALTER COLUMN crs_descricao SET NOT NULL,
	ADD CONSTRAINT unique_crs_descricao UNIQUE(crs_descricao),
	ADD PRIMARY KEY(crs_id);

INSERT INTO cores_racas(crs_descricao) values
('NÃO DECLARADO'),
('BRANCO'),
('PARDO'),
('PRETO'),
('AMARELO'),
('INDÍGENA');


CREATE TABLE pais(
	pai_id serial,
	pai_descricao varchar(200),
	pai_sigla char(5)
);

INSERT INTO pais(pai_descricao,pai_sigla) values ('Brasil','BRA');

ALTER TABLE pais
	ALTER COLUMN pai_id SET NOT NULL,
	ALTER COLUMN pai_descricao SET NOT NULL,
	ALTER COLUMN pai_sigla SET NOT NULL,
	ADD CONSTRAINT unique_pai_descricao UNIQUE (pai_descricao),
	ADD PRIMARY KEY (pai_id);


CREATE TABLE estados_federativos(
	efs_id serial,
	efs_descricao varchar(200),
	efs_sigla char(2),
	efs_pai_id integer
);

ALTER TABLE estados_federativos
	ALTER COLUMN efs_id SET NOT NULL,
	ALTER COLUMN efs_descricao SET NOT NULL,
	ALTER COLUMN efs_pai_id SET NOT NULL,
	ALTER COLUMN efs_sigla SET NOT NULL,
	ADD CONSTRAINT efs_pai_id_fk FOREIGN KEY (efs_pai_id) REFERENCES pais (pai_id) MATCH FULL,
	ADD CONSTRAINT unique_efs_descricao UNIQUE (efs_descricao),
	ADD PRIMARY KEY (efs_id);
	
INSERT INTO estados_federativos(efs_descricao,efs_sigla,efs_pai_id) VALUES ('Maranhão','MA',1);

CREATE TABLE cidades(
	cde_id serial,
	cde_descricao varchar(100),
	cde_efs_id integer
);
ALTER TABLE cidades
	ALTER COLUMN cde_id SET NOT NULL,
	ALTER COLUMN cde_efs_id SET NOT NULL,
	ALTER COLUMN cde_descricao SET NOT NULL,
	ADD CONSTRAINT unique_cde_descricao UNIQUE (cde_descricao),
	ADD CONSTRAINT cde_efs_id_fk FOREIGN KEY (cde_efs_id) REFERENCES estados_federativos (efs_id) MATCH FULL,
	ADD PRIMARY KEY (cde_id);

INSERT INTO cidades(cde_efs_id,cde_descricao) VALUES(1,'São Luís');

CREATE TABLE bairros(
	brr_id serial,
	brr_descricao varchar(100),
	brr_cde_id integer
);

ALTER TABLE bairros 
	ALTER COLUMN brr_id SET NOT NULL,
	ALTER COLUMN brr_descricao SET NOT NULL,
	ADD CONSTRAINT unique_brr_descricao UNIQUE (brr_descricao),
	ADD CONSTRAINT brr_cde_id_fk FOREIGN KEY (brr_cde_id) REFERENCES cidades (cde_id) MATCH FULL,
	ADD PRIMARY KEY (brr_id);


CREATE TABLE logradouros(
	lgr_id serial,
	lgr_descricao varchar(200),
	lgr_numero varchar(10),
	lgr_cep varchar(10),
	lgr_brr_id integer
);
ALTER TABLE logradouros
	ALTER COLUMN lgr_id SET NOT NULL,
	ALTER COLUMN lgr_descricao SET NOT NULL,
	ALTER COLUMN lgr_numero SET NOT NULL,
	ALTER COLUMN lgr_numero SET DEFAULT 'S/N',
	ALTER COLUMN lgr_cep SET NOT NULL,
	ALTER COLUMN lgr_brr_id SET NOT NULL,
	ADD CONSTRAINT lgr_brr_id_fk FOREIGN KEY (lgr_brr_id) REFERENCES bairros (brr_id) MATCH FULL,
	ADD CONSTRAINT unique_lgr_descricao UNIQUE (lgr_descricao),
	ADD PRIMARY KEY (lgr_id);



CREATE TABLE conselhos
(
	css_id serial,
	css_descricao varchar(25)
);
ALTER TABLE conselhos
	ALTER COLUMN css_id 				SET NOT NULL,
	ALTER COLUMN css_descricao			SET NOT NULL,
ADD PRIMARY KEY( css_id );
INSERT INTO conselhos(css_descricao) VALUES
('CRM'),
('CRO');


CREATE TABLE profissionais(
	pfs_id serial,
	pfs_con_id integer,
	pfs_css_id integer,
	pfs_numero_conselho varchar(20),
	pfs_nome varchar(50),
	pfs_conselho_uf_id integer,
	pfs_cnes varchar(20)
	
	--pfs_pss_id integer
);

ALTER TABLE profissionais 
	ALTER COLUMN pfs_id SET NOT NULL,
	ALTER COLUMN pfs_con_id SET NOT NULL,
	ALTER COLUMN pfs_css_id SET NOT NULL,
	ALTER COLUMN pfs_nome SET NOT NULL,
	ALTER COLUMN pfs_numero_conselho SET NOT NULL,
	ALTER COLUMN pfs_conselho_uf_id SET NOT NULL,
	--ALTER COLUMN pfs_pss_id SET NOT NULL,
	ADD CONSTRAINT pfs_con_id_fk FOREIGN KEY (pfs_con_id) REFERENCES consultorios (con_id) MATCH FULL,
	ADD CONSTRAINT pfs_conselho_uf_id_fk FOREIGN KEY (pfs_conselho_uf_id) REFERENCES estados_federativos (efs_id) MATCH FULL,
	ADD CONSTRAINT pfs_css_id_fk FOREIGN KEY (pfs_css_id) REFERENCES conselhos(css_id) MATCH FULL,
	--ADD CONSTRAINT pfs_pss_id_fk FOREIGN KEY (pfs_pss_id) REFERENCES pessoas (pss_id) MATCH FULL,
	ADD CONSTRAINT unique_pfs_conselho_numero_conselho_uf_id UNIQUE (pfs_numero_conselho,pfs_conselho_uf_id),
	ADD CONSTRAINT unique_pfs_id_con_id UNIQUE (pfs_id,pfs_con_id),
	ADD PRIMARY KEY (pfs_id);


CREATE TABLE estados_civis
(
	ecs_id serial,
	ecs_descricao varchar(35)
);
ALTER TABLE estados_civis
	ALTER COLUMN ecs_id SET NOT NULL,
	ALTER COLUMN ecs_descricao SET NOT NULL,
	ADD PRIMARY KEY(ecs_id);


CREATE TABLE empresas 
(
	emp_id serial,
	emp_nome_fantasia varchar(200),
	emp_razao_social varchar(200),
	emp_cnpj varchar(20),
	emp_inscricao_estadual varchar(25),
	emp_inscricao_municipal varchar(25),
	emp_cep varchar(10),
	emp_logradouro varchar(200),
	emp_numero varchar(200),
	emp_uf char(2),
	emp_cidade varchar(100),
	emp_bairro varchar(100)
	
);
ALTER TABLE empresas
	ALTER COLUMN emp_id 		SET NOT NULL,
	ALTER COLUMN emp_nome_fantasia 	SET NOT NULL,
	ALTER COLUMN emp_razao_social	SET NOT NULL,
	ALTER COLUMN emp_cnpj 		SET NOT NULL,
	--ADD CONSTRAINT emp_efs_id_fk	FOREIGN KEY(emp_efs_id) REFERENCES estados_federativos(efs_id) MATCH FULL,
	--ADD CONSTRAINT emp_brr_id_fk	FOREIGN KEY(emp_brr_id) REFERENCES bairros(brr_id) MATCH FULL,
	ADD PRIMARY KEY(emp_id);


CREATE TABLE consultorios
(
	con_id serial,
	con_nome 		varchar(50),
	con_emp_id		integer,
	con_ini_expediente 	varchar(5),
	con_fim_expediente 	varchar(5),
	con_fecha_para_almoco 	char(1),
	con_ini_almoco 		varchar(5),
	con_fim_almoco 		varchar(5),
	con_fun_segunda 	char(1),
	con_fun_terca 		char(1),
	con_fun_quarta 		char(1),
	con_fun_quinta 		char(1),
	con_fun_sexta 		char(1),
	con_fun_sabado 		char(1),
	con_fun_domingo 	char(1)
);

ALTER TABLE consultorios

	ALTER COLUMN con_id SET NOT NULL,
 	ALTER COLUMN con_nome SET NOT NULL,
	ALTER COLUMN con_emp_id SET NOT NULL,
	ALTER COLUMN con_ini_expediente SET NOT NULL,
	ALTER COLUMN con_ini_expediente SET DEFAULT '08:00',
	ALTER COLUMN con_fim_expediente SET NOT NULL,
	ALTER COLUMN con_fim_expediente SET DEFAULT '18:00',
 	ALTER COLUMN con_ini_almoco SET NOT NULL,
	ALTER COLUMN con_fecha_para_almoco SET NOT NULL,
	ALTER COLUMN con_fecha_para_almoco SET DEFAULT 'N',
 	ALTER COLUMN con_ini_almoco SET DEFAULT '12:00',
 	ALTER COLUMN con_fim_almoco SET NOT NULL,
 	ALTER COLUMN con_fim_almoco SET DEFAULT '14:00',
 	ALTER COLUMN con_fun_domingo SET NOT NULL,
	ALTER COLUMN con_fun_domingo SET DEFAULT 'N',
	ALTER COLUMN con_fun_segunda SET NOT NULL,
	ALTER COLUMN con_fun_segunda SET DEFAULT 'S',
 	ALTER COLUMN con_fun_terca SET NOT NULL,
 	ALTER COLUMN con_fun_terca SET DEFAULT 'S',
 	ALTER COLUMN con_fun_quarta SET NOT NULL,
	ALTER COLUMN con_fun_quarta SET DEFAULT 'S',
 	ALTER COLUMN con_fun_quinta SET NOT NULL,
	ALTER COLUMN con_fun_quinta SET DEFAULT 'S',
 	ALTER COLUMN con_fun_sexta SET NOT NULL,
	ALTER COLUMN con_fun_sexta SET DEFAULT 'S',
 	ALTER COLUMN con_fun_sabado SET NOT NULL,
	ALTER COLUMN con_fun_sabado SET DEFAULT 'S',
 	ADD CONSTRAINT unique_con_nome UNIQUE (con_nome),
	ADD CONSTRAINT chk_con_segunda_SN 		check(upper(con_fun_segunda) in ('S','N')),
	ADD CONSTRAINT chk_con_terca_SN 		check(upper(con_fun_terca) in ('S','N')),
	ADD CONSTRAINT chk_con_quarta_SN 		check(upper(con_fun_quarta) in ('S','N')),
	ADD CONSTRAINT chk_con_quinta_SN 		check(upper(con_fun_quinta) in ('S','N')),
	ADD CONSTRAINT chk_con_sexta_SN 		check(upper(con_fun_sexta) in ('S','N')),
	ADD CONSTRAINT chk_con_sabado_SN 		check(upper(con_fun_sabado) in ('S','N')),
	ADD CONSTRAINT chk_con_domingo_SN 		check(upper(con_fun_domingo) in ('S','N')),
	ADD CONSTRAINT chk_con_fecha_para_almoco_SN 	check(upper(con_fecha_para_almoco) in ('S','N')),
	ADD CONSTRAINT fk_com_emp_id FOREIGN KEY(con_emp_id) REFERENCES empresas(emp_id) MATCH FULL,
 	ADD PRIMARY KEY(con_id);

CREATE TABLE convenios_profissionais(
cps_id serial,
cps_descricao varchar(50),
cps_codigo_operadora varchar(20),
cps_codigo_registro_ans varchar(20)
);
ALTER TABLE convenios_profissionais
	ALTER COLUMN cps_id SET NOT NULL,
	ALTER COLUMN cps_descricao SET NOT NULL,
	ALTER COLUMN cps_codigo_operadora SET NOT NULL,
	ALTER COLUMN cps_codigo_registro_ans SET NOT NULL,
	ADD PRIMARY KEY(cps_id);
INSERT INTO convenios_profissionais(cps_descricao,cps_codigo_operadora,cps_codigo_registro_ans) values('Particular','0','0');
INSERT INTO convenios_profissionais(cps_descricao,cps_codigo_operadora,cps_codigo_registro_ans) values('Cortesia','0','0');
INSERT INTO convenios_profissionais(cps_descricao,cps_codigo_operadora,cps_codigo_registro_ans) values('Bradesco','1234','5678');


CREATE TABLE tipos_contatos(
	tco_id serial,
	tco_descricao varchar(35)
);
ALTER TABLE tipos_contatos
	ALTER COLUMN tco_id SET NOT NULL,
	ALTER COLUMN tco_descricao SET NOT NULL,
	ADD PRIMARY KEY(tco_id);


CREATE TABLE contatos_profissionais(
	ctp_id serial,
	ctp_pfs_id integer,
	ctp_tco_id integer,
	ctp_valor varchar(200)
);

ALTER TABLE contatos_profissionais
	ALTER COLUMN ctp_id SET NOT NULL,	
	ALTER COLUMN ctp_pfs_id SET NOT NULL,
	ALTER COLUMN ctp_tco_id SET NOT NULL,
	ALTER COLUMN ctp_valor SET NOT NULL,a
	ADD CONSTRAINT ctp_pfs_id_fk FOREIGN KEY (ctp_pfs_id) REFERENCES profissionais (pfs_id) MATCH FULL,
	ADD CONSTRAINT ctp_tco_id_fk FOREIGN KEY (ctp_tco_id) REFERENCES tipos_contatos (tco_id) MATCH FULL,
	ADD CONSTRAINT unique_ctp_pfs_valor UNIQUE (ctp_pfs_id,ctp_valor),
	ADD PRIMARY KEY(ctp_id);



CREATE TABLE profissionais_tem_convenios(
	ptc_id serial,
	ptc_pfs_id integer,
	ptc_cps_id integer
);

ALTER TABLE profissionais_tem_convenios
	ALTER COLUMN ptc_id SET NOT NULL,
	ALTER COLUMN ptc_pfs_id SET NOT NULL,
	ALTER COLUMN ptc_cps_id SET NOT NULL,
	ADD CONSTRAINT ptc_pfs_id_fk FOREIGN KEY (ptc_pfs_id) REFERENCES profissionais (pfs_id) MATCH FULL,
	ADD CONSTRAINT ptc_cps_id_fk FOREIGN KEY (ptc_cps_id) REFERENCES convenios_profissionais (cps_id) MATCH FULL,
	ADD CONSTRAINT unique_ptc_pfs_cms UNIQUE (ptc_pfs_id,ptc_cps_id),
	ADD PRIMARY KEY(ptc_id);


CREATE TABLE especialidades_profissionais(
	ems_id serial,
	ems_descricao varchar(50)
);

ALTER TABLE especialidades_profissionais 
	ALTER COLUMN ems_id SET NOT NULL,
	ALTER COLUMN ems_descricao SET NOT NULL,
	ADD CONSTRAINT unique_ems_descricao UNIQUE (ems_descricao),
	ADD PRIMARY KEY (ems_id);




CREATE TABLE profissionais_tem_especialidades(
	mte_id serial,
	mte_pfs_id integer,
	mte_ems_id integer
);
ALTER TABLE profissionais_tem_especialidades
	ALTER COLUMN mte_id SET NOT NULL,
	ALTER COLUMN mte_pfs_id SET NOT NULL,
	ALTER COLUMN mte_ems_id SET NOT NULL,
	ADD CONSTRAINT mte_pfs_id_fk FOREIGN KEY (mte_pfs_id) REFERENCES profissionais (pfs_id) MATCH FULL,
	ADD CONSTRAINT mte_ems_id_fk FOREIGN KEY (mte_ems_id) REFERENCES especialidades_profissionais (ems_id) MATCH FULL,
	ADD CONSTRAINT unique_mte_pfs_ems_id UNIQUE (mte_pfs_id,mte_ems_id),
	ADD PRIMARY KEY (mte_id);




CREATE TABLE agenda_profissional
(
	agm_id serial,
	agm_pfs_id integer,
	agm_con_id integer,
	agm_segunda char(1)
);



CREATE TABLE graus_de_escolaridades
(
	ges_id serial,
	ges_descricao varchar(35)
);

ALTER TABLE graus_de_escolaridades
	ALTER COLUMN ges_id SET NOT NULL,
	ALTER COLUMN ges_descricao SET NOT NULL,
	ADD CONSTRAINT unique_ges_descricao  UNIQUE (ges_descricao),
	ADD PRIMARY KEY(ges_id);

/*
CREATE TABLE necessidades_especiais
(
	nes_id serial,
	nes_descricao varchar(35),
	nes_caminho_icone varchar(255);

); -- fim necessidades_especiais

ALTER TABLE necessidades_especiais
	ALTER COLUMN nes_id 		SET NOT NULL,
	ALTER COLUMN nes_descricao 	SET NOT NULL,
	ADD PRIMARY KEY(nes_id);

INSERT INTO necessidades_especiais(nes_descricao,nes_caminho_icone) values
('app/images/trash-icon2.png)
*/


CREATE TABLE pacientes
(
	pts_id serial,
	pts_ecs_id integer, -- estados civis
	pts_crs_id integer, -- cores e raças
	pts_ges_id integer, -- grau de escolaridade
	pts_pne char(1),    -- portador necessidades especiais
	pts_nome varchar(50),
	pts_nome_mae varchar(50),
	pts_cpf varchar(14),
	pts_sexo char(1),
	pts_data_nascimento date,
	pts_cep varchar(10),
	pts_logradouro varchar(200),
	pts_numero varchar(200),
	pts_uf char(2),
	pts_cidade varchar(100),
	pts_bairro varchar(100),
	pts_data_cadastro date,
	pts_usr_id integer,
	pts_hda text
	
); -- fim pacientes
     ALTER TABLE pacientes

	ALTER COLUMN pts_id 			SET NOT NULL,
	ALTER COLUMN pts_nome 			SET NOT NULL,
	ALTER COLUMN pts_nome_mae 		SET NOT NULL,
	--ALTER COLUMN pts_sexo 			SET NOT NULL,
	ALTER COLUMN pts_data_nascimento 	SET NOT NULL,

	ALTER COLUMN pts_data_cadastro	 	SET DEFAULT NOW(),
	ALTER COLUMN pts_pne			SET DEFAULT 'N',

	ADD CONSTRAINT chk_pts_sexo 		CHECK(UPPER(pts_sexo) IN ('M','F')),
	ADD CONSTRAINT chk_pts_pne 		CHECK(UPPER(pts_pne) IN ('S','N')),
	ADD CONSTRAINT unique_pts_nome UNIQUE(pts_nome,pts_nome_mae),
	ADD CONSTRAINT fk_pts_ecs_id FOREIGN KEY(pts_ecs_id) REFERENCES estados_civis(ecs_id) MATCH FULL,
	ADD CONSTRAINT fk_pts_crs_id FOREIGN KEY(pts_crs_id) REFERENCES cores_racas(crs_id) MATCH FULL,
	ADD CONSTRAINT fk_pts_ges_id FOREIGN KEY(pts_ges_id) REFERENCES graus_de_escolaridades(ges_id) MATCH FULL,
	ADD PRIMARY KEY(pts_id); -- fim alter table pacientes


CREATE TABLE tipos_agendamentos
(
	tas_id serial,
	tas_descricao varchar(35),
	tas_cor varchar(10)
);
ALTER TABLE tipos_agendamentos
	ALTER COLUMN tas_id SET NOT NULL,
	ALTER COLUMN tas_descricao SET NOT NULL,
	ALTER COLUMN tas_cor SET NOT NULL,
	ADD CONSTRAINT unique_tas_descricao UNIQUE(tas_descricao),
	ADD PRIMARY KEY(tas_id);


CREATE TABLE procedimentos_profissionais
(
	pms_id serial,
	pms_descricao varchar(50),
	pms_cor varchar(10),
	pms_valor decimal(10,2)
);
ALTER TABLE procedimentos_profissionais
ALTER COLUMN pms_id 				SET NOT NULL,
ALTER COLUMN pms_descricao		 	SET NOT NULL,
ALTER COLUMN pms_cor			 	SET NOT NULL,
ALTER COLUMN pms_valor				SET NOT NULL,
ALTER COLUMN pms_valor				SET DEFAULT 0.0,
ADD CONSTRAINT unique_pms_descricao UNIQUE(pms_descricao),
ADD PRIMARY KEY(pms_id);


/*
select * from agenda_pacientes
delete from agenda_pacientes
*/
DROP TABLE agenda_pacientes
CREATE TABLE agenda_pacientes
(
	aps_id serial,
	aps_pts_id integer, -- paciente
	aps_pfs_id integer, -- profissional
	aps_pms_id integer, -- procedimento
	aps_cps_id integer, -- convenio
	aps_nome_paciente varchar(50),
	aps_data_nascimento date,
	aps_data_cadastro date,
	aps_data_agendada date,
	aps_hora_agendada time,
	aps_status char(1),
	aps_confirmado char(1),
	aps_telefone_contato1 varchar(15),
	aps_telefone_contato2 varchar(15)
);

--ALTER TABLE agenda_pacientes DROP CONSTRAINT aps_status_chk
--ALTER TABLE agenda_pacientes ADD CONSTRAINT aps_status_chk check(upper(aps_status) in ('A','C','E')),

ALTER TABLE agenda_pacientes
	ALTER COLUMN aps_id 			SET NOT NULL,
	ALTER COLUMN aps_pfs_id			SET NOT NULL,
	ALTER COLUMN aps_cps_id			SET NOT NULL,
	ALTER COLUMN aps_pms_id			SET NOT NULL,
	ALTER COLUMN aps_data_cadastro		SET NOT NULL,
	ALTER COLUMN aps_data_agendada		SET NOT NULL,
	ALTER COLUMN aps_hora_agendada		SET NOT NULL,
	ALTER COLUMN aps_data_cadastro  	SET DEFAULT CURRENT_TIMESTAMP,
	ALTER COLUMN aps_status			SET DEFAULT 'A', -- (A)gendado, (C)ancelado,
	ALTER COLUMN aps_confirmado		SET DEFAULT 'N', -- (N)ão, (S)im
	
	ADD CONSTRAINT aps_status_chk check(upper(aps_status) in ('A','C','E')),
	ADD CONSTRAINT aps_confirmado_chk check(upper(aps_confirmado) in ('S','N')),

	ADD CONSTRAINT fk_aps_pts_id FOREIGN KEY(aps_pts_id) REFERENCES pacientes(pts_id) MATCH FULL,
	ADD CONSTRAINT fk_aps_cps_id FOREIGN KEY(aps_cps_id) REFERENCES convenios_profissionais(cps_id) MATCH FULL,
	ADD CONSTRAINT fk_aps_pms_id FOREIGN KEY(aps_pms_id) REFERENCES procedimentos_profissionais(pms_id) MATCH FULL,
	ADD CONSTRAINT unique_aps_consulta UNIQUE (aps_pts_id,aps_pfs_id,aps_cps_id,aps_data_agendada,aps_hora_agendada),
	ADD PRIMARY KEY(aps_id);

	
CREATE TABLE fichas_medicas
(
	fms_id serial,
	fms_pts_id integer, 		-- id do paciente
	fms_data_hora timestamp,		-- data desta ficha
	fms_queixa_principal text,	-- queixa principal
	fms_hda text			-- histórico do atendimento
);
ALTER TABLE fichas_medicas
ALTER COLUMN fms_id SET NOT NULL,
ALTER COLUMN fms_pts_id SET NOT NULL,
ALTER COLUMN fms_data_hora SET NOT NULL,
ALTER COLUMN fms_queixa_principal SET NOT NULL,
ALTER COLUMN fms_hda SET NOT NULL,
ADD CONSTRAINT unique_fms_pts_data UNIQUE(fms_pts_id,fms_data_hora),
ADD PRIMARY KEY(fms_id);



CREATE TABLE cid10
(
	cid_id serial,
	cid_descricao varchar(200)
);
ALTER TABLE cid10
ALTER COLUMN cid_id SET NOT NULL,
ALTER COLUMN cid_descricao SET NOT NULL,
ADD CONSTRAINT unique_cid_descricao UNIQUE(cid_descricao),
ADD PRIMARY KEY(cid_id);




CREATE TABLE status_agendamento
(
	sas_id serial,
	sas_descricao varchar(50),
	sas_cor varchar(10)
);
	ALTER TABLE status_agendamento
ALTER COLUMN sas_id 				SET NOT NULL,
ALTER COLUMN sas_descricao		 	SET NOT NULL,
ALTER COLUMN sas_cor			 	SET NOT NULL,
ADD CONSTRAINT unique_scs_descricao UNIQUE(sas_descricao),
ADD PRIMARY KEY(sas_id);


-- talvez deva se chamar de fila de atendimento

CREATE TABLE consultas
(
	cns_id 				serial,
	cns_cid10_id 			integer,
	cns_pfs_id 			integer,
	cns_pts_id 			integer,
	cns_pms_id			integer, -- tipo de procedimento. ex. retorno
	cns_data_hora_ini_consulta 	timestamp,
	cns_data_hora_fim_consulta 	timestamp,
	cns_pressao_arterial_sistolica 	varchar(3),
	cns_pressao_arterial_diastolica varchar(3),
	cns_peso 			varchar(3),
	cns_altura 			varchar(4),
	cns_fc 				varchar(3), 	-- frequencia cardíaca
	cns_queixa_principal 		text,
	cns_hda 			text, 		-- hitórico da doença atual
	cns_hmp 			text, 		-- histórico médico pregressa
	cns_observacao 			text,
	cns_status 			char(1), -- 
	cns_valor			decimal(10,2),
	cns_valor_cobrado		decimal(10,2)
);

ALTER TABLE consultas
ALTER COLUMN cns_id 				SET NOT NULL,
ALTER COLUMN cns_data_hora_ini_consulta 	SET NOT NULL,
ALTER COLUMN cns_data_hora_fim_consulta 	SET NOT NULL,
ALTER COLUMN cns_pfs_id				SET NOT NULL,
ALTER COLUMN cns_pts_id				SET NOT NULL,
ALTER COLUMN cns_queixa_principal		SET NOT NULL,
ADD CONSTRAINT ch_cns_status			CHECK(UPPER(cns_status) IN ('A','E','F')), -- Aguardando, Em atendimento, Finalizado 
ADD CONSTRAINT fk_pfs_id FOREIGN KEY (cns_pfs_id) REFERENCES profissionais(pfs_id),
ADD CONSTRAINT fk_pts_id FOREIGN KEY (cns_pts_id) REFERENCES pacientes(pts_id),
ADD PRIMARY KEY ( cns_id );
	



/**
drop view convenios_profissionais_view
CREATE 	OR REPLACE VIEW convenios_profissionais_view(tco_id,tco_descricao,ctp_valor) AS 
select tco_id,tco_descricao,ctp_valor from profissionais med 
left join contatos_profissionais ctm on ctm.ctp_pfs_id = med.pfs_id
left join tipos_contatos tco on tco.tco_id = ctm.ctp_tco_id

--select * from convenios_profissionais_view

CREATE VIEW name AS WITH RECURSIVE name (columns) AS (SELECT ...) SELECT columns FROM name;

create table agenda_medica
(
	age_id serial,
	age_data_consulta date,
	age_hora_
);


create view profissionais_list_view as select 
	pfs_id,
	psf.psf_nome,
	psf.psf_nome_mae,
	psf_estado_civil_id,
	pfs_numero_crm,
	pfs_conselho_uf_id,
	efs.efs_sigla,
	pfs_cnes,
	psf_cpf,
	psf_rg,
	psf_sexo,
	psf_data_nascimento
	from profissionais med 
	inner join pessoas pss on med.pfs_pss_id = pss.pss_id
	left join pessoas_fisicas psf on psf.psf_pss_id = pss.pss_id
	left join estados_federativos efs on efs.efs_id = med.pfs_conselho_uf_id;



select
	pfs_id,
	psf.psf_nome,
	psf.psf_nome_mae,
	psf_estado_civil_id,
	pfs_numero_crm,
	pfs_conselho_uf_id,
	efs.efs_sigla,
	pfs_cnes,
	psf_cpf,
	psf_rg,
	psf_sexo,
	to_char(psf_data_nascimento,'DD/MM/YYYY'),
	from profissionais med 
	inner join pessoas pss on med.pfs_pss_id = pss.pss_id
	left join pessoas_fisicas psf on psf.psf_pss_id = pss.pss_id
	left join estados_federativos efs on efs.efs_id = med.pfs_conselho_uf_id;


select * from tipos_contatos
select * from contatos_profissionais
  
--delete from contatos_profissionais
--delete from profissionais


select * from profissionais


select * from bairros
insert into estados_federativos(efs_descricao, efs_pai_id) values
('Rio de Janeiro',1),
('São Paulo',1);

select * from estados_federativos
select * from cidades
insert into cidades(cde_descricao,cde_efs_id) values
('Diadema',3),
('Santo André',3);

update estados_federativos set efs_sigla = 'SP' where efs_id = 3
insert into bairros(brr_descricao,brr_cde_id) values
('Recreio dos bandeirantes',2),
('Copa cabana',2),
('Jardins',3),
('Morumbi',3),
('Higienópolis',3);

select * from estados_federativos
select * from cidades
select efs_descricao,efs_sigla,cde_descricao from estados_federativos efs inner join cidades cde on efs.efs_id = cde.cde_efs_id

	Select to_char(1250.75, 'L9G999G990D99')  -- converte para reais
	Select to_char(0.0, 'L9G999G990D99')  -- converte para reais
	Select replace(replace(replace(to_char(1250.75, 'L9G999G990D99'),',','-' ),'.',','),'-','.')

DROP TABLE IF EXISTS graus_de_escolaridades;


-- cadastro de PESSOAS
CREATE TABLE pessoas(
	pss_id serial,
	pss_tipo char(1)
);

ALTER TABLE pessoas
	ALTER COLUMN pss_id SET NOT NULL,
	ALTER COLUMN pss_tipo SET NOT NULL,
	ADD PRIMARY KEY(pss_id),
	ADD CONSTRAINT pss_tipochk CHECK (UPPER(pss_tipo) in ('F','J'));




-- cadastro de PESSOAS FÍSICAS

CREATE TABLE pessoas_fisicas(
	psf_id serial,
	psf_pss_id integer,
	psf_nome varchar(50),
	psf_nome_mae varchar(50),
	psf_rg varchar(20),
	psf_rg varchar(20),
	psf_cpf varchar(14),
	psf_data_nascimento date,
	psf_sexo char(1),
	psf_estado_civil_id integer
);

ALTER TABLE pessoas_fisicas
	ALTER COLUMN psf_pss_id SET NOT NULL,
	ALTER COLUMN psf_nome SET NOT NULL,
	ADD CONSTRAINT psf_pss_id_fk FOREIGN KEY (psf_pss_id) REFERENCES pessoas (pss_id) MATCH FULL,
	ADD CONSTRAINT psf_sexochk CHECK (UPPER(psf_sexo) in ('M','F')),
	ADD CONSTRAINT unique_psf_pss_id UNIQUE (psf_pss_id),
	ADD PRIMARY KEY(psf_id);

CREATE TABLE pessoas_juridicas(
	psj_pss_id integer,
	psj_razao_social varchar(50),
	psj_nome_fantasia varchar(50),
	psj_cnpj varchar(15),
	psj_inscricao_estadual varchar(30),
	psj_inscricao_municipal varchar(30)

);

ALTER TABLE pessoas_juridicas
	ALTER COLUMN psj_id SET NOT NULL,
	ALTER COLUMN psj_pessoa_id SET NOT NULL,
	ALTER COLUMN psj_razao_social SET NOT NULL,
	ALTER COLUMN psj_cnpj SET NOT NULL,
	ADD PRIMARY KEY(psj_id),
	ADD CONSTRAINT psj_id_fk FOREIGN KEY (psj_id) REFERENCES pessoas (pss_id) MATCH FULL;


select * from pessoas pss 
	left join pessoas_fisicas psf on psf.psf_pss_id = pss.pss_id
	left join profissionais med on med.pfs_pss_id = pss.pss_id
	left join contatos cts on cts.cts_pss_id = pss.pss_id
	left join tipos_contatos tco on tco.tco_id = cts_tco_id

	

CREATE TABLE contatos(
	cts_id serial,
	cts_pss_id integer,
	cts_tco_id integer,
	cts_valor varchar(200)
);

ALTER TABLE contatos
	ALTER COLUMN cts_id SET NOT NULL,
	ALTER COLUMN cts_tco_id SET NOT NULL,
	ALTER COLUMN cts_valor SET NOT NULL,
	ADD CONSTRAINT cts_pss_id_fk FOREIGN KEY (cts_pss_id) REFERENCES pessoas (pss_id) MATCH FULL,
	ADD CONSTRAINT cts_tco_id_fk FOREIGN KEY (cts_tco_id) REFERENCES tipos_contatos (tco_id) MATCH FULL,
	ADD PRIMARY KEY(cts_id);

CREATE TABLE pais(
	pai_id serial,
	pai_descricao varchar(200),
	pai_sigla char(5)
);

	
CREATE TABLE enderecos_pessoas(
	eps_id serial,
	eps_lgr_id integer,
	eps_tipo char(1),
	eps_correspondencia char(1)
);
ALTER TABLE enderecos_pessoas
	ALTER COLUMN eps_id SET NOT NULL,
	ALTER COLUMN eps_lgr_id SET NOT NULL,
	ALTER COLUMN eps_tipo SET NOT NULL,
	ALTER COLUMN eps_tipo SET DEFAULT 'R',
	ALTER COLUMN eps_correspondencia SET NOT NULL,
	ALTER COLUMN eps_correspondencia SET DEFAULT 'N',
	ADD CONSTRAINT eps_tipo_chk CHECK (UPPER(eps_tipo) in ('R','C')), -- Residencial ou Comercial
	ADD CONSTRAINT eps_correspondencia_chk CHECK (UPPER(eps_correspondencia) in ('S','N')), -- Sim ou Não
	ADD PRIMARY KEY (eps_id);


CREATE TABLE pessoas_tem_enderecos(
	pte_id serial,
	pte_pss_id integer,
	pte_eps_id integer
);

ALTER TABLE pessoas_tem_enderecos
	ALTER COLUMN pte_id SET NOT NULL,
	ALTER COLUMN pte_pss_id SET NOT NULL,
	ALTER COLUMN pte_eps_id SET NOT NULL,
	ADD CONSTRAINT pte_pss_id_fk FOREIGN KEY (pte_pss_id) REFERENCES pessoas (pss_id) MATCH FULL,
	ADD CONSTRAINT pte_eps_id_fk FOREIGN KEY (pte_eps_id) REFERENCES enderecos_pessoas (eps_id) MATCH FULL,
	ADD PRIMARY KEY (pte_id);


CREATE TABLE enderecos_pessoas(
eps_id serial,
eps_pss_id integer,
eps_tipo char(1),
eps_correspondencia char(1),
eps_cep varchar(10),
eps_logradouro varchar(200),
eps_bairro varchar(50),
eps_cidade varchar(200),
eps_uf char(2)
);
ALTER TABLE enderecos_pessoas
	ALTER COLUMN eps_id SET NOT NULL,
	ALTER COLUMN eps_pss_id SET NOT NULL,
	ADD CONSTRAINT eps_tipo_chk CHECK (UPPER(eps_tipo) in ('R','C')), -- Residencial ou Comercial
	ADD CONSTRAINT eps_correspondencia_chk CHECK (UPPER(eps_correspondencia) in ('S','N')), -- Sim ou Não
	ALTER COLUMN eps_cep SET NOT NULL,
	ALTER COLUMN eps_logradouro SET NOT NULL,
	ALTER COLUMN eps_logradouro SET NOT NULL,
	ALTER COLUMN eps_bairro SET NOT NULL,
	ALTER COLUMN eps_cidade SET NOT NULL,
	ALTER COLUMN eps_uf SET NOT NULL,
	ADD CONSTRAINT eps_pss_id_fk FOREIGN KEY (eps_pss_id) REFERENCES pessoas (pss_id) MATCH FULL,
	ADD PRIMARY KEY (eps_id);


-- query para endereços
select 
	(lgr_descricao ||' '|| lgr_cep ||' '|| lgr_numero ||' '|| brr_descricao ||' '|| cde_descricao ||' '|| efs_descricao ||' '|| pai_descricao) as "ENDEREÇO COMPLETO",

	lgr_descricao as Logradouro,
	lgr_cep as CEP,
	brr_descricao as BAIRRO,
	cde_descricao as CIDADE,
	efs_descricao as ESTADO,
	pai_descricao as PAIS
 from logradouros lgr inner join bairros brr on lgr.lgr_brr_id = brr_id
inner join cidades cde on cde.cde_id = brr.brr_cde_id
inner join estados_federativos efs on efs.efs_id = cde.cde_efs_id
inner join pais pai on pai.pai_id = efs.efs_pai_id

DROP TABLE IF EXISTS convenios;
CREATE TABLE convenios(
	cns_id serial,
	cns_descricao varchar(35),
	cns_registro_ans varchar(20),
	cns_codigo_operadora varchar(20)
	
);
ALTER TABLE convenios
	ALTER COLUMN cns_id SET NOT NULL,
	ALTER COLUMN cns_descricao SET NOT NULL,
	ALTER COLUMN cns_registro_ans SET NOT NULL,
	ALTER COLUMN cns_codigo_operadora SET NOT NULL,
	ADD PRIMARY KEY(cns_id);
INSERT INTO convenios(cns_id,cns_descricao,cns_registro_ans,cns_codigo_operadora) values(1,'Particular','NÃO ESPECIFICADO','NÃO ESPECIFICADO');

CREATE TABLE ocupacoes
(
ocs_id serial,
ocs_descricao varchar(250),
ocs_cbo varchar(10)
);

ALTER TABLE ocupacoes
ALTER COLUMN ocs_id SET NOT NULL,
ALTER COLUMN ocs_descricao SET NOT NULL,
ALTER COLUMN ocs_cbo SET NOT NULL,
ADD CONSTRAINT unique_ocs_cbo_descricao UNIQUE(ocs_cbo,ocs_descricao), 
ADD PRIMARY KEY(ocs_id);


*/


        
      select * from agenda_pacientes

      select * from pacientes