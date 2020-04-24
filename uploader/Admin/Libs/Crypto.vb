Imports System.Security.Cryptography

Namespace App
    Public Class Crypto
        Private Shared TripleDes As New TripleDESCryptoServiceProvider
        Private Shared Function TruncateHash(
    ByVal key As String,
    ByVal length As Integer) As Byte()

            Dim sha1 As New SHA1CryptoServiceProvider

            ' Hash the key.
            Dim keyBytes() As Byte =
                System.Text.Encoding.Unicode.GetBytes(key)
            Dim hash() As Byte = sha1.ComputeHash(keyBytes)

            ' Truncate or pad the hash.
            ReDim Preserve hash(length - 1)
            Return hash
        End Function

        Private Shared Function GetKey(
    ByVal key As String) As String
            'Setting auslesen
            If key Is Nothing Then key = My.Settings.cryptoKey

            'Local password encryption initialised?
            '-> get random key for encryption
            If My.Settings.cryptoKey = "" Then key = App.Crypto.GetSeed(32)
            Return key
        End Function

        Public Shared Function EncryptString(ByVal plaintext As String, Optional ByVal key As String = Nothing) As String
            If key Is Nothing Then key = My.Settings.cryptoKey

            ' Initialize the crypto provider.
            TripleDes.Key = App.Crypto.TruncateHash(key, TripleDes.KeySize \ 8)
            TripleDes.IV = App.Crypto.TruncateHash("", TripleDes.BlockSize \ 8)

            ' Convert the plaintext string to a byte array.
            Dim plaintextBytes() As Byte =
                System.Text.Encoding.Unicode.GetBytes(plaintext)

            ' Create the stream.
            Dim ms As New System.IO.MemoryStream
            ' Create the encoder to write to the stream.
            Dim encStream As New CryptoStream(ms,
                TripleDes.CreateEncryptor(),
                System.Security.Cryptography.CryptoStreamMode.Write)

            ' Use the crypto stream to write the byte array to the stream.
            encStream.Write(plaintextBytes, 0, plaintextBytes.Length)
            encStream.FlushFinalBlock()

            ' Convert the encrypted stream to a printable string.
            Return Convert.ToBase64String(ms.ToArray)
        End Function

        Public Shared Function DecryptString(ByVal encryptedtext As String, Optional ByVal key As String = Nothing) As String
            If key Is Nothing Then key = My.Settings.cryptoKey

            ' Initialize the crypto provider.
            TripleDes.Key = App.Crypto.TruncateHash(key, TripleDes.KeySize \ 8)
            TripleDes.IV = App.Crypto.TruncateHash("", TripleDes.BlockSize \ 8)

            ' Convert the encrypted text string to a byte array.
            Dim encryptedBytes() As Byte = Convert.FromBase64String(encryptedtext)

            ' Create the stream.
            Dim ms As New System.IO.MemoryStream
            ' Create the decoder to write to the stream.
            Dim decStream As New CryptoStream(ms,
                TripleDes.CreateDecryptor(),
                System.Security.Cryptography.CryptoStreamMode.Write)

            ' Use the crypto stream to write the byte array to the stream.
            decStream.Write(encryptedBytes, 0, encryptedBytes.Length)
            decStream.FlushFinalBlock()

            ' Convert the plaintext stream to a string.
            Return System.Text.Encoding.Unicode.GetString(ms.ToArray)
        End Function

        Public Shared Function GetSeed(ByVal length As Integer)
            'Make random random
            Randomize(Now.ToOADate())

            Dim sResult As String = ""
            Dim rdm As New Random()

            For i As Integer = 1 To length
                sResult &= ChrW(rdm.Next(32, 126))
            Next

            Return sResult
        End Function

    End Class
End Namespace