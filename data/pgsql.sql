CREATE TABLE users (
    id bigserial UNIQUE,
    username varchar(60),
    password varchar(500) NOT NULL,
    email varchar(100),
    udatetime timestamp NOT NULL DEFAULT now(),
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id, username, email)
);

CREATE TABLE profile (
    id bigserial,
    uid bigint NOT NULL REFERENCES users (id),
    birthdate date,
    sex varchar(1),
    country varchar(100),
    city varchar(100),
    udatetime timestamp NOT NULL DEFAULT now(),
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);

CREATE TABLE uconnection (
    id bigserial UNIQUE,
    user_one_id bigint REFERENCES users (id),
    user_two_id bigint REFERENCES users (id),
    is_blocked bool DEFAULT FALSE,
    ucon_id bigint,
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);

CREATE TABLE message (
    id bigserial,
    sender_id bigint REFERENCES users (id),
    receiver_id bigint REFERENCES users (id),
    exchange_id bigint REFERENCES uconnection (id),
    status smallint,
    is_read bool DEFAULT FALSE,
    udatetime timestamp NOT NULL DEFAULT now(),
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);

CREATE TABLE contact (
    id bigserial,
    uid bigint REFERENCES users (id),
    full_name varchar(100),
    email varchar(100),
    message text NOT NULL,
    net_id text,
    is_read bool NOT NULL DEFAULT FALSE,
    is_active bool NOT NULL DEFAULT TRUE,
    udatetime timestamp NOT NULL DEFAULT now(),
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);

CREATE TABLE content_type (
    id bigserial UNIQUE,
    label varchar(100) NOT NULL,
    table_id varchar(100),
    is_active bool NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id, label)
);

INSERT INTO content_type (id, label, table_id) VALUES (1, 'Discussion', 'discussion');
INSERT INTO content_type (id, label, table_id) VALUES (2, 'Chat', 'chat');
INSERT INTO content_type (id, label, table_id) VALUES (3, 'Gallery', 'gallery');

CREATE TABLE content_group (
    id bigserial UNIQUE,
    typeid int REFERENCES content_type (id),
    label varchar(500) NOT NULL,
    description varchar(1000),
    is_active bool NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id)
);

INSERT INTO content_group (typeid, label, description) VALUES (1, 'Sport', 'World Sport');
INSERT INTO content_group (typeid, label, description) VALUES (1, 'Entertainment', 'Entertainment News');
INSERT INTO content_group (typeid, label, description) VALUES (1, 'Politricks', 'Politics World');
INSERT INTO content_group (typeid, label, description) VALUES (1, 'Terrorism', 'Changing the World');
INSERT INTO content_group (typeid, label, description) VALUES (2, 'Main', 'Main General Chat');
INSERT INTO content_group (typeid, label, description) VALUES (2, 'Troll', 'Trolls Meet');


CREATE TABLE chat (
    id bigserial,
    uid bigint NOT NULL REFERENCES users (id),
    message text NOT NULL,
    room_id int NOT NULL REFERENCES content_group (id),
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);

CREATE TABLE discussion (
    id bigserial,
    uid bigint NOT NULL REFERENCES users (id),
    topic varchar(500) NOT NULL,
    body text,
    groupid int REFERENCES content_group (id),
    is_active bool NOT NULL DEFAULT TRUE,
    udatetime timestamp NOT NULL DEFAULT now(),
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);

CREATE TABLE comments (
    id bigserial,
    uid bigint REFERENCES users (id),
    body text,
    item_id bigint NOT NULL,
    typeid int NOT NULL REFERENCES content_type (id),
    is_active bool NOT NULL DEFAULT TRUE,
    udatetime timestamp NOT NULL DEFAULT now(),
    datetime timestamp NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);
