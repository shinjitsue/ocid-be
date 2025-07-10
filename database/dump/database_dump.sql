--
-- PostgreSQL database dump
--

-- Dumped from database version 14.4
-- Dumped by pg_dump version 14.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE IF EXISTS ocid_be;
--
-- Name: ocid_be; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE ocid_be WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'English_Philippines.1252';


ALTER DATABASE ocid_be OWNER TO postgres;

\connect ocid_be

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: campuses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.campuses (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    address character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.campuses OWNER TO postgres;

--
-- Name: campuses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.campuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.campuses_id_seq OWNER TO postgres;

--
-- Name: campuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.campuses_id_seq OWNED BY public.campuses.id;


--
-- Name: colleges; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.colleges (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    campus_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.colleges OWNER TO postgres;

--
-- Name: colleges_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.colleges_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.colleges_id_seq OWNER TO postgres;

--
-- Name: colleges_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.colleges_id_seq OWNED BY public.colleges.id;


--
-- Name: curriculum; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.curriculum (
    id bigint NOT NULL,
    image_url character varying(255) NOT NULL,
    program_id bigint NOT NULL,
    program_type character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT curriculum_program_type_check CHECK (((program_type)::text = ANY ((ARRAY['graduate'::character varying, 'undergrad'::character varying])::text[])))
);


ALTER TABLE public.curriculum OWNER TO postgres;

--
-- Name: curriculum_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.curriculum_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.curriculum_id_seq OWNER TO postgres;

--
-- Name: curriculum_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.curriculum_id_seq OWNED BY public.curriculum.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: forms; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.forms (
    id bigint NOT NULL,
    form_number character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    purpose text NOT NULL,
    link character varying(255) NOT NULL,
    revision character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.forms OWNER TO postgres;

--
-- Name: forms_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.forms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.forms_id_seq OWNER TO postgres;

--
-- Name: forms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.forms_id_seq OWNED BY public.forms.id;


--
-- Name: graduates; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.graduates (
    id bigint NOT NULL,
    program_name character varying(255) NOT NULL,
    college_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.graduates OWNER TO postgres;

--
-- Name: graduates_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.graduates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.graduates_id_seq OWNER TO postgres;

--
-- Name: graduates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.graduates_id_seq OWNED BY public.graduates.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: syllabus; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.syllabus (
    id bigint NOT NULL,
    image_url character varying(255) NOT NULL,
    program_id bigint NOT NULL,
    program_type character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT syllabus_program_type_check CHECK (((program_type)::text = ANY ((ARRAY['graduate'::character varying, 'undergrad'::character varying])::text[])))
);


ALTER TABLE public.syllabus OWNER TO postgres;

--
-- Name: syllabus_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.syllabus_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.syllabus_id_seq OWNER TO postgres;

--
-- Name: syllabus_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.syllabus_id_seq OWNED BY public.syllabus.id;


--
-- Name: undergrads; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.undergrads (
    id bigint NOT NULL,
    program_name character varying(255) NOT NULL,
    college_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.undergrads OWNER TO postgres;

--
-- Name: undergrads_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.undergrads_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.undergrads_id_seq OWNER TO postgres;

--
-- Name: undergrads_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.undergrads_id_seq OWNED BY public.undergrads.id;


--
-- Name: user_activities; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_activities (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    activity_type character varying(255) NOT NULL,
    ip_address character varying(45),
    user_agent text,
    metadata json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_activities OWNER TO postgres;

--
-- Name: user_activities_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_activities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_activities_id_seq OWNER TO postgres;

--
-- Name: user_activities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_activities_id_seq OWNED BY public.user_activities.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    avatar character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    last_login_at timestamp(0) without time zone,
    last_login_ip character varying(45),
    preferences json,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: campuses id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.campuses ALTER COLUMN id SET DEFAULT nextval('public.campuses_id_seq'::regclass);


--
-- Name: colleges id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.colleges ALTER COLUMN id SET DEFAULT nextval('public.colleges_id_seq'::regclass);


--
-- Name: curriculum id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.curriculum ALTER COLUMN id SET DEFAULT nextval('public.curriculum_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: forms id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.forms ALTER COLUMN id SET DEFAULT nextval('public.forms_id_seq'::regclass);


--
-- Name: graduates id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.graduates ALTER COLUMN id SET DEFAULT nextval('public.graduates_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: syllabus id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.syllabus ALTER COLUMN id SET DEFAULT nextval('public.syllabus_id_seq'::regclass);


--
-- Name: undergrads id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.undergrads ALTER COLUMN id SET DEFAULT nextval('public.undergrads_id_seq'::regclass);


--
-- Name: user_activities id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_activities ALTER COLUMN id SET DEFAULT nextval('public.user_activities_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: campuses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.campuses (id, name, address, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: colleges; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.colleges (id, name, campus_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: curriculum; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.curriculum (id, image_url, program_id, program_type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: forms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.forms (id, form_number, title, purpose, link, revision, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: graduates; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.graduates (id, program_name, college_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_07_07_055213_create_campuses_table	1
5	2025_07_07_055450_create_colleges_table	1
6	2025_07_07_055606_create_forms_table	1
7	2025_07_07_055617_create_undergrads_table	1
8	2025_07_07_055633_create_graduates_table	1
9	2025_07_07_055706_create_curriculum_table	1
10	2025_07_07_055726_create_syllabus_table	1
11	2025_07_08_064151_create_personal_access_tokens_table	1
12	2025_07_08_073420_create_user_activities_table	1
13	2025_07_09_041738_add_deleted_at_to_users_table	2
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
4	App\\Models\\User	2	Unknown Device	520706bf9290b38d5b6b12e841b43447928197143529d89d27b73058f6cd546d	["*"]	\N	2025-07-10 05:34:00	2025-07-09 05:34:00	2025-07-09 05:34:00
7	App\\Models\\User	3	Unknown Device	ad77dda677f626e8848cb83cf550e26ecf98d268a734acede2480e0457fea33d	["*"]	\N	2025-07-11 06:00:08	2025-07-10 06:00:08	2025-07-10 06:00:08
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
VmWhEiPVcQPI3AVObNNmxBlis36UEXlXzQBsFpOO	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0	YTozOntzOjY6Il90b2tlbiI7czo0MDoiNGMwQXBSelFtQm9GY2NKR1FHSzVNZmJ1VHNTSUpmYWFORWE5NzA3SiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1752030952
\.


--
-- Data for Name: syllabus; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.syllabus (id, image_url, program_id, program_type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: undergrads; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.undergrads (id, program_name, college_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: user_activities; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_activities (id, user_id, activity_type, ip_address, user_agent, metadata, created_at, updated_at) FROM stdin;
1	2	login	127.0.0.1	PostmanRuntime/7.44.1	{"remember_me":false,"timestamp":"2025-07-09T04:36:00.987123Z"}	2025-07-09 04:36:00	2025-07-09 04:36:00
2	2	logout	127.0.0.1	PostmanRuntime/7.44.1	{"timestamp":"2025-07-09T04:52:51.884184Z"}	2025-07-09 04:52:51	2025-07-09 04:52:51
3	2	login	127.0.0.1	PostmanRuntime/7.44.1	{"remember_me":false,"timestamp":"2025-07-09T04:56:09.191573Z"}	2025-07-09 04:56:09	2025-07-09 04:56:09
4	2	logout	127.0.0.1	PostmanRuntime/7.44.1	{"timestamp":"2025-07-09T05:07:21.739763Z"}	2025-07-09 05:07:21	2025-07-09 05:07:21
5	2	login	127.0.0.1	PostmanRuntime/7.44.1	{"remember_me":false,"timestamp":"2025-07-09T05:13:45.506702Z"}	2025-07-09 05:13:45	2025-07-09 05:13:45
6	2	password_change	127.0.0.1	PostmanRuntime/7.44.1	{"timestamp":"2025-07-09T05:16:40.371697Z"}	2025-07-09 05:16:40	2025-07-09 05:16:40
7	2	logout	127.0.0.1	PostmanRuntime/7.44.1	{"timestamp":"2025-07-09T05:33:30.546213Z"}	2025-07-09 05:33:30	2025-07-09 05:33:30
8	2	login	127.0.0.1	PostmanRuntime/7.44.1	{"remember_me":false,"timestamp":"2025-07-09T05:34:00.106479Z"}	2025-07-09 05:34:00	2025-07-09 05:34:00
9	3	register	127.0.0.1	PostmanRuntime/7.44.1	{"timestamp":"2025-07-10T05:57:04.605647Z"}	2025-07-10 05:57:04	2025-07-10 05:57:04
10	3	login	127.0.0.1	PostmanRuntime/7.44.1	{"remember_me":false,"timestamp":"2025-07-10T05:58:09.058515Z"}	2025-07-10 05:58:09	2025-07-10 05:58:09
11	3	password_change	127.0.0.1	PostmanRuntime/7.44.1	{"timestamp":"2025-07-10T05:59:01.696802Z"}	2025-07-10 05:59:01	2025-07-10 05:59:01
12	3	logout	127.0.0.1	PostmanRuntime/7.44.1	{"timestamp":"2025-07-10T05:59:27.758532Z"}	2025-07-10 05:59:27	2025-07-10 05:59:27
13	3	login	127.0.0.1	PostmanRuntime/7.44.1	{"remember_me":false,"timestamp":"2025-07-10T06:00:08.770626Z"}	2025-07-10 06:00:08	2025-07-10 06:00:08
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, avatar, is_active, last_login_at, last_login_ip, preferences, remember_token, created_at, updated_at, deleted_at) FROM stdin;
2	John Doe	john.doe@example.com	\N	$2y$12$LhQjpVi57pWloashwHgm3uV52CUDbDIW11uJQV3qaHJhrK9.6Edpq	\N	t	\N	\N	\N	\N	2025-07-09 04:29:08	2025-07-09 05:16:40	\N
3	Sample Doe	sample.doe@example.com	\N	$2y$12$VZZvqouK3Os6X29BipcbfONlPig1eCvZshPJJuJRsJ8vFzKgiG6q.	\N	t	\N	\N	\N	\N	2025-07-10 05:56:39	2025-07-10 05:59:01	\N
\.


--
-- Name: campuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.campuses_id_seq', 1, false);


--
-- Name: colleges_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.colleges_id_seq', 1, false);


--
-- Name: curriculum_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.curriculum_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: forms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.forms_id_seq', 1, false);


--
-- Name: graduates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.graduates_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 13, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 7, true);


--
-- Name: syllabus_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.syllabus_id_seq', 1, false);


--
-- Name: undergrads_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.undergrads_id_seq', 1, false);


--
-- Name: user_activities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_activities_id_seq', 13, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: campuses campuses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.campuses
    ADD CONSTRAINT campuses_pkey PRIMARY KEY (id);


--
-- Name: colleges colleges_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.colleges
    ADD CONSTRAINT colleges_pkey PRIMARY KEY (id);


--
-- Name: curriculum curriculum_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.curriculum
    ADD CONSTRAINT curriculum_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: forms forms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.forms
    ADD CONSTRAINT forms_pkey PRIMARY KEY (id);


--
-- Name: graduates graduates_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.graduates
    ADD CONSTRAINT graduates_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: syllabus syllabus_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.syllabus
    ADD CONSTRAINT syllabus_pkey PRIMARY KEY (id);


--
-- Name: undergrads undergrads_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.undergrads
    ADD CONSTRAINT undergrads_pkey PRIMARY KEY (id);


--
-- Name: user_activities user_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_activities
    ADD CONSTRAINT user_activities_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: user_activities_created_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX user_activities_created_at_index ON public.user_activities USING btree (created_at);


--
-- Name: user_activities_user_id_activity_type_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX user_activities_user_id_activity_type_index ON public.user_activities USING btree (user_id, activity_type);


--
-- Name: users_last_login_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX users_last_login_at_index ON public.users USING btree (last_login_at);


--
-- Name: colleges colleges_campus_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.colleges
    ADD CONSTRAINT colleges_campus_id_foreign FOREIGN KEY (campus_id) REFERENCES public.campuses(id);


--
-- Name: graduates graduates_college_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.graduates
    ADD CONSTRAINT graduates_college_id_foreign FOREIGN KEY (college_id) REFERENCES public.colleges(id);


--
-- Name: undergrads undergrads_college_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.undergrads
    ADD CONSTRAINT undergrads_college_id_foreign FOREIGN KEY (college_id) REFERENCES public.colleges(id);


--
-- Name: user_activities user_activities_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_activities
    ADD CONSTRAINT user_activities_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

