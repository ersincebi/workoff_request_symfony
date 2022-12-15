# php workoff request
Implementation of the application made with the php8, symfony 6.2, mysql 8

### Installation
- Clone the project
- In the project folder run `make build` or run the docker commands in Makefile
- When you done run the `make remove` for deleting, be cautious for other docker images on your local machine

### Requirements
- docker
- docker-compose
- make

### without docker
- php
- mysql
- composer

### About:
- In this project, I use PHP version 8, symfony 6.2 framework for the project.
- As a database engine mysql to store datas.
-

#### Requests
The routes available:

| Method | Route                                             | Parameters | Action |
|--------|---------------------------------------------------|------------|--------|
| `POST` | `/api/v1/employee/add`                            | tc_no, sgk_no, name, surname, begin_date, quit_date | employee add |
| `PUT` | `/api/v1/employee/{tc_no}/edit`                   | tc_no, sgk_no, name, surname, begin_date, quit_date | employee edit by identification number |
| `DELETE` | `/api/v1/employee/{tc_no}/delete`                 | no parameter needed | employee delete by identification number |
| `POST` | `/api/v1/workoff/add`                             | tc_no, begin_date, end_date | Add employee workoff info |
| `PUT` | `/api/v1/workoff/{tc_no}/{begin_date}/{end_date}` | tc_no, begin_date, end_date | Edit employee workoff info |
| `DELETE` | `/api/v1/workoff/{tc_no}/{begin_date}/{end_date}` | tc_no, begin_date, end_date | Delete employee workoff info |
| `GET` | `/api/v1/workoff/{date1}/{date2}`                 | no parameter needed | Get employee workoff info by date range |
| `GET` | `/api/v1/workoff/{name}/employee`                 | no parameter needed | Get employee workoff info by name |
| `GET` | `/api/v1/workoff/{workoff_status}/status`         | workoff_status in url can be set to present or absent | Get employee workoff info by workroff status |

