@echo off
cd /d "E:\al-jobat-spark-pair"
start /b php artisan serve --host=192.168.100.37 --port=8000
timeout /t 2 >nul
start http://192.168.100.37:8000