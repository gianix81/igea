@echo off
REM Checkout automatico IGEA — eseguito da Task Scheduler alle 19:00
REM Percorso PHP di Laragon:
"C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" "d:\igea\scripts\auto-checkout.php" >> "d:\igea\scripts\auto-checkout.log" 2>&1
