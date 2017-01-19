/*CREATE TABLE saison (id INT UNSIGNED AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, en_cours TINYINT(1) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE saison_fournisseur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, agent_saisie_id INT UNSIGNED DEFAULT NULL, agent_prod_id INT UNSIGNED DEFAULT NULL, agent_ma_jprod_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, saison_id INT UNSIGNED DEFAULT NULL, contrat SMALLINT UNSIGNED DEFAULT NULL, stock SMALLINT UNSIGNED DEFAULT NULL, flux SMALLINT UNSIGNED DEFAULT NULL, valide_options SMALLINT UNSIGNED DEFAULT NULL, earlybooking SMALLINT UNSIGNED DEFAULT NULL, condition_earlybooking LONGTEXT NOT NULL, fiche_techniques SMALLINT UNSIGNED DEFAULT 0, tarif_techniques SMALLINT UNSIGNED DEFAULT 0, photos_techniques SMALLINT UNSIGNED DEFAULT 0 NOT NULL, INDEX IDX_7E077A345670B5A9 (agent_saisie_id), INDEX IDX_7E077A34D39B5DDF (agent_prod_id), INDEX IDX_7E077A342E488C02 (agent_ma_jprod_id), INDEX IDX_7E077A34670C757F (fournisseur_id), INDEX IDX_7E077A34F965414C (saison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A345670B5A9 FOREIGN KEY (agent_saisie_id) REFERENCES utilisateur (id);
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A34D39B5DDF FOREIGN KEY (agent_prod_id) REFERENCES utilisateur (id);
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A342E488C02 FOREIGN KEY (agent_ma_jprod_id) REFERENCES utilisateur (id);
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A34670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A34F965414C FOREIGN KEY (saison_id) REFERENCES saison (id);*/
/*
ALTER TABLE saison_fournisseur CHANGE condition_earlybooking condition_earlybooking LONGTEXT DEFAULT NULL;*/
