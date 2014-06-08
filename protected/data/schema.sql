--
--
--
--

-- table for saving photo uploaded by user
CREATE TABLE photo
(
  id bigserial NOT NULL PRIMARY KEY,
  file_name varchar(256), -- full path to file
  url varchar(256), -- url that can be accessed by public
  width int, -- original width
  height int, -- original width
  upload_time timestamp DEFAULT NOW(), -- first upload
  update_time timestamp DEFAULT NOW()
);


CREATE TYPE user_status AS ENUM (
  'active', -- user is active
  'email-confirm', -- user has not confirmed email
  'email-changed', -- user changed email, and need confirmation
  'blocked' -- user is blocked
);

-- table containing user data
CREATE TABLE "user"
(
  id bigserial NOT NULL PRIMARY KEY,
  email varchar(64),
  password varchar(128),
  name varchar(32),
  about text,
  photo_id bigint REFERENCES photo(id) ON DELETE SET NULL,
  register_time timestamp DEFAULT now(),
  status user_status DEFAULT 'email-confirm',
  activation_code varchar (32),
  update_time timestamp DEFAULT NOW()
);


CREATE TABLE "profile"
(
  id bigserial NOT NULL PRIMARY KEY,
  user_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,
  prop_name varchar(32),
  prop_val varchar(256)
);


CREATE TABLE organization
(
  id bigserial NOT NULL PRIMARY KEY,
  name varchar(64),
  description varchar(128),
  info text,
  logo_id bigint REFERENCES photo(id) ON DELETE SET NULL,

  create_time timestamp DEFAULT now(),
  update_time timestamp DEFAULT NOW(),
  delete_time timestamp DEFAULT NULL
);


CREATE TABLE department
(
  id bigserial NOT NULL PRIMARY KEY,
  organization_id bigint NOT NULL REFERENCES organization (id) ON DELETE CASCADE,
  name varchar(64),
  description varchar(1024),

  create_time timestamp DEFAULT now(),
  update_time timestamp DEFAULT NOW(),
  delete_time timestamp DEFAULT NULL
);

CREATE TYPE role_type AS ENUM ('member', 'admin', 'super');
CREATE TYPE join_status AS ENUM ('invited', 'joint', 'archived');

CREATE TABLE role
(
  user_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,
  organization_id bigint NOT NULL REFERENCES organization (id) ON DELETE CASCADE,
  department_id bigint REFERENCES department (id) ON DELETE CASCADE DEFAULT NULL,
  type role_type DEFAULT 'member',
  position varchar(32),
  status join_status DEFAULT 'invited',
  join_time timestamp DEFAULT now(),

  PRIMARY KEY (user_id, organization_id)
);


CREATE TYPE event_type AS ENUM ('personal', 'department', 'admins', 'organization');

CREATE TABLE event
(
  id bigserial NOT NULL PRIMARY KEY,
  title varchar(64),
  description text,
  type event_type DEFAULT 'personal',
  owner_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,

  organization_id bigint REFERENCES organization (id) ON DELETE CASCADE, -- type=1,2,3
  department_id bigint REFERENCES department (id) ON DELETE CASCADE DEFAULT NULL, -- type 2

  create_time timestamp DEFAULT now(),
  update_time timestamp DEFAULT NOW(),
  delete_time timestamp DEFAULT NULL
);

CREATE TYPE event_vote_status AS ENUM ('close', 'open');

CREATE TABLE event_recurrence
(
  id bigserial NOT NULL PRIMARY KEY,
  event_id bigint NOT NULL REFERENCES "event" (id) ON DELETE CASCADE,
  vote_status event_vote_status NOT NULL DEFAULT 'close', -- 0: close, 1: open, 2:voted
  "date" date,
  begin_time time,
  end_time time,
  place varchar(256),

  create_time timestamp DEFAULT now(),
  update_time timestamp DEFAULT NOW(),
  delete_time timestamp DEFAULT NULL
);

CREATE TYPE attendance_status AS ENUM ('unknown', 'attend', 'not-attend');

CREATE TABLE event_attendance
(
  recurrence_id bigint NOT NULL REFERENCES "event_recurrence"(id) ON DELETE CASCADE,
  user_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,
  status attendance_status, -- 1: attend, 2: not attend
  comment text,
  update_time timestamp DEFAULT NOW(),

  PRIMARY KEY (recurrence_id, user_id)
);

CREATE TABLE event_time_options
(
  id bigserial NOT NULL PRIMARY KEY,
  recurrence_id bigint NOT NULL REFERENCES "event_recurrence"(id) ON DELETE CASCADE,
  "date" date NOT NULL,
  begin_time time NOT NULL,
  end_time time
);

