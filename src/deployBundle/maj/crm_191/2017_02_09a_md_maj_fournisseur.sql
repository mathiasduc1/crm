ALTER TABLE fournisseur ADD station_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA3221BDB235 FOREIGN KEY (station_id) REFERENCES station (id);
CREATE INDEX IDX_369ECA3221BDB235 ON fournisseur (station_id);