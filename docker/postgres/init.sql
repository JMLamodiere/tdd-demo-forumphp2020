create table RUNNING_SESSION
(
	ID integer not null
		constraint running_session_pk
			primary key,
	DISTANCE real not null,
	SHOES varchar(50) not null,
	TEMPERATURE_CELCIUS numeric(4,1) not null
);