CREATE TABLE event_vote
(
  option_id bigint NOT NULL REFERENCES "event_time_options"(id) ON DELETE CASCADE,
  user_id bigint NOT NULL REFERENCES "user"(id) ON DELETE CASCADE,
  comment text,

  PRIMARY KEY (option_id, user_id)
);


CREATE TYPE task_type AS ENUM ('personal', 'department');
CREATE TYPE task_priority AS ENUM ('low', 'medium', 'high');
CREATE TYPE task_status AS ENUM ('undone', 'done');

CREATE TABLE task
(
  id bigserial NOT NULL PRIMARY KEY,
  owner_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,
  title varchar(64) NOT NULL,
  description text,
  deadline timestamp NOT NULL,
  priority task_priority NOT NULL DEFAULT 'medium',
  status task_status NOT NULL DEFAULT 'undone',
  created timestamp DEFAULT now(),
  type task_type DEFAULT 'personal',

  organization_id bigint REFERENCES organization (id) ON DELETE CASCADE DEFAULT NULL,
  department_id bigint REFERENCES department (id) ON DELETE CASCADE DEFAULT NULL,

  create_time timestamp DEFAULT now(),
  update_time timestamp DEFAULT NOW(),
  delete_time timestamp DEFAULT NULL
);

-- only for department task
CREATE TABLE task_assign
(
  task_id bigint NOT NULL REFERENCES "task" (id) ON DELETE CASCADE,
  user_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,

  PRIMARY KEY (task_id, user_id)
);

CREATE TABLE task_progress
(
  id bigserial NOT NULL PRIMARY KEY,
  task_id bigint NOT NULL REFERENCES "task" (id) ON DELETE CASCADE,
  reporter_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,
  report_time timestamp DEFAULT now(),
  progress int,
  comment text,

  update_time timestamp DEFAULT NOW(),
  delete_time timestamp DEFAULT NULL
);

CREATE TABLE activity
(
  id bigserial NOT NULL PRIMARY KEY,
  user_id bigint NOT NULL REFERENCES "user" (id) ON DELETE CASCADE,
  "type" varchar(16) NOT NULL,
  datetime timestamp NOT NULL,
  organization_id bigint REFERENCES "organization"(id) ON DELETE CASCADE,
  department_id bigint REFERENCES "department"(id) ON DELETE CASCADE,
  recurrence_id bigint DEFAULT NULL REFERENCES event_recurrence(id) ON DELETE CASCADE,
  task_id bigint DEFAULT NULL REFERENCES task(id) ON DELETE CASCADE
);

----------------------------------------------------------------------------------------------
--
-- FUNCTIONS
--
----------------------------------------------------------------------------------------------


--
CREATE OR REPLACE FUNCTION combine_datetime(d DATE, t TIME ) RETURNS TIMESTAMP AS $BODY$
    BEGIN
      RETURN (d || ' ' || t)::TIMESTAMP;
    END;
$BODY$ LANGUAGE plpgsql;


-- list participant of the event
CREATE OR REPLACE  FUNCTION event_get_users_id(event) RETURNS setof bigint AS $event_get_users$
    DECLARE
      uid bigint ;
    BEGIN
      IF $1.type = 'personal' THEN
        RETURN NEXT $1.owner_id;
      ELSEIF $1.type = 'organization' THEN
        FOR uid in SELECT user_id FROM role WHERE organization_id = $1.organization_id loop
          return next uid;
        end loop;
      ELSEIF $1.type = 'department' THEN
        FOR uid in SELECT user_id FROM role WHERE department_id = $1.department_id loop
          return next uid;
        end loop;
      ELSEIF $1.type = 'admins' THEN
        FOR uid in SELECT user_id FROM role WHERE organization_id = $1.organization_id AND type IN ('admin', 'super') loop
          return next uid;
        end loop;
      END IF;
    END;
$event_get_users$ LANGUAGE plpgsql;



----------------------------------------------------------------------------------------------
--
-- TRIGGER
--
----------------------------------------------------------------------------------------------


CREATE OR REPLACE  FUNCTION role_trigger() RETURNS trigger AS $BODY$
DECLARE
  e event;
  r event_recurrence;

