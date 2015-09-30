
<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 4</title>
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
					case '1': $query = "SELECT * FROM lab4.weight;"; break;
					case '2': $query = "SELECT * FROM lab4.bmi;"; break;
					case '3': $query = "SELECT university_name, city FROM lab4.university WHERE NOT EXISTS (SELECT uid FROM lab4.person WHERE lab4.person.uid = lab4.university.uid);"; break;
					case '4': $query = "SELECT fname, lname FROM lab4.person WHERE uid IN (SELECT uid FROM lab4.university WHERE city = 'Columbia');"; break;
					case '5': $query = "SELECT activity_name FROM lab4.activity WHERE activity_name NOT IN (SELECT * FROM lab4.activity WHERE EXISTS (SELECT activity_name FROM lab4.participated_in WHERE lab4.participated_in.activity_name = lab4.activity.activity_name));"; break;
					case '6': $query = "SELECT pid FROM lab4.participated_in WHERE activity_name = 'running' UNION SELECT pid FROM lab4.participated_in WHERE activity_name = 'racquetball';"; break;
					case '7': $query = "SELECT fname, lname FROM lab4.person, lab4.body_composition WHERE age > 30 AND lab4.person.pid = lab4.body_composition.pid INTERSECT SELECT fname, lname FROM lab4.person, lab4.body_composition WHERE height > 65 AND lab4.person.pid = lab4.body_composition.pid"; break;
					case '8': $query = "SELECT fname, lname, weight, height, age FROM lab4.person, lab4.body_composition WHERE lab4.person.pid = lab4.body_composition.pid ORDER BY height DESC, weight ASC, lname ASC;"; break;
					case '9': $query = "WITH mizzou_students AS (SELECT pid, fname, lname FROM lab4.person WHERE uid = 2) SELECT * FROM mizzou_students INNER JOIN lab4.body_composition USING (pid);"; break;
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
