Public Class App_Settings
#Region "Initialisierung"
    Private Sub App_Settings_Load(sender As Object, e As EventArgs) Handles Me.Load
        ' TODO Autostart deaktivieren (Registry)
        'Laden der Einstellungen
        My.Settings.Reload()

        'Pfad
        LBLPath.Text = My.Settings.syncDir

        'Synchronisation aktiviert
        CBSyncActive.Checked = My.Settings.syncActive

        'Benachrichtigungen anzeigen
        CBBalloons.Checked = My.Settings.disableBalloons

        'Logindaten
        TXTUsername.Text = My.Settings.username
        If Not My.Settings.password = "" Then TXTPassword.Text = "******"
    End Sub
#End Region
#Region "Einstellungen"
    Private Sub CMDPath_Click(sender As Object, e As EventArgs) Handles CMDPath.Click
        'Dialog zur Auswahl zeigen
        FBD.ShowDialog()

        'Eingabe checken und verarbeiten
        Dim Path As String = FBD.SelectedPath
        If My.Computer.FileSystem.DirectoryExists(Path) Then
            My.Settings.syncDir = Path
            LBLPath.Text = Path
            Login.Watcher.Path = Path
            'CMDClose.Enabled = True
        Else
            MsgBox("Der angegebene Pfad ist nicht gültig!", MsgBoxStyle.Critical)
        End If
    End Sub

    'Synceinstellung umschalten
    Private Sub CBSyncActive_CheckedChanged(sender As Object, e As EventArgs) Handles CBSyncActive.CheckedChanged
        My.Settings.syncActive = CBSyncActive.Checked
        GroupSync.Enabled = CBSyncActive.Checked
    End Sub

    'Benachrichtigungseinstellung umschalten
    Private Sub CBBalloons_CheckedChanged(sender As Object, e As EventArgs) Handles CBBalloons.CheckedChanged
        My.Settings.disableBalloons = CBBalloons.Checked
    End Sub

    'Logindaten übernehmen
    Private Sub TXTUsername_Validated(sender As Object, e As EventArgs) Handles TXTUsername.Validated
        My.Settings.username = TXTUsername.Text
    End Sub
    Private Sub TXTPassword_Validated(sender As Object, e As EventArgs) Handles TXTPassword.Validated
        'Passwort verschlüsselt in den Einstellungen speichern
        'Leider sind die Moodlepasswörter gesalzen..
        '-> Passwortsalt lokal mitspeichern?!?
        '-> Sicherheitsrisiko
        My.Settings.password = App.Crypto.EncryptString(TXTPassword.Text)
    End Sub
#End Region
#Region "Exit"
    'Schließen-Button
    Private Sub CMDClose_Click(sender As Object, e As EventArgs) Handles CMDClose.Click
        Me.Close()
    End Sub

    'Fenster soll geschlossen werden..
    Private Sub AppSettings_FormClosing(sender As Object, e As FormClosingEventArgs) Handles Me.FormClosing
        'Daten für Synchronisation erzwingen..
        If Not App.Library.SyncConfigValid Then
            'Warnung anzeigen..
            MsgBox("Moment! Für die Verwendung der automatischen Synchronisationsfunktion müssen alle Felder korrekt ausgefüllt sein.", MsgBoxStyle.Exclamation)
            e.Cancel = True
        End If
    End Sub

    'Fenster wird geschlossen..
    Private Sub App_Settings_FormClosed(sender As Object, e As FormClosedEventArgs) Handles Me.FormClosed
        My.Settings.Save()
    End Sub
#End Region
End Class