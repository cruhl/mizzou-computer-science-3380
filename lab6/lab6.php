
<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 6</title>
	</head>
	<body>
		<form method="POST">
			<select name="query" id="query">
				<option value="1">Query 1</option>
				<option value="2">Query 2</option>
				<option value="3">Query 3</option>
				<option value="4">Query 4</option>
				<option value="5">Query 5</option>
				<option value="6">Query 6</option>
				<option value="7">Query 7</option>
				<option value="8">Query 8</option>
				<option value="9">Query 9</option>
				<option value="10">Query 10</option>
			</select>
			<input type="submit" name="submit" value="Execute" />
		</form>
		<hr />

		<?php

			// check if form submitted
			if (!isset($_POST['query'])) {

				// default message
				echo "<strong>Select a query from the above list.</strong>";
			
			} else {

				// connect to database
				$connection = pg_connect('host=dbhost-pgsql.cs.missouri.edu user=ctr9rc password=LDG74VGc');

				// get query string
				switch ($_POST['query']) {
					case '1': $query = "SELECT min(surface_area), max(surface_area), avg(surface_area) FROM lab6.country;"; break;
					case '2': $query = "SELECT DISTINCT region, sum(population) AS total_POP, sum(surface_area) AS total_area, sum(gnp) AS total_gnp FROM (SELECT region, population, gnp, surface_area FROM lab6.country) AS countries GROUP BY region ORDER BY total_gnp DESC;"; break;
					case '3': $query = "SELECT DISTINCT government_form, count(DISTINCT country.name) AS count, max(indep_year) AS most_recent_independence_year FROM lab6.country WHERE indep_year IS NOT NULL GROUP BY government_form ORDER BY count DESC, most_recent_independence_year DESC;"; break;
					case '4': $query = "SELECT co.name, COUNT(ci.id) AS count FROM lab6.country AS co, lab6.city AS ci WHERE (co.country_code = ci.country_code) GROUP BY co.name HAVING COUNT(*) > 100 ORDER BY count ASC;"; break;
					case '5': $query = "SELECT DISTINCT country.name as country_name, country.population AS country_population, sum(city.population) AS urban_population, cast (sum(city.population) AS float) / country.population * 100 AS urban_percentage FROM lab6.city INNER JOIN lab6.country ON country.country_code = city.country_code GROUP BY country_name, country_population ORDER BY urban_percentage ASC;"; break;
					case '6': $query = "SELECT * FROM (SELECT DISTINCT ON (country.name) country.name, city.name AS largest_city, max(city.population) AS population FROM lab6.country JOIN lab6.city ON country.country_code = city.country_code GROUP BY country.name, city.name ORDER BY country.name, population DESC) as placeholder ORDER BY population DESC;"; break;
					case '7': $query = "SELECT country.name, count(distinct city.name) AS count FROM lab6.country JOIN lab6.city ON country.country_code = city.country_code GROUP BY country.name ORDER BY count DESC, country.name ASC;"; break;
					case '8': $query = "SELECT * FROM (SELECT country.name AS country_name, city.name AS capital, count(distinct language) AS language_count FROM lab6.country JOIN lab6.country_language ON country.country_code = country_language.country_code LEFT JOIN lab6.city ON country.capital = city.id GROUP BY country.name, city.name) AS everything WHERE language_count BETWEEN 8 AND 12 ORDER BY language_count DESC, capital DESC;"; break;
					case '9': $query = "SELECT co.name AS country, ci.name AS city, ci.population, sum(ci.population) OVER (PARTITION BY co.name ORDER BY ci.population DESC) AS running_total FROM lab6.country AS co, lab6.city AS ci WHERE (co.country_code = ci.country_code) ORDER BY co.name, running_total ASC;"; break;
					case '10': $query = "SELECT co.name, lng.language, rank() OVER (PARTITION BY co.name ORDER BY lng.percentage DESC) AS popularity_rank FROM lab6.country AS co, lab6.country_language AS lng WHERE(co.country_code = lng.country_code) ORDER BY co.name ASC"; break;
				}

				// execute query
				$result = pg_query($query);
				$row_count = pg_num_rows($result);
				echo "<p>The query returned <em>$row_count</em> results.</p>\n";
				echo pg_field_name($result);

				echo pg_last_error();

				// render table
				echo "\t\t<table border=\"1\">\n";

				// render headers
				$row = pg_fetch_assoc($result, 0);
				echo "\t\t\t<tr>\n";
				foreach ($row as $column_value => $row_value) {
					echo "\t\t\t\t<th>$column_value</th>\n";
				}
				echo "\t\t\t</tr>\n";

				// render table values
				while ($row = pg_fetch_assoc($result)) {
					echo "\t\t\t<tr>\n";
					foreach ($row as $column_value => $row_value) {
						echo "\t\t\t\t<td><span>$row_value<span/></td>\n";
					}
					echo "\t\t\t</tr>\n";
				}
				echo "\t\t</table>\n";

				// maintain form selection
				$selected = $_POST['query'];
				echo "\t\t<script>document.getElementById('query').value = $selected;</script>\n";
			}
		?>
	</body>
</html>
