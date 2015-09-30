<?php

	// ------- Excuse the mess...

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(-1);

	// postgres setup
	pg_connect('host=dbhost-pgsql.cs.missouri.edu user=ctr9rc password=LDG74VGc');

	pg_prepare('add_city', 'INSERT INTO lab5.city (name, country_code, district, population) VALUES ($1, $2, $3, $4)');

	pg_prepare('edit_country_indep_year', 'UPDATE lab5.country SET indep_year = $1 WHERE country_code = $2;');
	pg_prepare('edit_country_population', 'UPDATE lab5.country SET population = $1 WHERE country_code = $2;');
	pg_prepare('edit_country_local_name', 'UPDATE lab5.country SET local_name = $1 WHERE country_code = $2;');
	pg_prepare('edit_country_government_form', 'UPDATE lab5.country SET government_form = $1 WHERE country_code = $2;');

	pg_prepare('edit_city_district', 'UPDATE lab5.city SET district = $1 WHERE id = $2;');
	pg_prepare('edit_city_population', 'UPDATE lab5.city SET population = $1 WHERE id = $2;');
	
	pg_prepare('edit_country_language_is_official', 'UPDATE lab5.country_language SET is_official = $1 WHERE language = $2 AND country_code = $3;');
	pg_prepare('edit_country_language_percentage', 'UPDATE lab5.country_language SET percentage = $1 WHERE language = $2 AND country_code = $3;');

	pg_prepare('delete_row_country', 'DELETE FROM lab5.country WHERE country_code = $1;');
	pg_prepare('delete_row_city', 'DELETE FROM lab5.city WHERE id = $1;');
	pg_prepare('delete_row_country_language', 'DELETE FROM lab5.country_language WHERE language = $1 AND country_code = $2;');

	pg_prepare('output_table_html_country', 'SELECT * FROM lab5.country WHERE name ILIKE $1 ORDER BY name ASC;');
	pg_prepare('output_table_html_city', 'SELECT * FROM lab5.city WHERE name ILIKE $1 ORDER BY name ASC;');
	pg_prepare('output_table_html_country_language', 'SELECT * FROM lab5.country_language WHERE language ILIKE $1 ORDER BY language ASC;');
	
	pg_prepare('get_data_for_select', 'SELECT name, country_code FROM lab5.country;');

	// api actions
	function add_city($name, $country_code, $district, $population) {
		$result = pg_execute('add_city', array($name, $country_code, $district, $population));
		if (!$result) throw new Exception();
	}

	function edit($table, $key_value, $column, $column_value, $extra_key) {
		$result;
		if ($table != 'country_language') {
			$result = pg_execute('edit_' . $table . '_' . $column, array($column_value, $key_value));
		} else {
			$result = pg_execute('edit_' . $table . '_' . $column, array($column_value, $key_value, $extra_key));
		}
		if (!$result) throw new Exception();
	}
	
	function delete_row($table, $key_value, $extra_key) {
		$result;
		if ($table != 'country_language') {
			$result = pg_execute('delete_row_' . $table, array($key_value));
		} else {
			$result = pg_execute('delete_row_' . $table, array($key_value, $extra_key));
		}
		echo pg_last_error();
		if (!$result) throw new Exception();
	}

	function output_table_html($table, $query_string) {		
		$result = pg_execute('output_table_html_' . $table, array($query_string . '%'));
		if (!$result) throw new Exception();

		echo "<table border=\"1\">\n";
		$row = pg_fetch_assoc($result, 0);	
		if (is_null($row) || pg_num_rows($result) == 0) throw new Exception();

		echo "\t<tr>\n";
		echo "\t\t<td><strong>actions</strong></td>\n";
		foreach ($row as $column_value => $row_value) {
			echo "\t\t<td><strong>$column_value</strong></td>\n";
		}
		echo "\t</tr>\n";


		while ($row = pg_fetch_assoc($result)) {
			echo "\t<tr>\n";

			// action buttons
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"button\" data-type=\"display\" value=\"Edit\" />\n";
			echo "\t\t\t<input type=\"button\" data-type=\"display\" value=\"Delete\" />\n";
			echo "\t\t\t<input type=\"button\" data-type=\"editing\" value=\"Save\" style=\"display: none;\" />\n";
			echo "\t\t\t<input type=\"button\" data-type=\"editing\" value=\"Cancel\" style=\"display: none;\" />\n";
			echo "\t\t</td>\n";

			foreach ($row as $column_value => $row_value) {
				echo "\t\t<td>$row_value</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";
	}

	// entry point for api actions
	if (isset($_GET['action'])) {
		switch ($_GET['action']) {

			case 'delete':

				$extra_key = isset($_GET['extra_key']) ? $_GET['extra_key'] : '';
				delete_row(
					htmlspecialchars($_GET['table']),
					htmlspecialchars($_GET['key_value']),
					htmlspecialchars($extra_key)
				);
				
				break;				

			case 'add_city':

				add_city(
					htmlspecialchars($_GET['name']),
					htmlspecialchars($_GET['country_code']),
					htmlspecialchars($_GET['district']),
					htmlspecialchars($_GET['population'])
				);
				
				break;	

			case 'edit':

				$extra_key = isset($_GET['extra_key']) ? $_GET['extra_key'] : '';
				edit(
					htmlspecialchars($_GET['table']),
					htmlspecialchars($_GET['key_value']),
					htmlspecialchars($_GET['column']),
					htmlspecialchars($_GET['column_value']),
					htmlspecialchars($extra_key)
				);
				
				break;			

			case 'output_table_html':

				output_table_html(
					htmlspecialchars($_GET['table']),
					htmlspecialchars($_GET['query_string'])
				);
				
				break;
		}
		exit;
	}

	// serve html if not an action call
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 5</title>
		<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
		<link href="http://fonts.googleapis.com/css?family=Roboto:400italic,700,400" rel="stylesheet" type="text/css">
		
		<!-- page styles -->
		<style type="text/css">
			body, html {
				padding: 0;
				margin: 0;
				font-family: "Roboto";
			}

			#controls {
				position: fixed;
				z-index: 100;
				left: 0; top: 0; bottom: 0;
				width: 300px;
				padding: 0 15px 15px 15px;

				background: hsl(0, 0%, 85%);
			}
				#log {
					height: 200px;
					padding: 15px;
					overflow: scroll;

					background: hsl(0, 0%, 95%);
				}
					#log div {
						margin-bottom: 10px;
					}
					#log .good {color: hsl(85, 65%, 35%);}
					#log .bad {color: hsl(0, 65%, 35%);}

			#results {
				position: absolute;
				left: 330px; top: 0; right: 0; bottom: 0;
				padding: 0 15px 15px 15px;
				overflow: scroll;
			}

			input[type="text"] {
				width: 95%;
			}			
			input[type="submit"] {
				margin-top: 10px;
			}
			.editing {background: hsl(40, 85%, 80%) !important;}
		</style>
		<script>

			// quick and dirty custom ajax function using jquery
			function selfCallAJAX(action, parameters, success, error) {

				// build request url
				var requestURL = "index.php?action=" + action;
				for (var parameter in parameters) {
					requestURL += "&" + parameter + "=" + parameters[parameter];
				}

				// execute
				$.ajax(requestURL, {
					success: success || function () {},
					error: error || function () {}
				});
			}

			// on load
			$(function () {

				// expose current table and its headers
				var table, headers;

				// add messages to a log element
				function log(type, message) {
					$("#log").prepend("<div style=\"display: none;\" class=\"" + type + "\">" + message + "</div><hr />");
					$("#log div:first").slideDown();
				}

				// ssearch button clicked
				$("input[name='search']").click(function () {
					table = $("input[name='searchBy']:checked").val();

					var outputElement = $("#searchOutput");
					var queryString = $("input[name='queryString']").val();

					selfCallAJAX(
						"output_table_html",
						{table: table, query_string: queryString},
						function (data) {
							outputElement.html(data);
							outputElement.prepend("<p>Result count: <em>" + (outputElement.find("tr").length - 1) + "</em></p>");
							$("tr:even").css("background", "#EEE");

							headers = [];
							$("tr:first td").each(function () {
								value = $(this).text()
								if (value != "actions") headers.push(value);
							});

							log("good", "Results found.");
						},
						function () {
							outputElement.html("<p>No results!</p>");
							log("bad", "Server couldn't produce results.");
						}
					);
				});

				// search on enter key
				$("input[name='queryString']").keyup(function (event) {
					event.preventDefault();
					if (event.which != 13) return;					
					$("input[name='search']").trigger("click");
				});

				// handle action button clicks
				function revertToDisplayMode(row) {
					row.find("input[type='text']").each(function () {
						$(this).parent().html($(this).val());
					});
					row.find("[data-type='editing']").hide();
					row.find("[data-type='display']").show();
					row.attr("class", "");
				}
				$("#searchOutput").on("click", "input[type='button']", function () {
					switch ($(this).val()) {

						case "Edit":
							var parent = $(this).parent();
							parent.find("[data-type='display']").hide();
							parent.find("[data-type='editing']").show();
							parent.parent().attr("class", "editing");

							var editableFields = "indep_year population local_name government_form district is_official percentage";
							parent.siblings().each(function () {
								var header = headers[$(this).index() - 1];
								if (editableFields.indexOf(header) == -1 || header == "name") return;
								$(this).html("<input type=\"text\" value=\"" + $(this).text() + "\" />");
							});
							break;

						case "Delete":
							var isLanguageTable = headers[0] == "country_code" && headers[1] == "language";
							var rowElement = $(this).parent().parent();
							selfCallAJAX(
								"delete",
								{
									table: table,
									key_value: rowElement.find("td").eq(!isLanguageTable ? 1 : 2).text(),
									extra_key: !isLanguageTable ? "" : rowElement.find("td").eq(1).text()
								},
								function () {
									rowElement.remove();
									log("good", "Deleted row.");
								},
								function () { log("bad", "Couldn't delete row."); }
							);
							break;						

						case "Save":
							var isLanguageTable = headers[0] == "country_code" && headers[1] == "language";
							var rowElement = $(this).parent().parent();

							var keyValue = rowElement.find("td").eq(!isLanguageTable ? 1 : 2).text();
							var extraKey = !isLanguageTable ? "" : rowElement.find("td").eq(1).text();

							rowElement.find("[data-changed='true']").each(function () {
								var column = headers[$(this).parent().index() - 1];
								selfCallAJAX(
									"edit",
									{
										table: table,
										column: column,
										column_value: $(this).val(),
										key_value: keyValue,
										extra_key: extraKey
									},
									function () { log("good", "\"" + column + "\" changed."); },
									function () { log("bad", "Couldn't change \"" + column + "\" in the database."); }
								);
							});

							revertToDisplayMode(rowElement);
							break;
						
						case "Cancel":
							revertToDisplayMode($(this).parent().parent());
							break;
					}
				});
				$("#searchOutput").on("focus", "input[type='text']", function () {
					$(this).attr("data-changed", "true");
				});

				// handle add city submit
				$("input[value='Add City']").click(function () {
					selfCallAJAX(
						"add_city",
						{
							name: $("input[name='cityName']").val(),
							country_code: $("option:selected").attr("data-country-code"),
							district: $("input[name='district']").val(),
							population: $("input[name='population']").val()
						},
						function () { log("good", "City added."); },
						function () { log("bad", "Couldn't add city."); }
					);
				});

			});

		</script>
	</head>
	<body style="padding: 0; margin: 0;">

		<div id="controls">

			<h1>Controls</h1>
			<hr />

			<h2>Search</h2>
			Find:
			<label><input type="radio" name="searchBy" value="country" checked="true" />Countries</label>
			<label><input type="radio" name="searchBy" value="city" />Cities</label>
			<label><input type="radio" name="searchBy" value="country_language" />Languages</label>
			<p>That begin with:</p>
			<input type="text" name="queryString" />
			<input type="button" name="search" value="Search" />
			<hr />

			<h2>Add New City</h2>
			<input type="text" name="cityName" placeholder="City Name" />
			<select>
				<?php
					$result = pg_execute('get_data_for_select', array());
					echo "\n";
					while ($row = pg_fetch_assoc($result)) {
						echo "\t\t\t\t<option data-country-code=\"" . $row["country_code"] . "\">" . $row["name"] . "</option>\n";
					}
				?>
			</select>
			<input type="text" name="district" placeholder="District" />
			<input type="text" name="population" placeholder="Population" />
			<input type="button" name="submit" value="Add City" />
			<hr />

			<h2>Event Log</h2>
			<div id="log"></div>
		</div>

		<div id="results">
			<div style="padding: 5px 15px 15px 15px;">
				<h1>Search Results</h1>
				<div id="searchOutput">
					<p>Search results will appear here...</p>
				</div>
			</div>
		</div>
	</body>
</html>