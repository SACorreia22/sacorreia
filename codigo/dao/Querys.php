<?php

class Querys
{
    // ############################################################
    // ##........................CREATE..........................##
    // ############################################################
    const CREATE_TABLE = <<<SQL
CREATE TABLE IF NOT EXISTS dashboard.tb_artifact
(
  artifact_id      INTEGER,
  tracker_id       INTEGER,
  group_id         INTEGER,
  submitted_by     VARCHAR(255),
  submitted_on     TIMESTAMP,
  last_update_date TIMESTAMP,
  data_insercao    TIMESTAMP DEFAULT now(),
  type             VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS dashboard.tb_bind
(
  bind_id          INTEGER NOT NULL
    CONSTRAINT tb_bind_pkey
    PRIMARY KEY,
  field_id         INTEGER,
  bind_value_id    INTEGER,
  bind_value_label VARCHAR(255),
  bind_value       VARCHAR(255),
  data_insercao    TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS dashboard.tb_configuracao
(
  group_id        INTEGER NOT NULL
    CONSTRAINT tb_configuracao_pkey
    PRIMARY KEY,
  unix_group_name VARCHAR(255),
  diretorio       VARCHAR(255),
  caminho_mer     VARCHAR(512)
);

CREATE TABLE IF NOT EXISTS dashboard.tb_cross_references
(
  artifact_id   INTEGER,
  ref           VARCHAR(255),
  url           VARCHAR(255),
  data_insercao TIMESTAMP DEFAULT now(),
  type          VARCHAR(20),
  artifact_ref  INTEGER
);

CREATE TABLE IF NOT EXISTS dashboard.tb_field
(
  field_id      SERIAL NOT NULL
    CONSTRAINT tb_field_pkey
    PRIMARY KEY,
  artifact_id   INTEGER,
  field_name    VARCHAR(255),
  field_label   VARCHAR(255),
  field_value   VARCHAR,
  data_insercao TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS dashboard.tb_group
(
  group_id        INTEGER,
  group_name      VARCHAR(255),
  unix_group_name VARCHAR(255),
  description     VARCHAR(255),
  data_insercao   TIMESTAMP DEFAULT now(),
  updated_at      TIMESTAMP,
  created_at      TIMESTAMP
);

CREATE TABLE IF NOT EXISTS dashboard.tb_tabelas
(
  id_tabela SERIAL NOT NULL CONSTRAINT pk_tb_tabelas_id_tabela PRIMARY KEY,
  schema VARCHAR(255) NOT NULL,
  tabela VARCHAR(255) NOT NULL,
  comentario VARCHAR,
  sugestao VARCHAR
);

CREATE TABLE IF NOT EXISTS dashboard.tb_tracker
(
  tracker_id    INTEGER,
  group_id      INTEGER,
  name          VARCHAR(255),
  description   VARCHAR,
  item_name     VARCHAR(255),
  data_insercao TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS dashboard.tb_usuario
(
  nome          VARCHAR(50),
  usuario       VARCHAR(50),
  senha         VARCHAR(50),
  perfil        INTEGER,
  tuleap_user   VARCHAR(50),
  tuleap_pass   VARCHAR(50),
  usuario_id    INTEGER NOT NULL
    CONSTRAINT tb_usuario_pkey
    PRIMARY KEY,
  ativo         CHAR      DEFAULT 'S' :: BPCHAR,
  data_insercao TIMESTAMP DEFAULT now()
);
SQL;

    // ############################################################
    // ##........................USUARIO.........................##
    // ############################################################
    const SELECT_LOGIN = 'SELECT * FROM dashboard.tb_usuario WHERE usuario = $1 AND senha = $2 AND ativo = \'S\'';
    const SELECT_USUARIO_BY_USUARIO_ATIVO = 'SELECT * FROM dashboard.tb_usuario WHERE usuario = $1 AND ativo = \'S\'';
    const SELECT_USUARIO = 'SELECT * FROM dashboard.tb_usuario WHERE 1 = 1 ';
    const SELECT_USUARIO_BY_ID = 'SELECT * FROM dashboard.tb_usuario WHERE usuario_id = $1 ';

    const INSERT_USUARIO = 'INSERT INTO dashboard.tb_usuario (nome,usuario,senha,perfil) VALUES ($1,$2,$3,$4)';
    const INSERT_USUARIO_TULEAP = 'INSERT INTO dashboard.tb_usuario (usuario_id,nome,usuario,senha,perfil,tuleap_user) VALUES ($1,$2,$3,$4,$5,$6)';

    const UPDATE_USUARIO = 'UPDATE dashboard.tb_usuario SET nome = $1, usuario = $2, tuleap_user = $3, tuleap_pass = $4 WHERE usuario_id = $5';
    const UPDATE_USUARIO_PERFIL = 'UPDATE dashboard.tb_usuario SET nome = $1, usuario = $2, perfil = $3 WHERE usuario_id = $4';
    const UPDATE_USUARIO_ATIVO = 'UPDATE dashboard.tb_usuario SET ativo = $1 WHERE usuario_id = $2';
    const UPDATE_USUARIO_SENHA = 'UPDATE dashboard.tb_usuario SET nome = $1, usuario = $2, senha = $3, tuleap_user = $4, tuleap_pass = $5 WHERE usuario_id = $6';
    const UPDATE_USUARIO_RESET_SENHA = 'UPDATE dashboard.tb_usuario SET senha = $1 WHERE usuario_id = $2';

    // ############################################################
    // ##.........................GROUP..........................##
    // ############################################################
    const SELECT_PROJETO = 'SELECT group_id, unix_group_name, group_name FROM dashboard.tb_group ORDER BY 3, 2';
    const SELECT_PROJETO_BY_ID = 'SELECT * FROM dashboard.tb_group WHERE group_id = $1';
    const SELECT_PROJETO_CONF_BY_ID = 'SELECT g.group_id, g.group_name, g.description, g.data_insercao, COALESCE (c.unix_group_name, g.unix_group_name) unix_group_name, diretorio, COALESCE(caminho_mer,\'mer_\' || COALESCE (c.unix_group_name, g.unix_group_name)) AS caminho_mer FROM dashboard.tb_group g LEFT JOIN dashboard.tb_configuracao c USING (group_id) WHERE g.group_id = $1';
    const SELECT_PROJETO_BY_ARTIFACT_ID = 'SELECT g.* FROM dashboard.tb_group g JOIN dashboard.tb_artifact a USING (group_id) WHERE a.artifact_id = $1';

    const INSERT_PROJETO = 'INSERT INTO dashboard.tb_group (group_id, group_name, unix_group_name, description) VALUES ($1,$2,$3,$4)';

    const UPDATE_PROJETO = 'UPDATE dashboard.tb_group SET group_name = $1, unix_group_name = $2, description = $3 WHERE group_id = $4';

    // ############################################################
    // ##........................TRACKER.........................##
    // ############################################################
    const SELECT_TRACKER_BY_ID = 'SELECT * FROM dashboard.tb_tracker WHERE tracker_id = $1';

    const INSERT_TRACKER = 'INSERT INTO dashboard.tb_tracker (tracker_id, group_id, name, description, item_name) VALUES ($1,$2,$3,$4,$5)';

    const UPDATE_TRACKER = 'UPDATE dashboard.tb_tracker SET group_id = $1, name = $2, description = $3, item_name = $4 WHERE tracker_id = $5';

    // ############################################################
    // ##.......................ARTIFACT.........................##
    // ############################################################
    const SELECT_ARTIFACT_BY_ID = 'SELECT * FROM dashboard.tb_artifact WHERE artifact_id = $1';
    const SELECT_RELEASE_BY_ARTIFACT_ID = 'SELECT artifact_ref AS rel FROM dashboard.tb_artifact a JOIN dashboard.tb_cross_references c USING (artifact_id) WHERE c.artifact_id = $1 AND c.type = \'release\'';

    const INSERT_ARTIFACT = 'INSERT INTO dashboard.tb_artifact (artifact_id, tracker_id, group_id, submitted_by, submitted_on, last_update_date, type) VALUES ($1,$2,$3,$4,$5,$6,$7)';
    const DELETE_ARTIFACT = 'DELETE FROM dashboard.tb_artifact WHERE artifact_id = $1';
    const UPDATE_ARTIFACT = 'UPDATE dashboard.tb_artifact SET tracker_id = $1, group_id = $2, submitted_by = $3, submitted_on = $4, last_update_date = $5, type = $6 WHERE artifact_id = $7';

    // ############################################################
    // ##.....................CROSS_REFERENCE....................##
    // ############################################################
    const SELECT_CROSS_REFERENCE_BY_ALL = 'SELECT * FROM dashboard.tb_cross_references WHERE artifact_id = $1 AND ref = $2 AND url = $3';

    const DELETE_CROSS_REFERENCE = 'DELETE FROM dashboard.tb_cross_references WHERE artifact_id = $1';

    const INSERT_CROSS_REFERENCE = 'INSERT INTO dashboard.tb_cross_references (artifact_id, ref, url, type, artifact_ref) VALUES ($1,$2,$3,$4,$5)';

    // ############################################################
    // ##.........................FIELD..........................##
    // ############################################################
    const SELECT_FIELD_BY_ARTIF_ID_FIELD_NAME = 'SELECT * FROM dashboard.tb_field WHERE artifact_id = $1 AND field_name = $2';

    const DELETE_FIELD = 'DELETE FROM dashboard.tb_field WHERE artifact_id = $1';

    const INSERT_FIELD = 'INSERT INTO dashboard.tb_field (artifact_id, field_name, field_label, field_value) VALUES ($1,$2,$3,$4)';

    const UPDATE_FIELD = 'UPDATE dashboard.tb_field SET field_value = $1 WHERE field_id = $2';

    // ############################################################
    // ##.....................CONFIGURACAO.......................##
    // ############################################################
    const SELECT_CONFIGURACAO_BY_GROUP_ID = 'SELECT * FROM dashboard.tb_configuracao WHERE group_id = $1';

    const INSERT_CONFIGURACAO = 'INSERT INTO dashboard.tb_configuracao (group_id, unix_group_name, diretorio, caminho_mer) VALUES ($1, $2, $3, $4)';

    const UPDATE_CONFIGURACAO = 'UPDATE dashboard.tb_configuracao SET unix_group_name = $1, diretorio = $2, caminho_mer = $3 WHERE group_id = $4';

    // ############################################################
    // ##.........................TABELA.........................##
    // ############################################################
    const SELECT_TABELA_BY_SCHEMA_TABELA = "SELECT schema || '.' || tabela AS nome, coalesce(comentario, sugestao) AS comentario, id_tabela FROM dashboard.tb_tabelas WHERE schema = $1 AND tabela = $2 ORDER BY 1";

    const INSERT_TABELA_SUGESTAO = 'INSERT INTO dashboard.tb_tabelas (schema, tabela, sugestao) VALUES ($1, $2, $3)';
    const UPDATE_TABELA_SUGESTAO = 'UPDATE dashboard.tb_tabelas SET sugestao = $3 WHERE schema = $1 AND tabela = $2';

    // ############################################################
    // ##.......................DASHBOARD........................##
    // ############################################################
    const SELECT_DASHBOARD = <<<SQL
SELECT
    group_name
    , group_id
    , type
    , SUM (qtd)                 AS qtd
    , CASE
      WHEN type = 'release'
          THEN '-'
      ELSE SUM (qtd_aberto)::VARCHAR END AS qtd_aberto
FROM (
         SELECT
             g.group_name
             , g.group_id
             , a.type
             , COUNT (DISTINCT a.artifact_id) AS qtd
             , sum (CASE
                    WHEN (
                        f.field_name LIKE '%status%'
                        AND lower (f.field_value) NOT IN ('done', 'close')
                    ) THEN 1
                    ELSE 0
                    END)           AS qtd_aberto
         FROM dashboard.tb_group g
             JOIN dashboard.tb_tracker t USING (group_id)
             JOIN dashboard.tb_artifact a USING (tracker_id, group_id)
             JOIN dashboard.tb_field f USING (artifact_id)
         GROUP BY
             g.group_name
             ,g.group_id
             ,a.type
         UNION ALL
         SELECT
             g.group_name
             , g.group_id
             , type
             , 0
             , CASE
               WHEN type = 'release'
                   THEN 0
               ELSE 0 END AS qtd_aberto
         FROM dashboard.tb_group g, (
                                        SELECT DISTINCT type
                                        FROM dashboard.tb_artifact) a
     ) a
GROUP BY
    group_name
    ,group_id
    ,type
ORDER BY group_name
    ,group_id
    ,type
SQL;

    const SELECT_FIELDS_HTML_BY_ARTIFACT = <<<SQL
    SELECT '<b>' || field_label || ':</b> ' AS label,  coalesce(field_value, '') || '<br>' AS value
FROM dashboard.tb_field f
WHERE artifact_id = $1
ORDER BY 1
SQL;

    const SELECT_SPRINT_RELEASE_BY_GROUP_ID = <<<SQL
SELECT
    a.artifact_id  AS sprint
  , c.artifact_ref AS rel
FROM dashboard.tb_group g
  JOIN dashboard.tb_tracker t USING (group_id)
  JOIN dashboard.tb_artifact a USING (tracker_id, group_id)
  JOIN dashboard.tb_cross_references c USING (artifact_id)
WHERE t.item_name = 'sprint'
      AND g.group_id = $1
      AND c.type = 'release'
ORDER BY 2, 1
SQL;

    const SELECT_VALUES_BY_SPRINT = <<<SQL
SELECT
    f.artifact_id
    , f.field_name
    , f.field_value
FROM dashboard.tb_field f
WHERE f.artifact_id IN (
    SELECT c.artifact_ref AS historia
    FROM dashboard.tb_artifact a
        JOIN dashboard.tb_cross_references c USING (artifact_id)
    WHERE c.artifact_id = $1 AND c.type = 'story') AND
      f.field_name IN ('como_demonstrar', 'observao', 'in_order_to_1', 'acceptance_criteria_1', 'i_want_to')
SQL;

    const SELECT_STORY_BY_SPRINT = <<<SQL
SELECT CAST(artifact_ref AS DECIMAL) AS historia
FROM dashboard.tb_artifact a
    JOIN dashboard.tb_cross_references c USING (artifact_id)
WHERE c.artifact_id = $1
      AND c.type = 'story'
SQL;


    const SELECT_TREE_BY_GROUPID = <<<SQL
WITH refcross AS (SELECT
                      a.*
                      , c.artifact_ref
                      , c.type AS type_ref
                  FROM dashboard.tb_artifact a
                      JOIN dashboard.tb_cross_references c USING (artifact_id)),
        status AS (
        SELECT
            LOWER (field_value) AS status
            , a.artifact_id
        FROM dashboard.tb_field f
            JOIN dashboard.tb_artifact a USING (artifact_id)
        WHERE
            field_name LIKE '%status%'),
        esforco_sp AS (
        SELECT
              tcr.artifact_ref                  sprint
            , sum (CASE
                   WHEN field_value IS NULL OR field_value = '' THEN 0
                   ELSE field_value :: INT END) qtd_esforco
        FROM dashboard.tb_field tf
            JOIN dashboard.tb_cross_references tcr USING (artifact_id)
        WHERE
            tf.field_name = 'estimated_effort_points'
            AND tcr.type = 'sprint'
        GROUP BY
            tcr.artifact_ref
    ),
        esforco_st AS (
        SELECT
              tf.artifact_id                    story
            , SUM (CASE
                   WHEN field_value IS NULL OR field_value = '' THEN 0
                   ELSE field_value :: INT END) qtd_esforco
        FROM dashboard.tb_field tf
        WHERE
            tf.field_name = 'estimated_effort_points'
        GROUP BY
            tf.artifact_id
    )
SELECT
    rel
    , sprint
    , (
          SELECT e.qtd_esforco
          FROM esforco_sp e
          WHERE
              e.sprint = a.sprint) esforco_sp
    , status_sprint
    , story
    , (
          SELECT e.qtd_esforco
          FROM esforco_st e
          WHERE
              e.story = a.story)   esforco_st
    , status_story
    , task
    , status_task
FROM
    (
        SELECT
            rel
            , sprint
            , (
                  SELECT status
                  FROM status S
                  WHERE
                      S.artifact_id = sprint)          AS status_sprint
            , story
            , (
                  SELECT status
                  FROM status S
                  WHERE
                      S.artifact_id = story)           AS status_story
            , tk.artifact_ref                          AS task
            , (
                  SELECT status
                  FROM status S
                  WHERE
                      S.artifact_id = tk.artifact_ref) AS status_task
        FROM (
                 SELECT
                     rel
                     , sprint
                     , st.artifact_ref AS story
                 FROM (
                          SELECT
                                sp.artifact_id  AS rel
                              , sp.artifact_ref AS sprint
                          FROM refcross sp
                          WHERE
                              sp.type = 'release'
                              AND sp.type_ref = 'sprint'
                              AND sp.group_id = $1
                      ) sp
                     LEFT JOIN refcross st
                         ON sp.sprint = st.artifact_id AND st.type = 'sprint' AND st.type_ref = 'story'
             ) st
            LEFT JOIN refcross tk ON st.story = tk.artifact_id AND tk.type = 'story' AND tk.type_ref = 'task') a
ORDER BY
    rel DESC,
    sprint,
    story,
    task
SQL;

    const SELECT_UNRELATED_BY_GROUPID = <<<SQL
WITH refcross AS (
    SELECT
      a.*,
      c.artifact_ref,
      c.type AS type_ref
    FROM dashboard.tb_artifact a
      LEFT JOIN dashboard.tb_cross_references c USING (artifact_id)
),
    status AS (
      SELECT
        LOWER(field_value) AS status,
        a.artifact_id
      FROM dashboard.tb_field f
        JOIN dashboard.tb_artifact a USING (artifact_id)
      WHERE field_name LIKE '%status%'
  ),
    relacionados AS (
      SELECT
        rel,
        sprint,
        story,
        tk.artifact_ref AS task
      FROM (
             SELECT
               rel,
               sprint,
               st.artifact_ref AS story
             FROM (
                    SELECT
                      sp.artifact_id  AS rel,
                      sp.artifact_ref AS sprint
                    FROM refcross sp
                    WHERE sp.type = 'release'
                          AND sp.type_ref = 'sprint'
                          AND sp.group_id = $1
                  ) sp
               LEFT JOIN refcross st
                 ON sp.sprint = st.artifact_id AND st.type = 'sprint' AND st.type_ref = 'story'
           ) st
        LEFT JOIN refcross tk ON st.story = tk.artifact_id AND tk.type = 'story' AND tk.type_ref = 'task'
  ),
    todos_ids AS (
      SELECT artifact_id
      FROM dashboard.tb_artifact
      WHERE group_id = $1
  ),
    relacionados_ids AS (
    SELECT rel
    FROM relacionados
    UNION ALL
    SELECT sprint
    FROM relacionados
    UNION ALL
    SELECT story
    FROM relacionados
    UNION ALL
    SELECT task
    FROM relacionados
  ),
    restantes_id AS (
    SELECT *
    FROM todos_ids
    EXCEPT
    SELECT *
    FROM relacionados_ids
  )
SELECT
  MAX(rel)          rel,
  story,
  MAX(status_story) status_story,
  MAX(task)         task,
  MAX(status_task)  status_task
FROM (
       SELECT
         rel,
         story,
         (
           SELECT status
           FROM status s
           WHERE s.artifact_id = story) AS status_story,
         task,
         (
           SELECT status
           FROM status s
           WHERE s.artifact_id = task)  AS status_task

       FROM (
              SELECT
                CASE WHEN a.type = 'release'
                  THEN sp.artifact_ref END AS rel,
                CASE WHEN sp.type = 'story'
                  THEN r.artifact_id END   AS story,
                CASE WHEN a.type = 'task'
                  THEN sp.artifact_ref END AS task
              FROM restantes_id r
                LEFT JOIN refcross sp ON r.artifact_id = sp.artifact_id
                LEFT JOIN dashboard.tb_artifact a ON (sp.artifact_ref = a.artifact_id)
              WHERE sp.type = 'story'
                    OR sp.type IS NULL
            ) a
     ) b
GROUP BY story
ORDER BY rel DESC, story, task
SQL;


}