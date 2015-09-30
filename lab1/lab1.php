<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Conner Ruhl - CS3380 Lab 1</title>
	</head>
	<body>
		<form method="POST" action="<?= $_SERVER["PHP_SELF"] ?>">
			<table border="1">
				<tr>
					<td>Number of Rows:</td>
					<td>
						<input type="text" name="rows" />
					</td>
				</tr>
				<tr>
					<td>Number of Columns:</td>
					<td>
						<select name="columns">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="4">4</option>
							<option value="8">8</option>
							<option value="16">16</option>
						</select>
					</td>
				</tr>
				<tr>
				 	<td>Operation:</td>
				 	<td>
				 		<input type="radio" name="operation" value="multiplication" checked="yes">Multiplication</input><br />
					 	<input type="radio" name="operation" value="addition">Addition</input>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="submit" value="Generate" />
					</td>
				</tr>
			</table>
		</form>

		<?php
			# check if form was submitted
			if (!isset($_POST["submit"]) || $_POST["submit"] == "") exit;

			$operation = $_POST["operation"];
			$rows = $_POST["rows"];
			$columns = $_POST["columns"];

			# check inputs
			if (!(is_numeric($rows) && $rows >= 0)) {
				echo "<p>The number of rows must be a number greater than zero!</p>";
				exit;
			}

			# start rendering table
			echo "<h1>The " . $rows . " x " . $columns . " " . $operation . " table:</h1>\n";
			echo "\t\t<table border=\"1\">\n";

			for ($row = 0; $row < $rows + 1; $row++) { 

				# start row
				echo "\t\t\t<tr>\n";

				for ($column = 0; $column < $columns + 1; $column++) {

					# add cell
					if ($row == 0) {
						$cellValue = $column;
						$cellBold = true;
					} else if ($column == 0) {
						$cellValue = $row;
						$cellBold = true;
					} else {
						$cellValue = $operation == "multiplication" ? $row * $column : $row + $column;
						$cellBold = false;
					}

					echo "\t\t\t\t<td" . ($cellBold ? " style=\"font-weight: bold;\"" : "") . ">" . $cellValue . "</td>\n";
				}

				echo "\t\t\t</tr>\n";
			}

			# table rendered
			echo "\t\t</table>";
		?>

	</body>
</html>