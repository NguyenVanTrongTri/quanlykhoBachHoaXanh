import os
import joblib
import gc # Bộ dọn rác để giải phóng RAM
import pandas as pd
from flask import Flask, jsonify, request
from flask_cors import CORS

# Import logic
from predict import LoraPredictor
from chat import clean_text, get_answer, DYNAMIC_RESPONSES, model as db_model, vectorizer
from readDatabase import read_data 

app = Flask(__name__)
CORS(app)

predictor = LoraPredictor()
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SNAPSHOT_PATH = os.path.join(BASE_DIR, "data_history", "database_snapshot.bin")

# --- NÂNG CẤP HÀM DỌN RÁC ---
def clear_memory():
    """Hàm giúp giải phóng RAM sau khi xử lý dữ liệu nặng"""
    gc.collect()

# --- ROUTE 1: DỰ BÁO HÀNG HÓA ---
@app.route('/api/forecast', methods=['GET'])
def get_forecast():
    try:
        days_param = request.args.get('days', default=1, type=int)

        # Kiểm tra file snapshot trước (không tự động read_data ở đây để tránh treo)
        if not os.path.exists(SNAPSHOT_PATH):
            return jsonify({"status": "error", "message": "Chưa có dữ liệu. Vui lòng gọi /api/sync trước."}), 404

        db_snapshot = joblib.load(SNAPSHOT_PATH)
        df_hanghoa = pd.DataFrame(db_snapshot["tables"]["hanghoa"])
        
        # Xóa snapshot ngay sau khi chuyển thành DataFrame để giải phóng RAM
        del db_snapshot 
        
        data, error = predictor.get_forecast_data(df_hanghoa, days_ahead=days_param)
        
        # Dọn dẹp DataFrame sau khi dự báo xong
        del df_hanghoa
        clear_memory()

        if error:
            return jsonify({"status": "error", "message": error}), 500
        
        return jsonify({"status": "success", "date": data['date'], "items": data['items']})
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

# --- ROUTE 2: CHATBOT AI ---
@app.route('/api/chat', methods=['POST'])
def chat_with_lora():
    try:
        user_data = request.json
        user_query = user_data.get("message", "")
        if not user_query:
            return jsonify({"response": "Bạn chưa nhập tin nhắn!"})

        cleaned = clean_text(user_query)
        X = vectorizer.transform([cleaned])
        intent = db_model.predict(X)[0]
        confidence = max(db_model.predict_proba(X)[0])
        
        ai_response = get_answer(intent, user_query, confidence, DYNAMIC_RESPONSES)

        return jsonify({
            "status": "success",
            "response": ai_response,
            "confidence": round(float(confidence), 2)
        })
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

# --- ROUTE 3: ĐỒNG BỘ THỦ CÔNG ---
@app.route('/api/sync', methods=['GET', 'POST'])
def manual_sync():
    # Gọi read_data() tại đây khi app đã Live ổn định
    if read_data():
        clear_memory()
        return jsonify({"status": "success", "message": "Đã đồng bộ và dọn dẹp RAM!"})
    return jsonify({"status": "error", "message": "Thất bại. Kiểm tra DB Cloud."}), 500

# --- KHỞI CHẠY (QUAN TRỌNG NHẤT) ---
if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    # KHÔNG gọi read_data() ở đây. App phải Live trước đã!
    print(f"Lora AI khởi động tại cổng: {port}")
    app.run(host='0.0.0.0', port=port)