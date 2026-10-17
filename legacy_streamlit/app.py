"""
-- ==============================================
-- BLOK KODE SQL UNTUK MEMBUAT DATABASE & TABEL
-- ==============================================
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
    sentimen_aktual VARCHAR(50),
    sentimen_prediksi VARCHAR(50),
    FOREIGN KEY (id_evaluasi) REFERENCES tb_evaluasi(id_evaluasi) ON DELETE CASCADE
);
"""

import streamlit as st
import pandas as pd
import numpy as np
import re
import joblib
import io
import mysql.connector
import matplotlib.pyplot as plt
import seaborn as sns
from wordcloud import WordCloud
from datetime import datetime
from fpdf import FPDF
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, confusion_matrix
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import matplotlib as mpl

# Set Global Matplotlib config for Dark Mode
mpl.rcParams.update({
    'text.color': "white",
    'axes.labelcolor': "white",
    'xtick.color': "white",
    'ytick.color': "white",
    'axes.edgecolor': "white"
})

# --- KONFIGURASI HALAMAN ---
st.set_page_config(page_title="Dashboard Sentimen Shopee", layout="wide")

import os

# Membaca file style.css eksternal
def local_css(file_name):
    with open(file_name) as f:
        st.markdown(f'<style>{f.read()}</style>', unsafe_allow_html=True)

local_css("style.css")

# --- INITIAL LOAD CACHING (Model & Sastrawi) ---
@st.cache_resource
def load_models():
    # Load Model and Vectorizer (menggunakan joblib)
    try:
        base_dir = os.path.dirname(__file__)
        model_path = os.path.join(base_dir, 'streamlit_model', 'model_nb.pkl')
        vectorizer_path = os.path.join(base_dir, 'streamlit_model', 'tfidf_vectorizer.pkl')
        
        model = joblib.load(model_path)
        vectorizer = joblib.load(vectorizer_path)
        return model, vectorizer
    except Exception as e:
        st.sidebar.error(f"Error Code Detail: {e}")
        return None, None

@st.cache_resource
def load_sastrawi():
    # Inisialisasi Stopword Sastrawi
    stopword_factory = StopWordRemoverFactory()
    stopword_remover = stopword_factory.create_stop_word_remover()
    
    # Inisialisasi Stemmer Sastrawi
    stemmer_factory = StemmerFactory()
    stemmer = stemmer_factory.create_stemmer()
    
    return stopword_remover, stemmer

# --- KAMUS NORMALISASI (Gaul/Typo) ---
# Bisa disesuaikan ukurannya, ini contoh standar
norm_dict = {
    'yg': 'yang',
    'dgn': 'dengan',
    'tdk': 'tidak',
    'bgt': 'banget',
    'klo': 'kalau',
    'jg': 'juga',
    'udh': 'sudah',
    'nya': '',
    'gw': 'saya'
}

def normalize_text(text):
    words = text.split()
    normalized_words = [norm_dict.get(word, word) for word in words]
    return ' '.join(normalized_words)

# --- FUNGSI PREPROCESSING ---
def preprocess_text(text, stopword_remover, stemmer):
    if pd.isna(text):
        return ""
    
    text = str(text)
    
    # a. Case folding
    text = text.lower()
    
    # b. Hapus URL
    text = re.sub(r'http\S+|www\S+', '', text)
    
    # c. Hapus Mention
    text = re.sub(r'@\w+', '', text)
    
    # d. Hapus Hashtag
    text = re.sub(r'#\w+', '', text)
    
    # e. Hapus angka
    text = re.sub(r'\d+', '', text)
    
    # f. Sisakan hanya huruf dan spasi
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    
    # g. Hapus spasi berlebih
    text = re.sub(r'\s+', ' ', text).strip()
    
    # h & i. Tokenisasi dan Normalisasi (melalui split the function above)
    text = normalize_text(text)
    
    # j. Stopword removal (Sastrawi)
    text = stopword_remover.remove(text)
    
    # k. Stemming (Sastrawi)
    text = stemmer.stem(text)
    
    return text

