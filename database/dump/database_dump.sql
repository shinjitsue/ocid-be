--
-- PostgreSQL database dump
--

\restrict 282GlI9pGBW7VdkNPaAaqfzhmbxOH7hUzuhxcvbHEzl6IPzMjxbow9M9S56rrtU

-- Dumped from database version 17.6 (Debian 17.6-1)
-- Dumped by pg_dump version 18.0 (Debian 18.0-1)

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

DROP DATABASE IF EXISTS ocid_be;
--
-- Name: ocid_be; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE ocid_be WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.UTF-8';


ALTER DATABASE ocid_be OWNER TO postgres;

\unrestrict 282GlI9pGBW7VdkNPaAaqfzhmbxOH7hUzuhxcvbHEzl6IPzMjxbow9M9S56rrtU
\connect ocid_be
\restrict 282GlI9pGBW7VdkNPaAaqfzhmbxOH7hUzuhxcvbHEzl6IPzMjxbow9M9S56rrtU

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
    updated_at timestamp(0) without time zone,
    acronym character varying(10)
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


ALTER SEQUENCE public.campuses_id_seq OWNER TO postgres;

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
    updated_at timestamp(0) without time zone,
    acronym character varying(10),
    logo_path character varying(255),
    logo_url character varying(255),
    logo_name character varying(255),
    logo_type character varying(255),
    logo_size integer
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


ALTER SEQUENCE public.colleges_id_seq OWNER TO postgres;

--
-- Name: colleges_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.colleges_id_seq OWNED BY public.colleges.id;


