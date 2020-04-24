Imports System.Net
Imports System.IO
Imports System.Text
'Imports System.Runtime.Serialization.Json

Public Class Plan
    'Private cred As System.Net.ICredentials = New NetworkCredential("ag", "ag")
    'Private ser As New DataContractJsonSerializer(GetType(List(Of Lesson)))
    Dim origList As New List(Of Lesson) 'for checking changes
    Dim beganEdit As Boolean = False
    Private Sub FetchData() Handles Me.Load, CMDRefresh.Click
        Try
            If beganEdit = True Then
                If MessageBox.Show("Alle Änderungen gespeichert?", "Achtung!", MessageBoxButtons.YesNo, MessageBoxIcon.Exclamation, MessageBoxDefaultButton.Button2) = MsgBoxResult.No Then
                    Exit Sub
                End If
            End If

            'get json from server
            Dim uri As String = My.Settings.downloadUrl & App.Config.User("token")
            Dim wc As New WebClient
            AddHandler wc.DownloadDataCompleted, AddressOf DownloadCompletedHander
            wc.DownloadDataAsync(New Uri(uri))

            beganEdit = False
        Catch ex As Exception
            MsgBox("Uncaught error: could not fetch data.", MsgBoxStyle.Exclamation)
            Me.Close()
        End Try
    End Sub

    Private Sub DownloadCompletedHander(ByVal sender As Object, ByVal e As DownloadDataCompletedEventArgs)
        Try
            If e.Cancelled = False AndAlso e.Error Is Nothing Then
                Dim data As Stream = New MemoryStream(e.Result)

                'deserialize json to lsit
                Dim myList As List(Of Lesson) = CType(App.Library.serLesson.ReadObject(data), List(Of Lesson))
                origList = myList

                'display list in datagridview
                RefreshDatagridView(myList)
            Else
                DownloadError()
            End If
        Catch ex As Exception
            MsgBox("Uncaught error: could not fetch data.", MsgBoxStyle.Exclamation)
            Me.Close()
        End Try
    End Sub

    Private Sub DownloadError()
        MsgBox("Abrufen der Daten fehlgeschlagen!", MsgBoxStyle.Critical)
        Me.Close()
    End Sub

    Private Sub BindingNavigatorAddNewItem_Click(sender As Object, e As EventArgs) Handles BindingNavigatorAddNewItem.Click
        Dim myList As List(Of Lesson) = DataGridView1.DataSource

        'creat new Eintrag and add it to the list
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
        myList.Add(xItem)

        'refresh DataGridView
        RefreshDatagridView(myList)
    End Sub

    Private Sub BindingNavigatorDeleteItem_Click(sender As Object, e As EventArgs) Handles BindingNavigatorDeleteItem.Click
        Dim myList As List(Of Lesson) = DataGridView1.DataSource
        myList.RemoveAt(DataGridView1.CurrentRow.Index)
        RefreshDatagridView(myList)
    End Sub

    Private Sub CMDClear_Click(sender As Object, e As EventArgs) Handles CMDClear.Click
        If MessageBox.Show("Sollen wirklich alle Einträge gelöscht werden?", "Achtung!", MessageBoxButtons.YesNo, MessageBoxIcon.Exclamation, MessageBoxDefaultButton.Button2) = MsgBoxResult.Yes Then
            Dim myList As List(Of Lesson) = DataGridView1.DataSource
            myList.Clear()
            RefreshDatagridView(myList)
            beganEdit = True
        End If
    End Sub

    Private Sub RefreshDatagridView(ByVal myList As List(Of Lesson))
        DataGridView1.DataSource = Nothing
        DataGridView1.DataSource = myList
    End Sub

    Private Sub DataGridView1_CellClick(sender As Object, e As DataGridViewCellEventArgs) Handles DataGridView1.CellClick
        sender.BeginEdit(True)
    End Sub

    Private Sub DataGridView1_CellBeginEdit(sender As Object, e As DataGridViewCellCancelEventArgs) Handles DataGridView1.CellBeginEdit
        beganEdit = True
    End Sub

    Private Sub DataGridView1_ColumnHeaderMouseClick(sender As Object, e As DataGridViewCellMouseEventArgs) Handles DataGridView1.ColumnHeaderMouseClick
        'If DataGridView1.Columns(e.ColumnIndex).HeaderText = "datum" Then
        '    DataGridView1.Sort(DataGridView1.Columns("datum"), System.ComponentModel.ListSortDirection.Ascending)
        'End If
    End Sub

    Private Sub SpeichernToolStripButton_Click(sender As Object, e As EventArgs) Handles SpeichernToolStripButton.Click
        DataGridView1.CommitEdit(DataGridViewDataErrorContexts.Commit)
        Dim output As String = ""
        Using ms As New MemoryStream()
            App.Library.serLesson.WriteObject(ms, DataGridView1.DataSource)
            output = Encoding.UTF8.GetString(ms.ToArray())
            'My.Application.Log.WriteEntry(output, TraceEventType.Information) 'Tracking!!!!!!
            'Exit Sub
        End Using

        'Upload result
        'Set up connection
        Dim client As WebClient = New WebClient()
        '  Encoding + Creditals
        client.Encoding = System.Text.Encoding.UTF8
        'client.Credentials = cred
        client.Headers(HttpRequestHeader.ContentType) = "application/x-www-form-urlencoded"

        '  Upload the data. 
        Dim reply As String = client.UploadString(My.Settings.uploadJsonUrl & App.Config.User("token"), "input=" & output)

        'My.Application.Log.WriteEntry(reply, TraceEventType.Information)
        'MsgBox(reply)

        '  Check the server's response.
        If reply.Contains("success") Then
            LBLStatus.Text = "Upload erfolgreich!"
            beganEdit = False
        Else
            LBLStatus.Text = "Änderungen konnten nicht übernommen werden!!"
        End If
    End Sub

    Private Sub CMDPrint_Click(sender As Object, e As EventArgs) Handles CMDPrint.Click
        SpeichernToolStripButton_Click(sender, e)
        If LBLStatus.Text = "Upload erfolgreich!" Then
            Process.Start(My.Settings.printUrl & App.Config.User("token"))
        End If
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