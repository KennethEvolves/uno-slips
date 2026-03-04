<p align="center">
	<img src="public/uno-logo.svg" alt="Universidad de Oriente" width="140" style="filter: brightness(0) invert(1);"/>
</p>
<p align="center">
	Plataforma de Gestión de Fichas de la Universidad de Oriente
</p>

<p align="center">
PHP + MySQL + phpMyAdmin project using Docker.
</p>

## Requirements

- Git
- Docker Desktop (with `docker compose`)
- PowerShell (Windows)

## 1) Clone the repository

```powershell
git clone <URL_DEL_REPO>
cd fichas-uno
```

## 2) Create `.env` file

You can copy the example and edit it with your values:

```powershell
Copy-Item .env.example .env
```

Minimal example:

```env
DB_HOST=db
DB_USER=root
DB_PASS=your_password
DB_NAME=database_name

APP_PORT=8080
PHPMYADMIN_PORT=8081
```

## 3) Start containers

```powershell
docker compose up -d --build
```

Expected containers:

- `servidor_fichas` (web)
- `db_fichas` (mysql)
- `gestor_db_fichas` (phpmyadmin)

## 4) Install Composer dependencies

```powershell
docker compose exec web composer install
```

## 5) Import initial database

Using PowerShell with the command you requested:

```powershell
Get-Content filename.sql | docker exec -i db_fichas mysql -u root -pYOUR_PASSWORD database_name
```

Notes:

- Replace `filename.sql` with the real filename.
- Replace `your_password` with the value of `DB_PASS` in your `.env`.
- Replace `database_name` with the value of `DB_NAME` in your `.env`.

## 6) Test access

- App: `http://localhost:APP_PORT` (e.g. `http://localhost:8080`)
- phpMyAdmin: `http://localhost:PHPMYADMIN_PORT` (e.g. `http://localhost:8081`)

## Useful commands

```powershell
docker compose ps
docker compose logs -f web
docker compose down
```
