import mysql.connector
import joblib
import os
import gc
from datetime import datetime

_CACHED_SNAPSHOT = None 

def read_data():
    global _CACHED_SNAPSHOT
    try:
        # Tự động xác định đường dẫn tuyệt đối để tránh lỗi trên Render
        base_dir = os.path.dirname(os.path.abspath(__file__))
        target_dir = os.path.join(base_dir, 'data_history')
        target_file = os.path.join(target_dir, "database_snapshot.bin")
        
        # ĐƯỜNG DẪN FILE CA.PEM (Phải để cùng thư mục với file này)
        ca_cert_path = os.path.join(base_dir, "ca.pem")

        # 1. LOAD BAN ĐẦU
        if _CACHED_SNAPSHOT is None:
            if os.path.exists(target_file):
                _CACHED_SNAPSHOT = joblib.load(target_file)
                total_rows = sum(len(rows) for rows in _CACHED_SNAPSHOT.get('tables', {}).values())
                print(f">>> [HỆ THỐNG]: Đã nạp {total_rows} dòng dữ liệu lịch sử.")
            else:
                _CACHED_SNAPSHOT = {"tables": {}, "schema": {}, "query_logic": {}}

        # 2. KẾT NỐI VỚI SSL (CẤU HÌNH QUAN TRỌNG NHẤT)
        config = {
            'host': "db-bach-hoa-xanh-trongtri14780-2a54.j.aivencloud.com",
            'port': "16063",
            'user': "avnadmin",
            'password': "AVNS_0jfSoEV9FoIcW9MljSE",
            'database': "db_quanlykho",
            'ssl_ca': ca_cert_path, # Truyền file CA tương tự như PHP
            'ssl_verify_cert': True
        }

        print(f">>> [HỆ THỐNG]: Đang kết nối tới Aiven MySQL (SSL)...")
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor(dictionary=True, buffered=True)

        cursor.execute("SHOW TABLES")
        tables_res = cursor.fetchall()
        tables = [list(row.values())[0] for row in tables_res]

        has_new_data = False
        updated_tables = []
        total_new_rows = 0

        print("-" * 50)
        for table in tables:
            # Tìm Primary Key
            cursor.execute(f"SHOW KEYS FROM {table} WHERE Key_name = 'PRIMARY'")
            pk_info = cursor.fetchone()
            id_col = pk_info['Column_name'] if pk_info else None
            
            if not id_col:
                cursor.execute(f"SHOW COLUMNS FROM {table}")
                col_res = cursor.fetchone()
                id_col = col_res['Field'] if col_res else None

            if not id_col: continue

            if table not in _CACHED_SNAPSHOT["tables"]:
                _CACHED_SNAPSHOT["tables"][table] = []
            
            existing_rows = _CACHED_SNAPSHOT["tables"][table]
            is_first_time = len(existing_rows) == 0

            # Lấy mốc ID để nạp phần chênh lệch
            max_val = 0
            if not is_first_time:
                try:
                    max_val = max(int(row.get(id_col, 0)) for row in existing_rows)
                except:
                    max_val = existing_rows[-1].get(id_col, 0)

            # Truy vấn dữ liệu mới
            query = f"SELECT * FROM {table} WHERE {id_col} > %s" if not is_first_time else f"SELECT * FROM {table}"
            cursor.execute(query, (max_val,) if not is_first_time else ())
            new_data = cursor.fetchall()

            if new_data:
                assigned_flag = 0 if is_first_time else 1
                for row in new_data:
                    row['is_new'] = assigned_flag 
                    row['sync_date'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

                _CACHED_SNAPSHOT["tables"][table].extend(new_data)
                print(f"    [+] {table.ljust(20)}: +{len(new_data)} dòng (flag={assigned_flag}).")
                has_new_data = True
                total_new_rows += len(new_data)
                updated_tables.append(table)

        # 3. LƯU VÀ DỌN DẸP
        if has_new_data:
            if not os.path.exists(target_dir): os.makedirs(target_dir)
            joblib.dump(_CACHED_SNAPSHOT, target_file)
            print(f">>> [SUCCESS]: CẬP NHẬT HOÀN TẤT! (+{total_new_rows} dòng)")
        else:
            print(">>> [INFO]: Dữ liệu đã đồng bộ.")

        cursor.close()
        conn.close()
        gc.collect() # Giải phóng RAM sau khi sync xong
        return True

    except Exception as e:
        print(f"\n[LỖI SYNC]: {e}")
        return False

if __name__ == "__main__":
    read_data()