CREATE TABLE profil_video (id INT UNSIGNED AUTO_INCREMENT NOT NULL, profil_id INT UNSIGNED DEFAULT NULL, video_id INT DEFAULT NULL, actif TINYINT(1) DEFAULT '1' NOT NULL, INDEX IDX_32CB0168275ED078 (profil_id), INDEX IDX_32CB016829C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE profil_video_traduction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, video_id INT UNSIGNED DEFAULT NULL, langue_id INT UNSIGNED DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_5468260629C1004E (video_id), INDEX IDX_546826062AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE profil_video ADD CONSTRAINT FK_32CB0168275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id);
ALTER TABLE profil_video ADD CONSTRAINT FK_32CB016829C1004E FOREIGN KEY (video_id) REFERENCES media__media (id);
ALTER TABLE profil_video_traduction ADD CONSTRAINT FK_5468260629C1004E FOREIGN KEY (video_id) REFERENCES profil_video (id);
ALTER TABLE profil_video_traduction ADD CONSTRAINT FK_546826062AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id);