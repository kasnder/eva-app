Imports System.Runtime.Serialization.Json
Imports System.IO
Imports System.Net
Imports System.Runtime.Serialization.Formatters.Binary
Imports System.Security.Cryptography

Namespace App
    Public Class Library
        Public Shared serLesson As New DataContractJsonSerializer(GetType(List(Of Lesson)))
        Public Shared Function ReadProcessArguments(ByVal Argument As String) As Boolean
            Return Environment.GetCommandLineArgs().Contains(Argument)
        End Function

        Public Shared Function SyncConfigValid() As Boolean
            'Ohne Check, ob Logindaten korrekt sind!
            Return (My.Settings.syncActive AndAlso _
                My.Computer.FileSystem.DirectoryExists(My.Settings.syncDir) And _
                Not My.Settings.username = "" And Not My.Settings.password = "") Or (My.Settings.syncActive = False)
        End Function

        Public Shared Function UploadTurbo(ByVal Path As String) As String
            'Wir wollen keine unschönen Fehlermeldungen..
            Try
                'Existiert die Datei?
                If Not My.Computer.FileSystem.FileExists(Path) Then Return False

                'Datei einlesen
                Dim output As String = ""
                Using sr As New StreamReader(Path, System.Text.Encoding.GetEncoding(1252))
                    output = sr.ReadToEnd()
                End Using

                'Inhalt hochladen
                'Verbindung herstellen
                Dim client As WebClient = New WebClient()
                client.Encoding = System.Text.Encoding.UTF8
                client.Headers(HttpRequestHeader.ContentType) = "application/x-www-form-urlencoded"

                'Datein POSTen.
                Dim reply As String = client.UploadString(My.Settings.uploadTurboUrl & App.Config.User("token"), "input=" & output)

                'Rückgabe checken
                'If Not reply.Contains("true") Then reply = ""
                Return reply
            Catch ex As Exception
                Return ""
            End Try
        End Function

        Public Shared Sub Log(ByVal Text As String)
            My.Application.Log.WriteEntry(Text, TraceEventType.Information)
        End Sub

        'Public Shared Function SHA1ofFile(ByVal Path As String) As String
        '    Try
        '        ' Open the binary file.
        '        Dim streamBinary As New FileStream(Path, FileMode.Open)

        '        ' Create a binary stream reader object.
        '        Dim readerInput As BinaryReader = New BinaryReader(streamBinary)

        '        ' Determine the number of bytes to read.
        '        Dim lengthFile As Integer = My.Computer.FileSystem.GetFileInfo(Path).Length

        '        ' Read the data in a byte array buffer.
        '        Dim inputData As Byte() = readerInput.ReadBytes(lengthFile)

        '        ' Close the file.
        '        streamBinary.Close()
        '        readerInput.Close()

        '        Dim sha As New SHA1CryptoServiceProvider()
        '        ' This is one implementation of the abstract class SHA1.
        '        'Dim enc As System.Text.Encoding = System.Text.Encoding.GetEncoding(1252).GetString()
        '        Return System.Text.Encoding.GetEncoding(1252).GetString(sha.ComputeHash(inputData))
        '    Catch ex As Exception
        '        Return False
        '    End Try
        'End Function

        Public Shared Function SHA1FileHash(ByVal sFile As String) As String
            Dim SHA1 As New SHA1CryptoServiceProvider
            Dim Hash As Byte()
            Dim Result As String = ""
            Dim Tmp As String = ""

            Dim FN As New FileStream(sFile, FileMode.Open, FileAccess.Read, FileShare.Read, 8192)
            SHA1.ComputeHash(FN)
            FN.Close()

            Hash = SHA1.Hash
            For i As Integer = 0 To Hash.Length - 1
                Tmp = Hex(Hash(i))
                If Len(Tmp) = 1 Then Tmp = "0" & Tmp
                Result += Tmp
            Next
            Return Result
        End Function
    End Class
End Namespace