<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Minimal, dependency-free .xlsx (OOXML SpreadsheetML) writer.
 *
 * An .xlsx file is just a ZIP of a few XML parts, so we can produce a genuine
 * Excel file with PHP's built-in ZipArchive — no Composer packages required.
 * Strings are written as inline strings and numeric values as number cells so
 * Excel/Sheets treat figures as numbers (sortable, summable).
 *
 * Scope is intentionally small: one sheet, a bold header row, no formulas.
 */
class SimpleXlsx
{
    /**
     * Build the workbook and return the path to a temporary .xlsx file.
     * Caller is responsible for sending + deleting it (e.g. download(...)->deleteFileAfterSend()).
     *
     * @param  array<int,string>        $headers  Header labels for row 1.
     * @param  array<int,array<int,mixed>>  $rows  Data rows; numeric values become number cells.
     */
    public static function create(array $headers, array $rows, string $sheetName = 'Sheet1'): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        if ($tmp === false) {
            throw new RuntimeException('Unable to create a temporary file for the Excel export.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to open the Excel archive for writing.');
        }

        $zip->addFromString('[Content_Types].xml', self::contentTypes());
        $zip->addFromString('_rels/.rels', self::rootRels());
        $zip->addFromString('xl/workbook.xml', self::workbook($sheetName));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::sheet($headers, $rows));

        $zip->close();

        return $tmp;
    }

    private static function sheet(array $headers, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        $rowIndex = 1;

        // Header row (bold style index 1).
        $xml .= '<row r="' . $rowIndex . '">';
        foreach (array_values($headers) as $col => $value) {
            $ref = self::columnLetter($col) . $rowIndex;
            $xml .= '<c r="' . $ref . '" s="1" t="inlineStr"><is><t xml:space="preserve">'
                . self::escape((string) $value) . '</t></is></c>';
        }
        $xml .= '</row>';

        foreach ($rows as $row) {
            $rowIndex++;
            $xml .= '<row r="' . $rowIndex . '">';
            foreach (array_values($row) as $col => $value) {
                $ref = self::columnLetter($col) . $rowIndex;
                if (is_int($value) || is_float($value)) {
                    $xml .= '<c r="' . $ref . '"><v>' . $value . '</v></c>';
                } else {
                    $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t xml:space="preserve">'
                        . self::escape((string) $value) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }

        return $xml . '</sheetData></worksheet>';
    }

    /** Zero-based column index to spreadsheet column letter (0 => A, 26 => AA). */
    private static function columnLetter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = intdiv($index - $mod, 26);
        }

        return $letter;
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private static function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function workbook(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::escape($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private static function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    /** Two cell formats: index 0 (default), index 1 (bold) for the header row. */
    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '</styleSheet>';
    }
}
