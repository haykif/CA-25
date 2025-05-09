; Fichier : UIDScanner.iss

[Setup]
AppName=UIDScanner
AppVersion=1.5
AppPublisher=Hamza Aydogdu
AppPublisherURL=http://ca25.charles-poncet.net:8083
AppSupportURL=https://github.com/haykif/CA-25/issues
AppUpdatesURL=https://github.com/haykif/CA-25/releases
DefaultDirName={pf}\UIDScanner
DefaultGroupName=UIDScanner
OutputDir=UIDScanner-installed
OutputBaseFilename=UIDScanner_Installer
Compression=lzma2
SolidCompression=yes
SetupIconFile=icon.ico
WizardImageFile=wizardImage.bmp
WizardSmallImageFile=smallimage.bmp
LicenseFile=LICENSE.txt
DisableWelcomePage=no
DisableFinishedPage=no
DisableDirPage=no
DisableProgramGroupPage=no

[Files]
Source: "UIDScanner.exe"; DestDir: "{app}"; Flags: ignoreversion

[Icons]
Name: "{group}\UIDScanner"; Filename: "{app}\UIDScanner.exe"
Name: "{commondesktop}\UIDScanner"; Filename: "{app}\UIDScanner.exe"; Tasks: desktopicon
Name: "{userstartup}\UIDScanner"; Filename: "{app}\UIDScanner.exe"; Tasks: autostart

[Tasks]
Name: "desktopicon"; Description: "Créer un raccourci sur le bureau"; GroupDescription: "Options :"
Name: "autostart";   Description: "Lancer UIDScanner automatiquement avec Windows"; GroupDescription: "Options :"

[Run]
Filename: "{app}\UIDScanner.exe"; Description: "Lancer UIDScanner maintenant"; Flags: nowait postinstall skipifsilent

[Code]
procedure InitializeWizard();
begin
  MsgBox('Bienvenue dans l''installation de UIDScanner ! Cette application permet de lire les cartes RFID ACR122U.', mbInformation, MB_OK);
end;
