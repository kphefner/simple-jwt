# simple-jwt

Simple-JWT is a basic REST api that authenticates a user and returns a JSON Web Token. The returned JWT is then used to access protected API endpoints.

## Installation

Requires a mysql "Users" table in a database, "jwt"

```sql
CREATE  TABLE IF NOT EXISTS `Users` (
  `id` INT  AUTO_INCREMENT ,
  `first_name` VARCHAR(150) NOT NULL ,
  `last_name` VARCHAR(150) NOT NULL ,
  `email` VARCHAR(255),
  `password` VARCHAR(255),
  PRIMARY KEY (`id`) );
 ```
 
 Copy all files to directory serving apache.
 
 Use POSTMAN to register a user posting to: http://127.0.0.1:8080/api/register.php
 ```json
 {
    "first_name":"Kaima",
    "last_name":"Abbes",
    "email":"kaima@email.com",
    "password":"987654321"
}
```

Use POSTMAN to login and get a webtoken: http://127.0.0.1:8080/api/login.php
```json
{
    "first_name": "Testing",
    "last_name": "Tester",
    "email":"kaima@email.com",
    "password":"987654321"
}
```
