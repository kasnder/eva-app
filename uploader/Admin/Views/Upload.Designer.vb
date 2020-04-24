<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class Upload
    Inherits System.Windows.Forms.Form

    'Das Formular überschreibt den Löschvorgang, um die Komponentenliste zu bereinigen.
    <System.Diagnostics.DebuggerNonUserCode()> _
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    'Wird vom Windows Form-Designer benötigt.
    Private components As System.ComponentModel.IContainer

    'Hinweis: Die folgende Prozedur ist für den Windows Form-Designer erforderlich.
    'Das Bearbeiten ist mit dem Windows Form-Designer möglich.  
    'Das Bearbeiten mit dem Code-Editor ist nicht möglich.
    <System.Diagnostics.DebuggerStepThrough()> _
    Private Sub InitializeComponent()
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(Upload))
        Me.OFD = New System.Windows.Forms.OpenFileDialog()
        Me.LBLPath = New System.Windows.Forms.Label()
        Me.CMDPath = New System.Windows.Forms.Button()
        Me.CMDUpload = New System.Windows.Forms.Button()
        Me.CBClear = New System.Windows.Forms.CheckBox()
        Me.SuspendLayout()
        '
        'OFD
        '
        Me.OFD.Filter = "CSV-Dateien|*.csv|Alle Dateien|*.*"
        '
        'LBLPath
        '
        Me.LBLPath.Location = New System.Drawing.Point(12, 13)
        Me.LBLPath.Name = "LBLPath"
        Me.LBLPath.Size = New System.Drawing.Size(226, 23)
        Me.LBLPath.TabIndex = 0
        Me.LBLPath.Text = "Datei auswählen.."
        Me.LBLPath.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'CMDPath
        '
        Me.CMDPath.Location = New System.Drawing.Point(244, 13)
        Me.CMDPath.Name = "CMDPath"
        Me.CMDPath.Size = New System.Drawing.Size(28, 23)
        Me.CMDPath.TabIndex = 1
        Me.CMDPath.Text = "..."
        Me.CMDPath.UseVisualStyleBackColor = True
        '
        'CMDUpload
        '
        Me.CMDUpload.Enabled = False
        Me.CMDUpload.Location = New System.Drawing.Point(12, 65)
        Me.CMDUpload.Name = "CMDUpload"
        Me.CMDUpload.Size = New System.Drawing.Size(260, 23)
        Me.CMDUpload.TabIndex = 2
        Me.CMDUpload.Text = "Hochladen"
        Me.CMDUpload.UseVisualStyleBackColor = True
        '
        'CBClear
        '
        Me.CBClear.Enabled = False
        Me.CBClear.Location = New System.Drawing.Point(156, 42)
        Me.CBClear.Name = "CBClear"
        Me.CBClear.Size = New System.Drawing.Size(116, 17)
        Me.CBClear.TabIndex = 3
        Me.CBClear.Text = "Alte Daten löschen"
        Me.CBClear.UseVisualStyleBackColor = True
        '
        'Upload
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(284, 98)
        Me.Controls.Add(Me.CBClear)
        Me.Controls.Add(Me.CMDUpload)
        Me.Controls.Add(Me.CMDPath)
        Me.Controls.Add(Me.LBLPath)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog
        Me.Icon = CType(resources.GetObject("$this.Icon"), System.Drawing.Icon)
        Me.MaximizeBox = False
        Me.Name = "Upload"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.Text = "Upload"
        Me.ResumeLayout(False)

    End Sub
    Friend WithEvents OFD As System.Windows.Forms.OpenFileDialog
    Friend WithEvents LBLPath As System.Windows.Forms.Label
    Friend WithEvents CMDPath As System.Windows.Forms.Button
    Friend WithEvents CMDUpload As System.Windows.Forms.Button
    Friend WithEvents CBClear As System.Windows.Forms.CheckBox
End Class
