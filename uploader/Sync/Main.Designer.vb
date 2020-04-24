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
        Me.components = New System.ComponentModel.Container()
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(Main))
        Me.CMDConnect = New System.Windows.Forms.Button()
        Me.LBLStatus = New System.Windows.Forms.Label()
        Me.CMDSync = New System.Windows.Forms.Button()
        Me.TXTPassword = New System.Windows.Forms.TextBox()
        Me.TXTUsername = New System.Windows.Forms.TextBox()
        Me.PasswordLabel = New System.Windows.Forms.Label()
        Me.UsernameLabel = New System.Windows.Forms.Label()
        Me.LBLPath = New System.Windows.Forms.Label()
        Me.CMDPath = New System.Windows.Forms.Button()
        Me.FolderBrowser = New System.Windows.Forms.FolderBrowserDialog()
        Me.Watcher = New System.IO.FileSystemWatcher()
        Me.NotifyContext = New System.Windows.Forms.ContextMenuStrip(Me.components)
        Me.OpenToolStripMenuItem = New System.Windows.Forms.ToolStripMenuItem()
        Me.PlanorrekturenToolStripMenuItem = New System.Windows.Forms.ToolStripMenuItem()
        Me.ToolStripSeparator1 = New System.Windows.Forms.ToolStripSeparator()
        Me.SyncToolStripMenuItem = New System.Windows.Forms.ToolStripMenuItem()
        Me.PrintToolStripMenuItem = New System.Windows.Forms.ToolStripMenuItem()
        Me.ToolStripSeparator2 = New System.Windows.Forms.ToolStripSeparator()
        Me.ExitToolStripMenuItem = New System.Windows.Forms.ToolStripMenuItem()
        Me.Notify = New System.Windows.Forms.NotifyIcon(Me.components)
        Me.Label1 = New System.Windows.Forms.Label()
        Me.Progress = New System.Windows.Forms.ProgressBar()
        Me.LBLBranding = New System.Windows.Forms.Label()
        Me.CMDCorrections = New System.Windows.Forms.Button()
        Me.CMDPrint = New System.Windows.Forms.Button()
        Me.GBActions = New System.Windows.Forms.GroupBox()
        CType(Me.Watcher, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.NotifyContext.SuspendLayout()
        Me.GBActions.SuspendLayout()
        Me.SuspendLayout()
        '
        'CMDConnect
        '
        Me.CMDConnect.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDConnect.Location = New System.Drawing.Point(12, 202)
        Me.CMDConnect.Name = "CMDConnect"
        Me.CMDConnect.Size = New System.Drawing.Size(260, 23)
        Me.CMDConnect.TabIndex = 9
        Me.CMDConnect.Text = "Verbinden"
        Me.CMDConnect.UseVisualStyleBackColor = True
        '
        'LBLStatus
        '
        Me.LBLStatus.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.LBLStatus.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Italic, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.LBLStatus.Location = New System.Drawing.Point(12, 13)
        Me.LBLStatus.Name = "LBLStatus"
        Me.LBLStatus.Size = New System.Drawing.Size(260, 22)
        Me.LBLStatus.TabIndex = 0
        Me.LBLStatus.Text = "Nicht verbunden."
        Me.LBLStatus.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'CMDSync
        '
        Me.CMDSync.Anchor = CType(((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDSync.Location = New System.Drawing.Point(133, 48)
        Me.CMDSync.Name = "CMDSync"
        Me.CMDSync.Size = New System.Drawing.Size(121, 23)
        Me.CMDSync.TabIndex = 2
        Me.CMDSync.Text = "Synchronisieren"
        Me.CMDSync.UseVisualStyleBackColor = True
        '
        'TXTPassword
        '
        Me.TXTPassword.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.TXTPassword.Location = New System.Drawing.Point(14, 154)
        Me.TXTPassword.Name = "TXTPassword"
        Me.TXTPassword.PasswordChar = Global.Microsoft.VisualBasic.ChrW(8226)
        Me.TXTPassword.Size = New System.Drawing.Size(258, 20)
        Me.TXTPassword.TabIndex = 7
        '
        'TXTUsername
        '
        Me.TXTUsername.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.TXTUsername.Location = New System.Drawing.Point(14, 111)
        Me.TXTUsername.Name = "TXTUsername"
        Me.TXTUsername.Size = New System.Drawing.Size(258, 20)
        Me.TXTUsername.TabIndex = 5
        '
        'PasswordLabel
        '
        Me.PasswordLabel.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.PasswordLabel.Location = New System.Drawing.Point(12, 134)
        Me.PasswordLabel.Name = "PasswordLabel"
        Me.PasswordLabel.Size = New System.Drawing.Size(260, 23)
        Me.PasswordLabel.TabIndex = 6
        Me.PasswordLabel.Text = "&Kennwort"
        Me.PasswordLabel.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'UsernameLabel
        '
        Me.UsernameLabel.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.UsernameLabel.Location = New System.Drawing.Point(12, 91)
        Me.UsernameLabel.Name = "UsernameLabel"
        Me.UsernameLabel.Size = New System.Drawing.Size(260, 23)
        Me.UsernameLabel.TabIndex = 4
        Me.UsernameLabel.Text = "&Benutzername"
        Me.UsernameLabel.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'LBLPath
        '
        Me.LBLPath.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.LBLPath.BackColor = System.Drawing.SystemColors.ControlLight
        Me.LBLPath.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.LBLPath.Location = New System.Drawing.Point(15, 61)
        Me.LBLPath.Name = "LBLPath"
        Me.LBLPath.Size = New System.Drawing.Size(223, 23)
        Me.LBLPath.TabIndex = 2
        Me.LBLPath.Text = "Ordner auswählen.."
        Me.LBLPath.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'CMDPath
        '
        Me.CMDPath.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDPath.Location = New System.Drawing.Point(244, 61)
        Me.CMDPath.Name = "CMDPath"
        Me.CMDPath.Size = New System.Drawing.Size(28, 23)
        Me.CMDPath.TabIndex = 3
        Me.CMDPath.Text = "..."
        Me.CMDPath.UseVisualStyleBackColor = True
        '
        'Watcher
        '
        Me.Watcher.EnableRaisingEvents = True
        Me.Watcher.Filter = "*.csv"
        Me.Watcher.NotifyFilter = CType((System.IO.NotifyFilters.FileName Or System.IO.NotifyFilters.LastWrite), System.IO.NotifyFilters)
        Me.Watcher.SynchronizingObject = Me
        '
        'NotifyContext
        '
        Me.NotifyContext.Items.AddRange(New System.Windows.Forms.ToolStripItem() {Me.OpenToolStripMenuItem, Me.PlanorrekturenToolStripMenuItem, Me.ToolStripSeparator1, Me.SyncToolStripMenuItem, Me.PrintToolStripMenuItem, Me.ToolStripSeparator2, Me.ExitToolStripMenuItem})
        Me.NotifyContext.Name = "NotifyContext"
        Me.NotifyContext.Size = New System.Drawing.Size(163, 148)
        '
        'OpenToolStripMenuItem
        '
        Me.OpenToolStripMenuItem.DisplayStyle = System.Windows.Forms.ToolStripItemDisplayStyle.Text
        Me.OpenToolStripMenuItem.Name = "OpenToolStripMenuItem"
        Me.OpenToolStripMenuItem.Size = New System.Drawing.Size(162, 22)
        Me.OpenToolStripMenuItem.Text = "Übersicht öffnen"
        '
        'PlanorrekturenToolStripMenuItem
        '
        Me.PlanorrekturenToolStripMenuItem.Name = "PlanorrekturenToolStripMenuItem"
        Me.PlanorrekturenToolStripMenuItem.Size = New System.Drawing.Size(162, 22)
        Me.PlanorrekturenToolStripMenuItem.Text = "Korrekturen vornehmen"
        '
        'ToolStripSeparator1
        '
        Me.ToolStripSeparator1.Name = "ToolStripSeparator1"
        Me.ToolStripSeparator1.Size = New System.Drawing.Size(159, 6)
        '
        'SyncToolStripMenuItem
        '
        Me.SyncToolStripMenuItem.Name = "SyncToolStripMenuItem"
        Me.SyncToolStripMenuItem.Size = New System.Drawing.Size(162, 22)
        Me.SyncToolStripMenuItem.Text = "Synchronisieren"
        '
        'PrintToolStripMenuItem
        '
        Me.PrintToolStripMenuItem.Name = "PrintToolStripMenuItem"
        Me.PrintToolStripMenuItem.Size = New System.Drawing.Size(162, 22)
        Me.PrintToolStripMenuItem.Text = "Druck öffnen"
        '
        'ToolStripSeparator2
        '
        Me.ToolStripSeparator2.Name = "ToolStripSeparator2"
        Me.ToolStripSeparator2.Size = New System.Drawing.Size(159, 6)
        '
        'ExitToolStripMenuItem
        '
        Me.ExitToolStripMenuItem.DisplayStyle = System.Windows.Forms.ToolStripItemDisplayStyle.Text
        Me.ExitToolStripMenuItem.Name = "ExitToolStripMenuItem"
        Me.ExitToolStripMenuItem.Size = New System.Drawing.Size(162, 22)
        Me.ExitToolStripMenuItem.Text = "Beenden"
        '
        'Notify
        '
        Me.Notify.ContextMenuStrip = Me.NotifyContext
        Me.Notify.Icon = Global.EVa_Sync.My.Resources.Resources.Logo_Red
        Me.Notify.Text = "EVa Administration"
        Me.Notify.Visible = True
        '
        'Label1
        '
        Me.Label1.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Label1.Location = New System.Drawing.Point(12, 40)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(260, 23)
        Me.Label1.TabIndex = 1
        Me.Label1.Text = "&Ordner zur Synchronisation"
        Me.Label1.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        '
        'Progress
        '
        Me.Progress.Anchor = CType(((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Progress.Location = New System.Drawing.Point(-3, -9)
        Me.Progress.MarqueeAnimationSpeed = 10
        Me.Progress.Name = "Progress"
        Me.Progress.Size = New System.Drawing.Size(293, 16)
        Me.Progress.Style = System.Windows.Forms.ProgressBarStyle.Marquee
        Me.Progress.TabIndex = 11
        Me.Progress.Visible = False
        '
        'LBLBranding
        '
        Me.LBLBranding.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.LBLBranding.Location = New System.Drawing.Point(12, 177)
        Me.LBLBranding.Name = "LBLBranding"
        Me.LBLBranding.Size = New System.Drawing.Size(260, 22)
        Me.LBLBranding.TabIndex = 8
        Me.LBLBranding.Text = "ACHTUNG!"
        Me.LBLBranding.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.LBLBranding.Visible = False
        '
        'CMDCorrections
        '
        Me.CMDCorrections.Anchor = CType(((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDCorrections.Location = New System.Drawing.Point(6, 19)
        Me.CMDCorrections.Name = "CMDCorrections"
        Me.CMDCorrections.Size = New System.Drawing.Size(248, 23)
        Me.CMDCorrections.TabIndex = 0
        Me.CMDCorrections.Text = "Korrekturen"
        Me.CMDCorrections.UseVisualStyleBackColor = True
        '
        'CMDPrint
        '
        Me.CMDPrint.Anchor = CType(((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.CMDPrint.Location = New System.Drawing.Point(6, 48)
        Me.CMDPrint.Name = "CMDPrint"
        Me.CMDPrint.Size = New System.Drawing.Size(121, 23)
        Me.CMDPrint.TabIndex = 1
        Me.CMDPrint.Text = "Druck"
        Me.CMDPrint.UseVisualStyleBackColor = True
        '
        'GBActions
        '
        Me.GBActions.Anchor = CType(((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.GBActions.Controls.Add(Me.CMDSync)
        Me.GBActions.Controls.Add(Me.CMDPrint)
        Me.GBActions.Controls.Add(Me.CMDCorrections)
        Me.GBActions.Enabled = False
        Me.GBActions.Location = New System.Drawing.Point(12, 264)
        Me.GBActions.Name = "GBActions"
        Me.GBActions.Size = New System.Drawing.Size(260, 77)
        Me.GBActions.TabIndex = 10
        Me.GBActions.TabStop = False
        Me.GBActions.Text = "Aktionen"
        '
        'Main
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(284, 353)
        Me.Controls.Add(Me.GBActions)
        Me.Controls.Add(Me.LBLBranding)
        Me.Controls.Add(Me.CMDConnect)
        Me.Controls.Add(Me.Progress)
        Me.Controls.Add(Me.TXTPassword)
        Me.Controls.Add(Me.TXTUsername)
        Me.Controls.Add(Me.PasswordLabel)
        Me.Controls.Add(Me.UsernameLabel)
        Me.Controls.Add(Me.LBLPath)
        Me.Controls.Add(Me.CMDPath)
        Me.Controls.Add(Me.LBLStatus)
        Me.Controls.Add(Me.Label1)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle
        Me.Icon = CType(resources.GetObject("$this.Icon"), System.Drawing.Icon)
        Me.MaximizeBox = False
        Me.MinimizeBox = False
        Me.MinimumSize = New System.Drawing.Size(300, 350)
        Me.Name = "Main"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "EVa Synchronisation - Einstellungen"
        CType(Me.Watcher, System.ComponentModel.ISupportInitialize).EndInit()
        Me.NotifyContext.ResumeLayout(False)
        Me.GBActions.ResumeLayout(False)
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub
    Friend WithEvents CMDConnect As System.Windows.Forms.Button
    Friend WithEvents LBLStatus As System.Windows.Forms.Label
    Friend WithEvents CMDSync As System.Windows.Forms.Button
    Friend WithEvents TXTPassword As System.Windows.Forms.TextBox
    Friend WithEvents TXTUsername As System.Windows.Forms.TextBox
    Friend WithEvents PasswordLabel As System.Windows.Forms.Label
    Friend WithEvents UsernameLabel As System.Windows.Forms.Label
    Friend WithEvents LBLPath As System.Windows.Forms.Label
    Friend WithEvents CMDPath As System.Windows.Forms.Button
    Friend WithEvents OpenToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
    Friend WithEvents ExitToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
    Friend WithEvents SyncToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
    Friend WithEvents Label1 As System.Windows.Forms.Label
    Private WithEvents FolderBrowser As System.Windows.Forms.FolderBrowserDialog
    Private WithEvents Watcher As System.IO.FileSystemWatcher
    Private WithEvents NotifyContext As System.Windows.Forms.ContextMenuStrip
    Private WithEvents Notify As System.Windows.Forms.NotifyIcon
    Friend WithEvents Progress As System.Windows.Forms.ProgressBar
    Friend WithEvents LBLBranding As System.Windows.Forms.Label
    Friend WithEvents CMDCorrections As System.Windows.Forms.Button
    Friend WithEvents PrintToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
    Friend WithEvents GBActions As System.Windows.Forms.GroupBox
    Friend WithEvents CMDPrint As System.Windows.Forms.Button
    Friend WithEvents PlanorrekturenToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
    Friend WithEvents ToolStripSeparator1 As System.Windows.Forms.ToolStripSeparator
    Friend WithEvents ToolStripSeparator2 As System.Windows.Forms.ToolStripSeparator

End Class
