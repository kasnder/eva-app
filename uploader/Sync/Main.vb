Imports System.Web.Script.Serialization
Imports System.Threading
Imports System.Net
Imports System.IO

Public Class Main
    'Stores the token and additional user information
    Public Shared User As Dictionary(Of String, Object)

    'Avoid multiple uploads of the same file by the watcher
    Private scheduleTime As Date ' Stores the latestAccessTime of the latest synced schedule
    Private countTries As Short = 0 ' Count login tries..
#Region "Form Events"
    Private Sub Main_Load(sender As Object, e As EventArgs) Handles Me.Load
        'Handle unhandled exceptions
        AddHandler AppDomain.CurrentDomain.UnhandledException, AddressOf Me.GLOBALERRORHANDLER
        AddHandler Application.ThreadException, AddressOf Me.THREADERRORHANDLER
#If DEBUG Then
        LBLBranding.Visible = True
        LBLBranding.Text = "Test Version"
        'Me.TopMost = False
#End If

        'Settings
        LBLPath.Text = My.Settings.path
        TXTUsername.Text = My.Settings.username
        If Not My.Settings.password = "" Then TXTPassword.Text = Crypto.DecryptString(My.Settings.password)
        Watcher.Filter = My.Settings.fileFilter

        ' Save startTime for the Watcher
        scheduleTime = Date.Now
    End Sub

    ' Check autostart - hide else
    Private Sub Main_Shown(sender As Object, e As EventArgs) Handles Me.Shown
        'Autostart?
        If CheckProcessArguments("-autostart") Then
            ' Only start if there is some config
            If Not SyncConfigValid() Then
                Environment.Exit(0)
            End If

            ' Hide App
            Me.Hide()

            'Auto-Login..
            Login()
        End If

        'User already logged in?
        If SyncConfigValid() Then
            Login()
        End If
    End Sub

    Public Shared Function CheckProcessArguments(Argument As String) As Boolean
        Return Environment.GetCommandLineArgs().Contains(Argument)
    End Function

    Private Sub Me_Show(sender As Object, e As EventArgs) Handles OpenToolStripMenuItem.Click, Notify.MouseDoubleClick
        Me.Show()
    End Sub

    Private Sub Me_Close(sender As Object, e As EventArgs) Handles ExitToolStripMenuItem.Click
        Environment.Exit(0)
    End Sub

    Private Sub Main_FormClosing(sender As Object, e As FormClosingEventArgs) Handles Me.FormClosing
        ' Just hide the form on UserClosing
        If e.CloseReason = CloseReason.UserClosing Then
            Me.Hide()
            e.Cancel = True
        Else
            Environment.Exit(0)
        End If
    End Sub

    Private Sub CMDCorrections_Click(sender As Object, e As EventArgs) Handles CMDCorrections.Click, PlanorrekturenToolStripMenuItem.Click
        If UserLoggedIn() Then
            Dim form As New Corrections
            form.ShowDialog()
        Else
            MsgBox("Bitte zuerst anmelden!", MsgBoxStyle.Information)
        End If
    End Sub

    Public Sub Print(sender As Object, e As EventArgs) Handles PrintToolStripMenuItem.Click, CMDPrint.Click
        If UserLoggedIn() Then
            Process.Start(My.Settings.host & "?component=content&view=print&token=" & Main.User("token"))
        Else
            MsgBox("Bitte zuerst anmelden!", MsgBoxStyle.Information)
        End If
    End Sub
#End Region