--
-- Name: curriculum; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.curriculum (
    id bigint NOT NULL,
    program_id bigint NOT NULL,
    program_type character varying(255) NOT NULL,
    file_path character varying(255),
    file_url character varying(255),
    file_name character varying(255),
    file_type character varying(255),
    file_size bigint,
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


ALTER SEQUENCE public.curriculum_id_seq OWNER TO postgres;

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


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: faqs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.faqs (
    id bigint NOT NULL,
    question character varying(255) NOT NULL,
    answer text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.faqs OWNER TO postgres;

--
-- Name: faqs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.faqs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.faqs_id_seq OWNER TO postgres;

--
-- Name: faqs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.faqs_id_seq OWNED BY public.faqs.id;


--
-- Name: forms; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.forms (
    id bigint NOT NULL,
    form_number character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    purpose text NOT NULL,
    link text,
    revision character varying(255),
    file_path character varying(255),
    file_url character varying(255),
    file_name character varying(255),
    file_type character varying(255),
    file_size bigint,
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


ALTER SEQUENCE public.forms_id_seq OWNER TO postgres;

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
    updated_at timestamp(0) without time zone,
    acronym character varying(10)
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


ALTER SEQUENCE public.graduates_id_seq OWNER TO postgres;

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


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

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


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

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


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

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
    program_id bigint NOT NULL,
    program_type character varying(255) NOT NULL,
    file_path character varying(255),
    file_url character varying(255),
    file_name character varying(255),
    file_type character varying(255),
    file_size bigint,
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


ALTER SEQUENCE public.syllabus_id_seq OWNER TO postgres;

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
    updated_at timestamp(0) without time zone,
    acronym character varying(10)
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


ALTER SEQUENCE public.undergrads_id_seq OWNER TO postgres;

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


ALTER SEQUENCE public.user_activities_id_seq OWNER TO postgres;

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


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

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
-- Name: faqs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faqs ALTER COLUMN id SET DEFAULT nextval('public.faqs_id_seq'::regclass);


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

COPY public.campuses (id, name, address, created_at, updated_at, acronym) FROM stdin;
1	Caraga State University - Main Campus	Ampayon, Butuan City, Agusan del Norte, Philippines	2025-07-29 10:32:55	2025-07-29 10:32:55	CSU-MAIN
2	Caraga State University - Cabadbaran Campus	Cabadbaran City, Agusan del Norte, Philippines	2025-07-29 10:32:55	2025-07-29 10:32:55	CSU-CC
\.


--
-- Data for Name: colleges; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.colleges (id, name, campus_id, created_at, updated_at, acronym, logo_path, logo_url, logo_name, logo_type, logo_size) FROM stdin;
2	College of Engineering and Information Technology	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CEIT	\N	\N	\N	\N	\N
3	College of Mathematics and Natural Sciences	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CMNS	\N	\N	\N	\N	\N
4	College of Teacher Education	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CTE	\N	\N	\N	\N	\N
5	College of Business and Management	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CBM	\N	\N	\N	\N	\N
6	College of Arts and Social Sciences	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CASS	\N	\N	\N	\N	\N
7	College of Agriculture and Natural Resources	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CANR	\N	\N	\N	\N	\N
8	College of Forestry and Environmental Sciences	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CFES	\N	\N	\N	\N	\N
9	College of Medicine	1	2025-07-29 10:57:39	2025-07-29 10:57:39	COM	\N	\N	\N	\N	\N
10	College of Nursing	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CON	\N	\N	\N	\N	\N
11	College of Public Administration and Governance	1	2025-07-29 10:57:39	2025-07-29 10:57:39	CPAG	\N	\N	\N	\N	\N
12	Graduate School	1	2025-07-29 10:57:39	2025-07-29 10:57:39	GS	\N	\N	\N	\N	\N
13	College of Teacher Education - Cabadbaran	2	2025-07-29 10:57:39	2025-07-29 10:57:39	CTE-CC	\N	\N	\N	\N	\N
14	College of Business and Management - Cabadbaran	2	2025-07-29 10:57:39	2025-07-29 10:57:39	CBM-CC	\N	\N	\N	\N	\N
15	College of Engineering and Information Technology - Cabadbaran	2	2025-07-29 10:57:39	2025-07-29 10:57:39	CEIT-CC	\N	\N	\N	\N	\N
16	College of Arts and Social Sciences - Cabadbaran	2	2025-07-29 10:57:39	2025-07-29 10:57:39	CASS-CC	\N	\N	\N	\N	\N
17	College of Agriculture and Natural Resources - Cabadbaran	2	2025-07-29 10:57:39	2025-07-29 10:57:39	CANR-CC	\N	\N	\N	\N	\N
\.


--
-- Data for Name: curriculum; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.curriculum (id, program_id, program_type, file_path, file_url, file_name, file_type, file_size, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: faqs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.faqs (id, question, answer, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: forms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.forms (id, form_number, title, purpose, link, revision, file_path, file_url, file_name, file_type, file_size, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: graduates; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.graduates (id, program_name, college_id, created_at, updated_at, acronym) FROM stdin;
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
13	2025_07_09_041738_add_deleted_at_to_users_table	1
14	2025_07_18_071350_add_acronym_to_colleges_table	1
15	2025_07_18_081216_add_acronym_to_campuses_table	1
16	2025_07_18_135943_add_logo_to_colleges_table	1
17	2025_07_19_004207_add_acronym_to_graduates_table	1
18	2025_07_19_004315_add_acronym_to_undergrads_table	1
19	2025_07_21_023236_create_faqs_table	1
20	2025_07_23_011424_add_performance_indexes_to_tables	1
21	2025_07_23_022721_update_performance_indexes_to_tables	1
22	2025_07_23_031101_add_optimized_performance_indexe	1
23	2025_08_11_140731_update_forms_link_column_to_text	1
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
3	App\\Models\\User	1	Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0	6d04ee3ed598de4d32a67105ab83abdd19c4cb6e62017d087c7339b599b42002	["*"]	2025-07-30 07:27:26	2025-07-31 03:05:57	2025-07-30 03:05:57	2025-07-30 07:27:26
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: syllabus; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.syllabus (id, program_id, program_type, file_path, file_url, file_name, file_type, file_size, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: undergrads; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.undergrads (id, program_name, college_id, created_at, updated_at, acronym) FROM stdin;
1	daskjdkashjkdahskjdha	2	2025-07-30 01:14:20	2025-07-30 01:14:20	CSJS
\.


--
-- Data for Name: user_activities; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_activities (id, user_id, activity_type, ip_address, user_agent, metadata, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, avatar, is_active, last_login_at, last_login_ip, preferences, remember_token, created_at, updated_at, deleted_at) FROM stdin;
1	Ren Doe	sample.doe@example.com	\N	$2y$12$UHmohB5X4/UHbmwlqSMld.nmjdV.FvaI97m5KARlhYgfARRm1CWB.	\N	t	\N	\N	\N	\N	2025-07-29 09:46:46	2025-07-29 09:46:46	\N
\.


--
-- Name: campuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.campuses_id_seq', 2, true);


--
-- Name: colleges_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.colleges_id_seq', 21, true);


--
-- Name: curriculum_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.curriculum_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: faqs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.faqs_id_seq', 1, false);


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

SELECT pg_catalog.setval('public.migrations_id_seq', 22, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 3, true);


--
-- Name: syllabus_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.syllabus_id_seq', 1, false);


--
-- Name: undergrads_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.undergrads_id_seq', 1, true);


--
-- Name: user_activities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_activities_id_seq', 4, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


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
-- Name: faqs faqs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.faqs
    ADD CONSTRAINT faqs_pkey PRIMARY KEY (id);


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
-- Name: campuses_acronym_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campuses_acronym_index ON public.campuses USING btree (acronym);


--
-- Name: colleges_acronym_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX colleges_acronym_index ON public.colleges USING btree (acronym);


--
-- Name: graduates_acronym_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX graduates_acronym_index ON public.graduates USING btree (acronym);


--
-- Name: idx_colleges_acronym_campus; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_colleges_acronym_campus ON public.colleges USING btree (acronym, campus_id);


--
-- Name: idx_colleges_covering; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_colleges_covering ON public.colleges USING btree (id, name, acronym, campus_id, logo_url, created_at);


--
-- Name: idx_colleges_name; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_colleges_name ON public.colleges USING btree (name);


--
-- Name: idx_curriculum_file_type; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_curriculum_file_type ON public.curriculum USING btree (file_type);


--
-- Name: idx_curriculum_graduate; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_curriculum_graduate ON public.curriculum USING btree (program_id, created_at) WHERE ((program_type)::text = 'graduate'::text);


--
-- Name: idx_curriculum_program_full; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_curriculum_program_full ON public.curriculum USING btree (program_id, program_type, created_at);


--
-- Name: idx_curriculum_type_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_curriculum_type_created ON public.curriculum USING btree (program_type, created_at);


--
-- Name: idx_curriculum_type_program; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_curriculum_type_program ON public.curriculum USING btree (program_type, program_id);


--
-- Name: idx_curriculum_undergrad; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_curriculum_undergrad ON public.curriculum USING btree (program_id, created_at) WHERE ((program_type)::text = 'undergrad'::text);


--
-- Name: idx_graduates_acronym; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_graduates_acronym ON public.graduates USING btree (acronym);


--
-- Name: idx_graduates_covering; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_graduates_covering ON public.graduates USING btree (id, program_name, acronym, college_id, created_at);


--
-- Name: idx_graduates_program_name; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_graduates_program_name ON public.graduates USING btree (program_name);


--
-- Name: idx_syllabus_file_type; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_syllabus_file_type ON public.syllabus USING btree (file_type);


--
-- Name: idx_syllabus_graduate; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_syllabus_graduate ON public.syllabus USING btree (program_id, created_at) WHERE ((program_type)::text = 'graduate'::text);


--
-- Name: idx_syllabus_program_full; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_syllabus_program_full ON public.syllabus USING btree (program_id, program_type, created_at);


--
-- Name: idx_syllabus_type_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_syllabus_type_created ON public.syllabus USING btree (program_type, created_at);


--
-- Name: idx_syllabus_type_program; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_syllabus_type_program ON public.syllabus USING btree (program_type, program_id);


--
-- Name: idx_syllabus_undergrad; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_syllabus_undergrad ON public.syllabus USING btree (program_id, created_at) WHERE ((program_type)::text = 'undergrad'::text);


--
-- Name: idx_undergrads_acronym; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_undergrads_acronym ON public.undergrads USING btree (acronym);


--
-- Name: idx_undergrads_covering; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_undergrads_covering ON public.undergrads USING btree (id, program_name, acronym, college_id, created_at);


--
-- Name: idx_undergrads_program_name; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_undergrads_program_name ON public.undergrads USING btree (program_name);


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
-- Name: undergrads_acronym_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX undergrads_acronym_index ON public.undergrads USING btree (acronym);


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

\unrestrict 282GlI9pGBW7VdkNPaAaqfzhmbxOH7hUzuhxcvbHEzl6IPzMjxbow9M9S56rrtU

