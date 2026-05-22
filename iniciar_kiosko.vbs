Set WshShell = CreateObject("WScript.Shell")
' Ejecuta el archivo .bat en modo invisible (0) para evitar ventanas negras parpadeantes
WshShell.Run chr(34) & "iniciar_kiosko.bat" & chr(34), 0
Set WshShell = Nothing
