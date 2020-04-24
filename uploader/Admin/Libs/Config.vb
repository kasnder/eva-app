Namespace App
    Public NotInheritable Class Config
        Public Shared User As Dictionary(Of String, Object)

        'Public Shared Watcher As New FileSystemWatcher 'With {.ContextMenuStrip = NotifyContext, .Icon = My.Resources.ops, .Text = "EVa Administration"}
        'Public Shared AnwendungÖffnenToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
        'Public Shared BeendenToolStripMenuItem As System.Windows.Forms.ToolStripMenuItem
        'Public Shared NotifyContext As New ContextMenuStrip With {.ContextMenuStrip = NotifyContext, .Text = "EVa Administration"}
        'Public Shared Notify As New NotifyIcon With {.ContextMenuStrip = NotifyContext, .Icon = My.Resources.ops, .Text = "EVa Administration"}

        'Public Shared Sub Initialise()
        'App.Config.NotifyContext.Items.AddRange(New System.Windows.Forms.ToolStripItem() {App.Config.AnwendungÖffnenToolStripMenuItem, App.Config.BeendenToolStripMenuItem})
        'App.Config.AnwendungÖffnenToolStripMenuItem.Text = "Anwendung öffnen"
        'App.Config.BeendenToolStripMenuItem.Text = "Beenden"
        'End Sub
        Public Shared Sub Reset()
            User.Clear()
        End Sub
    End Class
End Namespace