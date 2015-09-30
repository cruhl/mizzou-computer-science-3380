DROP SCHEMA IF EXISTS lab10 CASCADE;
CREATE SCHEMA lab10;
SET search_path = lab10;


DROP TABLE IF EXISTS group_standings;
CREATE TABLE group_standings (
	team varchar(52) NOT NULL,
	wins smallint DEFAULT 0 NOT NULL,
	losses smallint DEFAULT 0 NOT NULL,
	draws smallint DEFAULT 0 NOT NULL,
	points smallint DEFAULT 0 NOT NULL,
	CONSTRAINT group_standings_draws_check CHECK (draws >= 0),
	CONSTRAINT group_standings_losses_check CHECK (losses >= 0),
	CONSTRAINT group_standings_points_check CHECK (points >= 0),
	CONSTRAINT group_standings_wins_check CHECK (wins >= 0),
	PRIMARY KEY (team)
);
\copy group_standings FROM '/facstaff/klaricm/public_cs3380/lab10/lab10_data.csv' WITH CSV HEADER;



CREATE FUNCTION calc_points_total(integer, integer) RETURNS integer AS $$
	SELECT $1 * 3 + $2;
$$ LANGUAGE sql;



CREATE FUNCTION update_points_total() returns trigger as $$
	BEGIN
		NEW.points = calc_points_total(NEW.wins, NEW.draws);
		RETURN NEW;
	END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER tr_update_points_total BEFORE INSERT OR UPDATE ON group_standings
	FOR EACH ROW EXECUTE PROCEDURE update_points_total();



CREATE FUNCTION disallow_team_name_update() returns trigger as $$
	BEGIN
		IF NEW.team != OLD.team THEN
			RAISE EXCEPTION 'Changing the team name is not allowed!';
		END IF;
	END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER tr_disallow_team_name_update BEFORE INSERT OR UPDATE ON group_standings
	FOR EACH ROW EXECUTE PROCEDURE disallow_team_name_update();