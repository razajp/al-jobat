@echo off
cd /d "E:\al-jobat-spark-pair"
start /b php artisan serve --host=0.0.0.0 --port=8000
timeout /t 2 >nul
start http://127.0.0.1:8000
