#!/bin/bash

psql -U resto -h 127.0.0.1 <<SQL
DROP SCHEMA resto;
CREATE SCHEMA IF NOT EXISTS resto;
CREATE EXTENSION IF NOT EXISTS ltree WITH SCHEMA resto;
SQL

pg_restore -h 127.0.0.1 -U resto -d resto ~/dump-resto-202601301123.sql

psql -U resto -h 127.0.0.1 <<SQL
--- Migration 9.7 and 9.8
ALTER TABLE resto.catalog ADD COLUMN IF NOT EXISTS stac_url TEXT;
ALTER TABLE resto.group ADD CONSTRAINT idx_name_unique UNIQUE (name);
ALTER TABLE resto.catalog ADD COLUMN pinned BOOLEAN;
--- End Migration

UPDATE resto.catalog SET id='project/'||id WHERE rtype IS NULL OR rtype = 'catalog' AND id NOT LIKE 'variable_families/%'; 
SQL