# --- FUNGSI DATABASE (MYSQL.CONNECTOR) ---
def insert_to_db(metrics, df_pred):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            port=3307,
            user="root",
            password="",
            database="db_sentimen_shopee"
        )
        cursor = conn.cursor()
        
        # Insert ke tb_evaluasi
        sql_eval = """INSERT INTO tb_evaluasi (tanggal, akurasi, presisi, recall, f1_score)
                      VALUES (%s, %s, %s, %s, %s)"""
        val_eval = (datetime.now(), metrics['accuracy'], metrics['precision'], metrics['recall'], metrics['f1'])
        cursor.execute(sql_eval, val_eval)
        
        id_evaluasi = cursor.lastrowid # Ambil key / id yg baru saja masuk
        
        # Siapkan Bulk Insert (Executemany) untuk Prediksi ke tb_hasil_prediksi
        sql_pred = """INSERT INTO tb_hasil_prediksi (id_evaluasi, ulasan_asli, ulasan_bersih, sentimen_aktual, sentimen_prediksi) 
                      VALUES (%s, %s, %s, %s, %s)"""
        val_pred = []
        for _, row in df_pred.iterrows():
            val_pred.append((id_evaluasi, str(row['ulasan']), str(row['ulasan_bersih']), str(row['sentimen_aktual']), str(row['prediksi'])))
            
        cursor.executemany(sql_pred, val_pred)
        conn.commit()
        
        return True, "Data prediksi dan metrik evaluasi berhasil disimpan ke MySQL!"
    except Exception as e:
        return False, str(e)
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()
            
# --- FUNGSI PDF (FPDF) ---
def generate_pdf(metrics, df):
    pdf = FPDF()
    pdf.add_page()
    
    # Judul Skripsi Laporan
    pdf.set_font("Arial", size=14, style='B')
    pdf.cell(200, 8, txt="LAPORAN ANALISIS SENTIMEN KELUHAN PENGGUNA", ln=1, align='C')
    pdf.set_font("Arial", size=12, style='B')
    pdf.cell(200, 8, txt="FITUR PESAN ANTAR MAKANAN APLIKASI SHOPEE", ln=1, align='C')
    pdf.set_font("Arial", size=10)
    pdf.cell(200, 10, txt=f"Tanggal Cetak: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}", ln=1, align='C')
    
    # Rekap Analisis
    pdf.ln(5)
    pdf.set_font("Arial", size=12, style='B')
    pdf.cell(200, 10, txt="1. Evaluasi Performa Model Naive Bayes", ln=1)
    
    pdf.set_font("Arial", size=11)
    pdf.cell(200, 8, txt=f"Total Keseluruhan Ulasan: {len(df)} Ulasan", ln=1)
    
    pos_count = len(df[df['prediksi'] == 'Positif'])
    neg_count = len(df[df['prediksi'] == 'Negatif'])
    pdf.cell(200, 8, txt=f"Prediksi Sentimen -> Positif: {pos_count} | Negatif: {neg_count}", ln=1)
    
    pdf.ln(3)
    pdf.cell(200, 8, txt=f"- Akurasi  : {metrics['accuracy'] * 100:.2f} %", ln=1)
    pdf.cell(200, 8, txt=f"- Presisi  : {metrics['precision'] * 100:.2f} %", ln=1)
    pdf.cell(200, 8, txt=f"- Recall   : {metrics['recall'] * 100:.2f} %", ln=1)
    pdf.cell(200, 8, txt=f"- F1-Score : {metrics['f1'] * 100:.2f} %", ln=1)
    
    return pdf.output(dest='S').encode('latin-1')

