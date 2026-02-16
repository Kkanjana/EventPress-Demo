# EventPress-Demo: Custom WordPress Theme
---

## โครงสร้างโปรเจกต์ (Project Structure)
เพื่อให้ Docker รันได้ถูกต้อง โปรดจัดเรียงไฟล์ตามนี้:
```text
EventPress-Demo/
├── docker-compose.yml       # ไฟล์ตั้งค่า Docker (MySQL 5.7 + WP)
├── README.md                # ไฟล์คู่มือนี้
├── init-db/                 # โฟลเดอร์สำหรับใส่ฐานข้อมูลเริ่มต้น
│   └── database.sql         # ไฟล์ SQL ที่ Export มาจากเครื่องหลัก
└── wp-content/
    ├── themes/
    │   └── my-premium-theme/ # ไฟล์ธีมทั้งหมด (functions.php, style.css, etc.)
    └── uploads/             # โฟลเดอร์รูปภาพที่อัปโหลด (ถ้ามี)
```

## การสั่งรันด้วย Docker
```text
docker-compose up -d
```
## การเข้าใช้งาน
```text

หน้าเว็บไซต์: http://localhost:8000
จัดการฐานข้อมูล: http://localhost:8080 (phpMyAdmin)
User: root | Password: password

WordPress 
User: Kanjana
Pass: WP_kan@6629
```
