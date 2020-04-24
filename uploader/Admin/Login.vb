Imports System.Net
Imports System.IO
Imports System.Threading
Imports System.Web.Script.Serialization

Public Class Login
    Public background As Boolean
    'Private Shared Choose As Short = 1

    'Shared Sub Main()
    '    Application.EnableVisualStyles()
    '    If Choose = 1 Then
    '        Dim form As New Login
    '        form.Visible = False
    '        Application.Run(New Login)
    '    ElseIf Choose = 2 Then
    '        Application.Run(New Main)
    '    End If
    'End Sub

#Region "Load"
    Private Sub Login_Load(sender As Object, e As EventArgs) Handles Me.Load
#If DEBUG Then
        TXTUsername.Text = "konrad.kollnig"
        TXTPassword.Text = "PeTer110!"
        LBLBranding.Visible = True
        LBLBranding.ForeColor = Color.Red
        LBLBranding.Text = "DEBUG_VERSION"
        'My.Settings.Reset()
#End If

        'Erster Start?
        If My.Settings.syncActive And My.Settings.syncDir = "" Then
            MsgBox("Herzlich willkommen zur Administration des elektronischen Vertretungsplansystems!" & _
                   vbNewLine & vbNewLine & "Da Sie dieses Programm zum ersten Mal starten, muss das Verzeichnis festgelegt werden, in das die Vertretungsplandaten gespeichert werden. " & _
                   "Bitte nehmen Sie diese Konfiguration im nachfolgenden Dialog vor..", MsgBoxStyle.Information)
            App_Settings.ShowDialog()
            Exit Sub
        End If

        'Checken, ob Syncfolder existiert..
        If My.Settings.syncActive AndAlso Not My.Computer.FileSystem.DirectoryExists(My.Settings.syncDir) Then
            MsgBox("Leider musste festgestellt werden, dass das Verzeichnis, das mit dem Server synchronisiert werden soll, nicht mehr existiert." & _
                   vbNewLine & "Bitte aktualisieren Sie deshalb die Einstellungen.", MsgBoxStyle.Exclamation)
            App_Settings.ShowDialog()
            Exit Sub
        End If

        'Checken, ob Logindaten etc. angegeben..
        If My.Settings.syncActive And Not App.Library.SyncConfigValid Then
            MsgBox("Achtung! Die automatische Synchronisation funktioniert nur mit vollständig ausgefüllten Logindaten!" & _
                   vbNewLine & "Bitte aktualisieren Sie deshalb die Einstellungen!", MsgBoxStyle.Exclamation)
            App_Settings.ShowDialog()
            Exit Sub
        End If

        'Einlogg-Hilfe
        If App.Library.SyncConfigValid Then
            TXTUsername.Text = My.Settings.username
            TXTPassword.Text = App.Crypto.DecryptString(My.Settings.password)
        End If


        'Einstellungen laden
        'Watcher scharf machen
        Watcher.Path = My.Settings.syncDir
        'Watcher.EnableRaisingEvents = True
    End Sub



    Private Sub Login_Shown(sender As Object, e As EventArgs) Handles Me.Shown
        'Checken, ob Anwendung im Hintergrund gestartet werden soll.
        '-> Folderwatcher
        If App.Library.ReadProcessArguments("-autostart") Then
            ' Autostart nur mit aktivierter Synchro
            If Not My.Settings.syncActive Then
                'MsgBox("Bitte richten Sie die automatische Synchronisation ein oder löschen Sie den Autostart!", MsgBoxStyle.Exclamation)
                Application.Exit()
            End If

            'Anwendung verstecken
            Me.Hide()

            'Auto-Login..
            ShowBalloon(500, "EVa Administration", "Die Anwendung wurde soeben gestartet!", ToolTipIcon.Info)
            Login2Server(My.Settings.username, App.Crypto.DecryptString(My.Settings.password), True)
        End If
    End Sub
#End Region

    Private Sub OK_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles CMDOk.Click
        If TXTUsername.Text = "" Or TXTPassword.Text = "" Then
            MsgBox("Bitte vervollständigen Sie die Logindaten!", MsgBoxStyle.Information)
            Exit Sub
        End If

        Login2Server(TXTUsername.Text, TXTPassword.Text)
    End Sub

#Region "Login"
    Private Sub Login2Server(ByVal Username As String, ByVal Password As String, Optional ByVal Sync As Boolean = False)
        'Um die Variable auch im Handler weiter verwenden zu können
        background = Sync

        If background Then
            'Visual feedback
            ShowBalloon(500, "EVa Administration", "Anmelden..", ToolTipIcon.Info)

            'Nur mit gültigen Daten anmelden..
            If Not App.Library.SyncConfigValid Then
                ShowBalloon(500, "Synchronisation fehlgeschlagen.", "Ungültige Logindaten.", ToolTipIcon.Info)
                Exit Sub
            End If
        End If

        'Visual Feedback
        PB.Visible = True

        'Get Token
        Dim uri As String = My.Settings.loginUrl & "username=" & Username & "&password=" & Password

        Dim wc As New WebClient
        AddHandler wc.DownloadStringCompleted, AddressOf TokenDownloaded
        wc.DownloadStringAsync(New Uri(uri))
    End Sub
    Private Sub TokenDownloaded(ByVal sender As Object, ByVal e As DownloadStringCompletedEventArgs)
        If e.Cancelled = False AndAlso e.Error Is Nothing Then
            Dim data As String = CStr(e.Result)

            ' Invalid Response?
            ' TODO: Parse error
            If data = "" OrElse data.Contains("error") Then
                DownloadError()
                Exit Sub
            End If

            ' Parse respone to array
            Dim jss = New JavaScriptSerializer()
            Dim user = jss.Deserialize(Of Dictionary(Of String, Object))(data)
            If user("token") Is Nothing OrElse user("token") = "" Then
                DownloadError()
                Exit Sub
            End If
            App.Config.User = user

            'Synchronisation scharf schalten
            If My.Settings.syncActive And App.Library.SyncConfigValid Then
                Watcher.EnableRaisingEvents = True
            End If

            ' Checken, ob Hintergrund-Start (durch autostart) oder nicht
            If Not background Then
                'Dim xMain As New Main
                Main.Show()
                Me.Hide()
            Else
                ShowBalloon(500, "EVa Administration", "Anmeldung erfolgreich!", ToolTipIcon.Info)
            End If
        Else
            DownloadError()
        End If
        PB.Visible = False
    End Sub

    Private Sub DownloadError(Optional ByVal message As String = "Anmeldung fehlgeschlagen!")
        PB.Visible = False
        ShowBalloon(1000, "EVa Administration", message, ToolTipIcon.Error)
    End Sub
