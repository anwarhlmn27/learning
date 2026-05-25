Stop-Service -Name mysql -ErrorAction SilentlyContinue
taskkill /F /IM mysqld.exe /T 2>$null
Rename-Item -Path "C:\xampp\mysql\data" -NewName "data_old"
New-Item -Path "C:\xampp\mysql\data" -ItemType Directory
Copy-Item -Path "C:\xampp\mysql\backup\*" -Destination "C:\xampp\mysql\data" -Recurse
$exclude = @('mysql', 'performance_schema', 'phpmyadmin')
Get-ChildItem -Path "C:\xampp\mysql\data_old" -Directory | Where-Object { $_.Name -notin $exclude } | ForEach-Object { Copy-Item -Path $_.FullName -Destination "C:\xampp\mysql\data" -Recurse }
Copy-Item -Path "C:\xampp\mysql\data_old\ibdata1" -Destination "C:\xampp\mysql\data\ibdata1" -Force
Write-Host "MySQL data has been successfully restored. You can now start MySQL in XAMPP."
