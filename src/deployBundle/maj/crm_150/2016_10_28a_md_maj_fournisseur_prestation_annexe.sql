CREATE TABLE fournisseur_prestation_annexe_param (id INT UNSIGNED AUTO_INCREMENT NOT NULL, capacite_id INT UNSIGNED DEFAULT NULL, duree_sejour_id INT UNSIGNED DEFAULT NULL, fournisseur_prestation_annexe_id INT UNSIGNED DEFAULT NULL, type INT UNSIGNED NOT NULL, mode_affectation INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_AC601C267C79189D (capacite_id), UNIQUE INDEX UNIQ_AC601C269CDD2F46 (duree_sejour_id), INDEX IDX_AC601C26DF2F2EF6 (fournisseur_prestation_annexe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE fournisseur_prestation_annexe_param_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, param_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle_param VARCHAR(255) NOT NULL, libelle_fournisseur_prestation_annexe_param VARCHAR(255) NOT NULL, INDEX IDX_5EFDA4455647C863 (param_id), INDEX IDX_5EFDA4452AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur_prestation_annexe_param ADD CONSTRAINT FK_AC601C267C79189D FOREIGN KEY (capacite_id) REFERENCES fournisseur_prestation_annexe_capacite (id);
ALTER TABLE fournisseur_prestation_annexe_param ADD CONSTRAINT FK_AC601C269CDD2F46 FOREIGN KEY (duree_sejour_id) REFERENCES fournisseur_prestation_annexe_duree_sejour (id);
ALTER TABLE fournisseur_prestation_annexe_param ADD CONSTRAINT FK_AC601C26DF2F2EF6 FOREIGN KEY (fournisseur_prestation_annexe_id) REFERENCES fournisseur_prestation_annexe (id);
ALTER TABLE fournisseur_prestation_annexe_param_traduction ADD CONSTRAINT FK_5EFDA4455647C863 FOREIGN KEY (param_id) REFERENCES fournisseur_prestation_annexe_param (id);
ALTER TABLE fournisseur_prestation_annexe_param_traduction ADD CONSTRAINT FK_5EFDA4452AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);

ALTER TABLE fournisseur_prestation_annexe DROP FOREIGN KEY FK_9AB97BB37C79189D;
ALTER TABLE fournisseur_prestation_annexe DROP FOREIGN KEY FK_9AB97BB39CDD2F46;
DROP INDEX UNIQ_9AB97BB37C79189D ON fournisseur_prestation_annexe;
DROP INDEX UNIQ_9AB97BB39CDD2F46 ON fournisseur_prestation_annexe;
ALTER TABLE fournisseur_prestation_annexe DROP capacite_id, DROP duree_sejour_id, DROP type, DROP mode_affectation;
ALTER TABLE prestation_annexe_tarif DROP FOREIGN KEY FK_D1663A6833AD7BEF;
DROP INDEX IDX_D1663A6833AD7BEF ON prestation_annexe_tarif;
ALTER TABLE prestation_annexe_tarif CHANGE prestation_annexe_id param_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe_tarif ADD CONSTRAINT FK_D1663A685647C863 FOREIGN KEY (param_id) REFERENCES fournisseur_prestation_annexe_param (id);
CREATE INDEX IDX_D1663A685647C863 ON prestation_annexe_tarif (param_id);
ALTER TABLE prestation_annexe_fournisseur DROP FOREIGN KEY FK_F8A26796DF2F2EF6;
DROP INDEX IDX_F8A26796DF2F2EF6 ON prestation_annexe_fournisseur;
ALTER TABLE prestation_annexe_fournisseur CHANGE fournisseur_prestation_annexe_id param_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe_fournisseur ADD CONSTRAINT FK_F8A267965647C863 FOREIGN KEY (param_id) REFERENCES fournisseur_prestation_annexe_param (id);
CREATE INDEX IDX_F8A267965647C863 ON prestation_annexe_fournisseur (param_id);
ALTER TABLE prestation_annexe_hebergement DROP FOREIGN KEY FK_866E7038DF2F2EF6;
DROP INDEX IDX_866E7038DF2F2EF6 ON prestation_annexe_hebergement;
ALTER TABLE prestation_annexe_hebergement CHANGE fournisseur_prestation_annexe_id param_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe_hebergement ADD CONSTRAINT FK_866E70385647C863 FOREIGN KEY (param_id) REFERENCES fournisseur_prestation_annexe_param (id);
CREATE INDEX IDX_866E70385647C863 ON prestation_annexe_hebergement (param_id);
ALTER TABLE prestation_annexe_logement DROP FOREIGN KEY FK_647C0B63DF2F2EF6;
DROP INDEX IDX_647C0B63DF2F2EF6 ON prestation_annexe_logement;
ALTER TABLE prestation_annexe_logement CHANGE fournisseur_prestation_annexe_id param_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe_logement ADD CONSTRAINT FK_647C0B635647C863 FOREIGN KEY (param_id) REFERENCES fournisseur_prestation_annexe_param (id);
CREATE INDEX IDX_647C0B635647C863 ON prestation_annexe_logement (param_id);
ALTER TABLE prestation_annexe_station DROP FOREIGN KEY FK_7A4D4838DF2F2EF6;
DROP INDEX IDX_7A4D4838DF2F2EF6 ON prestation_annexe_station;
ALTER TABLE prestation_annexe_station CHANGE fournisseur_prestation_annexe_id param_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE prestation_annexe_station ADD CONSTRAINT FK_7A4D48385647C863 FOREIGN KEY (param_id) REFERENCES fournisseur_prestation_annexe_param (id);
CREATE INDEX IDX_7A4D48385647C863 ON prestation_annexe_station (param_id);