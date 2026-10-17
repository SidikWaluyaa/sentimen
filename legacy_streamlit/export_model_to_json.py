import joblib
import json
import numpy as np

try:
    model = joblib.load('streamlit_model/model_nb.pkl')
    vec = joblib.load('streamlit_model/tfidf_vectorizer.pkl')
    
    print("Model classes:", model.classes_)
    print("Model class_log_prior_:", model.class_log_prior_)
    print("Model feature_log_prob_ shape:", model.feature_log_prob_.shape)
    print("Vectorizer vocab size:", len(vec.vocabulary_))
    print("Vectorizer idf_ shape:", vec.idf_.shape)
    
    data = {
        "classes": model.classes_.tolist() if hasattr(model.classes_, "tolist") else list(model.classes_),
        "class_log_prior": model.class_log_prior_.tolist(),
        "feature_log_prob": model.feature_log_prob_.tolist(),
        "vocabulary": {str(k): int(v) for k, v in vec.vocabulary_.items()},
        "idf": vec.idf_.tolist(),
        "norm": getattr(vec, "norm", "l2"),
        "sublinear_tf": getattr(vec, "sublinear_tf", False),
        "use_idf": getattr(vec, "use_idf", True),
        "smooth_idf": getattr(vec, "smooth_idf", True)
    }
    
    with open("ml_model.json", "w", encoding="utf-8") as f:
        json.dump(data, f, indent=2)
        
    print("Successfully exported ml_model.json! File size:", len(json.dumps(data)))
except Exception as e:
    print("Error exporting model:", e)
