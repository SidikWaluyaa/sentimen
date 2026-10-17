CREATE DATABASE IF NOT EXISTS db_sentimen_shopee;
USE db_sentimen_shopee;

CREATE TABLE IF NOT EXISTS tb_evaluasi (
    id_evaluasi INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATETIME,
    akurasi FLOAT,
    presisi FLOAT,
    recall FLOAT,
    f1_score FLOAT
);

CREATE TABLE IF NOT EXISTS tb_hasil_prediksi (
    id_prediksi INT AUTO_INCREMENT PRIMARY KEY,
    id_evaluasi INT,
    ulasan_asli TEXT,
    ulasan_bersih TEXT,
    sentimen VARCHAR(50),
    FOREIGN KEY (id_evaluasi) REFERENCES tb_evaluasi(id_evaluasi) ON DELETE CASCADE
);
