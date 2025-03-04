<?php
//memuat data dari file gaji.php
function loadData()
{
    $file = __DIR__ . '/Model/gaji.php';
    if (file_exists($file)) {
        return include($file);
    }
    return [];
}

//menyimpan data ke file gaji.php
function saveData($DataKaryawan)
{
    $file = __DIR__ . '/Model/gaji.php';
    $content = "<?php\nreturn " . var_export($DataKaryawan, true) . ";\n";
    file_put_contents($file, $content);
}

//fungsi untuk menampilkan menu
function displayMenu()
{
    echo "\n== SISTEM MANAGEMENT GAJI ==\n";
    echo "1. Lihat Karyawan\n";
    echo "2. Tambah Karyawan\n";
    echo "3. Update Karyawan\n";
    echo "4. Hapus Karyawan\n";
    echo "5. Hitung Gaji Karyawan\n";
    echo "6. Keluar Aplikasi\n";
    echo "Pilih menu (1-6): ";
}

//fungsi untuk mendapatkan input tanpa spasi tambahan
function getCleanInput()
{
    return trim(fgets(STDIN));
}

//program utama
$running = true;
$DataKaryawan =  loadData();

while ($running) {
    displayMenu();
    $Pilihan = getCleanInput();

    switch ($Pilihan) {
        case "1":
            //lihat karyawan
            echo "\nDAFTAR KARYAWAN:\n";
            if (empty($DataKaryawan)) {
                echo "Belum ada karyawan terdaftar.\n";
            } else {
                foreach ($DataKaryawan as $id => $Karyawan) {
                    echo ($id + 1) . ". Nama: {$Karyawan['nama']}, Jabatan: {$Karyawan['jabatan']}\n";
                }
            }
            echo "\nTekan Enter untuk kembali...";
            fgets(STDIN);
            break;

        case "2":
            //tambah karyawan
            echo "\nMasukkan nama karyawan: ";
            $nama = getCleanInput();
            $jabatanValid = false;
            while (!$jabatanValid) {
                echo "Masukkan jabatan (Manajer/Supervisor/Staff): ";
                $jabatan = getCleanInput();

                if (in_array($jabatan, ['Manajer', 'Supervisor', 'Staff'])) {
                    $jabatanValid = true;
                } else {
                    echo "Input jabatan tidak valid. Harap masukkan salah satu dari: Manajer, Supervisor atau Staff.\n";
                }
            }
            
            $DataKaryawan[] = ['nama' => $nama, 'jabatan' => $jabatan];
            saveData($DataKaryawan);
            echo "Karyawan berhasil ditambahkan!\n";

            echo "\nTekan Enter untuk kembali...";
            fgets(STDIN);
            break;

        case "3":
            //update karyawan
            echo "\nUPDATE DATA KARYAWAN\n";
            if (empty($DataKaryawan)) {
                echo "Belum ada karyawan terdaftar.\n";
            } else {
                foreach ($DataKaryawan as $id => $Karyawan) {
                    echo ($id + 1) . ". {$Karyawan['nama']} - {$Karyawan['jabatan']}\n";
                }
                echo "\nMasukkan nomor karyawan yang akan diupdate: ";
                $id = (int)getCleanInput() - 1;

                if (isset($DataKaryawan[$id])) {
                    echo "Masukkan nama baru (biarkan kosong jika tidak diubah): ";
                    $nama = getCleanInput();
                    echo "Masukkan jabatan baru (biarkan kosong jika tidak diubah): ";
                    $jabatan = getCleanInput();

                    if (!empty($nama)) {
                        $DataKaryawan[$id]['nama'] = $nama;
                    }
                    if (!empty($jabatan)) {
                        $DataKaryawan[$id]['jabatan'] = $jabatan;
                    }

                    saveData($DataKaryawan);
                    echo "Data karyawan berhasil diupdate!\n";
                } else {
                    echo "Karyawan tidak ditemukan!\n";
                }
            }
            echo "\nTekan Enter untuk kembali...";
            fgets(STDIN);
            break;

        case "4":
            //hapus karyawan
            echo "\nHAPUS KARYAWAN\n";
            if (empty($DataKaryawan)) {
                echo "Belum ada karyawan terdaftar.\n";
            } else {
                foreach ($DataKaryawan as $id => $Karyawan) {
                    echo ($id + 1) . ". {$Karyawan['nama']} - {$Karyawan['jabatan']}\n";
                }
                echo "\nMasukkan nomor karyawan yang akan dihapus: ";
                $id = (int)getCleanInput() - 1;
                if (isset($DataKaryawan[$id])) {
                    echo "\nData Karyawan yang akan dihapus:\n";
                    echo "Nama      : {$DataKaryawan[$id]['nama']}\n";
                    echo "Jabatan   : {$DataKaryawan[$id]['jabatan']}\n";

                    echo "\nApakah anda yakin ingin menghapus karyawan ini? (yes/no): ";
                    $konfirmasi = strtolower(trim(fgets(STDIN)));

                    if ($konfirmasi === 'yes') {
                        unset($DataKaryawan[$id]);
                        $DataKaryawan = array_values($DataKaryawan);
                        saveData($DataKaryawan);
                        echo "Karyawan berhasil dihapus!\n";
                    } else {
                        echo "Penghapusan karyawan dibatalkan.\n";
                    }
                } else {
                    echo "Karyawan tidak ditemukan!\n";
                }
            }
            echo "\nTekan Enter untuk kembali...";
            fgets(STDIN);
            break;

        case "5":
            //hitung gaji
            function totalgaji($jabatan, $jamLembur, $rating) {
                //logika
                if ($jabatan == "Manajer") {
                    $gajiPokok = 10000000;
                    $tunjangan = 3000000;
                } elseif ($jabatan == "Supervisor") {
                    $gajiPokok = 7000000;
                    $tunjangan = 2000000;
                } else {
                    $gajiPokok = 5000000;
                    $tunjangan = 1000000;
                }
                $upahLembur = $jamLembur * 25000;
                $bonusKinerja = match ($rating) {
                    5 => 0.2 * $gajiPokok,
                    4 => 0.15 * $gajiPokok,
                    3 => 0.1 * $gajiPokok,
                    2 => 0.05 * $gajiPokok,
                    default => 0
                };

                return [
                    'gajiPokok' => $gajiPokok,
                    'tunjangan' => $tunjangan,
                    'upahLembur' => $upahLembur,
                    'bonusKinerja' => $bonusKinerja,
                    'totalGaji' => $gajiPokok + $tunjangan + $upahLembur + $bonusKinerja
                ];
            }
            echo "\nHITUNG GAJI KARYAWAN\n";
            if (empty($DataKaryawan)) {
                echo "Belum ada karyawan terdaftar.\n";
            } else {
                foreach ($DataKaryawan as $id => $Karyawan) {
                    echo ($id + 1) . ". {$Karyawan['nama']} - {$Karyawan['jabatan']}\n";
                }
                echo "Masukkan nomor karyawan: ";
                $id = (int)getCleanInput() - 1;

                if (isset($DataKaryawan[$id])) {
                    echo "Masukkan jumlah jam lembur: ";
                    $jamLembur = (int)trim(fgets(STDIN));
                    echo "Masukkan rating kinerja (1-5): ";
                    $rating = (int)trim(fgets(STDIN));

                    $gaji = totalgaji($DataKaryawan[$id]['jabatan'], $jamLembur, $rating);

                    $DataKaryawan[$id]['last_gaji'] = array_merge(
                        $gaji,
                        [
                            'jamLembur' => $jamLembur,
                            'rating' => $rating,
                            'tanggal' => date('Y-m-d H:i:s')
                        ]
                        );
                        saveData($DataKaryawan);

                        echo "\nDetail Gaji {$DataKaryawan[$id]['nama']}:\n";
                        echo "Gaji Pokok: Rp " . number_format($gaji['gajiPokok']) . "\n";
                        echo "Tunjangan: Rp " . number_format($gaji['tunjangan']) . "\n";
                        echo "Upah Lembur: Rp " . number_format($gaji['upahLembur']) . "\n";
                        echo "Bonus Kinerja: Rp " . number_format($gaji['bonusKinerja']) . "\n";
                        echo "Total Gaji: Rp " . number_format($gaji['totalGaji']) . "\n";
                } else {
                    echo "Karyawan tidak ditemukan!\n";
                }
            }
            echo "\nTekan Enter untuk kembali...";
            fgets(STDIN);
            break;

        case "6":
            echo "\nTerimakasih, sampai jumpa!\n";
            $running = false;
            break;

        default:
            echo "Menu tidak valid!\n";
    }
}
?>