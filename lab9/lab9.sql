-- ### QUERY 1 ### --
SELECT
	name10
FROM
	tl_2010_us_state10
WHERE
	ST_Intersects(
		coords,
    	ST_GeomFromText('POLYGON((-110 35, -110 36, -109 36, -109 35, -110 35))', 4326)
    )
ORDER BY
	name10 DESC;

-- ### QUERY 2 ### --
SELECT
	stusps10,
	name10
FROM
	tl_2010_us_state10 
WHERE
	ST_Touches(
		coords,
		(
			SELECT
				coords
			FROM
				tl_2010_us_state10
			WHERE
				name10='North Carolina'
		)
	)
ORDER BY
	name10 ASC;

-- ### QUERY 3 ### --
SELECT
	uac.name10
FROM
	tl_2010_us_uac10 AS uac,
	tl_2010_us_state10 AS state
WHERE
	state.name10 = 'Colorado'
	AND 
		ST_contains(state.coords, uac.coords)
ORDER BY
	uac.name10 ASC;
    
-- ### QUERY 4 ### --
SELECT
	uac.name10,
	((uac.aland10 + uac.awater10) / 1000000) AS sq_km
FROM
	tl_2010_us_uac10 AS uac,
	tl_2010_us_state10 AS state
WHERE
	state.name10 = 'Pennsylvania' 
    AND
    	ST_Overlaps(state.coords, uac.coords);

-- ### QUERY 5 ### --
SELECT
    uac1.name10,
    uac2.name10
FROM
    tl_2010_us_uac10 AS uac1,
    tl_2010_us_uac10 AS uac2
WHERE
	ST_Intersects(uac1.coords, uac2.coords)
    AND uac1.gid != uac2.gid
    AND uac1.gid < uac2.gid;

-- ### QUERY 6 ### --
SELECT
	uac.name10,
	COUNT(state.name10) AS count
FROM
	tl_2010_us_uac10 AS uac,
	tl_2010_us_state10 AS state
WHERE
	((uac.aland10 + uac.awater10) > 1500000000)
    AND
    	ST_Intersects(state.coords, uac.coords)
GROUP BY
	uac.name10 HAVING COUNT(state.name10) > 1
ORDER BY
	uac.name10 ASC;