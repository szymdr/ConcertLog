--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2 (Debian 17.2-1.pgdg120+1)
-- Dumped by pg_dump version 17.2

-- Started on 2025-01-30 22:43:48 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 255 (class 1255 OID 24877)
-- Name: check_artist_name_not_empty(); Type: FUNCTION; Schema: public; Owner: root
--

CREATE FUNCTION public.check_artist_name_not_empty() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF NEW.name IS NULL OR NEW.name = '' THEN
        RAISE EXCEPTION 'Nazwa artysty nie może być pusta!';
    END IF;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.check_artist_name_not_empty() OWNER TO root;

--
-- TOC entry 254 (class 1255 OID 24772)
-- Name: log_database_changes(); Type: FUNCTION; Schema: public; Owner: root
--

CREATE FUNCTION public.log_database_changes() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    record_identifier TEXT;
BEGIN
    record_identifier := NEW.user_id::TEXT;

    INSERT INTO public.database_changes_log (
        table_name, 
        operation_type, 
        record_id, 
        old_values, 
        new_values, 
        changed_at
    )
    VALUES (
        TG_TABLE_NAME, 
        TG_OP, 
        record_identifier::INTEGER, 
        to_jsonb(OLD) - 'user_details_id', 
        to_jsonb(NEW) - 'user_details_id', 
        CURRENT_TIMESTAMP
    );

    RETURN NEW;
END;
$$;