#Region "Login"
    Private Sub Enter_KeyUp(sender As Object, e As KeyEventArgs) Handles TXTPassword.KeyUp, TXTUsername.KeyUp
        If e.KeyCode = Keys.Enter Then
            Login()
        End If
    End Sub

    Private Sub Login()
        'Force validation of Textboxes
        Validate_TXTs()

        'Synchronisation beenden
        Watcher.EnableRaisingEvents = False

        ' Clear all previous data
        'If User.Count > 0 Then User.Clear()

        Dim username = My.Settings.username
        Dim password = Crypto.DecryptString(My.Settings.password)

        ' Nur mit gültigen Daten anmelden..
        If Not SyncConfigValid() Then
            MsgBox("Ungültige Logindaten!", MsgBoxStyle.Critical)
            Exit Sub
        End If

        ' Visual feedback
        ShowBalloon("Anmelden..")
        CMDConnect.Enabled = False
        Progress.Visible = True

        ' Login at server
        Dim uri As String = My.Settings.host & "?component=auth&view=login&username=" & username & "&password=" & password
        Dim client As New WebClient
        AddHandler client.DownloadStringCompleted, AddressOf TokenDownloaded
        client.DownloadStringAsync(New Uri(uri))
    End Sub
    Private Sub TokenDownloaded(ByVal sender As Object, ByVal e As DownloadStringCompletedEventArgs)
        If e.Cancelled = False AndAlso e.Error Is Nothing Then
            Dim data As String = CStr(e.Result)
            Dim jss = New JavaScriptSerializer()

            ' Invalid Response?
            If data = "" Then
                LoginError()
                Exit Sub
            End If

            ' Error?
            ' Try to parse the error
            If data.Contains("error") Then
                Try
                    Dim response As Dictionary(Of String, Object)
                    response = jss.Deserialize(Of Dictionary(Of String, Object))(data)

                    If response IsNot Nothing OrElse response("error") IsNot Nothing Then
                        LoginError(response("error"))
                    Else
                        LoginError()
                    End If
                Catch ex As Exception
                    LoginError()
                End Try
                Exit Sub
            End If

            ' Try to parse the respone in an array
            Try
                User = jss.Deserialize(Of Dictionary(Of String, Object))(data)

                ' Check token
                If User("token") Is Nothing OrElse User("token") = "" Then
                    LoginError("Ungültige Serverantwort.")
                    Exit Sub
                End If
            Catch ex As Exception
                LoginError("Ungültige Serverantwort.")
                Exit Sub
            End Try

            ' Enable watcher
            If SyncConfigValid() Then
                Watcher.Path = My.Settings.path
                Watcher.EnableRaisingEvents = True
            End If

            ' Visual feedback
            LBLStatus.Text = "Verbunden."
            ShowBalloon("Anmeldung erfolgreich!")

            countTries = 0


            GBActions.Enabled = True
            CMDConnect.Enabled = True
            Notify.Icon = My.Resources.Logo_Blue
            Progress.Visible = False
        Else
            LoginError()
        End If
    End Sub

    Private Sub LoginError(Optional ByVal message As String = "Anmeldung fehlgeschlagen!")
        ' Retry?
        countTries += 1
        If countTries < 5 Then
            Login()
        Else
            countTries = 0
            'If User.Count > 0 Then User.Clear()
            GBActions.Enabled = False
            CMDConnect.Enabled = True
            Progress.Visible = False
            Notify.Icon = My.Resources.Logo_Red

            LBLStatus.Text = "Nicht verbunden."
            ShowBalloon(message, ToolTipIcon.Error, 1000)
        End If
    End Sub
#End Region

#Region "Sync"
    'Manual Sync
    Private Sub Me_Sync(sender As Object, e As EventArgs) Handles CMDSync.Click, SyncToolStripMenuItem.Click
        If UserLoggedIn() Then
            Dim file As String = FindLatestFile(My.Settings.path)

            ' Do request
            Dim response As String = UploadTurbo(file)
            MsgBox(response)
        Else
            MsgBox("Bitte zuerst anmelden!", MsgBoxStyle.Information)
        End If
    End Sub

    Private Sub Watcher_Upload_Event(sender As Object, e As FileSystemEventArgs) Handles Watcher.Changed, Watcher.Renamed, Watcher.Created
        Dim info As New IO.FileInfo(e.FullPath)

        ' Make sure that only one event per second is accepted
        ' info.LastWriteTime
        If Date.Now() < scheduleTime.AddSeconds(5) Then
            Exit Sub
        End If
        scheduleTime = Date.Now()

        ' Ask user to sync
        If MessageBox.Show("Neuesten Plan synchronisieren?", "EVa Administration: Änderungen erkannt!", MessageBoxButtons.YesNo, MessageBoxIcon.Information) = Windows.Forms.DialogResult.Yes Then
            ' Wait some seconds for the Turbo Vertretung to close
            System.Threading.Thread.Sleep(10000)

            Dim file As String = e.FullPath
            Dim response As String = UploadTurbo(file)

            ShowBalloon(response)
        End If

        'Watcher.EnableRaisingEvents = True
    End Sub

    Private Sub Watcher_Error(sender As Object, e As ErrorEventArgs) Handles Watcher.Error
        ShowBalloon("Upload fehlgeschlagen!", ToolTipIcon.Error, 1000)
    End Sub

    ' Find file in direcotry with latest LastWriteTime
    Private Function FindLatestFile(path As String)
        Dim fi As New System.IO.DirectoryInfo(My.Settings.path)
        Dim files = fi.GetFiles.ToList
        Dim first = (From file In files Select file Order By file.LastWriteTime Descending).FirstOrDefault

        Return first.FullName
    End Function

    ' Upload Function
    ' Returns a parsed result
    Public Shared Function UploadTurbo(ByVal Path As String) As String
        Try
            'Does the file exist?
            If Not My.Computer.FileSystem.FileExists(Path) Then Return "Datei nicht gefunden."

            'Read file
            Dim output As String = ""
            Using sr As New StreamReader(Path, System.Text.Encoding.GetEncoding(1252))
                output = sr.ReadToEnd()
            End Using

            'POST file
            Dim client As WebClient = New WebClient()
            client.Encoding = System.Text.Encoding.UTF8
            client.Headers(HttpRequestHeader.ContentType) = "application/x-www-form-urlencoded"

            'Get creditals
            Dim username As String = My.Settings.username
            Dim password As String = Crypto.DecryptString(My.Settings.password)

            ' Prepare request
            Dim reply As String = client.UploadString(My.Settings.host & "?component=upload&view=parser&username=" & username & "&password=" & password, "input=" & output)

            Dim dict As Dictionary(Of String, Object)
            Dim jss = New JavaScriptSerializer()
            dict = jss.Deserialize(Of Dictionary(Of String, Object))(reply)

            ' Check response
            If dict Is Nothing OrElse dict("success") Is Nothing OrElse dict("success") = False Then
                If Not My.Computer.Network.IsAvailable Then
                    Return "Keine Internetverbindung."
                Else
                    Return "Änderungen konnten nicht übernommen werden."
                End If

                Exit Function
            End If

            ' No last_update defined.. :(
            If dict("last_update") Is Nothing OrElse dict("last_update") = "" Then
                Return "Upload erfolgreich!"
            End If

            Return "Upload erfolgreich (Stand: " & dict("last_update") & ")!"
        Catch ex As Exception
            Return "Änderungen konnten nicht übernommen werden."
        End Try
    End Function
