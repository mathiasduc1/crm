CREATE TABLE saison (id INT UNSIGNED AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, en_cours TINYINT(1) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE saison_fournisseur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, agent_ma_jprod_id INT UNSIGNED DEFAULT NULL, agent_ma_jsaisie_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, saison_id INT UNSIGNED DEFAULT NULL, contrat SMALLINT UNSIGNED DEFAULT NULL, stock SMALLINT UNSIGNED DEFAULT NULL, flux SMALLINT UNSIGNED DEFAULT NULL, valide_options SMALLINT UNSIGNED DEFAULT NULL, earlybooking SMALLINT UNSIGNED DEFAULT NULL, condition_earlybooking LONGTEXT DEFAULT NULL, fiche_techniques SMALLINT UNSIGNED DEFAULT 0, tarif_techniques SMALLINT UNSIGNED DEFAULT 0, photos_techniques SMALLINT UNSIGNED DEFAULT 0 NOT NULL, INDEX IDX_7E077A342E488C02 (agent_ma_jprod_id), INDEX IDX_7E077A349332116A (agent_ma_jsaisie_id), INDEX IDX_7E077A34670C757F (fournisseur_id), INDEX IDX_7E077A34F965414C (saison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE saison_hebergement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, saison_id INT UNSIGNED DEFAULT NULL, hebergement_id INT UNSIGNED DEFAULT NULL, valide_fiche TINYINT(1) NOT NULL, valide_tarif TINYINT(1) NOT NULL, valide_photo TINYINT(1) NOT NULL, actif TINYINT(1) NOT NULL, INDEX IDX_CB6D9AF965414C (saison_id), INDEX IDX_CB6D9A23BB0F66 (hebergement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A342E488C02 FOREIGN KEY (agent_ma_jprod_id) REFERENCES utilisateur (id);
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A349332116A FOREIGN KEY (agent_ma_jsaisie_id) REFERENCES utilisateur (id);
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A34670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE saison_fournisseur ADD CONSTRAINT FK_7E077A34F965414C FOREIGN KEY (saison_id) REFERENCES saison (id);
ALTER TABLE saison_hebergement ADD CONSTRAINT FK_CB6D9AF965414C FOREIGN KEY (saison_id) REFERENCES saison (id);
ALTER TABLE saison_hebergement ADD CONSTRAINT FK_CB6D9A23BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id);