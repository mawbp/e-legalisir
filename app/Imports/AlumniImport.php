<?php

namespace App\Imports;

use App\Models\Alumni;
use App\Models\Prodi;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AlumniImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public $errors = [];
    public $successCount = 0;
    protected $existingNIM;
    protected $prodiName;

    public function __construct()
    {
        $this->existingNIM = Alumni::pluck('nim')->toArray();
        $this->prodiName = Prodi::pluck('nama_prodi')->toArray();
    }


    public function collection(Collection $rows)
    {
        $dupe = [];
        $rows->each(function ($row, $index) use (&$dupe){
          $rowNumber = $index + 2;

          try {
            if(empty($row['nim']) || empty($row['nama']) || empty($row['prodi']) || empty($row['tahun_angkatan']) ){
              throw new \Exception("Ada data kosong di baris $rowNumber");
            }

            if(!preg_match('/^[0-9]+$/', $row['nim'])){
              throw new \Exception("NIM hanya boleh berisi angka (baris $rowNumber)");
            }

            if(!preg_match('/^[0-9]+$/', $row['tahun_angkatan'])){
              throw new \Exception("NIM hanya boleh berisi angka (baris $rowNumber)");
            }

            if(preg_match('/[0-9]/', $row['nama'])){
              throw new \Exception("Nama tidak boleh berisi angka (baris $rowNumber)");
            }

            if(!preg_match('/^[\pL\s]+$/u', $row['nama'])){
              throw new \Exception("Nama hanya boleh berisi huruf dan spasi (baris $rowNumber)");
            }

            if(strlen($row['nim']) < 7){
              throw new \Exception("NIM harus minimal 7 digit (baris $rowNumber)");
            }

            if(in_array($row['nim'], $dupe)){
              throw new \Exception("Ada duplikat NIM {$row['nim']} pada file excel (baris $rowNumber)");
            }

            if(in_array($row['nim'], $this->existingNIM)){
              throw new \Exception("NIM {$row['nim']} sudah terdaftar di database (baris $rowNumber)");
            }
            
            if(!in_array($row['prodi'], $this->prodiName)){
                throw new \Exception("Prodi {$row['prodi']} tidak ditemukan di database (baris $rowNumber)");
            }

            $dupe[] = $row['nim'];

            $formattedData = [
              'nim' => $row['nim'],
              'nama' => Str::title($row['nama']),
              'prodi' => Str::title($row['prodi']),
              'tahun_angkatan' => $row['tahun_angkatan'],
              'kode_file' => Str::random(32)
            ];

            Alumni::create($formattedData);
            $this->successCount++;
          } catch(\Exception $e) {
            $this->errors[] = [
              'row' => $rowNumber,
              'message' => $e->getMessage(),
              'data' => $row->toArray()
            ];
          }
        });
    }
}
