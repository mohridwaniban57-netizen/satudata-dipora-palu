@echo off
title Satu Data Dispora Kota Palu
echo ====================================================
echo  Satu Data Dispora Kota Palu - Server Tunggal
echo  Alamat Web: http://localhost:8000
echo ====================================================
echo.
cd /d "%~dp0"
"d:\xampp8\php\php.exe" artisan serve --port=8000
pause

