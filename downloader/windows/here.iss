[Setup]
AppName=Mon Appli
AppVersion=1.0
DefaultDirName={pf}\MonAppli
DefaultGroupName=Mon Appli
OutputDir=.\output
OutputBaseFilename=MonAppli_Installer
Compression=lzma
SolidCompression=yes

[Files]
Source: "MonApp.exe"; DestDir: "{app}"; Flags: ignoreversion
Source: "data\*"; DestDir: "{app}\data"; Flags: ignoreversion recursesubdirs createallsubdirs

[Icons]
Name: "{group}\Mon Appli"; Filename: "{app}\MonApp.exe"
Name: "{commondesktop}\Mon Appli"; Filename: "{app}\MonApp.exe"; Tasks: desktopicon

[Tasks]
Name: "desktopicon"; Description: "Créer un raccourci sur le bureau"; GroupDescription: "Raccourcis :"
