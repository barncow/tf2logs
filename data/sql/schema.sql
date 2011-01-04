CREATE TABLE event (id BIGINT AUTO_INCREMENT, log_id BIGINT NOT NULL, event_type VARCHAR(10) NOT NULL, elapsed_seconds INT NOT NULL, attacker BIGINT, attacker_coord VARCHAR(17), victim BIGINT, victim_coord VARCHAR(17), assist BIGINT, assist_coord VARCHAR(17), player_id BIGINT, text VARCHAR(255), INDEX log_id_idx (log_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE log (id BIGINT AUTO_INCREMENT, name VARCHAR(100) NOT NULL, redscore INT DEFAULT 0 NOT NULL, bluescore INT DEFAULT 0 NOT NULL, elapsed_time BIGINT DEFAULT 0 NOT NULL, map_name VARCHAR(50), error_log_name VARCHAR(50), error_exception TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE log_file (log_id BIGINT, log_data MEDIUMTEXT NOT NULL, PRIMARY KEY(log_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE player (id BIGINT AUTO_INCREMENT, numeric_steamid BIGINT NOT NULL, steamid VARCHAR(30) NOT NULL, credential VARCHAR(10) DEFAULT 'user' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE player_stat (player_id BIGINT, stat_id BIGINT, kills INT DEFAULT 0 NOT NULL, deaths INT DEFAULT 0 NOT NULL, PRIMARY KEY(player_id, stat_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE role (id BIGINT AUTO_INCREMENT, key_name VARCHAR(12) NOT NULL, name VARCHAR(20), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE role_stat (role_id BIGINT, stat_id BIGINT, time_played BIGINT DEFAULT 0 NOT NULL, PRIMARY KEY(role_id, stat_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE stat (id BIGINT AUTO_INCREMENT, log_id BIGINT NOT NULL, name VARCHAR(100) NOT NULL, player_id BIGINT NOT NULL, team VARCHAR(4) NOT NULL, kills INT DEFAULT 0 NOT NULL, assists INT DEFAULT 0 NOT NULL, deaths INT DEFAULT 0 NOT NULL, longest_kill_streak INT DEFAULT 0 NOT NULL, capture_points_blocked INT DEFAULT 0 NOT NULL, capture_points_captured INT DEFAULT 0 NOT NULL, dominations INT DEFAULT 0 NOT NULL, times_dominated INT DEFAULT 0 NOT NULL, revenges INT DEFAULT 0 NOT NULL, builtobjects INT DEFAULT 0 NOT NULL, destroyedobjects INT DEFAULT 0 NOT NULL, extinguishes INT DEFAULT 0 NOT NULL, ubers INT DEFAULT 0 NOT NULL, dropped_ubers INT DEFAULT 0 NOT NULL, INDEX log_id_idx (log_id), INDEX player_id_idx (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE weapon (id BIGINT AUTO_INCREMENT, key_name VARCHAR(40) NOT NULL, name VARCHAR(40), role_id BIGINT, INDEX role_id_idx (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE weapon_stat (weapon_id BIGINT, stat_id BIGINT, kills INT DEFAULT 0 NOT NULL, deaths INT DEFAULT 0 NOT NULL, PRIMARY KEY(weapon_id, stat_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
ALTER TABLE event ADD CONSTRAINT event_log_id_log_id FOREIGN KEY (log_id) REFERENCES log(id) ON DELETE CASCADE;
ALTER TABLE player_stat ADD CONSTRAINT player_stat_stat_id_stat_id FOREIGN KEY (stat_id) REFERENCES stat(id) ON DELETE CASCADE;
ALTER TABLE player_stat ADD CONSTRAINT player_stat_player_id_player_id FOREIGN KEY (player_id) REFERENCES player(id);
ALTER TABLE role_stat ADD CONSTRAINT role_stat_stat_id_stat_id FOREIGN KEY (stat_id) REFERENCES stat(id) ON DELETE CASCADE;
ALTER TABLE role_stat ADD CONSTRAINT role_stat_role_id_role_id FOREIGN KEY (role_id) REFERENCES role(id);
ALTER TABLE stat ADD CONSTRAINT stat_player_id_player_id FOREIGN KEY (player_id) REFERENCES player(id);
ALTER TABLE stat ADD CONSTRAINT stat_log_id_log_id FOREIGN KEY (log_id) REFERENCES log(id) ON DELETE CASCADE;
ALTER TABLE weapon ADD CONSTRAINT weapon_role_id_role_id FOREIGN KEY (role_id) REFERENCES role(id);
ALTER TABLE weapon_stat ADD CONSTRAINT weapon_stat_weapon_id_weapon_id FOREIGN KEY (weapon_id) REFERENCES weapon(id);
ALTER TABLE weapon_stat ADD CONSTRAINT weapon_stat_stat_id_stat_id FOREIGN KEY (stat_id) REFERENCES stat(id) ON DELETE CASCADE;