#End Region

#Region "Error"
    Private Sub GLOBALERRORHANDLER(ByVal sender As Object, ByVal args As UnhandledExceptionEventArgs)
        Dim ex As Exception = DirectCast(args.ExceptionObject, Exception)
        MsgBox("Unbehandelte Ausnahme: " & ex.Message, MsgBoxStyle.Critical)
        'Log error
#If DEBUG Then
        LogError(ex)
#End If
    End Sub

    Private Sub THREADERRORHANDLER(ByVal sender As Object, ByVal args As ThreadExceptionEventArgs)
        Dim ex As Exception = DirectCast(args.Exception, Exception)
        MsgBox("Unbehandelte Ausnahme: " & ex.Message, MsgBoxStyle.Critical)

        'Log error
#If DEBUG Then
        LogError(ex)
#End If
    End Sub
    Private Sub LogError(ex As Exception)
        Dim logfile As String = Environment.GetFolderPath(Environment.SpecialFolder.Desktop) & "\EVa.log"
        My.Computer.FileSystem.WriteAllText(logfile, Date.Now.ToString & " " & ex.Message & vbNewLine & _
                                                vbNewLine & _
                                                ex.StackTrace.ToString & vbNewLine & _
                                                vbNewLine & vbNewLine, True)
    End Sub
#End Region

#Region "Settings"
    'Choose path to watch
    Private Sub CMDPath_Click(sender As Object, e As EventArgs) Handles CMDPath.Click
        'Dialog zur Auswahl zeigen
        FolderBrowser.ShowDialog()

        'Eingabe checken und verarbeiten
        Dim Path As String = FolderBrowser.SelectedPath
        If My.Computer.FileSystem.DirectoryExists(Path) Then
            My.Settings.path = Path
            My.Settings.Save()
            LBLPath.Text = Path
            'CMDClose.Enabled = True
        Else
            MsgBox("Der angegebene Pfad ist nicht gültig!", MsgBoxStyle.Critical)
        End If
    End Sub

    'Save Creditals

    Private Sub Validate_TXTs()
        TXTUsername_Validated()
        TXTPassword_Validated()
    End Sub

    Private Sub TXTUsername_Validated() Handles TXTUsername.Validated
        My.Settings.username = TXTUsername.Text
        My.Settings.Save()
    End Sub

    Private Sub TXTPassword_Validated() Handles TXTPassword.Validated
        'Encrypt password before saving
        My.Settings.password = Crypto.EncryptString(TXTPassword.Text)
        My.Settings.Save()
    End Sub
#End Region

#Region "Helpers"
    Public Shared Function SyncConfigValid() As Boolean
        Return (My.Computer.FileSystem.DirectoryExists(My.Settings.path) And _
            Not My.Settings.username = "" And Not My.Settings.password = "")
    End Function

    Public Sub ShowBalloon(tipText As String, Optional tipIcon As System.Windows.Forms.ToolTipIcon = ToolTipIcon.Info, Optional timeout As Integer = 500)
        'Notify.Visible = True
        Notify.ShowBalloonTip(timeout, "EVa Administration", tipText, tipIcon)
    End Sub

    Public Function UserLoggedIn() As Boolean
        Return (Main.User IsNot Nothing AndAlso Main.User("token") IsNot Nothing)
    End Function


    ' Log to debugger
    '#If DEBUG Then
    'Public Shared Sub Log(ByVal Text As String)
    '    My.Application.Log.WriteEntry(Text, TraceEventType.Information)
    'End Sub
    '#End If
#End Region

End Class
