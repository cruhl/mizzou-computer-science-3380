SET search_path = lab7;

------------- Question 1 

	EXPLAIN ANALYZE SELECT id, is_active, assets, name FROM banks WHERE id = '17317';
	
	/*
		Result
			Index Scan using banks_pkey on banks  (cost=0.00..8.27 rows=1 width=40) (actual time=0.042..0.044 rows=1 loops=1)
				Index Cond: (id = 17317)
			Total runtime: 0.076 ms

		Answer
			When the table 'banks' was created, psql automatically generated an index for the primary key called
			"banks_pkey". This index was used by the query.
	*/

------------- Question 2
	
	DROP INDEX IF EXISTS state_index;
	EXPLAIN ANALYZE SELECT * FROM banks WHERE state = 'Missouri';

	/*
		Result
			Seq Scan on banks  (cost=0.00..894.98 rows=996 width=124) (actual time=0.438..15.330 rows=996 loops=1)
				Filter: ((state)::text = 'Missouri'::text)
			Total runtime: 16.369 ms
	*/

	
	CREATE INDEX state_index ON banks (state);
	EXPLAIN ANALYZE SELECT * FROM banks WHERE state = 'Missouri';

	/*
		Result
			Bitmap Heap Scan on banks  (cost=23.97..598.42 rows=996 width=124) (actual time=0.408..1.991 rows=996 loops=1)
				Recheck Cond: ((state)::text = 'Missouri'::text)
					->  Bitmap Index Scan on state_name_index  (cost=0.00..23.72 rows=996 width=0) (actual time=0.316..0.316 rows=996 loops=1)
						Index Cond: ((state)::text = 'Missouri'::text)
			Total runtime: 2.987 ms

		Answer
			The pre-index speed was 16.369 ms and the post-index speed was 2.987 ms.
			The creation of an index resulted in a speed increase of 13.382 ms or 548%.
	*/

------------- Question 3
	
	DROP INDEX IF EXISTS name_index;
	EXPLAIN ANALYZE SELECT * FROM banks ORDER BY name;

	/*
		Result
			Sort  (cost=4657.15..4726.14 rows=27598 width=124) (actual time=309.810..447.909 rows=27598 loops=1)
			Sort Key: name
			Sort Method: external merge  Disk: 3760kB
				->  Seq Scan on banks  (cost=0.00..825.98 rows=27598 width=124) (actual time=0.015..33.555 rows=27598 loops=1)
			Total runtime: 475.914 ms
	*/

	
	CREATE INDEX name_index ON banks (name);
	EXPLAIN ANALYZE SELECT * FROM banks ORDER BY name;

	/*
		Result
			Index Scan using name_index on banks  (cost=0.00..3294.27 rows=27598 width=124) (actual time=0.042..31.264 rows=27598 loops=1)
			Total runtime: 48.103 ms

		Answer
			The pre-index speed was 475.914 ms and the post-index speed was 48.103 ms.
			The creation of an index resulted in a speed increase of 427.811 ms or 989%.
	*/

------------- Question 4

	DROP INDEX IF EXISTS is_active_index;
	CREATE INDEX is_active_index ON banks (is_active);

------------- Question 5

	EXPLAIN ANALYZE SELECT * FROM banks WHERE is_active = TRUE;
	EXPLAIN ANALYZE SELECT * FROM banks WHERE is_active = FALSE;

	/*
		Result (is_active = TRUE)
			Bitmap Heap Scan on banks  (cost=132.77..750.53 rows=6776 width=124) (actual time=1.219..10.167 rows=6776 loops=1)
				Filter: is_active
				->  Bitmap Index Scan on is_active_index  (cost=0.00..131.07 rows=6776 width=0) (actual time=1.098..1.098 rows=6776 loops=1)
					Index Cond: (is_active = true)
			Total runtime: 16.747 ms

		Result (is_active = FALSE)
			Seq Scan on banks  (cost=0.00..825.98 rows=20822 width=124) (actual time=0.008..24.743 rows=20822 loops=1)
				Filter: (NOT is_active)
			Total runtime: 44.660 ms

		Answer
			The query where "is_active = TRUE" used the index, the second query was sequential. This is
			because there are almost as many rows for "is_active = FALSE" as there are rows in the table.
			Because of that, a sequential scan would be faster than an index search.
	*/

------------- Question 6

	DROP INDEX IF EXISTS insured_index;
	EXPLAIN ANALYZE SELECT * FROM banks WHERE insured >= '2000-01-01';

	/*
		Result
			Seq Scan on banks  (cost=0.00..894.98 rows=1450 width=124) (actual time=1.894..8.110 rows=1451 loops=1)
				Filter: (insured >= '2000-01-01'::date)
			Total runtime: 9.542 ms
	*/

	CREATE INDEX insured_index ON banks (insured) WHERE NOT insured = '1934-01-01';
	EXPLAIN ANALYZE SELECT * FROM banks WHERE insured >= '2000-01-01';

	/*
		Result
			Index Scan using insured_index on banks  (cost=0.00..573.89 rows=1450 width=124) (actual time=0.027..1.497 rows=1451 loops=1)
				Index Cond: (insured >= '2000-01-01'::date)
			Total runtime: 2.464 ms

		Answer
			The pre-index speed was 9.542 ms and the post-index speed was 2.464 ms.
			The creation of an index resulted in a speed increase of 7.078 ms or 387%.
	*/

------------- Question 7

	DROP INDEX IF EXISTS assets_deposits_ratio_index;
	EXPLAIN ANALYZE SELECT id, name, city, state, assets, deposits FROM banks WHERE deposits != 0 AND assets / deposits <= 0.5;

	/*
		Result
			Seq Scan on banks  (cost=0.00..1032.97 rows=9166 width=63) (actual time=26.198..34.857 rows=46 loops=1)
				Filter: ((deposits <> 0::numeric) AND ((assets / deposits) <= 0.5))
			Total runtime: 34.919 ms
	*/

	CREATE INDEX assets_deposits_ratio_index ON banks ((assets / deposits)) WHERE deposits != 0;
	EXPLAIN ANALYZE SELECT id, name, city, state, assets, deposits FROM banks WHERE deposits != 0 AND assets / deposits <= 0.5;
	
	/*
		Result
			Bitmap Heap Scan on banks  (cost=215.54..925.95 rows=9166 width=63) (actual time=0.062..0.166 rows=46 loops=1)
				Recheck Cond: (((assets / deposits) <= 0.5) AND (deposits <> 0::numeric))
					->  Bitmap Index Scan on assets_deposits_ratio_index  (cost=0.00..213.25 rows=9166 width=0) (actual time=0.046..0.046 rows=46 loops=1)
						Index Cond: ((assets / deposits) <= 0.5)
			Total runtime: 0.242 ms

		Answer
			The pre-index speed was 34.919 ms and the post-index speed was 0.242 ms.
			The creation of an index resulted in a speed increase of 34.677 ms or 14429%.
	*/