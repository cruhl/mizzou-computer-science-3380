
<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 3</title>
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
				<option value="11">Query 11</option>
				<option value="12">Query 12</option>
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

					case '1': $query = "SELECT district, population FROM lab3.city WHERE name='Springfield'"; break;
					case '2': $query = "SELECT name, district, population FROM lab3.city WHERE country_code='BRA' ORDER BY name ASC"; break;
					case '3': $query = "SELECT name, continent, surface_area FROM lab3.country ORDER BY surface_area ASC LIMIT 20"; break;
					case '4': $query = "SELECT name, continent, government_form, GNP FROM lab3.country WHERE GNP > 200000 ORDER BY name ASC"; break;
					case '5': $query = "SELECT name, life_expectancy FROM lab3.country WHERE life_expectancy IS NOT NULL ORDER BY life_expectancy DESC OFFSET 10 LIMIT 10"; break;
					case '6': $query = "SELECT name FROM lab3.city WHERE name LIKE 'B%s' ORDER BY population DESC"; break;
					case '7': $query = "SELECT lab3.city.name AS city_name, lab3.country.name, lab3.city.population FROM lab3.city, lab3.country WHERE lab3.city.country_code = lab3.country.country_code AND lab3.city.population > 6000000 ORDER BY lab3.city.population DESC"; break;
					case '8': $query = "SELECT lab3.country.name, lab3.country_language.language, lab3.country_language.percentage FROM lab3.country_language, lab3.country WHERE lab3.country.country_code = lab3.country_language.country_code AND lab3.country_language.is_official = FALSE AND lab3.country.population > 50000000 ORDER BY lab3.country_language.percentage DESC"; break;
					case '9': $query = "SELECT name, indep_year, region FROM lab3.country, lab3.country_language WHERE lab3.country.country_code = lab3.country_language.country_code AND lab3.country_language.language = 'English' AND lab3.country_language.is_official = TRUE ORDER BY lab3.country.region ASC, lab3.country.name ASC"; break;
					case '10': $query = "SELECT lab3.city.name AS capital_name, lab3.country.name AS country_name, 100 * lab3.city.population / lab3.country.population AS urabn_pct FROM lab3.country, lab3.city WHERE lab3.country.capital = lab3.city.id ORDER BY urabn_pct DESC"; break;
					case '11': $query = "SELECT lab3.country.name, lab3.country_language.language, round(lab3.country_language.percentage * lab3.country.population) AS speakers FROM lab3.country, lab3.country_language WHERE lab3.country.country_code = lab3.country_language.country_code AND lab3.country_language.is_official = TRUE ORDER BY speakers DESC"; break;
					case '12': $query = "SELECT name, region, gnp, gnp_old, (gnp - gnp_old) / gnp_old AS gnp_real_change FROM lab3.country WHERE gnp_old IS NOT NULL ORDER BY gnp_real_change DESC"; break;
				}
				// execute query
				$result = pg_query($query);
				$row_count = pg_num_rows($result);
				echo "<p>The query returned <em>$row_count</em> results.</p>\n";
				echo pg_field_name($result);

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
						echo "\t\t\t\t<td>$row_value</td>\n";
					}
					echo "\t\t\t</tr>\n";
				}
				echo "\t\t</table>\n";

				// maintain form select
				$selected = $_POST['query'];
				echo "\t\t<script>document.getElementById('query').value = $selected;</script>\n";
			}
		?>
	</body>
</html>