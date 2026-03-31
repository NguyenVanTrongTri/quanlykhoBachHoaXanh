import mysql.connector
import joblib
import os
import gc
from datetime import datetime

# Tuyệt đối không để snapshot lơ lửng trong RAM nếu không cần thiết
_CACHED_SNAPSHOT = None 

def read_data():
    global _CACHED_SNAPSHOT
    try:
        base_dir = os.path.dirname(os.path.abspath(__file__))
        target_dir = os.path.join(base_dir, 'data_history')
        target_file = os.path.join(target_dir, "database_snapshot.bin")
        ca_cert_path = os.path.join(base_dir, "ca.pem")

        # 1. LOAD TỐI ƯU
        if os.path.exists(target_file):
            print(">>> [HỆ THỐNG]: Đang nạp snapshot từ ổ đĩa...")
            # Sử dụng mmap_mode để không nạp toàn bộ file vào RAM cùng lúc
            _CACHED_SNAPSHOT = joblib.load(target_file, mmap_mode='r+')
        else:
            _CACHED_SNAPSHOT = {"tables": {}, "schema": {}, "query_logic": {}}

        config = {
            'host': "db-bach-hoa-xanh-trongtri14780-2a54.j.aivencloud.com",
            'port': "16063",
            'user': "avnadmin",
            'password': "AVNS_0jfSoEV9FoIcW9MljSE",
            'database': "db_quanlykho",
            'ssl_ca': ca_cert_path,
            'ssl_verify_cert': True,
            'connection_timeout': 60 # Tăng timeout tránh lỗi SIGKILL khi chờ lâu
        }

        conn = mysql.connector.connect(**config)
        # Sử dụng cursor không buffered để lấy dữ liệu theo luồng, tiết kiệm RAM
        cursor = conn.cursor(dictionary=True)

        cursor.execute("SHOW TABLES")
        tables = [list(row.values())[0] for row in cursor.fetchall()]

        has_new_data = False
        total_new_rows = 0

        for table in tables:
            # Bỏ qua các bảng log quá nặng nếu không cần thiết cho AI
            if table.lower() in ['sys_logs', 'audit_trail']: continue

            # Lấy Primary Key
            cursor.execute(f"SHOW KEYS FROM {table} WHERE Key_name = 'PRIMARY'")
            pk_info = cursor.fetchone()
            id_col = pk_info['Column_name'] if pk_info else None
            if not id_col: continue

            if table not in _CACHED_SNAPSHOT["tables"]:
                _CACHED_SNAPSHOT["tables"][table] = []
            
            existing_rows = _CACHED_SNAPSHOT["tables"][table]
            is_first_time = len(existing_rows) == 0

            max_val = 0
            if not is_first_time:
                try:
                    max_val = max(int(row.get(id_col, 0)) for row in existing_rows)
                except:
                    max_val = existing_rows[-1].get(id_col, 0)

            # GIỚI HẠN DỮ LIỆU: Chỉ nạp tối đa 5000 dòng mới mỗi lần sync để tránh OOM
            query = f"SELECT * FROM {table} WHERE {id_col} > %s ORDER BY {id_col} ASC LIMIT 5000" if not is_first_time else f"SELECT * FROM {table} ORDER BY {id_col} ASC LIMIT 10000"
            cursor.execute(query, (max_val,) if not is_first_time else ())
            
            new_data = cursor.fetchall()

            if new_data:
                assigned_flag = 0 if is_first_time else 1
                for row in new_data:
                    row['is_new'] = assigned_flag 
                    row['sync_date'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

                _CACHED_SNAPSHOT["tables"][table].extend(new_data)
                
                # ÉP GIẢI PHÓNG BỘ NHỚ ĐỆM CỦA DANH SÁCH
                if len(_CACHED_SNAPSHOT["tables"][table]) > 50000:
                    # Nếu bảng quá 50k dòng, chỉ giữ lại 50k dòng mới nhất để cứu RAM
                    _CACHED_SNAPSHOT["tables"][table] = _CACHED_SNAPSHOT["tables"][table][-50000:]
                
                print(f"    [+] {table.ljust(20)}: +{len(new_data)} dòng.")
                has_new_data = True
                total_new_rows += len(new_data)
                
                # Giải phóng biến tạm ngay lập tức
                del new_data
                gc.collect()

        if has_new_data:
            if not os.path.exists(target_dir): os.makedirs(target_dir)
            # Nén file khi lưu để tiết kiệm dung lượng ổ đĩa và RAM khi load lại
            joblib.dump(_CACHED_SNAPSHOT, target_file, compress=3)
            print(f">>> [SUCCESS]: Đã lưu snapshot an toàn.")
        
        cursor.close()
        conn.close()
        
        # XOÁ SẠCH RAM TRƯỚC KHI KẾT THÚC
        _CACHED_SNAPSHOT = None
        gc.collect()
        return True

    except Exception as e:
        print(f"\n[LỖI SYNC]: {e}")
        return False