BEGIN
  IF (TG_OP = 'DELETE' OR TG_OP = 'UPDATE') AND OLD.status <> 'invited' THEN
    DELETE FROM activity WHERE user_id = OLD.user_id AND organization_id = OLD.organization_id;
  END IF;

  IF (TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND OLD.status = 'invited')) AND NEW.status <> 'invited' THEN
    FOR e IN SELECT * FROM event WHERE
      (type = 'organization' AND organization_id = NEW.organization_id) OR
      (type = 'department' AND  department_id = NEW.department_id) OR
      (type = 'admins' AND NEW.type <> 'member' AND  organization_id = NEW.organization_id)
    LOOP

      FOR r IN SELECT * FROM event_recurrence WHERE event_id = e.id         LOOP
        INSERT INTO activity (user_id, datetime, organization_id, department_id, recurrence_id, type) VALUES
        (NEW.user_id, combine_datetime(r.date, r.begin_time), e.organization_id, e.department_id, r.id, 'event-' || e.type);
      END LOOP;

    END LOOP;
  END IF;
  RETURN NEW;
END;
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER role_trigger BEFORE INSERT OR UPDATE OR DELETE ON role
FOR EACH ROW EXECUTE PROCEDURE role_trigger();



CREATE OR REPLACE FUNCTION event_recurrent_trigger() RETURNS trigger AS $event_recurrent_update$
    DECLARE
      uid bigint;
      e event;
      is_update boolean;
    BEGIN
      is_update = TG_OP = 'UPDATE' AND (OLD.date != NEW.date OR OLD.begin_time != NEW.begin_time);

      IF TG_OP = 'DELETE' OR is_update THEN
        DELETE FROM activity WHERE recurrence_id = OLD.id;
      END IF;

      IF TG_OP = 'INSERT' OR is_update THEN
        SELECT * from event WHERE id = NEW.event_id INTO e;
        FOR uid IN SELECT event_get_users_id(e) LOOP
          INSERT INTO activity (user_id, type, datetime, recurrence_id, organization_id, department_id)
            VALUES (uid, 'event-' || e.type, combine_datetime (NEW.date, NEW.begin_time), NEW.id, e.organization_id, e.department_id);
        END LOOP;
      END IF;

      RETURN NEW;
    END;
$event_recurrent_update$ LANGUAGE plpgsql;

CREATE TRIGGER event_recurrent_trigger AFTER INSERT OR UPDATE ON event_recurrence
FOR EACH ROW EXECUTE PROCEDURE event_recurrent_trigger();


CREATE OR REPLACE  FUNCTION task_trigger() RETURNS trigger AS $BODY$
    DECLARE
      uid bigint;

    BEGIN
      IF TG_OP = 'DELETE' THEN
        DELETE FROM activity WHERE task_id = OLD.id;

      ELSEIF TG_OP = 'UPDATE' THEN
        IF OLD.deadline != NEW.deadline THEN
          UPDATE activity SET datetime = NEW.deadline WHERE task_id = NEW.id;
        END IF;

      ELSEIF TG_OP = 'INSERT' THEN
        IF NEW.type = 'department' THEN -- departemnt task
          FOR uid IN SELECT user_id FROM task_assign WHERE task_id = NEW.id LOOP
            INSERT INTO activity (user_id, type, datetime, task_id, organization_id, department_id)
              VALUES (uid, 'task-' || NEW.type, NEW.deadline, NEW.id, NEW.organization_id, NEW.department_id);
          END LOOP ;
        ELSE
          INSERT INTO activity (user_id, type, datetime, task_id) VALUES
            (NEW.owner_id, 'task-' || NEW.type, NEW.deadline, NEW.id);
        END IF;
      END IF;

      RETURN NEW;
    END;
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER task_trigger AFTER INSERT OR UPDATE OR DELETE ON task
FOR EACH ROW EXECUTE PROCEDURE task_trigger();



CREATE OR REPLACE  FUNCTION task_assign_trigger() RETURNS trigger AS $BODY$
    DECLARE
      oid bigint;
      t task;

    BEGIN
      IF TG_OP = 'DELETE' THEN
        DELETE FROM activity WHERE task_id = OLD.task_id AND user_id = OLD.user_id;

      ELSEIF TG_OP = 'INSERT' THEN
        SELECT * FROM task WHERE id = NEW.task_id INTO t;

        INSERT INTO activity (user_id, type, datetime, task_id, organization_id, department_id)
          VALUES (NEW.user_id, 'task-' || t.type , t.deadline, t.id, t.organization_id, t.department_id);

      END IF;

      RETURN NEW;
    END;
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER task_assign_trigger AFTER INSERT OR DELETE ON task_assign
FOR EACH ROW EXECUTE PROCEDURE task_assign_trigger();


