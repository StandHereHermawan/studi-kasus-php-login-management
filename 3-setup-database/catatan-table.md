### Buat 2 Table di masing-masing database.

1. _Tabel user_
    -   1. Berisi kolom id
    -   2. Berisi kolom nama
    -   3. Berisi kolom password

```
create table users
(
    id          VARCHAR(255) PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    password    VARCHAR(255) NOT NULL
) ENGINE InnoDB;
```

2. _Tabel session_
    -   1. Berisi kolom
    -   2. Berisi user_id

```
create table sessions
(
    id          VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id     VARCHAR(255) NOT NULL
) ENGINE InnoDB;
```

3. Menambah Constraint foreign key

```
ALTER TABLE sessions
ADD CONSTRAINT fk_session_user
    FOREIGN KEY (user_id)
        REFERENCES users(id);
```

4. Cek semua table

```
show tables;
```

5. Cek struktur masing-masing table

```
describe sessions;
describe users;
```
