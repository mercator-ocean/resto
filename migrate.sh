#!/bin/bash

# restic -r swift:restic-bckp-restodb:/PRODUCTION --verbose restore latest --target .

psql -U resto -h 127.0.0.1 <<SQL
DROP SCHEMA resto CASCADE;
DROP SCHEMA public CASCADE;
CREATE SCHEMA IF NOT EXISTS resto;
CREATE SCHEMA IF NOT EXISTS public;
CREATE EXTENSION IF NOT EXISTS ltree WITH SCHEMA resto;
SQL

pg_restore -h 127.0.0.1 -U resto -d resto share/restodb.bak

psql -U resto -h 127.0.0.1 <<SQL
--- Migration 9.7 and 9.8
ALTER TABLE resto.catalog ADD COLUMN IF NOT EXISTS stac_url TEXT;
ALTER TABLE resto.group ADD CONSTRAINT idx_name_unique UNIQUE (name);
ALTER TABLE resto.catalog ADD COLUMN pinned BOOLEAN;
--- End Migration

UPDATE resto.catalog_feature SET catalogid = 'projects/'||catalogid WHERE catalogid IN (SELECT id FROM resto."catalog" c WHERE c.rtype IS NULL OR c.rtype = 'catalog' AND c.id NOT LIKE 'variable_families/%');
UPDATE resto.catalog_feature SET path=text2ltree(replace(catalogid, '/', '.'));

UPDATE resto.catalog SET id='projects/'||id WHERE rtype IS NULL OR rtype = 'catalog' AND id NOT LIKE 'variable_families/%'; 

ALTER TABLE resto.user ALTER COLUMN settings SET DEFAULT '{"createdCatalogIsPublic":true,"createdCollectionIsPublic":true,"createdItemIsPublic":true,"notifyOnAddFeature":true,"notifyOnNewFollower":true,"notifyOnLikeFeature":true,"notifyOnAddComment":true,"showBio":false,"showIdentity":false,"showTopics":false,"showEmail":false,"profileNeedReview":true}';
UPDATE resto.user SET settings = settings::jsonb || jsonb '{"showBio":false,"showIdentity":false,"showTopics":false,"showEmail":false}';

SQL
