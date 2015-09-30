#include <iostream>
#include <sqlite3.h>
#include <cstring>
#include <fstream>
using namespace std;

int main(int argc, char** argv) {

	// test arguments
	if (argc != 4) {
		std::cerr << "USAGE: " << argv[0] << " <database file> <table name> <CSV file>" << std::endl;
		return 1;
	}

	// get command line interface
	char* database_file = argv[1];
	string table_name = argv[2];
	string csv_file = argv[3];

	// open database
	sqlite3* database;
	sqlite3_open(database_file, &database);
	sqlite3_stmt* statement;

	// clear table
	string sql = "DELETE FROM " + table_name + ";";
	sqlite3_prepare(database, sql.c_str(), -1, &statement, NULL);
	sqlite3_step(statement);
	sqlite3_finalize(statement);

	// open CSV
	ifstream file;
	string line;

	// loop through CSV
	file.open(argv[3], ifstream::in);
	while (file.good()) {
		while (getline(file, line)) {
			string sql = "INSERT INTO " + table_name + " VALUES (" + line + ");";
			sqlite3_prepare(database, sql.c_str(), -1, &statement, NULL);
			sqlite3_step(statement);
			sqlite3_finalize(statement);
		}
	}

	// close loose resources
	file.close();
	sqlite3_close(database);
	return 0;
}