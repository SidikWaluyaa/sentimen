import joblib
import json
import re
import os
import zipfile
import xml.etree.ElementTree as ET
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

# Initialize Sastrawi
stopword_factory = StopWordRemoverFactory()
stopword_remover = stopword_factory.create_stop_word_remover()

stemmer_factory = StemmerFactory()
stemmer = stemmer_factory.create_stemmer()

# Exact norm_dict from app.py
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

def preprocess_text(text):
    if not text:
        return ""
    text = str(text).lower()
    text = re.sub(r'http\S+|www\S+', '', text)
    text = re.sub(r'@\w+', '', text)
    text = re.sub(r'#\w+', '', text)
    text = re.sub(r'\d+', '', text)
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    text = re.sub(r'\s+', ' ', text).strip()
    text = normalize_text(text)
    text = stopword_remover.remove(text)
    text = stemmer.stem(text)
    return text.strip()

# Load models
model = joblib.load('legacy_streamlit/streamlit_model/model_nb.pkl')
vec = joblib.load('legacy_streamlit/streamlit_model/tfidf_vectorizer.pkl')

# Read Excel rows
with zipfile.ZipFile('KANDIDAT_VALIDASI_SHOPEEFOOD_FINAL.xlsx', 'r') as z:
    shared_strings = []
    if 'xl/sharedStrings.xml' in z.namelist():
        tree = ET.fromstring(z.read('xl/sharedStrings.xml'))
        for si in tree.findall('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}si'):
            t = si.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}t')
            if t is not None and t.text:
                shared_strings.append(t.text)
            else:
                parts = [elem.text for elem in si.iter('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}t') if elem.text]
                shared_strings.append(''.join(parts))
                
    tree = ET.fromstring(z.read('xl/worksheets/sheet1.xml'))
    rows = tree.findall('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}sheetData/{http://schemas.openxmlformats.org/spreadsheetml/2006/main}row')
    
    records = []
    for r in rows[1:]:
        col_map = {}
        for c in r.findall('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}c'):
            cell_ref = c.attrib.get('r', '')
            col_letter = ''.join([ch for ch in cell_ref if ch.isalpha()])
            cell_type = c.attrib.get('t')
            v = c.find('{http://schemas.openxmlformats.org/spreadsheetml/2006/main}v')
            val = v.text if v is not None else ''
            if cell_type == 's' and val.isdigit():
                val = shared_strings[int(val)]
            col_map[col_letter] = val
        
        ulasan = col_map.get('D', '')
        rating = col_map.get('E', '')
        if ulasan and rating and rating != '3':
            records.append({'ulasan': ulasan, 'rating': int(rating)})

# Pick diverse 30 samples (first 15 positive, first 15 negative)
pos_samples = [r for r in records if r['rating'] >= 4][:15]
neg_samples = [r for r in records if r['rating'] <= 2][:15]
test_samples = pos_samples + neg_samples

benchmark_data = []
for item in test_samples:
    raw = item['ulasan']
    cleaned = preprocess_text(raw)
    if not cleaned:
        continue
    X = vec.transform([cleaned])
    pred = model.predict(X)[0]
    proba = model.predict_proba(X)[0] # [P(Negatif), P(Positif)]
    
    benchmark_data.append({
        'raw_text': raw,
        'rating': item['rating'],
        'cleaned_python': cleaned,
        'expected_prediction': str(pred),
        'proba_negatif': round(float(proba[0]), 4),
        'proba_positif': round(float(proba[1]), 4)
    })

os.makedirs('tests/fixtures', exist_ok=True)
with open('tests/fixtures/streamlit_parity_benchmark.json', 'w', encoding='utf-8') as f:
    json.dump(benchmark_data, f, indent=2, ensure_ascii=False)

print(f"Generated benchmark with {len(benchmark_data)} samples in tests/fixtures/streamlit_parity_benchmark.json")
