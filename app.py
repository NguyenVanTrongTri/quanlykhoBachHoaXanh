import os
import joblib
import pandas as pd
from flask import Flask, jsonify, request
from flask_cors import CORS

# Import các công cụ logic
from predict import LoraPredictor
from chat import clean_text, get_answer, DYNAMIC_RESPONSES, model as db_model, vectorizer
from readDatabase import read_data 

app = Flask(__name__)
CORS(app)

predictor = LoraPredictor()

# --- ROUTE 1: DỰ BÁO HÀNG HÓA (ĐÃ NÂNG CẤP CHỌN NGÀY) ---
@app.route('/api/forecast', methods=['GET'])
def get_forecast():
    try:
        # NÂNG CẤP: Lấy số ngày từ query string (ví dụ: /api/forecast?days=3)
        # Mặc định là 1 nếu không truyền gì lên
        days_param = request.args.get('days', default=1, type=int)

        read_data() 

        db_snapshot = joblib.load("data_history/database_snapshot.bin")
        if "tables" not in db_snapshot or "hanghoa" not in db_snapshot["tables"]:
            return jsonify({"status": "error", "message": "Không tìm thấy dữ liệu hàng hóa."}), 404
            
        df_hanghoa = pd.DataFrame(db_snapshot["tables"]["hanghoa"])
        
        # TRUYỀN THAM SỐ days_param VÀO ĐÂY
        data, error = predictor.get_forecast_data(df_hanghoa, days_ahead=days_param)
        
        if error:
            return jsonify({"status": "error", "message": error}), 500
        
        return jsonify({
            "status": "success",
            "date": data['date'],
            "forecast_days": days_param, # Trả về số ngày đã dự báo để Frontend kiểm tra
            "accuracy": data['accuracy'],
            "items": data['items']
        })
    except Exception as e:
        return jsonify({"status": "error", "message": f"Lỗi dự báo: {str(e)}"}), 500

# --- ROUTE 2: CHATBOT AI ---
@app.route('/api/chat', methods=['POST'])
def chat_with_lora():
    try:
        user_data = request.json
        user_query = user_data.get("message", "")
        if not user_query:
            return jsonify({"response": "Lora AI: Bạn chưa nhập tin nhắn nè!"})

        read_data()

        cleaned = clean_text(user_query)
        X = vectorizer.transform([cleaned])
        intent = db_model.predict(X)[0]
        confidence = max(db_model.predict_proba(X)[0])
        
        ai_response = get_answer(intent, user_query, confidence, DYNAMIC_RESPONSES)

        return jsonify({
            "status": "success",
            "response": ai_response,
            "intent": intent,
            "confidence": round(float(confidence), 2)
        })
    except Exception as e:
        return jsonify({"status": "error", "message": f"Lỗi Chat: {str(e)}"}), 500

# --- ROUTE 3: ĐỒNG BỘ THỦ CÔNG ---
@app.route('/api/sync', methods=['POST'])
def manual_sync():
    if read_data():
        return jsonify({"status": "success", "message": "Đã đồng bộ dữ liệu thành công!"})
    return jsonify({"status": "error", "message": "Đồng bộ thất bại."}), 500

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    read_data()
    app.run(host='0.0.0.0', port=port)