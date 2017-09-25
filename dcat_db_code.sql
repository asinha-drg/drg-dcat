CREATE EXTENSION tablefunc;

CREATE TABLE users
	(
		id serial,
		user_id text NOT NULL,
		password text NOT NULL,
		name text NOT NULL,
		mobile text,
		address text,
		security_question text,
		security_answer text
	);

CREATE TABLE projects
	(
		id serial,
		name text NOT NULL,
		user_id text NOT NULL
	);

CREATE TABLE themes
	(
		id serial,
		name text NOT NULL,
		project_id integer NOT NULL
	);

CREATE TABLE keywords
	(
		id serial,
		name text NOT NULL,
		theme_id integer NOT NULL
	);

CREATE TABLE files
	(
		id serial,
		name text NOT NULL,
		project_id integer NOT NULL,
		status text
	);

CREATE TABLE file_detail
	(
		id serial,
		file_id int NOT NULL,
		d_date character varying(30),
		full_text text,
		url text,
		domain text,
		page_type character varying(300),
		language character varying(30),
		country character varying(300),
		author text
	);

CREATE TABLE mapped_data
	(
		data_row_id integer,
		theme_id integer,
		flag boolean
	);

CREATE OR REPLACE FUNCTION process_digital_data(_project_id integer, _file_id integer)
	RETURNS void AS $$
	DECLARE
		_themes integer[];
		_theme integer;
		_keywords text[];
		_keyword text;
		_data_row_id integer;
		_theme_keyword_flag boolean;

	BEGIN
		SELECT array_agg(coalesce(id,0000)) FROM themes INTO _themes  WHERE project_id = $1;

		FOREACH _theme IN ARRAY _themes
		LOOP

			SELECT array_agg(coalesce(name,'xxxxx')) INTO _keywords FROM keywords WHERE theme_id = _theme;
			FOR _data_row_id IN SELECT ID FROM file_detail WHERE file_id = _file_id
			LOOP
				FOREACH _keyword IN ARRAY _keywords
				LOOP
					_theme_keyword_flag =false;
					SELECT
						EXISTS
						(
							SELECT
								1
							FROM
								file_detail
							WHERE
								file_id = _file_id
								AND ID = _data_row_id
								AND full_text like '%'||_keyword||'%'
						)
					INTO  _theme_keyword_flag;
					IF _theme_keyword_flag THEN
						INSERT INTO mapped_data VALUES(_data_row_id, _theme, 't');
						raise info '%:%',_data_row_id,_keyword;
					END IF;
					EXIT WHEN _theme_keyword_flag;

				END LOOP;
			END LOOP;
		END LOOP;
		UPDATE files SET status = 'Processed' WHERE id = _file_id;
	END;
	$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION return_theme_based_data(_project_id int, _file_id integer)
	RETURNS
	TABLE
	(
		d_date text,
		full_text text,
		url text,
		domain text,
		page_type text,
		language text,
		country text,
		author text,
		themes text[]
	) AS $$
	DECLARE
		_query text;
	BEGIN

		SELECT '
			SELECT
				d_date::text date,
				full_text,
				url,
				domain,
				page_type::text page_type,
				language::text,
				country::text country,
				author::text author,
				array_agg(themes.name::text) themes
			FROM
				file_detail
				LEFT JOIN mapped_data ON file_detail.id = mapped_data.data_row_id AND file_detail.file_id = '||$2||'
				LEFT JOIN themes ON themes.id = mapped_data.theme_id AND themes.project_id = '||$1||'
			GROUP BY
				d_date,
				full_text,
				url,
				domain,
				page_type,
				language,
				country,
				author
		' INTO _query;

		RETURN QUERY EXECUTE _query;
	END;
	$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.return_theme_based_data_json(IN _project_id integer,IN _file_id integer)
	RETURNS TABLE(result json) AS
	$BODY$

	DECLARE
		_query text;
		_col_def text;
		_json_def text;
	BEGIN
		select
			string_agg(quote_ident(name)||' bool ' , ','),
			string_agg(''''||name||''''||','||'coalesce('||quote_ident(name)||',false)',',')
		INTO _col_def,_json_def
		from
			themes
		where project_id = _project_id;

		SELECT '
			select
				json_build_object
				(
					''date'',date,
					''full_text'',full_text,
					''url'',url,
					''domain'',domain,
					''page_type'',page_type,
					''language'',language,
					''country'' ,country,
					''author'',author,
					'||_json_def||'
				)
			FROM
				CROSSTAB
				(
					$$
					with a as
					(
						select 1
					)
					select
						d_date::text date,
						full_text,
						url,
						domain,
						page_type::text page_type,
						language::text,
						country::text country,
						author::text author,
						themes.name theme_name,
						coalesce(mapped_data.flag,false) flag
					FROM
						file_detail
						left JOIN mapped_data ON file_detail.id = mapped_data.data_row_id
						left JOIN themes ON themes.id = mapped_data.theme_id AND themes.project_id = '||$1||'
					where
						file_detail.file_id = '||$2||'
					order by 1
					$$,
					$$ SELECT name as theme_name FROM themes where project_id = '||$1||' order by 1 $$
				)
				AS x
				(
					date text,
					full_text text,
					url text,
					domain text,
					page_type text,
					language text,
					country text,
					author text,
					'||_col_def||'
				)'
		INTO _query;
		raise info '%', _query;
		RETURN QUERY EXECUTE _query;
	END;
	$BODY$ LANGUAGE plpgsql;
