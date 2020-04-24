<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class App_Settings
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
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(App_Settings))
        Me.CMDClose = New System.Windows.Forms.Button()
        Me.CMDPath = New System.Windows.Forms.Button()
        Me.LBLPath = New System.Windows.Forms.Label()
        Me.FBD = New System.Windows.Forms.FolderBrowserDialog()
        Me.PathLabel = New System.Windows.Forms.Label()
        Me.GroupSync = New System.Windows.Forms.GroupBox()
        Me.TXTPassword = New System.Windows.Forms.TextBox()
        Me.TXTUsername = New System.Windows.Forms.TextBox()
        Me.PasswordLabel = New System.Windows.Forms.Label()
        Me.UsernameLabel = New System.Windows.Forms.Label()
        Me.CBSyncActive = New System.Windows.Forms.CheckBox()
        Me.CBBalloons = New System.Windows.Forms.CheckBox()
        Me.GroupSync.SuspendLayout()
        Me.SuspendLayout()
        '
        'CMDClose
        '
        Me.CMDClose.Anchor = CType(((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDClose.Location = New System.Drawing.Point(15, 225)
        Me.CMDClose.Name = "CMDClose"
        Me.CMDClose.Size = New System.Drawing.Size(258, 42)
        Me.CMDClose.TabIndex = 3
        Me.CMDClose.Text = "Schließen"
        Me.CMDClose.UseVisualStyleBackColor = True
        '
        'CMDPath
        '
        Me.CMDPath.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDPath.Location = New System.Drawing.Point(224, 30)
        Me.CMDPath.Name = "CMDPath"
        Me.CMDPath.Size = New System.Drawing.Size(28, 23)
        Me.CMDPath.TabIndex = 2
        Me.CMDPath.Text = "..."
        Me.CMDPath.UseVisualStyleBackColor = True
        '
        'LBLPath
        '
        Me.LBLPath.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.LBLPath.BackColor = System.Drawing.SystemColors.ControlLight
        Me.LBLPath.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.LBLPath.Location = New System.Drawing.Point(9, 30)
        Me.LBLPath.Name = "LBLPath"
        Me.LBLPath.Size = New System.Drawing.Size(209, 23)
        Me.LBLPath.TabIndex = 1
        Me.LBLPath.Text = "Ordner auswählen.."
        Me.LBLPath.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'PathLabel
        '
        Me.PathLabel.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.PathLabel.Location = New System.Drawing.Point(6, 16)
        Me.PathLabel.Name = "PathLabel"
        Me.PathLabel.Size = New System.Drawing.Size(246, 17)
        Me.PathLabel.TabIndex = 0
        Me.PathLabel.Text = "&Planpfad"
        '
        'GroupSync
        '
        Me.GroupSync.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.GroupSync.Controls.Add(Me.TXTPassword)
        Me.GroupSync.Controls.Add(Me.TXTUsername)
        Me.GroupSync.Controls.Add(Me.PasswordLabel)
        Me.GroupSync.Controls.Add(Me.UsernameLabel)
        Me.GroupSync.Controls.Add(Me.LBLPath)
        Me.GroupSync.Controls.Add(Me.CMDPath)
        Me.GroupSync.Controls.Add(Me.PathLabel)
        Me.GroupSync.Location = New System.Drawing.Point(12, 58)
        Me.GroupSync.Name = "GroupSync"
        Me.GroupSync.Size = New System.Drawing.Size(258, 155)
        Me.GroupSync.TabIndex = 2
        Me.GroupSync.TabStop = False
        Me.GroupSync.Text = "Synchronisationseinstellungen"
        '
        'TXTPassword
        '
        Me.TXTPassword.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.TXTPassword.Location = New System.Drawing.Point(8, 119)
        Me.TXTPassword.Name = "TXTPassword"
        Me.TXTPassword.PasswordChar = Global.Microsoft.VisualBasic.ChrW(8226)
        Me.TXTPassword.Size = New System.Drawing.Size(246, 20)
        Me.TXTPassword.TabIndex = 6
        '
        'TXTUsername
        '
        Me.TXTUsername.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.TXTUsername.Location = New System.Drawing.Point(8, 76)
        Me.TXTUsername.Name = "TXTUsername"
        Me.TXTUsername.Size = New System.Drawing.Size(246, 20)
        Me.TXTUsername.TabIndex = 4
        '
        'PasswordLabel
        '
        Me.PasswordLabel.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.PasswordLabel.Location = New System.Drawing.Point(6, 99)
        Me.PasswordLabel.Name = "PasswordLabel"
        Me.PasswordLabel.Size = New System.Drawing.Size(246, 23)
        Me.PasswordLabel.TabIndex = 5
        Me.PasswordLabel.Text = "&Kennwort"
        Me.PasswordLabel.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'UsernameLabel
        '
        Me.UsernameLabel.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.UsernameLabel.Location = New System.Drawing.Point(6, 56)
        Me.UsernameLabel.Name = "UsernameLabel"
        Me.UsernameLabel.Size = New System.Drawing.Size(246, 23)
        Me.UsernameLabel.TabIndex = 3
        Me.UsernameLabel.Text = "&Benutzername"
        Me.UsernameLabel.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'CBSyncActive
        '
        Me.CBSyncActive.AutoSize = True
        Me.CBSyncActive.Checked = True
        Me.CBSyncActive.CheckState = System.Windows.Forms.CheckState.Checked
        Me.CBSyncActive.Location = New System.Drawing.Point(12, 35)
        Me.CBSyncActive.Name = "CBSyncActive"
        Me.CBSyncActive.Size = New System.Drawing.Size(150, 17)
        Me.CBSyncActive.TabIndex = 1
        Me.CBSyncActive.Text = "Synchronisation aktivieren"
        Me.CBSyncActive.UseVisualStyleBackColor = True
        '
        'CBBalloons
        '
        Me.CBBalloons.AutoSize = True
        Me.CBBalloons.Location = New System.Drawing.Point(12, 12)
        Me.CBBalloons.Name = "CBBalloons"
        Me.CBBalloons.Size = New System.Drawing.Size(172, 17)
        Me.CBBalloons.TabIndex = 0
        Me.CBBalloons.Text = "Benachrichtigungen verbergen"
        Me.CBBalloons.UseVisualStyleBackColor = True
        '
        'App_Settings
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(282, 279)
        Me.Controls.Add(Me.CBBalloons)
        Me.Controls.Add(Me.CBSyncActive)
        Me.Controls.Add(Me.GroupSync)
        Me.Controls.Add(Me.CMDClose)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.SizableToolWindow
        Me.Icon = CType(resources.GetObject("$this.Icon"), System.Drawing.Icon)
        Me.Name = "App_Settings"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.Text = "Einstellungen"
        Me.GroupSync.ResumeLayout(False)
        Me.GroupSync.PerformLayout()
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub
    Friend WithEvents CMDClose As System.Windows.Forms.Button
    Friend WithEvents CMDPath As System.Windows.Forms.Button
    Friend WithEvents LBLPath As System.Windows.Forms.Label
    Friend WithEvents FBD As System.Windows.Forms.FolderBrowserDialog
    Friend WithEvents PathLabel As System.Windows.Forms.Label
    Friend WithEvents GroupSync As System.Windows.Forms.GroupBox
    Friend WithEvents CBSyncActive As System.Windows.Forms.CheckBox
    Friend WithEvents TXTPassword As System.Windows.Forms.TextBox
    Friend WithEvents TXTUsername As System.Windows.Forms.TextBox
    Friend WithEvents PasswordLabel As System.Windows.Forms.Label
    Friend WithEvents UsernameLabel As System.Windows.Forms.Label
    Friend WithEvents CBBalloons As System.Windows.Forms.CheckBox
End Class
