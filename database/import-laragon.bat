@echo off
set MYSQL_BIN=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe

if not exist "%MYSQL_BIN%" (
  echo Could not find Laragon MySQL at:
  echo %MYSQL_BIN%
  echo Update MYSQL_BIN in this file to match your Laragon MySQL version.
  exit /b 1
)

"%MYSQL_BIN%" -uroot < "%~dp0schema.sql"

if errorlevel 1 (
  echo Import failed. Make sure Laragon MySQL is started.
  exit /b 1
)

echo T-World database imported successfully.
