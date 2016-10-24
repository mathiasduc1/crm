CREATE TABLE departement_video (id INT UNSIGNED AUTO_INCREMENT NOT NULL, departement_id INT UNSIGNED DEFAULT NULL, video_id INT DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, INDEX IDX_E965B6A9CCF9E01E (departement_id), INDEX IDX_E965B6A929C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE departement_video_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, video_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_2F7476C229C1004E (video_id), INDEX IDX_2F7476C22AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE departement_video ADD CONSTRAINT FK_E965B6A9CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id);
ALTER TABLE departement_video ADD CONSTRAINT FK_E965B6A929C1004E FOREIGN KEY (video_id) REFERENCES media__media (id);
ALTER TABLE departement_video_traduction ADD CONSTRAINT FK_2F7476C229C1004E FOREIGN KEY (video_id) REFERENCES departement_video (id);
ALTER TABLE departement_video_traduction ADD CONSTRAINT FK_2F7476C22AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);
