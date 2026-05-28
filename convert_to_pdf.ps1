param (
    [string]$SourcePath,
    [string]$TargetPath
)

$extension = [System.IO.Path]::GetExtension($SourcePath).ToLower()

if ($extension -eq ".doc" -or $extension -eq ".docx") {
    try {
        $word = New-Object -ComObject Word.Application
        $word.Visible = $false
        $word.DisplayAlerts = 0 # wdAlertsNone
        
        $doc = $word.Documents.Open($SourcePath, $false, $true) # Open read-only
        
        # 17 = wdFormatPDF
        $doc.SaveAs($TargetPath, 17)
        
        $doc.Close($false)
        $word.Quit()
        Write-Host "SUCCESS"
        exit 0
    } catch {
        Write-Host "ERROR: $_"
        if ($word) { $word.Quit() }
        exit 1
    }
}
elseif ($extension -eq ".ppt" -or $extension -eq ".pptx") {
    try {
        $ppt = New-Object -ComObject PowerPoint.Application
        
        # Open PowerPoint presentation (WithWindow = $false to run headlessly)
        $presentation = $ppt.Presentations.Open($SourcePath, $true, $false, $false)
        
        # 32 = ppSaveAsPDF
        $presentation.SaveAs($TargetPath, 32)
        
        $presentation.Close()
        $ppt.Quit()
        Write-Host "SUCCESS"
        exit 0
    } catch {
        Write-Host "ERROR: $_"
        if ($ppt) { $ppt.Quit() }
        exit 1
    }
}
else {
    Write-Host "ERROR: Unsupported extension $extension"
    exit 1
}
