import datetime

import mysql.connector
import joblib
import os
from datetime import datetime
_CACHED_SNAPSHOT = None 

def read_data():
    global _CACHED_SNAPSHOT
    try:
        target_dir = 'data_history'
        target_file = os.path.join(target_dir, "database_snapshot.bin")
        
        # 1. LOAD BAN ĐẦU
        if _CACHED_SNAPSHOT is None:
            if os.path.exists(target_file):
                _CACHED_SNAPSHOT = joblib.load(target_file)
                # Tính tổng tất cả các dòng trong các bảng
                total_rows = sum(len(rows) for rows in _CACHED_SNAPSHOT['tables'].values())
                print(f">>> [HỆ THỐNG]: Đã nạp {total_rows} dòng dữ liệu lịch sử vào RAM.")
            else:
                _CACHED_SNAPSHOT = {"tables": {}, "schema": {}, "query_logic": {}}

        conn = mysql.connector.connect(
        host="db-bach-hoa-xanh-trongtri14780-2a54.j.aivencloud.com",
        port="16063",
        user="avnadmin",
        password="AVNS_0jfSoEV9FoIcW9MljSE",
        database="db_quanlykho"
        )
        cursor = conn.cursor(dictionary=True, buffered=True)

        cursor.execute("SHOW TABLES")
        tables_res = cursor.fetchall()
        tables = [list(row.values())[0] for row in tables_res]

        has_new_data = False
        updated_tables = [] # Lưu danh sách các bảng có dữ liệu mới
        total_new_rows = 0

        print("-" * 50)
        for table in tables:
            # 1. Tìm cột khóa chính (ID)
            cursor.execute(f"SHOW KEYS FROM {table} WHERE Key_name = 'PRIMARY'")
            pk_info = cursor.fetchone()
            id_col = pk_info['Column_name'] if pk_info else None
            
            if not id_col:
                cursor.execute(f"SHOW COLUMNS FROM {table}")
                col_res = cursor.fetchone()
                id_col = col_res['Field'] if col_res else None

            if not id_col: continue

            # 2. Khởi tạo bảng trong snapshot nếu chưa có
            is_first_time = False
            if table not in _CACHED_SNAPSHOT["tables"] or not _CACHED_SNAPSHOT["tables"][table]:
                _CACHED_SNAPSHOT["tables"][table] = []
                is_first_time = True # Đánh dấu đây là lần nạp gốc (130k dòng)
            
            existing_rows = _CACHED_SNAPSHOT["tables"][table]

            # 3. Tìm mốc ID lớn nhất để lấy phần chênh lệch
            max_val = 0
            if not is_first_time:
                try:
                    max_val = max(int(row.get(id_col, 0)) for row in existing_rows)
                except:
                    max_val = existing_rows[-1].get(id_col, '')

            # 4. Truy vấn dữ liệu mới từ MySQL
            query = f"SELECT * FROM {table} WHERE {id_col} > %s" if not is_first_time else f"SELECT * FROM {table}"
            cursor.execute(query, (max_val,) if not is_first_time else ())
            new_data = cursor.fetchall()

            if new_data:
                # --- [NÂNG CẤP CHỐT]: PHÂN LOẠI 0 VÀ 1 ---
                # Nếu là lần đầu nạp (130k dòng) -> Gán cờ 0
                # Nếu là nạp thêm sau này -> Gán cờ 1
                assigned_flag = 0 if is_first_time else 1
                
                for row in new_data:
                    row['is_new'] = assigned_flag 
                    row['sync_date'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

                # Nạp vào Snapshot
                _CACHED_SNAPSHOT["tables"][table].extend(new_data)
                
                print(f"    [+] {table.ljust(20)}: +{len(new_data)} dòng (is_new={assigned_flag}).")
                has_new_data = True
                total_new_rows += len(new_data)
                updated_tables.append(table)

        print("-" * 50)
        
        # 5. THÔNG BÁO KẾT QUẢ
        if has_new_data:
            if not os.path.exists(target_dir): os.makedirs(target_dir)
            joblib.dump(_CACHED_SNAPSHOT, target_file)
            
            # Tính lại tổng dòng sau khi cập nhật
            final_total = sum(len(rows) for rows in _CACHED_SNAPSHOT['tables'].values())
            
            print(f">>> [SUCCESS]: CẬP NHẬT HOÀN TẤT!")
            print(f"    - Bảng đã cập nhật: {', '.join(updated_tables)}")
            print(f"    - Tổng dòng nạp thêm: {total_new_rows}")
            print(f"    - Tổng quy mô dữ liệu hiện tại: {final_total} dòng.")
        else:
            print(">>> [INFO]: Dữ liệu đồng bộ. Không có thông tin mới nào được nạp thêm.")

        cursor.close()
        conn.close()
        return True

    except Exception as e:
        print(f"\n[LỖI]: {e}")
        return False

if __name__ == "__main__":
    read_data()