# --- MAIN APP LOGIC ---
def main():
    st.markdown("<h1 class='custom-title'>ANALISIS SENTIMEN</h1>", unsafe_allow_html=True)
    st.markdown("<p class='custom-subtitle'>Klasifikasi Sentimen Pelanggan Menggunakan Algoritma Naive Bayes</p>", unsafe_allow_html=True)
    
    # Memuat object pre-trained model dan text stemmer Sastrawi
    model, vectorizer = load_models()
    stopword_remover, stemmer = load_sastrawi()
    
    # Warning box jika tidak menemukan file pkl
    if model is None or vectorizer is None:
         st.error("❌ ERROR: Model atau Vectorizer tidak ditemukan!")
         st.info("Pastikan Anda sudah membuat folder bernama 'streamlit_model' di root projek ini (sejajar dengan app.py). Letakkan 'model_nb.pkl' dan 'tfidf_vectorizer.pkl' ke dalam folder tersebut sebelum menjalakankan fitur prediksi.")
         st.stop()
        
    st.header("1. Upload Dataset dan Prediksi")
    uploaded_file = st.file_uploader("Upload File (CSV/Excel) Bersisi 'ulasan' dan 'rating':", type=['csv', 'xlsx'])
    
    if uploaded_file is not None:
        try:
            # Load Data
            if uploaded_file.name.endswith('.csv'):
                df = pd.read_csv(uploaded_file)
            else:
                df = pd.read_excel(uploaded_file)
                
            df.columns = df.columns.str.lower()
            
            # Constraint/Filter 1: Cek Kolom
            if 'ulasan' not in df.columns or 'rating' not in df.columns:
                st.error("Error: Dataset harus memiliki kolom bernama 'ulasan' dan 'rating'.")
                st.stop()
                
            # Filter Data (Hapus Rating 3, Ubah 1,2 jd Negatif, 4,5 Positif)
            st.info("Menerapkan aturan Rating: Menghapus rating bernilai 3 dan membagi rating [1,2] -> Negatif, [4,5] -> Positif sebagai Sentimen Aktual...")
            
            df = df.dropna(subset=['ulasan', 'rating'])
            df = df[df['rating'] != 3].copy()
            df['sentimen_aktual'] = df['rating'].apply(lambda x: 'Negatif' if int(x) <= 2 else 'Positif')
            
            with st.spinner("⏳ Menjalankan Preprocessing Text & Model Prediksi Naive Bayes... (Mohon tunggu, Stemming Sastrawi memakan waktu sejenak)"):
                # Menjalankan Text Preprocessing
                df['ulasan_bersih'] = df['ulasan'].apply(lambda x: preprocess_text(x, stopword_remover, stemmer))
                # Hentikan atau skip jika ada baris yg akhirnya kosong / blank space setelah di pre-proses
                df = df[df['ulasan_bersih'].str.strip() != ""]
                
                # Transform (Prediksi berdasarkan vocab pre-trained vectorizer (TIDAK FIT ULANG))
                X_tfidf = vectorizer.transform(df['ulasan_bersih'])
                df['prediksi'] = model.predict(X_tfidf)
                
                # Kalkulasi Matrix Performa Metrics (Prediksi vs Aktual)
                pos_label = 'Positif' if 'Positif' in df['sentimen_aktual'].values else df['sentimen_aktual'].unique()[0]
                
                acc = accuracy_score(df['sentimen_aktual'], df['prediksi'])
                try:
                    prec = precision_score(df['sentimen_aktual'], df['prediksi'], pos_label=pos_label)
                    rec = recall_score(df['sentimen_aktual'], df['prediksi'], pos_label=pos_label)
                    f1 = f1_score(df['sentimen_aktual'], df['prediksi'], pos_label=pos_label)
                except ValueError:
                    prec = precision_score(df['sentimen_aktual'], df['prediksi'], average='weighted', zero_division=0)
                    rec = recall_score(df['sentimen_aktual'], df['prediksi'], average='weighted', zero_division=0)
                    f1 = f1_score(df['sentimen_aktual'], df['prediksi'], average='weighted', zero_division=0)
                    
                metrics = {'accuracy': acc, 'precision': prec, 'recall': rec, 'f1': f1}
                st.success("✅ Prediksi Selesai!")
                
                # Custom Glassmorphism Metric Cards
                st.markdown(f"""
                <div style="display: flex; gap: 20px; justify-content: space-between; margin-bottom: 2rem; margin-top: 1rem; flex-wrap: wrap;">
                    <div class="glass-metric" style="flex: 1; min-width: 150px;">
                        <div class="metric-title">Akurasi</div>
                        <div class="metric-value">{acc*100:.1f}%</div>
                    </div>
                    <div class="glass-metric" style="flex: 1; min-width: 150px;">
                        <div class="metric-title">Presisi</div>
                        <div class="metric-value">{prec*100:.1f}%</div>
                    </div>
                    <div class="glass-metric" style="flex: 1; min-width: 150px;">
                        <div class="metric-title">Recall</div>
                        <div class="metric-value">{rec*100:.1f}%</div>
                    </div>
                    <div class="glass-metric" style="flex: 1; min-width: 150px;">
                        <div class="metric-title">F1-Score</div>
                        <div class="metric-value">{f1*100:.1f}%</div>
                    </div>
                </div>
                """, unsafe_allow_html=True)
                
            # ----------------------------------------------------
            # DASHBOARD EVALUASI VISUALISASI
            # ----------------------------------------------------
            st.header("2. Visualisasi Sentimen & Metrik Evaluasi")
            col1, col2, col3 = st.columns(3)
            
            # Pie Chart
            with col1:
                st.write("**Distribusi Prediksi (Pie Chart)**")
                pred_counts = df['prediksi'].value_counts()
                fig1, ax1 = plt.subplots(figsize=(4, 4))
                fig1.patch.set_alpha(0.0) # Transparent background
                colors = ['#FF4B4B','#00CC96'] if 'Negatif' in pred_counts.index[0] else ['#00CC96','#FF4B4B']
                ax1.pie(pred_counts, labels=pred_counts.index, autopct='%1.1f%%', startangle=90, colors=colors, textprops={'color':"white", 'weight':"bold"})
                ax1.axis('equal')
                st.pyplot(fig1)
                
            # Seaborn Bar Chart
            with col2:
                st.write("**Metrik Performa Model (Seaborn)**")
                metrik_names = ['Accuracy', 'Precision', 'Recall', 'F1']
                metrik_vals = [metrics['accuracy']*100, metrics['precision']*100, metrics['recall']*100, metrics['f1']*100]
                
                fig2, ax2 = plt.subplots(figsize=(4, 3.5))
                fig2.patch.set_alpha(0.0) # Transparent background
                ax2.patch.set_alpha(0.0)
                sns_plot = sns.barplot(x=metrik_names, y=metrik_vals, palette="coolwarm", ax=ax2)
                ax2.set_ylim(0, 115)
                # Menambahkan value persentase diatas bar seaborn
                for i in sns_plot.containers:
                    ax2.bar_label(i, fmt='%.1f%%', padding=2, color='white', weight='bold')
                st.pyplot(fig2)
                
            # Wordcloud Negatif
            with col3:
                st.write("**WordCloud (Hanya Ulasan Negatif)**")
                df_neg = df[df['prediksi'] == 'Negatif']
                if not df_neg.empty:
                    neg_text_comb = " ".join(val for val in df_neg['ulasan_bersih'])
                    if len(neg_text_comb.strip()) > 0:
                        wc = WordCloud(width=500, height=400, background_color=None, mode="RGBA", colormap='Oranges', max_words=100).generate(neg_text_comb)
                        fig3, ax3 = plt.subplots(figsize=(4, 3.5))
                        fig3.patch.set_alpha(0.0)
                        ax3.imshow(wc, interpolation='bilinear')
                        ax3.axis("off")
                        st.pyplot(fig3)
                    else:
                         st.info("Tidak ada kata untuk di-visualisasikan.")
                else:
                    st.info("Tidak ada ulasan yg terprediksi 'Negatif'.")
                    
            # ----------------------------------------------------
            # TABEL INTERAKTIF PENCARIAN
            # ----------------------------------------------------
            st.header("3. Hasil Prediksi Keseluruhan & Filter Data")
            sentiments_opt = df['prediksi'].unique()
            selected_sents = st.multiselect("Filter Data Ulasan Berdasarkan Klasifikasi:", sentiments_opt, default=sentiments_opt)
            
            final_df = df[df['prediksi'].isin(selected_sents)]
            
            # Pengurutan kolom (munculkan yg terpenting dulu)
            first_cols = ['ulasan', 'ulasan_bersih', 'rating', 'sentimen_aktual', 'prediksi'] 
            other_cols = [c for c in final_df.columns if c not in first_cols]
            st.dataframe(final_df[first_cols + other_cols], use_container_width=True)
            
            # ----------------------------------------------------
            # EXPORT & DATABASE MYSQL
            # ----------------------------------------------------
            st.header("4. Database Storage & Akses Laporan Cetak")
            col_db, col_pdf = st.columns(2)
            
            with col_db:
                if st.button("💾 Simpan Data ke Database MySQL (Lokal)", type="primary"):
                    with st.spinner("Sedang memproses penyimpanan ke Database..."):
                        is_success, msg = insert_to_db(metrics, df) 
                        if is_success:
                            st.success(msg)
                        else:
                            st.error(f"Gagal koneksi! Pastikan Layanan MySQL (XAMPP/WAMP) menyala/Running. \n\nDetail: {msg}")
                            
            with col_pdf:
                # PDF Generation & Button
                pdf_bytes = generate_pdf(metrics, df)
                st.download_button(
                    label="📄 Unduh Laporan PDF",
                    data=pdf_bytes,
                    file_name="Laporan_Analisis_Sentimen_Shopee.pdf",
                    mime="application/pdf"
                )
                
        except Exception as e:
            st.error(f"Terjadi kesalahan Fatal saat eksekusi file utama: {str(e)}")

if __name__ == "__main__":
    main()
