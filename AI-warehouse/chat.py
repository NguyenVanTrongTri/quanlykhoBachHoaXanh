import joblib
import json
import re
import unicodedata
import pandas as pd
import duckdb
import subprocess
import sys
import os
import io
from datetime import datetime
from predict import LoraPredictor

# --- CẤU HÌNH HỆ THỐNG ---
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

# Khởi tạo đối tượng dự báo
predictor = LoraPredictor()

# --- BIẾN TOÀN CỤC (Load sẵn để app.py có thể import) ---
MODEL_DIR = "model"
DYNAMIC_RESPONSES = {}
model = None
vectorizer = None

def load_resources():
    global DYNAMIC_RESPONSES, model, vectorizer
    try:
        model = joblib.load(os.path.join(MODEL_DIR, "warehouse_model.pkl"))
        vectorizer = joblib.load(os.path.join(MODEL_DIR, "vectorizer.pkl"))
        with open(os.path.join(MODEL_DIR, "intent_map.json"), "r", encoding="utf-8") as f:
            DYNAMIC_RESPONSES = json.load(f)
        
        # Load database snapshot vào globals để duckdb truy vấn
        db_snapshot = joblib.load("data_history/database_snapshot.bin")
        for table_name, data in db_snapshot["tables"].items():
            globals()[table_name] = pd.DataFrame(data)
    except Exception as e:
        print(f"[Lỗi Resource] {e}")

# Gọi hàm load ngay khi file được import hoặc chạy
load_resources()

# --- CÁC HÀM XỬ LÝ LOGIC ---
def sync_database():
    try:
        env = os.environ.copy()
        env["PYTHONIOENCODING"] = "utf-8"
        subprocess.run([sys.executable, "readDatabase.py"], 
                        check=True, stdout=subprocess.DEVNULL, env=env)
    except Exception as e:
        print(f"[Hệ thống] Lưu ý: Không thể cập nhật dữ liệu mới từ MySQL. (Lỗi: {e})")

def remove_accents(input_str):
    if not input_str: return ""
    nfkd_form = unicodedata.normalize('NFKD', input_str)
    result = "".join([c for c in nfkd_form if not unicodedata.combining(c)])
    return result.replace('đ', 'd').replace('Đ', 'D')

def clean_text(text):
    if not text: return ""
    text = text.lower()
    text = remove_accents(text)
    text = re.sub(r'[^a-z0-9\s]', '', text)
    return " ".join(text.split())

