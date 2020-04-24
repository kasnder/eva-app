Imports System.Net
Imports System.IO
Imports System.Text
Imports System.Runtime.Serialization.Json

Public Class Corrections
    Private serLesson As New DataContractJsonSerializer(GetType(List(Of Lesson)))

    ' To inform the user about unsaved changes
    Dim beganEdit As Boolean = False
#Region "Fetch current schedule"
    Private Sub FetchData() Handles Me.Load, CMDRefresh.Click
        Try
            If beganEdit = True Then
                If MessageBox.Show("Alle Änderungen gespeichert?", "Achtung!", MessageBoxButtons.YesNo, MessageBoxIcon.Exclamation, MessageBoxDefaultButton.Button2) = MsgBoxResult.No Then
                    Exit Sub
                End If
            End If

            ' Receive some JSON from the server
            Dim uri As String = My.Settings.host & "?component=content&view=json&token=" & Main.User("token")
            Dim wc As New WebClient
            AddHandler wc.DownloadDataCompleted, AddressOf DownloadCompletedHander
            wc.DownloadDataAsync(New Uri(uri))

            beganEdit = False
        Catch ex As Exception
            MsgBox("Abrufen der Daten fehlgeschlagen.", MsgBoxStyle.Exclamation)
            Me.Close()
        End Try
    End Sub

    Private Sub DownloadCompletedHander(ByVal sender As Object, ByVal e As DownloadDataCompletedEventArgs)
        Try
            If e.Cancelled = False AndAlso e.Error Is Nothing Then
                Dim data As Stream = New MemoryStream(e.Result)

                ' Deserialize the received JSON to a list
                Dim myList As List(Of Lesson) = CType(serLesson.ReadObject(data), List(Of Lesson))

                ' TODO Fix this issue
                If myList.Count = 0 Then
                    MsgBox("Keine Daten vorhanden. Kein Editieren möglich.", MsgBoxStyle.Exclamation)
                    Me.Close()
                End If

                ' Display the list in our datagridview
                RefreshDatagridView(myList)
            Else
                DownloadError()
            End If
        Catch ex As Exception
            MsgBox("Abrufen der Daten fehlgeschlagen.", MsgBoxStyle.Exclamation)
            Me.Close()
        End Try
    End Sub

    Private Sub DownloadError()
        MsgBox("Abrufen der Daten fehlgeschlagen!", MsgBoxStyle.Critical)
        Me.Close()
    End Sub
#End Region
#Region "Manage the DataGridView"
    Private Sub BindingNavigatorAddNewItem_Click(sender As Object, e As EventArgs) Handles BindingNavigatorAddNewItem.Click
        Dim myList As List(Of Lesson) = ScheduleView.DataSource

        ' Create a new entry and add it to the list
        Dim xItem As New Lesson
        xItem.bemerkung = ""
        xItem.datum = Format(Date.Now, "yyyy-MM-dd")
        xItem.fach = ""
        xItem.klasse = ""
        xItem.lehrerid = ""
        xItem.vertretung = ""
        xItem.raum = ""
        xItem.aufsicht = False
        xItem.wichtig = False

        xItem.hinzufuegen = True
        xItem.loeschen = False

        myList.Add(xItem)

        ' Refresh the DataGridView
        RefreshDatagridView(myList)
    End Sub

    Private Sub BindingNavigatorDeleteItem_Click(sender As Object, e As EventArgs) Handles BindingNavigatorDeleteItem.Click
        Dim myList As List(Of Lesson) = ScheduleView.DataSource
        myList.RemoveAt(ScheduleView.CurrentRow.Index)
        RefreshDatagridView(myList)
    End Sub

    Private Sub CMDClear_Click(sender As Object, e As EventArgs) Handles CMDClear.Click
        If MessageBox.Show("Sollen wirklich alle Einträge gelöscht werden?", "Achtung!", MessageBoxButtons.YesNo, MessageBoxIcon.Exclamation, MessageBoxDefaultButton.Button2) = MsgBoxResult.Yes Then
            Dim myList As List(Of Lesson) = ScheduleView.DataSource
            myList.Clear()
            RefreshDatagridView(myList)
            beganEdit = True
        End If
    End Sub

    Private Sub RefreshDatagridView(ByVal myList As List(Of Lesson))
        ScheduleView.DataSource = Nothing
        ScheduleView.DataSource = myList
    End Sub

    Private Sub DataGridView1_CellClick(sender As Object, e As DataGridViewCellEventArgs) Handles ScheduleView.CellClick
        sender.BeginEdit(True)
    End Sub

    Private Sub DataGridView1_CellBeginEdit(sender As Object, e As DataGridViewCellCancelEventArgs) Handles ScheduleView.CellBeginEdit
        beganEdit = True
    End Sub
#End Region

    Private Sub SpeichernToolStripButton_Click(sender As Object, e As EventArgs) Handles SpeichernToolStripButton.Click
        ScheduleView.CommitEdit(DataGridViewDataErrorContexts.Commit)
        Dim output As String = ""
        Using ms As New MemoryStream()
            serLesson.WriteObject(ms, ScheduleView.DataSource)
            output = Encoding.UTF8.GetString(ms.ToArray())
        End Using

        'Upload result
        'Set up connection
        Dim client As WebClient = New WebClient()
        client.Encoding = System.Text.Encoding.UTF8
        client.Headers(HttpRequestHeader.ContentType) = "application/x-www-form-urlencoded"

        'Get creditals
        Dim username As String = My.Settings.username
        Dim password As String = Crypto.DecryptString(My.Settings.password)

        '  Upload the data.
        Dim reply As String = client.UploadString(My.Settings.host & "?component=upload&view=json&username=" & username & "&password=" & password, "input=" & output)

        'My.Application.Log.WriteEntry(reply, TraceEventType.Information)

        '  Check the server's response.
        If reply.Contains("success") Then
            LBLStatus.Text = "Upload erfolgreich!"
            beganEdit = False
        Else
            LBLStatus.Text = "Änderungen konnten nicht übernommen werden!!"
        End If
    End Sub

    Private Sub CMDPrint_Click(sender As Object, e As EventArgs) Handles CMDPrint.Click
        Main.Print(sender, e)
    End Sub

    Private Sub Main_FormClosing(sender As Object, e As FormClosingEventArgs) Handles Me.FormClosing
        If beganEdit = True Then
            If MessageBox.Show("Alle Änderungen gespeichert?", "Achtung!", MessageBoxButtons.YesNo, MessageBoxIcon.Exclamation, MessageBoxDefaultButton.Button2) = MsgBoxResult.No Then
                e.Cancel = True
            End If
        End If
    End Sub
    Private Sub Plan_FormClosed(sender As Object, e As FormClosedEventArgs) Handles Me.FormClosed
        Main.Show()
    End Sub
End Class