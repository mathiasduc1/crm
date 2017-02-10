/*
CREATE TABLE commande (id INT UNSIGNED AUTO_INCREMENT NOT NULL, site_id INT UNSIGNED DEFAULT NULL, date_commande DATETIME NOT NULL, num_commande INT NOT NULL, type INT NOT NULL, UNIQUE INDEX UNIQ_6EEAA67DB7F9FFBB (num_commande), INDEX IDX_6EEAA67DF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_client (commande_id INT UNSIGNED NOT NULL, client_id INT UNSIGNED NOT NULL, INDEX IDX_C510FF8082EA2E54 (commande_id), INDEX IDX_C510FF8019EB6921 (client_id), PRIMARY KEY(commande_id, client_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_etat_dossier (id INT UNSIGNED AUTO_INCREMENT NOT NULL, commande_id INT UNSIGNED DEFAULT NULL, etat_dossier_id INT UNSIGNED DEFAULT NULL, date_heure DATETIME NOT NULL, INDEX IDX_6B7FF982EA2E54 (commande_id), INDEX IDX_6B7FF9C8503043 (etat_dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_ligne (id INT UNSIGNED AUTO_INCREMENT NOT NULL, commande_id INT UNSIGNED DEFAULT NULL, montant INT NOT NULL, type INT NOT NULL, INDEX IDX_6E98044082EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_ligne_frais_dossier (id INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_ligne_prestation_annexe (id INT UNSIGNED NOT NULL, commande_ligne_sejour_id INT UNSIGNED DEFAULT NULL, INDEX IDX_E26A93A3A16A9F82 (commande_ligne_sejour_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_ligne_remise (id INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_ligne_sejour (id INT UNSIGNED NOT NULL, logement_id INT UNSIGNED DEFAULT NULL, INDEX IDX_8AD31C2558ABF955 (logement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE commande_statut_dossier (id INT AUTO_INCREMENT NOT NULL, commande_id INT UNSIGNED DEFAULT NULL, statut_dossier_id INT DEFAULT NULL, DateHeure DATETIME NOT NULL, INDEX IDX_94A2F5A182EA2E54 (commande_id), INDEX IDX_94A2F5A114D7015F (statut_dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE etat_dossier (id INT UNSIGNED AUTO_INCREMENT NOT NULL, code_couleur VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE etat_dossier_traduction (id INT AUTO_INCREMENT NOT NULL, langue_id INT UNSIGNED DEFAULT NULL, etat_dossier_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_BFC380142AADBACD (langue_id), INDEX IDX_BFC38014C8503043 (etat_dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE groupe_statut_dossier (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE groupe_statut_dossier_traduction (id INT AUTO_INCREMENT NOT NULL, langue_id INT UNSIGNED DEFAULT NULL, groupe_statut_dossier_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_FD16142C2AADBACD (langue_id), INDEX IDX_FD16142CBF10D97D (groupe_statut_dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE sejour_nuite (id INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE sejour_periode (id INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE statut_dossier (id INT AUTO_INCREMENT NOT NULL, groupe_statut_dossier_id INT DEFAULT NULL, code_couleur VARCHAR(255) NOT NULL, INDEX IDX_51F9FA7BBF10D97D (groupe_statut_dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE statut_dossier_traduction (id INT AUTO_INCREMENT NOT NULL, langue_id INT UNSIGNED DEFAULT NULL, statut_dossier_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_D573BCAE2AADBACD (langue_id), INDEX IDX_D573BCAE14D7015F (statut_dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id);
ALTER TABLE commande_client ADD CONSTRAINT FK_C510FF8082EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE;
ALTER TABLE commande_client ADD CONSTRAINT FK_C510FF8019EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE;
ALTER TABLE commande_etat_dossier ADD CONSTRAINT FK_6B7FF982EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id);
ALTER TABLE commande_etat_dossier ADD CONSTRAINT FK_6B7FF9C8503043 FOREIGN KEY (etat_dossier_id) REFERENCES etat_dossier (id);
ALTER TABLE commande_ligne ADD CONSTRAINT FK_6E98044082EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id);
ALTER TABLE commande_ligne_frais_dossier ADD CONSTRAINT FK_76968F8EBF396750 FOREIGN KEY (id) REFERENCES commande_ligne (id) ON DELETE CASCADE;
ALTER TABLE commande_ligne_prestation_annexe ADD CONSTRAINT FK_E26A93A3A16A9F82 FOREIGN KEY (commande_ligne_sejour_id) REFERENCES commande_ligne_sejour (id);
ALTER TABLE commande_ligne_prestation_annexe ADD CONSTRAINT FK_E26A93A3BF396750 FOREIGN KEY (id) REFERENCES commande_ligne (id) ON DELETE CASCADE;
ALTER TABLE commande_ligne_remise ADD CONSTRAINT FK_D5CA9CABF396750 FOREIGN KEY (id) REFERENCES commande_ligne (id) ON DELETE CASCADE;
ALTER TABLE commande_ligne_sejour ADD CONSTRAINT FK_8AD31C2558ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id);
ALTER TABLE commande_ligne_sejour ADD CONSTRAINT FK_8AD31C25BF396750 FOREIGN KEY (id) REFERENCES commande_ligne (id) ON DELETE CASCADE;
ALTER TABLE commande_statut_dossier ADD CONSTRAINT FK_94A2F5A182EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id);
ALTER TABLE commande_statut_dossier ADD CONSTRAINT FK_94A2F5A114D7015F FOREIGN KEY (statut_dossier_id) REFERENCES statut_dossier (id);
ALTER TABLE etat_dossier_traduction ADD CONSTRAINT FK_BFC380142AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE etat_dossier_traduction ADD CONSTRAINT FK_BFC38014C8503043 FOREIGN KEY (etat_dossier_id) REFERENCES etat_dossier (id);
ALTER TABLE groupe_statut_dossier_traduction ADD CONSTRAINT FK_FD16142C2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE groupe_statut_dossier_traduction ADD CONSTRAINT FK_FD16142CBF10D97D FOREIGN KEY (groupe_statut_dossier_id) REFERENCES groupe_statut_dossier (id);
ALTER TABLE sejour_nuite ADD CONSTRAINT FK_656CD101BF396750 FOREIGN KEY (id) REFERENCES commande_ligne (id) ON DELETE CASCADE;
ALTER TABLE sejour_periode ADD CONSTRAINT FK_19BCD6EBBF396750 FOREIGN KEY (id) REFERENCES commande_ligne (id) ON DELETE CASCADE;
ALTER TABLE statut_dossier ADD CONSTRAINT FK_51F9FA7BBF10D97D FOREIGN KEY (groupe_statut_dossier_id) REFERENCES groupe_statut_dossier (id);
ALTER TABLE statut_dossier_traduction ADD CONSTRAINT FK_D573BCAE2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
ALTER TABLE statut_dossier_traduction ADD CONSTRAINT FK_D573BCAE14D7015F FOREIGN KEY (statut_dossier_id) REFERENCES statut_dossier (id);
*/

