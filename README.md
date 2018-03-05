### Задание 1. 
####Запуск приложения:

Перед началом работы необходимо создать две базы. Одна для работы, другая для тестов

Тут прописать учетные данные рабочей базы


- config/db.php

Тут для тестовой

- config/test_db.php
- codeception.yml

**Встроенный сервер:**
```
php yii serve
```
**Миграции:**
```
php yii migrate
php yii-test migrate
```

**Команда импорта:**
```
php yii dump
```
Чтобы не ждать, дамп сгенерированной базы тут ```/data/dump.gz```

**API:**
```
POST v1/post 
POST v1/mark
GET v1/ip-list
GET v1/post/top
```

**Запуск тестов**
```
vendor/codeception/base/codecept run
```

### Задание 2.

```postgresql
WITH intervals AS (SELECT start_id, finish_id FROM (
    SELECT
      (SELECT first_value(u.id) OVER()
        FROM ts u
        WHERE
          u.id <= main.id
          AND u.group_id = main.group_id
          AND u.id > COALESCE(
            (SELECT id
            FROM ts
            WHERE id < main.id AND group_id <> main.group_id
            ORDER BY id DESC LIMIT 1), (SELECT 0)
          )
        ORDER BY u.id DESC LIMIT 1) as start_id,
      (CASE
          WHEN lead(group_id) OVER() <> group_id OR lead(group_id) OVER() IS NULL
          THEN id
          ELSE null
      END) as finish_id
    FROM ts main) sub WHERE start_id IS NOT NULL AND finish_id IS NOT NULL)
SELECT
  intervals.start_id,
  intervals.finish_id,
  (SELECT COUNT(*) FROM ts WHERE id >= intervals.start_id AND id <= intervals.finish_id) as counter,
  (SELECT MIN(id) FROM ts WHERE id >= intervals.start_id AND id <= intervals.finish_id) as min
FROM intervals
```