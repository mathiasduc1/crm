ALTER TABLE hebergement ADD actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE logement CHANGE actif actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE zone_touristique ADD actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE secteur ADD actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE region ADD actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE departement ADD actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE profil ADD actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE domaine ADD actif TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE station ADD actif TINYINT(1) DEFAULT '1' NOT NULL;