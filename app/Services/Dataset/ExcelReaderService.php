<?php

namespace App\Services\Dataset;

use Exception;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class ExcelReaderService
{
    /**
     * Parse either an XLSX or CSV file and return array of records with lowercase column keys.
     *
     * @param string $filePath Absolute path to the uploaded file
     * @return array List of associative arrays representing rows
     */
    public function readFile(string $filePath, ?string $extension = null): array
    {
        $ext = strtolower($extension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'csv' || $ext === 'txt') {
            return $this->readCsv($filePath);
        } elseif ($ext === 'xlsx') {
            return $this->readXlsx($filePath);
        } else {
            throw new RuntimeException("Format file tidak didukung. Harap unggah berkas .xlsx atau .csv.");
        }
    }

    protected function readCsv(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = null;
            while (($data = fgetcsv($handle, 4096, ',')) !== false) {
                // If delimiter is semicolon
                if (count($data) === 1 && strpos($data[0], ';') !== false) {
                    $data = str_getcsv($data[0], ';');
                }

                if (!$headers) {
                    $headers = array_map(function ($h) {
                        return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h)));
                    }, $data);
                    continue;
                }

                if (count($data) >= count($headers)) {
                    $row = [];
                    foreach ($headers as $i => $header) {
                        $row[$header] = $data[$i] ?? '';
                    }
                    $rows[] = $row;
                }
            }
            fclose($handle);
        }

        return $rows;
    }

    protected function readXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException("Gagal membuka berkas Excel (.xlsx). Pastikan file tidak rusak.");
        }

        // 1. Read Shared Strings
        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $xml = simplexml_load_string($sharedXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } else {
                        $textParts = [];
                        foreach ($si->r as $r) {
                            $textParts[] = (string)$r->t;
                        }
                        $sharedStrings[] = implode('', $textParts);
                    }
                }
            }
        }

        // 2. Read Sheet1
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            throw new RuntimeException("Lembar kerja (sheet1.xml) tidak ditemukan dalam berkas Excel.");
        }

        $xml = simplexml_load_string($sheetXml);
        $zip->close();

        if (!$xml || !isset($xml->sheetData->row)) {
            return [];
        }

        $headers = [];
        $rows = [];

        foreach ($xml->sheetData->row as $r) {
            $rowNum = (int)$r['r'];
            $colValues = [];

            foreach ($r->c as $c) {
                $cellRef = (string)$c['r'];
                $colLetter = preg_replace('/[0-9]/', '', $cellRef);
                $type = (string)$c['t'];
                $val = isset($c->v) ? (string)$c->v : '';

                if ($type === 's' && is_numeric($val)) {
                    $val = $sharedStrings[(int)$val] ?? '';
                }

                $colValues[$colLetter] = $val;
            }

            if ($rowNum === 1) {
                // Header row
                foreach ($colValues as $col => $val) {
                    $headers[$col] = strtolower(trim($val));
                }
            } else {
                if (empty($headers)) continue;

                $row = [];
                foreach ($headers as $col => $headerName) {
                    $row[$headerName] = $colValues[$col] ?? '';
                }
                $rows[] = $row;
            }
        }

        return $rows;
    }
}
