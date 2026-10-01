<?php
require 'includes/auth.php';

$daftar = $pdo->query("SELECT * FROM pendaftar ORDER BY created_at DESC")->fetchAll();

$headers = ['Nama Anak', 'Usia', 'Nama Orang Tua', 'Alamat', 'WhatsApp', 'Tanggal Daftar', 'Status'];
$rows = [];
foreach ($daftar as $p) {
    $rows[] = [
        $p['nama_anak'],
        $p['usia_anak'] !== null ? $p['usia_anak'] . ' tahun' : '-',
        $p['nama_ortu'],
        $p['alamat'] ?? '',
        $p['whatsapp'],
        date('d/m/Y H:i', strtotime($p['created_at'])),
        $p['status'],
    ];
}

if (class_exists('ZipArchive')) {
    $cellXml = static function (string $column, int $row, $value, int $style): string {
        $value = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return '<c r="' . $column . $row . '" t="inlineStr" s="' . $style . '"><is><t xml:space="preserve">' . $value . '</t></is></c>';
    };

    $sheetRows = '<row r="1" ht="30" customHeight="1">';
    foreach ($headers as $index => $heading) {
        $sheetRows .= $cellXml(chr(65 + $index), 1, $heading, 1);
    }
    $sheetRows .= '</row>';

    foreach ($rows as $rowIndex => $values) {
        $excelRow = $rowIndex + 2;
        $style = $rowIndex % 2 === 0 ? 2 : 3;
        $sheetRows .= '<row r="' . $excelRow . '">';
        foreach ($values as $columnIndex => $value) {
            $sheetRows .= $cellXml(chr(65 + $columnIndex), $excelRow, $value, $style);
        }
        $sheetRows .= '</row>';
    }

    $lastRow = count($rows) + 1;
    $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
        . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
        . '<cols><col min="1" max="1" width="25" customWidth="1"/><col min="2" max="2" width="13" customWidth="1"/><col min="3" max="3" width="27" customWidth="1"/><col min="4" max="4" width="48" customWidth="1"/><col min="5" max="5" width="20" customWidth="1"/><col min="6" max="6" width="21" customWidth="1"/><col min="7" max="7" width="16" customWidth="1"/></cols>'
        . '<sheetData>' . $sheetRows . '</sheetData><autoFilter ref="A1:G' . $lastRow . '"/>'
        . '<pageMargins left="0.3" right="0.3" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
        . '<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';

    $files = [
        '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>',
        '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
        'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><bookViews><workbookView/></bookViews><sheets><sheet name="Data Pendaftar" sheetId="1" r:id="rId1"/></sheets></workbook>',
        'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
        'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF328C39"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF1F7EF"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left/><right/><top/><bottom style="thin"><color rgb="FFDCE7D9"/></bottom><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="4"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"><alignment vertical="top" wrapText="1"/></xf><xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0"><alignment horizontal="center" vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"><alignment vertical="top" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0"><alignment vertical="top" wrapText="1"/></xf></cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>',
        'xl/worksheets/sheet1.xml' => $sheet,
    ];

    $tempFile = tempnam(sys_get_temp_dir(), 'pendaftar-');
    $zip = new ZipArchive();
    if ($tempFile !== false && $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        foreach ($files as $path => $contents) {
            $zip->addFromString($path, $contents);
        }
        $zip->close();

        $verifiedZip = new ZipArchive();
        $validWorkbook = $verifiedZip->open($tempFile) === true;
        if ($validWorkbook) {
            foreach ($files as $path => $contents) {
                if ($verifiedZip->locateName($path) === false || simplexml_load_string($contents) === false) {
                    $validWorkbook = false;
                    break;
                }
            }
            $verifiedZip->close();
        }

        if ($validWorkbook) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename=data_pendaftar_' . date('Y-m-d') . '.xlsx');
            header('Content-Length: ' . filesize($tempFile));
            readfile($tempFile);
            unlink($tempFile);
            exit;
        }
    }
    if ($tempFile !== false && is_file($tempFile)) {
        unlink($tempFile);
    }
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_pendaftar_' . date('Y-m-d') . '.csv');

$out = fopen('php://output', 'w');
fputcsv($out, $headers);
foreach ($rows as $row) {
    fputcsv($out, $row);
}

fclose($out);
exit;
