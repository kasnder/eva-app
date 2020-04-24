<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
<Global.System.Diagnostics.CodeAnalysis.SuppressMessage("Microsoft.Naming", "CA1726")> _
Partial Class Login
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
    Friend WithEvents LogoPictureBox As System.Windows.Forms.PictureBox
    Friend WithEvents UsernameLabel As System.Windows.Forms.Label
    Friend WithEvents PasswordLabel As System.Windows.Forms.Label
    Friend WithEvents TXTUsername As System.Windows.Forms.TextBox
    Friend WithEvents TXTPassword As System.Windows.Forms.TextBox
    Friend WithEvents CMDOk As System.Windows.Forms.Button

    'Wird vom Windows Form-Designer benötigt.
    Private components As System.ComponentModel.IContainer

    'Hinweis: Die folgende Prozedur ist für den Windows Form-Designer erforderlich.
    'Das Bearbeiten ist mit dem Windows Form-Designer möglich.  
    'Das Bearbeiten mit dem Code-Editor ist nicht möglich.
    <System.Diagnostics.DebuggerStepThrough()> _
    Private Sub InitializeComponent()
        Me.components = New System.ComponentModel.Container()
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(Login))
        Me.LogoPictureBox = New System.Windows.Forms.PictureBox()
        Me.UsernameLabel = New System.Windows.Forms.Label()
        Me.PasswordLabel = New System.Windows.Forms.Label()
        Me.TXTUsername = New System.Windows.Forms.TextBox()
        Me.TXTPassword = New System.Windows.Forms.TextBox()
        Me.CMDOk = New System.Windows.Forms.Button()
        Me.CMDCancel = New System.Windows.Forms.Button()
        Me.PB = New System.Windows.Forms.ProgressBar()
        Me.Notify = New System.Windows.Forms.NotifyIcon(Me.components)
        Me.NotifyContext = New System.Windows.Forms.ContextMenuStrip(Me.components)
        Me.AnwendungÖffnenToolStripMenuItem = New System.Windows.Forms.ToolStripMenuItem()
        Me.BeendenToolStripMenuItem = New System.Windows.Forms.ToolStripMenuItem()
        Me.Watcher = New System.IO.FileSystemWatcher()
        Me.CMDSettings = New System.Windows.Forms.Button()
        Me.LBLBranding = New System.Windows.Forms.Label()
        CType(Me.LogoPictureBox, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.NotifyContext.SuspendLayout()
        CType(Me.Watcher, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'LogoPictureBox
        '
        Me.LogoPictureBox.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left), System.Windows.Forms.AnchorStyles)
        Me.LogoPictureBox.Image = CType(resources.GetObject("LogoPictureBox.Image"), System.Drawing.Image)
        Me.LogoPictureBox.Location = New System.Drawing.Point(0, 0)
        Me.LogoPictureBox.Name = "LogoPictureBox"
        Me.LogoPictureBox.Size = New System.Drawing.Size(165, 210)
        Me.LogoPictureBox.SizeMode = System.Windows.Forms.PictureBoxSizeMode.CenterImage
        Me.LogoPictureBox.TabIndex = 0
        Me.LogoPictureBox.TabStop = False
        '
        'UsernameLabel
        '
        Me.UsernameLabel.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.UsernameLabel.Location = New System.Drawing.Point(172, 24)
        Me.UsernameLabel.Name = "UsernameLabel"
        Me.UsernameLabel.Size = New System.Drawing.Size(232, 23)
        Me.UsernameLabel.TabIndex = 0
        Me.UsernameLabel.Text = "&Benutzername"
        Me.UsernameLabel.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'PasswordLabel
        '
        Me.PasswordLabel.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.PasswordLabel.Location = New System.Drawing.Point(172, 81)
        Me.PasswordLabel.Name = "PasswordLabel"
        Me.PasswordLabel.Size = New System.Drawing.Size(232, 23)
        Me.PasswordLabel.TabIndex = 2
        Me.PasswordLabel.Text = "&Kennwort"
        Me.PasswordLabel.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'TXTUsername
        '
        Me.TXTUsername.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.TXTUsername.Location = New System.Drawing.Point(174, 44)
        Me.TXTUsername.Name = "TXTUsername"
        Me.TXTUsername.Size = New System.Drawing.Size(232, 20)
        Me.TXTUsername.TabIndex = 1
        '
        'TXTPassword
        '
        Me.TXTPassword.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.TXTPassword.Location = New System.Drawing.Point(174, 101)
        Me.TXTPassword.Name = "TXTPassword"
        Me.TXTPassword.PasswordChar = Global.Microsoft.VisualBasic.ChrW(42)
        Me.TXTPassword.Size = New System.Drawing.Size(232, 20)
        Me.TXTPassword.TabIndex = 3
        '
        'CMDOk
        '
        Me.CMDOk.Anchor = CType((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDOk.Location = New System.Drawing.Point(218, 180)
        Me.CMDOk.Name = "CMDOk"
        Me.CMDOk.Size = New System.Drawing.Size(94, 23)
        Me.CMDOk.TabIndex = 4
        Me.CMDOk.Text = "&OK"
        '
        'CMDCancel
        '
        Me.CMDCancel.Anchor = CType((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel
        Me.CMDCancel.Location = New System.Drawing.Point(313, 180)
        Me.CMDCancel.Name = "CMDCancel"
        Me.CMDCancel.Size = New System.Drawing.Size(94, 23)
        Me.CMDCancel.TabIndex = 5
        Me.CMDCancel.Text = "&Abbrechen"
        '
        'PB
        '
        Me.PB.Anchor = CType((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.PB.Location = New System.Drawing.Point(218, 164)
        Me.PB.MarqueeAnimationSpeed = 10
        Me.PB.Name = "PB"
        Me.PB.Size = New System.Drawing.Size(189, 10)
        Me.PB.Style = System.Windows.Forms.ProgressBarStyle.Marquee
        Me.PB.TabIndex = 6
        Me.PB.Visible = False
        '
        'Notify
        '
        Me.Notify.ContextMenuStrip = Me.NotifyContext
        Me.Notify.Icon = CType(resources.GetObject("Notify.Icon"), System.Drawing.Icon)
        Me.Notify.Text = "EVa Administration"
        Me.Notify.Visible = True
        '
        'NotifyContext
        '
        Me.NotifyContext.Items.AddRange(New System.Windows.Forms.ToolStripItem() {Me.AnwendungÖffnenToolStripMenuItem, Me.BeendenToolStripMenuItem})
        Me.NotifyContext.Name = "NotifyContext"
        Me.NotifyContext.Size = New System.Drawing.Size(178, 48)
        '
        'AnwendungÖffnenToolStripMenuItem
        '
        Me.AnwendungÖffnenToolStripMenuItem.DisplayStyle = System.Windows.Forms.ToolStripItemDisplayStyle.Text
        Me.AnwendungÖffnenToolStripMenuItem.Name = "AnwendungÖffnenToolStripMenuItem"
        Me.AnwendungÖffnenToolStripMenuItem.Size = New System.Drawing.Size(177, 22)
        Me.AnwendungÖffnenToolStripMenuItem.Text = "Anwendung öffnen"
        '
        'BeendenToolStripMenuItem
        '
        Me.BeendenToolStripMenuItem.DisplayStyle = System.Windows.Forms.ToolStripItemDisplayStyle.Text
        Me.BeendenToolStripMenuItem.Name = "BeendenToolStripMenuItem"
        Me.BeendenToolStripMenuItem.Size = New System.Drawing.Size(177, 22)
        Me.BeendenToolStripMenuItem.Text = "Beenden"
        '
        'Watcher
        '
        Me.Watcher.EnableRaisingEvents = True
        Me.Watcher.Filter = "*.csv"
        Me.Watcher.NotifyFilter = CType((System.IO.NotifyFilters.FileName Or System.IO.NotifyFilters.LastWrite), System.IO.NotifyFilters)
        Me.Watcher.SynchronizingObject = Me
        '
        'CMDSettings
        '
        Me.CMDSettings.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDSettings.BackgroundImage = CType(resources.GetObject("CMDSettings.BackgroundImage"), System.Drawing.Image)
        Me.CMDSettings.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Stretch
        Me.CMDSettings.Location = New System.Drawing.Point(383, 6)
        Me.CMDSettings.Name = "CMDSettings"
        Me.CMDSettings.Size = New System.Drawing.Size(24, 23)
        Me.CMDSettings.TabIndex = 7
        Me.CMDSettings.UseVisualStyleBackColor = True
        '
        'LBLBranding
        '
        Me.LBLBranding.AutoSize = True
        Me.LBLBranding.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.LBLBranding.Location = New System.Drawing.Point(175, 8)
        Me.LBLBranding.Name = "LBLBranding"
        Me.LBLBranding.Size = New System.Drawing.Size(71, 13)
        Me.LBLBranding.TabIndex = 8
        Me.LBLBranding.Text = "ACHTUNG!"
        Me.LBLBranding.Visible = False
        '
        'Login
        '
        Me.AcceptButton = Me.CMDOk
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.CancelButton = Me.CMDCancel
        Me.ClientSize = New System.Drawing.Size(413, 210)
        Me.Controls.Add(Me.LBLBranding)
        Me.Controls.Add(Me.LogoPictureBox)
        Me.Controls.Add(Me.PB)
        Me.Controls.Add(Me.CMDCancel)
        Me.Controls.Add(Me.CMDOk)
        Me.Controls.Add(Me.TXTPassword)
        Me.Controls.Add(Me.TXTUsername)
        Me.Controls.Add(Me.PasswordLabel)
        Me.Controls.Add(Me.CMDSettings)
        Me.Controls.Add(Me.UsernameLabel)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog
        Me.Icon = CType(resources.GetObject("$this.Icon"), System.Drawing.Icon)
        Me.MaximizeBox = False
        Me.MinimizeBox = False
        Me.Name = "Login"
        Me.SizeGripStyle = System.Windows.Forms.SizeGripStyle.Hide
        Me.Text = "OPV-Admin: Anmeldung"
        CType(Me.LogoPictureBox, System.ComponentModel.ISupportInitialize).EndInit()
        Me.NotifyContext.ResumeLayout(False)
        CType(Me.Watcher, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub
    Friend WithEvents CMDCancel As System.Windows.Forms.Button
    Friend WithEvents PB As System.Windows.Forms.ProgressBar
    Friend WithEvents Notify As System.Windows.Forms.NotifyIcon
    Friend WithEvents Watcher As System.IO.FileSystemWatcher
    Friend WithEvents NotifyContext As System.Windows.Forms.ContextMenuStrip
    Friend WithEvents AnwendungÖffnenToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
    Friend WithEvents BeendenToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
    Friend WithEvents CMDSettings As System.Windows.Forms.Button
    Friend WithEvents LBLBranding As System.Windows.Forms.Label
End Class
