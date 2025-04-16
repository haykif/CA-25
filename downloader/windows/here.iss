[Setup]
AppName=UIDScanner
AppVersion=1.5
DefaultDirName={pf}\UIDScanner
DefaultGroupName=UIDScanner
OutputDir=output
OutputBaseFilename=UIDScanner_Installer
Compression=lzma
SolidCompression=yes
SetupIconFile=icon.ico
WizardImageFile=uidscannerimage.png
WizardSmallImageFile=uidscannerimage.png

[Files]
Source: "UIDScanner.exe"; DestDir: "{app}"; Flags: ignoreversion

[Icons]
Name: "{group}\UIDScanner"; Filename: "{app}\UIDScanner.exe"
Name: "{commondesktop}\UIDScanner"; Filename: "{app}\UIDScanner.exe"; Tasks: desktopicon

[Tasks]
Name: "desktopicon"; Description: "Créer un raccourci sur le bureau"; GroupDescription: "Options :"

[Run]
Filename: "{app}\UIDScanner.exe"; Description: "Lancer UIDScanner"; Flags: nowait postinstall skipifsilent

[UninstallRun]
Filename: "{app}\UIDScanner.exe"; Parameters: "/uninstall"; Flags: skipifdoesntexist

[Messages]
SetupCompletedMessage=UIDScanner a été installé avec succès !
