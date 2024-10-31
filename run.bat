@echo off
echo Starting PHP server...
cd %~dp0
cd lib
php.exe -S localhost:8000 -t ../src

echo Press Ctrl+C to stop, then press any key to exit.
pause