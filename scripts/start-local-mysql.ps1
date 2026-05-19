$mysqlConfig = Join-Path $PSScriptRoot '..\storage\mysql\my-local.ini'
$mysqlConfig = (Resolve-Path $mysqlConfig).Path
$mysqlServer = 'C:\Program Files\MySQL\MySQL Server 8.4\bin\mysqld.exe'

if (-not (Test-Path $mysqlServer)) {
    throw "mysqld.exe was not found at $mysqlServer"
}

if (-not (Test-Path $mysqlConfig)) {
    throw "MySQL config was not found at $mysqlConfig"
}

Write-Host "Starting local MySQL with $mysqlConfig"
Start-Process -FilePath $mysqlServer -ArgumentList "--defaults-file=$mysqlConfig"
