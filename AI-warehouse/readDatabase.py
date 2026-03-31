import mysql.connector
import joblib
import os
import gc
from datetime import datetime

_CACHED_SNAPSHOT = None 

def read_data():
    global _CACHED_SNAPSHOT
    try:
        # 1. XÁC ĐỊNH ĐƯỜNG DẪN & TỰ ĐỘNG TẠO THƯ MỤC
        base_dir = os.path.dirname(os.path.abspath(__file__))
        target_dir = os.path.join(base_dir, 'data_history')
        
        # Nếu chưa có folder data_history thì tạo mới để tránh FileNotFoundError
        if not os.path.exists(target_dir):
            os.makedirs(target_dir)
            print(f">>> [HỆ THỐNG]: Đã tạo thư mục lưu trữ: {target_dir}")
            
        target_file = os.path.join(target_dir, "database_snapshot.bin")
        ca_cert_path = os.path.join(base_dir, "ca.pem") # File chứng chỉ SSL

        # 2. LOAD SNAPSHOT (Dùng mmap_mode để tiết kiệm RAM)
        if _CACHED_SNAPSHOT is None:
            if os.path.exists(target_file):
                _CACHED_SNAPSHOT = joblib.load(target_file, mmap_mode='r+')
                total_rows = sum(len(rows) for rows in _CACHED_SNAPSHOT.get('tables', {}).values())
                print(f">>> [HỆ THỐNG]: Đã nạp {total_rows} dòng từ snapshot cũ.")
            else:
                _CACHED_SNAPSHOT = {"tables": {}, "schema": {}, "query_logic": {}}
                print(">>> [HỆ THỐNG]: Khởi tạo snapshot mới.")

        # 3. CẤU HÌNH KẾT NỐI AIVEN MYSQL (THAY THẾ LOCALHOST)
        config = {
            'host': "db-bach-hoa-xanh-trongtri14780-2a54.j.aivencloud.com",
            'port': "16063",
            'user': "avnadmin",
            'password': "AVNS_0jfSoEV9FoIcW9MljSE",
            'database': "db_quanlykho",
            'ssl_ca': ca_cert_path, # Bắt buộc phải có để kết nối Aiven
            'ssl_verify_cert': True
        }

        print(f">>> [HỆ THỐNG]: Đang kết nối tới Aiven Cloud...")
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor(dictionary=True) # Không dùng buffered để tránh ngốn RAM khi nạp 130k dòng

        cursor.execute("SHOW TABLES")
        tables = [list(row.values())[0] for row in cursor.fetchall()]

        has_new_data = False
        total_new_rows = 0

        # 4. LẶP QUA CÁC BẢNG ĐỂ BỔ SUNG DỮ LIỆU
        for table in tables:
            # Tìm khóa chính để xác định mốc dữ liệu mới
            cursor.execute(f"SHOW KEYS FROM {table} WHERE Key_name = 'PRIMARY'")
            pk_info = cursor.fetchone()
            id_col = pk_info['Column_name'] if pk_info else None
            if not id_col: continue

            if table not in _CACHED_SNAPSHOT["tables"]:
                _CACHED_SNAPSHOT["tables"][table] = []
            
            existing_rows = _CACHED_SNAPSHOT["tables"][table]
            
            # Lấy mốc ID lớn nhất để chỉ tải phần "chênh lệch"
            max_val = 0
            if existing_rows:
                try:
                    max_val = max(int(row.get(id_col, 0)) for row in existing_rows)
                except:
                    max_val = existing_rows[-1].get(id_col, 0)

            # Chỉ nạp những dòng mới phát sinh
            query = f"SELECT * FROM {table} WHERE {id_col} > %s"
            cursor.execute(query, (max_val,))
            new_data = cursor.fetchall()

            if new_data:
                # Đánh dấu dữ liệu mới và thời gian đồng bộ
                for row in new_data:
                    row['sync_date'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

                _CACHED_SNAPSHOT["tables"][table].extend(new_data)
                print(f"    [+] {table.ljust(20)}: +{len(new_data)} dòng mới.")
                has_new_data = True
                total_new_rows += len(new_data)
                
                # Giải phóng biến tạm sau mỗi bảng để cứu RAM
                del new_data
                gc.collect()

        # 5. GHI LẠI VÀO FILE (DÙNG NÉN ĐỂ GIẢM QUY MÔ)
        if has_new_data:
            # compress=3 giúp file .bin nhỏ hơn, nạp nhanh hơn trên Render
            joblib.dump(_CACHED_SNAPSHOT, target_file, compress=3)
            print(f">>> [SUCCESS]: Đã bổ sung thành công dữ liệu mới.")
        else:
            print(">>> [INFO]: Dữ liệu đã đồng bộ hoàn toàn.")

        cursor.close()
        conn.close()
        
        # Xóa cache trong RAM trước khi kết thúc hàm
        _CACHED_SNAPSHOT = None
        gc.collect() 
        return True

    except Exception as e:
        print(f"\n[LỖI KẾT NỐI/SYNC]: {e}")
        return False