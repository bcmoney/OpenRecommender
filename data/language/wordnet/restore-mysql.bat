@echo off
rem bbou@ac-toulouse.fr
rem 17.04.2009
set DB=wordnet30
set DBTYPE=mysql
set DBUSER=root
set /P DBPWD="Enter %DBUSER% password:"
set MODULES=wn legacy vn xwn bnc sumo

call :dbexists
if not %DBEXISTS%==0 call :createdb
for %%M in (%MODULES%) do call :process %DBTYPE%-%%M-schema.sql schema %DB%
for %%M in (%MODULES%) do call :process %DBTYPE%-%%M-data.sql data %DB%
for %%M in (%MODULES%) do call :process %DBTYPE%-%%M-constraints.sql constraints %DB% --force
for %%M in (%MODULES%) do call :process %DBTYPE%-%%M-views.sql views %DB%
goto :eof

:process
setlocal
if not exist %1 goto :endprocess
echo %2
mysql -u %DBUSER% --password=%DBPWD% %4 %3 < %1
:endprocess
endlocal
goto :eof

:dbexists
setlocal
mysql -u %DBUSER% --password=%DBPWD% -e "\q" %DB% > NUL 2> NUL
endlocal & set DBEXISTS=%ERRORLEVEL% 
goto :eof

:deletedb
setlocal
echo "delete %DB%"
mysql -u %DBUSER% --password=%DBPWD% -e "DROP DATABASE %DB%;"
endlocal
goto :eof

:createdb
setlocal
echo "create %DB%"
mysql -u %DBUSER% --password=%DBPWD% -e "CREATE DATABASE %DB% DEFAULT CHARACTER SET UTF8;"
endlocal
goto :eof

:eof