ALTER FUNCTION public.log_database_changes() OWNER TO root;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 16506)
-- Name: artists; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.artists (
    artist_id integer NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.artists OWNER TO root;

--
-- TOC entry 223 (class 1259 OID 16505)
-- Name: artists_artist_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.artists_artist_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.artists_artist_id_seq OWNER TO root;

--
-- TOC entry 3515 (class 0 OID 0)
-- Dependencies: 223
-- Name: artists_artist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.artists_artist_id_seq OWNED BY public.artists.artist_id;


--
-- TOC entry 232 (class 1259 OID 16573)
-- Name: concert_artist; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.concert_artist (
    concert_artist_id integer NOT NULL,
    artist_id integer NOT NULL,
    concert_id integer NOT NULL
);


ALTER TABLE public.concert_artist OWNER TO root;

--
-- TOC entry 231 (class 1259 OID 16572)
-- Name: concert_artist_concert_artist_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.concert_artist_concert_artist_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.concert_artist_concert_artist_id_seq OWNER TO root;

--
-- TOC entry 3516 (class 0 OID 0)
-- Dependencies: 231
-- Name: concert_artist_concert_artist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.concert_artist_concert_artist_id_seq OWNED BY public.concert_artist.concert_artist_id;


--
-- TOC entry 238 (class 1259 OID 16619)
-- Name: concert_genre; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.concert_genre (
    genre_id integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.concert_genre OWNER TO root;

--
-- TOC entry 230 (class 1259 OID 16556)
-- Name: concerts; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.concerts (
    concert_id integer NOT NULL,
    title character varying(100),
    date date NOT NULL,
    venue_id integer,
    location_id integer,
    genre_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.concerts OWNER TO root;

--
-- TOC entry 226 (class 1259 OID 16532)
-- Name: locations; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.locations (
    location_id integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.locations OWNER TO root;

--
-- TOC entry 228 (class 1259 OID 16541)
-- Name: venues; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.venues (
    venue_id integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.venues OWNER TO root;

--
-- TOC entry 241 (class 1259 OID 24789)
-- Name: concert_details; Type: VIEW; Schema: public; Owner: root
--

CREATE VIEW public.concert_details AS
 SELECT c.concert_id,
    c.title AS concert_title,
    c.date AS concert_date,
    v.name AS venue_name,
    l.name AS location_name,
    g.name AS genre_name,
    a.name AS artist_name
   FROM (((((public.concerts c
     LEFT JOIN public.venues v ON ((c.venue_id = v.venue_id)))
     LEFT JOIN public.locations l ON ((c.location_id = l.location_id)))
     LEFT JOIN public.concert_genre g ON ((c.genre_id = g.genre_id)))
     LEFT JOIN public.concert_artist ca ON ((c.concert_id = ca.concert_id)))
     LEFT JOIN public.artists a ON ((ca.artist_id = a.artist_id)));


ALTER VIEW public.concert_details OWNER TO root;

--
-- TOC entry 237 (class 1259 OID 16618)
-- Name: concert_genre_genre_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.concert_genre_genre_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.concert_genre_genre_id_seq OWNER TO root;

--
-- TOC entry 3517 (class 0 OID 0)
-- Dependencies: 237
-- Name: concert_genre_genre_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.concert_genre_genre_id_seq OWNED BY public.concert_genre.genre_id;


--
-- TOC entry 236 (class 1259 OID 16607)
-- Name: concert_picture; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.concert_picture (
    concert_picture_id integer NOT NULL,
    concert_id integer NOT NULL,
    picture_path character varying(255) NOT NULL
);


ALTER TABLE public.concert_picture OWNER TO root;

--
-- TOC entry 235 (class 1259 OID 16606)
-- Name: concert_picture_concert_picture_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.concert_picture_concert_picture_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.concert_picture_concert_picture_id_seq OWNER TO root;

--
-- TOC entry 3518 (class 0 OID 0)
-- Dependencies: 235
-- Name: concert_picture_concert_picture_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.concert_picture_concert_picture_id_seq OWNED BY public.concert_picture.concert_picture_id;


--
-- TOC entry 234 (class 1259 OID 16590)
-- Name: concert_user; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.concert_user (
    concert_user_id integer NOT NULL,
    user_id integer NOT NULL,
    concert_id integer NOT NULL
);


ALTER TABLE public.concert_user OWNER TO root;

--
-- TOC entry 233 (class 1259 OID 16589)
-- Name: concert_user_concert_user_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.concert_user_concert_user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.concert_user_concert_user_id_seq OWNER TO root;

--
-- TOC entry 3519 (class 0 OID 0)
-- Dependencies: 233
-- Name: concert_user_concert_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.concert_user_concert_user_id_seq OWNED BY public.concert_user.concert_user_id;


--
-- TOC entry 229 (class 1259 OID 16555)
-- Name: concerts_concert_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.concerts_concert_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.concerts_concert_id_seq OWNER TO root;

--
-- TOC entry 3520 (class 0 OID 0)
-- Dependencies: 229
-- Name: concerts_concert_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.concerts_concert_id_seq OWNED BY public.concerts.concert_id;


--
-- TOC entry 240 (class 1259 OID 24763)
-- Name: database_changes_log; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.database_changes_log (
    log_id integer NOT NULL,
    table_name text NOT NULL,
    operation_type text NOT NULL,
    record_id integer NOT NULL,
    old_values jsonb,
    new_values jsonb,
    changed_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.database_changes_log OWNER TO root;

--
-- TOC entry 239 (class 1259 OID 24762)
-- Name: database_changes_log_log_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.database_changes_log_log_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.database_changes_log_log_id_seq OWNER TO root;

--
-- TOC entry 3521 (class 0 OID 0)
-- Dependencies: 239
-- Name: database_changes_log_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.database_changes_log_log_id_seq OWNED BY public.database_changes_log.log_id;


--
-- TOC entry 225 (class 1259 OID 16531)
-- Name: location_location_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.location_location_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.location_location_id_seq OWNER TO root;

--
-- TOC entry 3522 (class 0 OID 0)
-- Dependencies: 225
-- Name: location_location_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.location_location_id_seq OWNED BY public.locations.location_id;


--
-- TOC entry 222 (class 1259 OID 16445)
-- Name: users; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.users (
    user_id integer NOT NULL,
    username character varying(50) NOT NULL,
    email character varying(100) NOT NULL,
    password_hash character varying(255) NOT NULL,
    user_type_id integer DEFAULT 1 NOT NULL,
    user_details_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.users OWNER TO root;

--
-- TOC entry 242 (class 1259 OID 24798)
-- Name: user_concerts; Type: VIEW; Schema: public; Owner: root
--

CREATE VIEW public.user_concerts AS
 SELECT u.user_id,
    u.username,
    u.email,
    c.title AS concert_title,
    c.date AS concert_date
   FROM ((public.users u
     JOIN public.concert_user cu ON ((u.user_id = cu.user_id)))
     JOIN public.concerts c ON ((cu.concert_id = c.concert_id)));


ALTER VIEW public.user_concerts OWNER TO root;

--
-- TOC entry 220 (class 1259 OID 16436)
-- Name: user_details; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.user_details (
    user_details_id integer NOT NULL,
    profile_picture character varying(255),
    bio character varying(255)
);


ALTER TABLE public.user_details OWNER TO root;

--
-- TOC entry 219 (class 1259 OID 16435)
-- Name: user_details_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.user_details_detail_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_details_detail_id_seq OWNER TO root;

--
-- TOC entry 3523 (class 0 OID 0)
-- Dependencies: 219
-- Name: user_details_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.user_details_detail_id_seq OWNED BY public.user_details.user_details_id;


--
-- TOC entry 218 (class 1259 OID 16405)
-- Name: user_types; Type: TABLE; Schema: public; Owner: root
--

CREATE TABLE public.user_types (
    user_type_id integer NOT NULL,
    type_name character varying(50) NOT NULL,
    description text
);


ALTER TABLE public.user_types OWNER TO root;

--
-- TOC entry 217 (class 1259 OID 16404)
-- Name: user_types_user_type_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.user_types_user_type_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_types_user_type_id_seq OWNER TO root;

--
-- TOC entry 3524 (class 0 OID 0)
-- Dependencies: 217
-- Name: user_types_user_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.user_types_user_type_id_seq OWNED BY public.user_types.user_type_id;


--
-- TOC entry 221 (class 1259 OID 16444)
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.users_user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_user_id_seq OWNER TO root;

--
-- TOC entry 3525 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.users_user_id_seq OWNED BY public.users.user_id;


--
-- TOC entry 227 (class 1259 OID 16540)
-- Name: venue_venue_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE public.venue_venue_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.venue_venue_id_seq OWNER TO root;

--
-- TOC entry 3526 (class 0 OID 0)
-- Dependencies: 227
-- Name: venue_venue_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE public.venue_venue_id_seq OWNED BY public.venues.venue_id;


--
-- TOC entry 3281 (class 2604 OID 16509)
-- Name: artists artist_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.artists ALTER COLUMN artist_id SET DEFAULT nextval('public.artists_artist_id_seq'::regclass);


--
-- TOC entry 3286 (class 2604 OID 16576)
-- Name: concert_artist concert_artist_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_artist ALTER COLUMN concert_artist_id SET DEFAULT nextval('public.concert_artist_concert_artist_id_seq'::regclass);


--
-- TOC entry 3289 (class 2604 OID 16622)
-- Name: concert_genre genre_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_genre ALTER COLUMN genre_id SET DEFAULT nextval('public.concert_genre_genre_id_seq'::regclass);


--
-- TOC entry 3288 (class 2604 OID 16610)
-- Name: concert_picture concert_picture_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_picture ALTER COLUMN concert_picture_id SET DEFAULT nextval('public.concert_picture_concert_picture_id_seq'::regclass);


--
-- TOC entry 3287 (class 2604 OID 16593)
-- Name: concert_user concert_user_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_user ALTER COLUMN concert_user_id SET DEFAULT nextval('public.concert_user_concert_user_id_seq'::regclass);


--
-- TOC entry 3284 (class 2604 OID 16559)
-- Name: concerts concert_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concerts ALTER COLUMN concert_id SET DEFAULT nextval('public.concerts_concert_id_seq'::regclass);


--
-- TOC entry 3290 (class 2604 OID 24766)
-- Name: database_changes_log log_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.database_changes_log ALTER COLUMN log_id SET DEFAULT nextval('public.database_changes_log_log_id_seq'::regclass);


--
-- TOC entry 3282 (class 2604 OID 16535)
-- Name: locations location_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.locations ALTER COLUMN location_id SET DEFAULT nextval('public.location_location_id_seq'::regclass);


--
-- TOC entry 3276 (class 2604 OID 16439)
-- Name: user_details user_details_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.user_details ALTER COLUMN user_details_id SET DEFAULT nextval('public.user_details_detail_id_seq'::regclass);


--
-- TOC entry 3275 (class 2604 OID 16408)
-- Name: user_types user_type_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.user_types ALTER COLUMN user_type_id SET DEFAULT nextval('public.user_types_user_type_id_seq'::regclass);


--
-- TOC entry 3277 (class 2604 OID 16448)
-- Name: users user_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.users ALTER COLUMN user_id SET DEFAULT nextval('public.users_user_id_seq'::regclass);


--
-- TOC entry 3283 (class 2604 OID 16544)
-- Name: venues venue_id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.venues ALTER COLUMN venue_id SET DEFAULT nextval('public.venue_venue_id_seq'::regclass);


--
-- TOC entry 3493 (class 0 OID 16506)
-- Dependencies: 224
-- Data for Name: artists; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.artists (artist_id, name) FROM stdin;
1	Travis Scott
2	OIO
3	Oki
4	Mata
5	Lemon
6	Kendrick Lamar
7	SZA
8	Harry Styles
9	The Weeknd
10	Piotr Rubik
11	Ed Sheeran
12	Bedoes 2115
13	Taylor Swift
14	Taco Hemingway
\.


--
-- TOC entry 3501 (class 0 OID 16573)
-- Dependencies: 232
-- Data for Name: concert_artist; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.concert_artist (concert_artist_id, artist_id, concert_id) FROM stdin;
1	1	1
2	2	2
3	3	3
4	4	4
5	5	5
6	6	6
7	7	7
8	6	8
9	8	9
10	9	10
11	10	11
12	1	12
13	3	13
14	11	14
15	12	15
16	13	16
17	14	17
\.


--
-- TOC entry 3507 (class 0 OID 16619)
-- Dependencies: 238
-- Data for Name: concert_genre; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.concert_genre (genre_id, name) FROM stdin;
1	Hip-hop
2	Polish Hip-hop
3	Pop
\.


--
-- TOC entry 3505 (class 0 OID 16607)
-- Dependencies: 236
-- Data for Name: concert_picture; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.concert_picture (concert_picture_id, concert_id, picture_path) FROM stdin;
1	1	679bd22f44b2e2.54755757.jpg
2	1	679bd22f4aacb9.95771511.jpg
3	1	679bd22f5005a9.65052119.jpg
4	2	679bd307ad9d62.09141142.jpg
5	3	679bd35e48b8d5.09165967.jpg
6	3	679bd35e4fb3a3.95026214.jpg
7	3	679bd35e535ee6.92703259.jpg
8	4	679bd9d7a3d0d8.03694653.jpg
9	4	679bd9d7aeb677.64715030.jpg
10	4	679bd9d7b48d82.14654641.jpg
11	5	679bda4e5208b4.55246081.jpg
12	6	679bdabe558605.04879769.jpg
13	6	679bdabe5bc0d7.16076166.jpg
14	6	679bdabe60d939.58862073.jpg
15	7	679bdb91bf7687.14271423.jpg
16	7	679bdb91c4a4f6.61172712.jpg
17	8	679bdc0916d883.86030756.jpg
18	8	679bdc091cbe33.06428486.jpg
19	9	679bdc626d4a59.86939453.jpg
20	10	679bdcdbd1dfa4.31174426.jpg
21	10	679bdcdbd6e689.70891827.jpg
22	11	679bdd30ccb1a8.88955771.jpg
23	11	679bdd30d1d0e9.85298802.jpg
24	12	679bdd89dbb366.25614701.jpg
25	12	679bdd89e04c08.54818935.jpg
26	12	679bdd89e298d4.88615109.jpg
27	13	679bde0cd96267.26988237.jpg
28	14	679be7f05a4c97.47209668.jpg
29	15	679be877a194f5.10190072.jpg
30	16	679be8be6111b4.01253455.jpg
31	17	679be92a0213d8.95118005.jpg
\.


--
-- TOC entry 3503 (class 0 OID 16590)
-- Dependencies: 234
-- Data for Name: concert_user; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.concert_user (concert_user_id, user_id, concert_id) FROM stdin;
1	2	1
2	2	2
3	2	3
4	2	4
5	2	5
6	2	6
7	2	7
8	2	8
9	2	9
10	2	10
11	2	11
12	2	12
13	2	13
14	3	14
15	3	15
16	3	16
17	3	17
\.


--
-- TOC entry 3499 (class 0 OID 16556)
-- Dependencies: 230
-- Data for Name: concerts; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.concerts (concert_id, title, date, venue_id, location_id, genre_id, created_at) FROM stdin;
1	Astroworld – Wish You Were Here Tour	2019-07-03	1	1	1	2025-01-30 19:25:35.358128
2	OIO Tour	2021-07-31	2	2	2	2025-01-30 19:29:11.743234
3	PRODUKT47 TOUR	2022-06-19	3	2	2	2025-01-30 19:30:38.361578
4	MATA TOUR:)	2022-07-23	4	2	2	2025-01-30 19:58:15.809157
5	Żadnego końca świata dziś nie będzie!	2022-10-01	4	2	3	2025-01-30 20:00:14.394874
6	The Big Steppers Tour	2022-10-10	5	3	1	2025-01-30 20:02:06.455872
7	SOS Tour	2023-06-29	1	1	3	2025-01-30 20:05:37.827574
8	The Big Steppers Tour	2023-07-02	1	1	1	2025-01-30 20:07:37.142044
9	Love on Tour	2023-07-02	6	4	3	2025-01-30 20:09:06.484095
10	After Hours til Dawn Tour	2023-08-09	6	4	3	2025-01-30 20:11:07.904916
11	Niech mówią, że to nie jest miłość	2024-03-22	4	2	3	2025-01-30 20:12:32.889925
12	Circus Maximus Tour	2024-07-02	4	2	1	2025-01-30 20:14:01.952471
13	We Are Era 47	2024-12-19	4	2	2	2025-01-30 20:16:12.926461
14	+–=÷× Tour	2022-01-01	7	5	3	2025-01-30 20:58:24.413724
15	Łódź Summer Festival 2023	2023-07-30	8	6	2	2025-01-30 21:00:39.70018
16	The Eras Tour	2024-08-01	6	4	3	2025-01-30 21:01:50.457947
17	1-800-TOUR	2024-06-22	6	4	2	2025-01-30 21:03:38.045121
\.


--
-- TOC entry 3509 (class 0 OID 24763)
-- Dependencies: 240
-- Data for Name: database_changes_log; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.database_changes_log (log_id, table_name, operation_type, record_id, old_values, new_values, changed_at) FROM stdin;
\.


--
-- TOC entry 3495 (class 0 OID 16532)
-- Dependencies: 226
-- Data for Name: locations; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.locations (location_id, name) FROM stdin;
1	Gdynia, Poland
2	Krakow, Poland
3	Prague, Czech Republic
4	Warsaw, Poland
5	Vienna, Austria
6	Łódź, Poland
\.


--
-- TOC entry 3489 (class 0 OID 16436)
-- Dependencies: 220
-- Data for Name: user_details; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.user_details (user_details_id, profile_picture, bio) FROM stdin;
1	admin_profile_picture.png	\N
5	679bde3d3e7c86.90341733.jpg	\N
6	679be9f3dd1017.67432434.jpg	\N
7	default_profile_picture.png	\N
8	default_profile_picture.png	\N
\.


--
-- TOC entry 3487 (class 0 OID 16405)
-- Dependencies: 218
-- Data for Name: user_types; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.user_types (user_type_id, type_name, description) FROM stdin;
1	regular	\N
2	admin	\N
\.


--
-- TOC entry 3491 (class 0 OID 16445)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.users (user_id, username, email, password_hash, user_type_id, user_details_id, created_at, updated_at) FROM stdin;
2	jkowalski	jkowalski@gmail.com	8f6de86901ba906047425ff9f71550dd	1	5	2025-01-30 18:42:58.92488	2025-01-30 20:17:01.280783
3	anna_nowak	anna.nowak@gmail.com	339a5558474c69b2792f4467dcc37255	1	6	2025-01-30 20:53:11.882201	2025-01-30 21:06:59.96805
1	admin	admin@admin.com	21232f297a57a5a743894a0e4a801fc3	2	1	2025-01-30 16:40:16.138169	2025-01-30 16:40:46.500197
5	john.snow	john.snow@gmail.com	a5391e96f8d48a62e8c85381df108e98	1	8	2025-01-30 22:32:29.604709	2025-01-30 22:32:29.604709
\.


--
-- TOC entry 3497 (class 0 OID 16541)
-- Dependencies: 228
-- Data for Name: venues; Type: TABLE DATA; Schema: public; Owner: root
--

COPY public.venues (venue_id, name) FROM stdin;
1	Lotnisko Gdynia-Kosakowo
2	Hypepark
3	Klub Studio
4	Tauron Arena Kraków
5	O2 Arena
6	PGE Narodowy
7	Ernst-Happel-Stadion
8	mBank Scena Główna
\.


--
-- TOC entry 3527 (class 0 OID 0)
-- Dependencies: 223
-- Name: artists_artist_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.artists_artist_id_seq', 14, true);


--
-- TOC entry 3528 (class 0 OID 0)
-- Dependencies: 231
-- Name: concert_artist_concert_artist_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.concert_artist_concert_artist_id_seq', 17, true);


--
-- TOC entry 3529 (class 0 OID 0)
-- Dependencies: 237
-- Name: concert_genre_genre_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.concert_genre_genre_id_seq', 3, true);


--
-- TOC entry 3530 (class 0 OID 0)
-- Dependencies: 235
-- Name: concert_picture_concert_picture_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.concert_picture_concert_picture_id_seq', 31, true);


--
-- TOC entry 3531 (class 0 OID 0)
-- Dependencies: 233
-- Name: concert_user_concert_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.concert_user_concert_user_id_seq', 17, true);


--
-- TOC entry 3532 (class 0 OID 0)
-- Dependencies: 229
-- Name: concerts_concert_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.concerts_concert_id_seq', 17, true);


--
-- TOC entry 3533 (class 0 OID 0)
-- Dependencies: 239
-- Name: database_changes_log_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.database_changes_log_log_id_seq', 1, false);


--
-- TOC entry 3534 (class 0 OID 0)
-- Dependencies: 225
-- Name: location_location_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.location_location_id_seq', 6, true);


--
-- TOC entry 3535 (class 0 OID 0)
-- Dependencies: 219
-- Name: user_details_detail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.user_details_detail_id_seq', 8, true);


--
-- TOC entry 3536 (class 0 OID 0)
-- Dependencies: 217
-- Name: user_types_user_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.user_types_user_type_id_seq', 2, true);


--
-- TOC entry 3537 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.users_user_id_seq', 5, true);


--
-- TOC entry 3538 (class 0 OID 0)
-- Dependencies: 227
-- Name: venue_venue_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('public.venue_venue_id_seq', 8, true);


--
-- TOC entry 3305 (class 2606 OID 16513)
-- Name: artists artists_artist_name_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.artists
    ADD CONSTRAINT artists_artist_name_key UNIQUE (name);


--
-- TOC entry 3307 (class 2606 OID 16511)
-- Name: artists artists_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.artists
    ADD CONSTRAINT artists_pkey PRIMARY KEY (artist_id);


--
-- TOC entry 3319 (class 2606 OID 16578)
-- Name: concert_artist concert_artist_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_artist
    ADD CONSTRAINT concert_artist_pkey PRIMARY KEY (concert_artist_id);


--
-- TOC entry 3325 (class 2606 OID 16624)
-- Name: concert_genre concert_genre_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_genre
    ADD CONSTRAINT concert_genre_pkey PRIMARY KEY (genre_id);


--
-- TOC entry 3323 (class 2606 OID 16612)
-- Name: concert_picture concert_picture_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_picture
    ADD CONSTRAINT concert_picture_pkey PRIMARY KEY (concert_picture_id);


--
-- TOC entry 3321 (class 2606 OID 16595)
-- Name: concert_user concert_user_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_user
    ADD CONSTRAINT concert_user_pkey PRIMARY KEY (concert_user_id);


--
-- TOC entry 3317 (class 2606 OID 16561)
-- Name: concerts concerts_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concerts
    ADD CONSTRAINT concerts_pkey PRIMARY KEY (concert_id);


--
-- TOC entry 3327 (class 2606 OID 24771)
-- Name: database_changes_log database_changes_log_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.database_changes_log
    ADD CONSTRAINT database_changes_log_pkey PRIMARY KEY (log_id);


--
-- TOC entry 3309 (class 2606 OID 16539)
-- Name: locations location_location_name_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT location_location_name_key UNIQUE (name);


--
-- TOC entry 3311 (class 2606 OID 16537)
-- Name: locations location_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT location_pkey PRIMARY KEY (location_id);


--
-- TOC entry 3297 (class 2606 OID 16443)
-- Name: user_details user_details_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.user_details
    ADD CONSTRAINT user_details_pkey PRIMARY KEY (user_details_id);


--
-- TOC entry 3293 (class 2606 OID 16412)
-- Name: user_types user_types_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.user_types
    ADD CONSTRAINT user_types_pkey PRIMARY KEY (user_type_id);


--
-- TOC entry 3295 (class 2606 OID 16414)
-- Name: user_types user_types_type_name_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.user_types
    ADD CONSTRAINT user_types_type_name_key UNIQUE (type_name);


--
-- TOC entry 3299 (class 2606 OID 16456)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3301 (class 2606 OID 16452)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- TOC entry 3303 (class 2606 OID 16454)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 3313 (class 2606 OID 16548)
-- Name: venues venue_name_key; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.venues
    ADD CONSTRAINT venue_name_key UNIQUE (name);


--
-- TOC entry 3315 (class 2606 OID 16546)
-- Name: venues venue_pkey; Type: CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.venues
    ADD CONSTRAINT venue_pkey PRIMARY KEY (venue_id);


--
-- TOC entry 3338 (class 2620 OID 24878)
-- Name: artists trigger_check_artist_name; Type: TRIGGER; Schema: public; Owner: root
--

CREATE TRIGGER trigger_check_artist_name BEFORE INSERT OR UPDATE ON public.artists FOR EACH ROW EXECUTE FUNCTION public.check_artist_name_not_empty();


--
-- TOC entry 3333 (class 2606 OID 16579)
-- Name: concert_artist concert_artist_artist_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_artist
    ADD CONSTRAINT concert_artist_artist_id_fkey FOREIGN KEY (artist_id) REFERENCES public.artists(artist_id) ON DELETE CASCADE;


--
-- TOC entry 3334 (class 2606 OID 16584)
-- Name: concert_artist concert_artist_concert_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_artist
    ADD CONSTRAINT concert_artist_concert_id_fkey FOREIGN KEY (concert_id) REFERENCES public.concerts(concert_id) ON DELETE CASCADE;


--
-- TOC entry 3337 (class 2606 OID 16613)
-- Name: concert_picture concert_picture_concert_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_picture
    ADD CONSTRAINT concert_picture_concert_id_fkey FOREIGN KEY (concert_id) REFERENCES public.concerts(concert_id) ON DELETE CASCADE;


--
-- TOC entry 3335 (class 2606 OID 16601)
-- Name: concert_user concert_user_concert_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_user
    ADD CONSTRAINT concert_user_concert_id_fkey FOREIGN KEY (concert_id) REFERENCES public.concerts(concert_id) ON DELETE CASCADE;


--
-- TOC entry 3336 (class 2606 OID 16596)
-- Name: concert_user concert_user_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concert_user
    ADD CONSTRAINT concert_user_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- TOC entry 3330 (class 2606 OID 16625)
-- Name: concerts concerts_genre_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concerts
    ADD CONSTRAINT concerts_genre_id_fkey FOREIGN KEY (genre_id) REFERENCES public.concert_genre(genre_id);


--
-- TOC entry 3331 (class 2606 OID 16567)
-- Name: concerts concerts_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concerts
    ADD CONSTRAINT concerts_location_id_fkey FOREIGN KEY (location_id) REFERENCES public.locations(location_id);


--
-- TOC entry 3332 (class 2606 OID 16562)
-- Name: concerts concerts_venue_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.concerts
    ADD CONSTRAINT concerts_venue_id_fkey FOREIGN KEY (venue_id) REFERENCES public.venues(venue_id);


--
-- TOC entry 3328 (class 2606 OID 16462)
-- Name: users users_user_details_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_user_details_id_fkey FOREIGN KEY (user_details_id) REFERENCES public.user_details(user_details_id) ON DELETE SET NULL;


--
-- TOC entry 3329 (class 2606 OID 16457)
-- Name: users users_user_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_user_type_id_fkey FOREIGN KEY (user_type_id) REFERENCES public.user_types(user_type_id) ON DELETE SET NULL;


-- Completed on 2025-01-30 22:43:49 UTC

--
-- PostgreSQL database dump complete
--

