Imports System.Net
Imports System.IO
Imports System.Text
Imports System.Web.Script.Serialization

Public Class Upload
    Private Path As String
    Private Sub CMDPath_Click(sender As Object, e As EventArgs) Handles CMDPath.Click
        OFD.ShowDialog()
        Path = OFD.FileName
        LBLPath.Text = Path
        CMDUpload.Enabled = True
    End Sub

    Private Sub CMDUpload_Click(sender As Object, e As EventArgs) Handles CMDUpload.Click
        Dim jss = New JavaScriptSerializer()
        Dim response As Object
        response = App.Library.UploadTurbo(Path)
        response = jss.Deserialize(Of Dictionary(Of String, Object))(response)

        If response Is Nothing OrElse response("success") Is Nothing OrElse response("success") = False Then
            If Not My.Computer.Network.IsAvailable Then
                MsgBox("Keine Internetverbindung.", MsgBoxStyle.Critical)
            Else
                MsgBox("Änderungen konnten nicht übernommen werden.", MsgBoxStyle.Critical)
            End If
        Else
            MsgBox("Upload erfolgreich (Stand: " & response("last_update") & ")!", MsgBoxStyle.Information)
        End If
    End Sub
    Private Sub Plan_FormClosed(sender As Object, e As FormClosedEventArgs) Handles Me.FormClosed
        Main.Show()
    End Sub
End Class