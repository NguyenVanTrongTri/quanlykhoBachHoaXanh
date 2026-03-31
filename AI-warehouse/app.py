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

# --- NÂNG CẤP: ĐỊNH NGHĨA ĐƯỜNG DẪN TUYỆT ĐỐI ---
# Lấy đường dẫn của chính file app.py hiện tại
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
# Ghép với folder data_history để ra đường dẫn chuẩn
SNAPSHOT_PATH = os.path.join(BASE_DIR, "data_history", "database_snapshot.bin")

# --- ROUTE 1: DỰ BÁO HÀNG HÓA ---
@app.route('/api/forecast', methods=['GET'])
def get_forecast():
    try:
        days_param = request.args.get('days', default=1, type=int)

        # Chạy read_data để cập nhật dữ liệu từ DB Cloud
        read_data() 

        # Kiểm tra file tồn tại trước khi load
        if not os.path.exists(SNAPSHOT_PATH):
            return jsonify({
                "status": "error", 
                "message": f"Không tìm thấy file snapshot tại: {SNAPSHOT_PATH}. Vui lòng chạy Sync trước."
            }), 404

        db_snapshot = joblib.load(SNAPSHOT_PATH)
        if "tables" not in db_snapshot or "hanghoa" not in db_snapshot["tables"]:
            return jsonify({"status": "error", "message": "Dữ liệu hàng hóa bị trống."}), 404
            
        df_hanghoa = pd.DataFrame(db_snapshot["tables"]["hanghoa"])
        data, error = predictor.get_forecast_data(df_hanghoa, days_ahead=days_param)
        
        if error:
            return jsonify({"status": "error", "message": error}), 500
        
        return jsonify({
            "status": "success",
            "date": data['date'],
            "forecast_days": days_param,
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

# --- ROUTE 3: ĐỒNG BỘ THỦ CÔNG (Cho phép GET để test nhanh) ---
@app.route('/api/sync', methods=['GET', 'POST'])
def manual_sync():
    if read_data():
        return jsonify({"status": "success", "message": "Đã đồng bộ dữ liệu thành công!"})
    return jsonify({"status": "error", "message": "Đồng bộ thất bại. Kiểm tra kết nối DB Cloud."}), 500

if __name__ == "__main__":
    # 1. Lấy port từ Render
    port = int(os.environ.get("PORT", 10000))
    
    # 2. KHÔNG gọi read_data() ở đây. 
    # Để app.run lên đầu tiên để Render thấy cổng 10000 mở ngay lập tức.
    print("Lora AI đang khởi tạo cổng...")
    app.run(host='0.0.0.0', port=port)