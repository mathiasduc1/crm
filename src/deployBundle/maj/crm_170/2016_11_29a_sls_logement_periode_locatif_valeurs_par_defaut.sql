ALTER TABLE logement_periode_locatif CHANGE prix_public prix_public NUMERIC(10, 2) DEFAULT '0' NOT NULL, CHANGE stock stock INT DEFAULT 0 NOT NULL, CHANGE prix_fournisseur prix_fournisseur NUMERIC(10, 2) DEFAULT '0' NOT NULL, CHANGE prix_achat prix_achat NUMERIC(10, 2) DEFAULT '0' NOT NULL;