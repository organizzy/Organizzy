

INSERT INTO "user" (id, email, password, name, about) VALUES
  (100, 'demo@demo.com', '$1$ITmgEqUA$.sBUwbaY7PQmHhzK975Pf1', 'Demo User', 'Organizzy Demo User'),
  (101, 'user1@demo.com', '$1$ITmgEqUA$.sBUwbaY7PQmHhzK975Pf1', 'Test User 1', 'Organizzy Demo User'),
  (102, 'user2@demo.com', '$1$ITmgEqUA$.sBUwbaY7PQmHhzK975Pf1', 'Test User 2', 'Organizzy Demo User'),
  (103, 'user3@demo.com', '$1$ITmgEqUA$.sBUwbaY7PQmHhzK975Pf1', 'Test User 3', 'Organizzy Demo User')

;

INSERT INTO "profile" (user_id, prop_name, prop_val) VALUES
  (100, 'birth-date', '1990-01-01'), (100, 'job', 'Tester'), (100, 'company', 'Organizzy');

INSERT INTO "organization" (id, name, description, info) VALUES
  (1000, 'Test', 'Test Organization', 'Describe vision, mission, and value of organization'),
  (1001, 'Other', 'Other Committee', 'Describe vision, mission, and value of committee');

INSERT INTO "department" (id, organization_id, name, description) VALUES
  (2001, 1000, 'Department 1', 'Test Department 1'),
  (2002, 1000, 'Department 2', 'Test Department 2');

INSERT INTO "role" (user_id, organization_id, department_id, type, position) VALUES
  (100, 1000, null, 'super', 'Owner');


INSERT INTO "event" (id, title, description, type, owner_id, organization_id, department_id) VALUES
  (1001, 'Personal Event', 'Test personal Event', 'personal', 100, NULL, NULL),
  (1002, 'Organization Event', 'Test organization Event', 'organization', 100, 1000, NULL),
  (1003, 'Admin Only Event', 'Test admin-only event', 'admins', 100, 1000, NULL),
  (1004, 'Department Event', 'Test department event', 'admins', 100, 1000, 2001);

INSERT INTO "event_recurrence" (event_id, date, begin_time, end_time) VALUES
  (1001, '2014-01-01', '10:00:00', '12:00:00'),
  (1002, '2014-01-01', '16:00:00', '18:00:00'),
  (1003, '2014-01-02', '20:00:00', '24:00:00'),
  (1003, '2014-01-09', '20:00:00', '24:00:00'),
  (1004, '2014-01-03', '10:00:00', '14:00:00');

INSERT INTO "task" (id, owner_id, type, title, description, deadline, department_id) VALUES
  (1001, 100, 'personal', 'Personal Task', 'Test personal task', '2014-01-02 12:00:00', NULL),
  (1002, 100, 'department', 'Department Task', 'Test department task', '2014-01-03 23:59:59', 2002);
