Public Class Main

    Private Sub Main_Shown(sender As Object, e As EventArgs) Handles Me.Shown
        LBLWelcome.Text = "Hallo " & App.Config.User("firstname") & " " & App.Config.User("lastname") & "!"
    End Sub
#Region "Komponenten"
    'Die einzelnen Forms zeigen.
    Private Sub CMDPlan_Click(sender As Object, e As EventArgs) Handles CMDPlan.Click
        Plan.Show()
        Me.Hide()
    End Sub

    Private Sub CMDUpload_Click(sender As Object, e As EventArgs) Handles CMDUpload.Click
        Upload.Show()
        Me.Hide()
    End Sub

    Private Sub CMDApp_Settings_Click(sender As Object, e As EventArgs) Handles CMDApp_Settings.Click
        App_Settings.ShowDialog()
    End Sub
#End Region

    Private Sub CMDLogout_Click(sender As Object, e As EventArgs) Handles CMDLogout.Click
        'Lokale Userdaten löschen
        '-> Token, Name, ..
        App.Config.Reset()

        'Synchronisation beenden
        Login.Watcher.EnableRaisingEvents = False

        'zurück zur Anmeldeform wechseln
        Login.Show()
        Me.Hide()
    End Sub

    Private Sub Main_FormClosing(sender As Object, e As FormClosingEventArgs) Handles Me.FormClosing
        If e.CloseReason = CloseReason.UserClosing Then
            Me.Hide()
            e.Cancel = True
            'Login.ShowBalloon(50, "EVa Administration", "Ich laufe weiter und warte auf Änderungen!", ToolTipIcon.Info)
        Else
            CMDLogout_Click(sender, e)
            Environment.Exit(0)
        End If
    End Sub
End Class