/*ALTER TABLE sejour_periode ADD periode_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE sejour_periode ADD CONSTRAINT FK_19BCD6EBF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id);
CREATE INDEX IDX_19BCD6EBF384C1CF ON sejour_periode (periode_id);*/

/*
ALTER TABLE commande_ligne_sejour ADD nb_participants INT NOT NULL;
*/

/*
ALTER TABLE commande_ligne_sejour CHANGE nb_participants nb_participants INT UNSIGNED NOT NULL;
*/

/*
ALTER TABLE commande_ligne ADD prix_catalogue INT UNSIGNED DEFAULT 0 NOT NULL, ADD prix_public INT UNSIGNED DEFAULT 0 NOT NULL, ADD prix_achat INT UNSIGNED DEFAULT 0 NOT NULL, ADD quantite INT UNSIGNED DEFAULT 0 NOT NULL, DROP montant;
*/

/*
ALTER TABLE commande_ligne ADD date_achat DATETIME NOT NULL;
*/

/*
ALTER TABLE commande_ligne CHANGE quantite quantite INT UNSIGNED DEFAULT 1 NOT NULL;
*/

/*
ALTER TABLE commande_ligne_prestation_annexe ADD fournisseur_prestation_annexe_param_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE commande_ligne_prestation_annexe ADD CONSTRAINT FK_E26A93A3F144C3CB FOREIGN KEY (fournisseur_prestation_annexe_param_id) REFERENCES fournisseur_prestation_annexe_param (id);
CREATE INDEX IDX_E26A93A3F144C3CB ON commande_ligne_prestation_annexe (fournisseur_prestation_annexe_param_id);
*/

ALTER TABLE commande_ligne_prestation_annexe ADD date_debut DATETIME NOT NULL, ADD date_fin DATETIME NOT NULL;
