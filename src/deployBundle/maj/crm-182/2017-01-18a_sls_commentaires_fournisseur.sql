CREATE TABLE fournisseur_commentaire (id INT UNSIGNED AUTO_INCREMENT NOT NULL, auteur_id INT UNSIGNED DEFAULT NULL, fournisseur_id INT UNSIGNED DEFAULT NULL, date_heure_creation DATETIME NOT NULL, date_heure_modification DATETIME NOT NULL, validation_moderateur TINYINT(1) NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_10072F60BB6FE6 (auteur_id), INDEX IDX_10072F670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE auteur (id INT UNSIGNED AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE utilisateur_auteur (id INT UNSIGNED NOT NULL, utilisateur_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_64F78BCBFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE fournisseur_commentaire ADD CONSTRAINT FK_10072F60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id);
ALTER TABLE fournisseur_commentaire ADD CONSTRAINT FK_10072F670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id);
ALTER TABLE utilisateur_auteur ADD CONSTRAINT FK_64F78BCBFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id);
ALTER TABLE utilisateur_auteur ADD CONSTRAINT FK_64F78BCBBF396750 FOREIGN KEY (id) REFERENCES auteur (id) ON DELETE CASCADE;