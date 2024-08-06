create table users
(
    id          VARCHAR(255) PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    password    VARCHAR(255) NOT NULL
) ENGINE InnoDB;

create table sessions
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id     VARCHAR(255) NOT NULL
) ENGINE InnoDB;

ALTER TABLE sessions
ADD CONSTRAINT fk_session_user
    FOREIGN KEY (user_id)
        REFERENCES users(id);

show tables;

describe sessions;

describe users;