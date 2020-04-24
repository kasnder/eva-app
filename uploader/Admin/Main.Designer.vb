<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class Main
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
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(Main))
        Me.CMDPlan = New System.Windows.Forms.Button()
        Me.CMDNote = New System.Windows.Forms.Button()
        Me.CMDUsers = New System.Windows.Forms.Button()
        Me.LBLWelcome = New System.Windows.Forms.Label()
        Me.CMDLogout = New System.Windows.Forms.Button()
        Me.CMDUpload = New System.Windows.Forms.Button()
        Me.CMDSettings = New System.Windows.Forms.Button()
        Me.CMDApp_Settings = New System.Windows.Forms.Button()
        Me.SuspendLayout()
        '
        'CMDPlan
        '
        Me.CMDPlan.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDPlan.Location = New System.Drawing.Point(12, 50)
        Me.CMDPlan.Name = "CMDPlan"
        Me.CMDPlan.Size = New System.Drawing.Size(260, 40)
        Me.CMDPlan.TabIndex = 0
        Me.CMDPlan.Text = "Vertretungsplan eingeben"
        Me.CMDPlan.UseVisualStyleBackColor = True
        '
        'CMDNote
        '
        Me.CMDNote.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDNote.Enabled = False
        Me.CMDNote.Location = New System.Drawing.Point(12, 142)
        Me.CMDNote.Name = "CMDNote"
        Me.CMDNote.Size = New System.Drawing.Size(260, 40)
        Me.CMDNote.TabIndex = 1
        Me.CMDNote.Text = "Hinweise verwalten"
        Me.CMDNote.UseVisualStyleBackColor = True
        '
        'CMDUsers
        '
        Me.CMDUsers.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDUsers.Enabled = False
        Me.CMDUsers.Location = New System.Drawing.Point(12, 188)
        Me.CMDUsers.Name = "CMDUsers"
        Me.CMDUsers.Size = New System.Drawing.Size(260, 40)
        Me.CMDUsers.TabIndex = 2
        Me.CMDUsers.Text = "Benutzerliste"
        Me.CMDUsers.UseVisualStyleBackColor = True
        '
        'LBLWelcome
        '
        Me.LBLWelcome.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.LBLWelcome.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Italic, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.LBLWelcome.Location = New System.Drawing.Point(13, 13)
        Me.LBLWelcome.Name = "LBLWelcome"
        Me.LBLWelcome.Size = New System.Drawing.Size(258, 34)
        Me.LBLWelcome.TabIndex = 3
        Me.LBLWelcome.Text = "Hallo <User>!"
        Me.LBLWelcome.TextAlign = System.Drawing.ContentAlignment.TopCenter
        '
        'CMDLogout
        '
        Me.CMDLogout.Anchor = CType(((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDLogout.Location = New System.Drawing.Point(12, 305)
        Me.CMDLogout.Name = "CMDLogout"
        Me.CMDLogout.Size = New System.Drawing.Size(259, 21)
        Me.CMDLogout.TabIndex = 4
        Me.CMDLogout.Text = "Ausloggen"
        Me.CMDLogout.UseVisualStyleBackColor = True
        '
        'CMDUpload
        '
        Me.CMDUpload.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDUpload.Location = New System.Drawing.Point(12, 96)
        Me.CMDUpload.Name = "CMDUpload"
        Me.CMDUpload.Size = New System.Drawing.Size(260, 40)
        Me.CMDUpload.TabIndex = 5
        Me.CMDUpload.Text = "Vertretungsplan hochladen"
        Me.CMDUpload.UseVisualStyleBackColor = True
        '
        'CMDSettings
        '
        Me.CMDSettings.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDSettings.Enabled = False
        Me.CMDSettings.Location = New System.Drawing.Point(12, 234)
        Me.CMDSettings.Name = "CMDSettings"
        Me.CMDSettings.Size = New System.Drawing.Size(260, 40)
        Me.CMDSettings.TabIndex = 6
        Me.CMDSettings.Text = "Serverkonfiguration"
        Me.CMDSettings.UseVisualStyleBackColor = True
        '
        'CMDApp_Settings
        '
        Me.CMDApp_Settings.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDApp_Settings.BackgroundImage = CType(resources.GetObject("CMDApp_Settings.BackgroundImage"), System.Drawing.Image)
        Me.CMDApp_Settings.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Stretch
        Me.CMDApp_Settings.Location = New System.Drawing.Point(248, 8)
        Me.CMDApp_Settings.Name = "CMDApp_Settings"
        Me.CMDApp_Settings.Size = New System.Drawing.Size(24, 23)
        Me.CMDApp_Settings.TabIndex = 8
        Me.CMDApp_Settings.UseVisualStyleBackColor = True
        '
        'Main
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(283, 338)
        Me.Controls.Add(Me.CMDApp_Settings)
        Me.Controls.Add(Me.CMDSettings)
        Me.Controls.Add(Me.CMDUpload)
        Me.Controls.Add(Me.CMDLogout)
        Me.Controls.Add(Me.LBLWelcome)
        Me.Controls.Add(Me.CMDUsers)
        Me.Controls.Add(Me.CMDNote)
        Me.Controls.Add(Me.CMDPlan)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog
        Me.Icon = CType(resources.GetObject("$this.Icon"), System.Drawing.Icon)
        Me.MaximizeBox = False
        Me.Name = "Main"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.Text = "OPV-Admin: Auswahl"
        Me.ResumeLayout(False)

    End Sub
    Friend WithEvents CMDPlan As System.Windows.Forms.Button
    Friend WithEvents CMDNote As System.Windows.Forms.Button
    Friend WithEvents CMDUsers As System.Windows.Forms.Button
    Friend WithEvents LBLWelcome As System.Windows.Forms.Label
    Friend WithEvents CMDLogout As System.Windows.Forms.Button
    Friend WithEvents CMDUpload As System.Windows.Forms.Button
    Friend WithEvents CMDSettings As System.Windows.Forms.Button
    Friend WithEvents CMDApp_Settings As System.Windows.Forms.Button
End Class
