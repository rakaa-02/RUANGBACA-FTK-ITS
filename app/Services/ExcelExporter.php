<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Pintu tunggal untuk ekspor Excel.
 *  - Bila ekstensi PHP "zip" aktif  → berkas .xlsx
 *  - Bila tidak aktif               → berkas .xls (Excel XML 2003), tetap terbuka di Excel
 */
class ExcelExporter
{
    /** @param iterable<array<int, scalar|null>> $baris */
    public static function unduh(string $namaFile, string $sheet, array $header, iterable $baris): BinaryFileResponse
    {
        if (class_exists(\ZipArchive::class)) {
            $path = XlsxExporter::make($sheet, $header, $baris);
            $nama = $namaFile.'.xlsx';
        } else {
            $path = self::buatXml($sheet, $header, $baris);
            $nama = $namaFile.'.xls';
        }

        return response()->download($path, $nama)->deleteFileAfterSend(true);
    }

    private static function buatXml(string $sheet, array $header, iterable $baris): string
    {
        $path = tempnam(sys_get_temp_dir(), 'xls');
        $f = fopen($path, 'w');

        fwrite($f, '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<?mso-application progid="Excel.Sheet"?>'."\n"
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Styles><Style ss:ID="h"><Font ss:Bold="1"/></Style></Styles>'
            .'<Worksheet ss:Name="'.self::esc(mb_substr($sheet, 0, 31)).'"><Table>');

        fwrite($f, self::baris($header, true));
        foreach ($baris as $b) {
            fwrite($f, self::baris($b));
        }

        fwrite($f, '</Table></Worksheet></Workbook>');
        fclose($f);

        return $path;
    }

    private static function baris(array $sel, bool $tebal = false): string
    {
        $x = '<Row>';
        foreach (array_values($sel) as $v) {
            $gaya = $tebal ? ' ss:StyleID="h"' : '';
            if (is_int($v) || is_float($v)) {
                $x .= '<Cell'.$gaya.'><Data ss:Type="Number">'.$v.'</Data></Cell>';
            } else {
                $x .= '<Cell'.$gaya.'><Data ss:Type="String">'.self::esc((string) $v).'</Data></Cell>';
            }
        }

        return $x.'</Row>';
    }

    private static function esc(string $v): string
    {
        $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $v);

        return htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
