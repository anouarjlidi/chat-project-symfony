@echo off
echo Starting install ...
cls
echo Add this line:
echo.
echo 127.0.0.1 chatsymfony
echo.
echo in file C:\Windows\System32\drivers\etc\hosts
echo.
setlocal
:PROMPT
SET /P AREYOUSURE=Is it done ? [Y]
IF /I "%AREYOUSURE%" NEQ "Y" GOTO END
    cls
    echo Add this line:
    echo.
    echo ^<VirtualHost *:80^> DocumentRoot "C:/xampp/htdocs/public" ServerName chatsymfony ^</VirtualHost^>
    echo.
    echo in file C:\xampp\apache\conf\extra\httpd-vhosts.conf
    echo.
    setlocal
    :PROMPT
    SET /P AREYOUSURE=Is it done ? [Y]
    IF /I "%AREYOUSURE%" NEQ "Y" GOTO END
        cls
        echo Please change this line with your parameters:
        echo.
        echo DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/socialsock
        echo.
        echo Don't forget to change socialsock to socialsock_dev
        echo.
        echo in file .env of the project
        echo.
        setlocal
        :PROMPT
        SET /P AREYOUSURE=Is it done ? [Y]
        IF /I "%AREYOUSURE%" NEQ "Y" GOTO END
            cd ../../../ && php bin/console doctrine:database:create & cd script/windows/dev && echo Please run update.bat
        :END
        endlocal
    :END
    endlocal
:END
endlocal