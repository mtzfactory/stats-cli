SELECT 
	cal.datefield as DTIME, 
	cal.CELL,
	IFNULL(gsm.2G_NORMSEIZ, 0) AS 2G_NORMSEIZ
FROM (
	SELECT 
		DTIME, 
        CELL,
		2G_NORMSEIZ
	FROM gsm_nokia_report
	WHERE CELL='VA_SIMANCVEGA_G1') gsm 
RIGHT JOIN (
	SELECT 
		datefield, 
		'VA_SIMANCVEGA_G1' as CELL
	FROM calendar) cal 
ON gsm.dtime = cal.datefield 
WHERE (cal.datefield BETWEEN (SELECT MIN(DATE(dtime)) FROM gsm_nokia_report ) AND (SELECT MAX(DATE(dtime)) FROM gsm_nokia_report))
GROUP BY datefield;