def execute_query_on_bin(sql_template, user_input):
    final_sql = sql_template
    column_translator = {
        "MaHangHoa": "Mã hàng", "DonViTinh": "Đơn vị tính",
        "TenHangHoa": "Tên mặt hàng", "SoLuong": "Số lượng",
        "GiaBan": "Giá bán", "MaPhieuNhap": "Số phiếu",
        "is_new": "Trạng thái" # Thêm để nếu có hiện ra thì biết là đồ mới
    }

    # 1. Xử lý tham số (:param) như cũ
    if ":param" in sql_template:
        match = re.search(r'([A-Z]*\d+)', user_input.upper())
        if not match:
            return "LORA cần bạn cung cấp mã cụ thể (ví dụ: PN001) để tra cứu chính xác."
        param = match.group(1)
        final_sql = sql_template.replace(":param", param)

    # --- [NÂNG CẤP CHỐT]: TỰ ĐỘNG LỌC DỮ LIỆU MỚI (is_new = 1) ---
    # Logic: Nếu câu lệnh chưa có WHERE, thêm WHERE is_new = 1
    # Nếu đã có WHERE, thêm AND is_new = 1
    final_sql_lower = final_sql.lower()
    if "where" in final_sql_lower:
        # Chèn vào trước ORDER BY hoặc LIMIT nếu có, hoặc cuối câu
        if "order by" in final_sql_lower:
            final_sql = re.sub(r"(?i)order by", "AND is_new = 1 ORDER BY", final_sql)
        elif "limit" in final_sql_lower:
            final_sql = re.sub(r"(?i)limit", "AND is_new = 1 LIMIT", final_sql)
        else:
            final_sql += " AND is_new = 1"
    else:
        # Nếu không có WHERE, chèn trước GROUP BY, ORDER BY, LIMIT
        if "group by" in final_sql_lower:
            final_sql = re.sub(r"(?i)group by", "WHERE is_new = 1 GROUP BY", final_sql)
        elif "order by" in final_sql_lower:
            final_sql = re.sub(r"(?i)order by", "WHERE is_new = 1 ORDER BY", final_sql)
        else:
            final_sql += " WHERE is_new = 1"

    try:
        # DuckDB sẽ truy vấn trên các DataFrame (globals) đã nạp
        result_df = duckdb.query(final_sql).to_df()
        
        if result_df.empty:
            return "Hiện tại không có dữ liệu mới nào từ Web khớp với yêu cầu của Leader."

        columns = [col for col in result_df.columns if col != 'is_new'] # Ẩn cột cờ hiệu khi hiển thị
        rows = result_df.values
        
        response_text = f"Kết quả mới cập nhật ({len(rows)} bản ghi):\n"
        for row in rows:
            # Chỉ hiển thị các cột không phải is_new để giao diện sạch
            row_parts = [f"{column_translator.get(columns[i], columns[i])}: {row[i]}" for i in range(len(columns))]
            response_text += " | ".join(row_parts) + "\n"
        return response_text
        
    except Exception as e:
        # In ra câu SQL lỗi để Leader dễ debug nếu câu SQL template bị sai cấu trúc
        print(f"[Debug SQL]: {final_sql}")
        return f"LORA AI: Lỗi truy vấn dữ liệu mới ({e})"
    final_sql = sql_template
    column_translator = {
        "MaHangHoa": "Mã hàng", "DonViTinh": "Đơn vị tính",
        "TenHangHoa": "Tên mặt hàng", "SoLuong": "Số lượng",
        "GiaBan": "Giá bán", "MaPhieuNhap": "Số phiếu"
    }

    if ":param" in sql_template:
        match = re.search(r'([A-Z]*\d+)', user_input.upper())
        if not match:
            return "LORA cần bạn cung cấp mã cụ thể (ví dụ: PN001) để tra cứu chính xác."
        param = match.group(1)
        final_sql = sql_template.replace(":param", param)
    
    try:
        result_df = duckdb.query(final_sql).to_df()
        if result_df.empty:
            return "Rất tiếc, mình không tìm thấy dữ liệu nào khớp với yêu cầu."

        columns = result_df.columns
        rows = result_df.values
        response_text = f"Kết quả tìm thấy ({len(rows)} bản ghi):\n"
        for row in rows:
            row_parts = [f"{column_translator.get(col, col)}: {row[i]}" for i, col in enumerate(columns)]
            response_text += " | ".join(row_parts) + "\n"
        return response_text
    except Exception as e:
        return f"LORA AI: Lỗi truy vấn dữ liệu ({e})"

def get_answer(intent, user_input, confidence, dynamic_responses):
    if confidence < 0.20:
        return "Mình chưa hiểu ý bạn, Leader vui lòng nhập lại rõ hơn nhé."

    forecast_intents = ["predict_import", "prediction_import_count"]
    if intent in forecast_intents:
        # Lấy dataframe hanghoa từ globals
        df_hh = globals().get('hanghoa')
        return predictor.handle_forecast_logic(df_hh)

    response_data = dynamic_responses.get(intent, "")
    if intent.startswith("sql_") or "SELECT" in response_data.upper():
        return execute_query_on_bin(response_data, user_input)
    
    return response_data

def log_to_results(user_input, intent, confidence, ai_response, dynamic_responses):
    folder_path = "dataset"
    file_path = os.path.join(folder_path, "results.json")
    if not os.path.exists(folder_path): os.makedirs(folder_path)

    original_resp = dynamic_responses.get(intent, str(ai_response))
    new_pair = [
        {"intent": intent, "patterns": [user_input], "response": original_resp},
        {"metadata": {
            "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
            "confidence": round(float(confidence), 4),
            "status": "pending",
            "is_sql": intent.startswith("sql_")
        }}
    ]
    # ... (Phần còn lại của hàm log_to_results giữ nguyên) ...

# --- CHẾ ĐỘ CHẠY TERMINAL ---
if __name__ == "__main__":
    sync_database()
    print("\n[HỆ THỐNG] LORA AI LEVEL 3 ĐÃ SẴN SÀNG (TERMINAL MODE)!") 
    print("-" * 50)

    while True:
        try:
            query = input("Bạn: ").strip()
            if not query: continue
            if query.lower() in ['exit', 'quit', 'thoát']: break
            
            cleaned = clean_text(query)
            X = vectorizer.transform([cleaned])
            intent = model.predict(X)[0]
            confidence = max(model.predict_proba(X)[0])
            
            answer = get_answer(intent, query, confidence, DYNAMIC_RESPONSES)
            print(f"LORA AI: {answer}\n")
            log_to_results(query, intent, confidence, answer, DYNAMIC_RESPONSES)
        except KeyboardInterrupt:
            break