#End Region

#Region "Buttons"
    Private Sub OpenAppFromTray(sender As Object, e As EventArgs) Handles AnwendungÖffnenToolStripMenuItem.Click, Notify.MouseDoubleClick
        If Not App.Config.User("token") = "" Then
            'Dim xMain As New Main
            Main.Show()
        Else
            Me.Show()
        End If
    End Sub

    Private Sub CMDSettings_Click(sender As Object, e As EventArgs) Handles CMDSettings.Click
        App_Settings.ShowDialog()
    End Sub
#End Region

    Public Sub ShowBalloon(ByVal timeout As Integer, tipTitle As String, tipText As String, tipIcon As System.Windows.Forms.ToolTipIcon)
        If Not My.Settings.disableBalloons Then
            Notify.Visible = True
            Notify.ShowBalloonTip(timeout, tipTitle, tipText, tipIcon)
        End If
    End Sub

    Private Sub Notify_BalloonTipClosed(sender As Object, e As EventArgs) Handles Notify.BalloonTipClosed
        'Schließen, wenn Fenster geöffnet..
        'TODO
    End Sub

    Private Sub Watcher_Upload_Event(sender As Object, e As FileSystemEventArgs) Handles Watcher.Changed, Watcher.Renamed, Watcher.Created
#If DEBUG Then
        ShowBalloon(500, "EVa Administration", "Folder Watcher Event " & DateTime.Now, ToolTipIcon.Info)
#End If

        'Avoid multiple firing events..
        Threading.Thread.Sleep(Int((150 * Rnd()) + 50))
        If Watcher.EnableRaisingEvents = False Then Exit Sub
        Watcher.EnableRaisingEvents = False

        Try
            Dim file As String = e.FullPath
            'Dim cksum As String = App.Library.SHA1FileHash(file)

            ''Keine überflüssigen Uploads
            'If Not My.Settings.sha1 = cksum Then
            '    My.Settings.sha1 = cksum
            '    My.Settings.Save()

            'Visual Feedback
            'ShowBalloon(100, "EVa Administration", "Änderungen erkannt!", ToolTipIcon.Info)

            ' For sure bro?
            If MessageBox.Show("Neuesten Plan synchronisieren?", "EVa Administration: Änderungen erkannt!", MessageBoxButtons.YesNo, MessageBoxIcon.Information) = Windows.Forms.DialogResult.Yes Then
                Dim jss = New JavaScriptSerializer()
                Dim response As Object
                response = App.Library.UploadTurbo(file)
                response = jss.Deserialize(Of Dictionary(Of String, Object))(response)

                If response Is Nothing OrElse response("success") Is Nothing OrElse response("success") = "false" Then
                    If Not My.Computer.Network.IsAvailable Then
                        ShowBalloon(500, "EVa Administration", "Keine Internetverbindung!", ToolTipIcon.Error)
                    Else
                        ShowBalloon(500, "EVa Administration", "Upload fehlgeschlagen!", ToolTipIcon.Error)
                    End If
                Else
                    Dim last_update As String = ""
                    If response("last_update") IsNot Nothing AndAlso Not response("last_update") = "" Then last_update = response("last_update")

                    ShowBalloon(500, "EVa Administration", "Upload erfolgreich (Stand: " & last_update & ")!", ToolTipIcon.Info)
                End If
            End If

            'Else
            '    ShowBalloon(500, "EVa Administration", "Änderungen erkannt, Daten unverändert!", ToolTipIcon.Info)
            'End If

            'Stand aktualisieren
            'TODO
        Catch ex As IOException
            'MsgBox("Doppelter Zugriff!", MsgBoxStyle.Critical)
            ShowBalloon(500, "EVa Administration", "Doppelter Zugriff! Upload fehlgeschlagen!", ToolTipIcon.Error)
        Catch ex As Exception
            MsgBox("Unerwartete Ausnahme!", MsgBoxStyle.Critical)
        End Try

        Watcher.EnableRaisingEvents = True
    End Sub

    Private Sub Watcher_Error(sender As Object, e As ErrorEventArgs) Handles Watcher.Error
        ShowBalloon(500, "EVa Administration", "Upload fehlgeschlagen!", ToolTipIcon.Error)
    End Sub

#Region "Exit"
    Private Sub Cancel_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles CMDCancel.Click
        Me.Close()
    End Sub

    Private Sub BeendenToolStripMenuItem_Click(sender As Object, e As EventArgs) Handles BeendenToolStripMenuItem.Click
        Me.Close()
    End Sub

    Private Sub Login_FormClosing(sender As Object, e As FormClosingEventArgs) Handles Me.FormClosing
        Application.Exit()
    End Sub
#End Region
End Class