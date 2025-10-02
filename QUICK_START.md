# 🚀 Quick Start Guide - VigazaFarm DSS

## 🔐 Login Credentials

### Owner Account (Full Access)
- **Username**: `lopa123`
- **Password**: `lopa123`
- **Email**: bolopa@gmail.com
- **Role**: Owner

### Operator Account (Limited Access)
- **Username**: `op1`
- **Password**: `op1`
- **Email**: op1@local
- **Role**: Operator

---

## 🌐 Access URLs

- **Login Page**: http://localhost/vigazafarm/public/login
- **Dashboard**: http://localhost/vigazafarm/public/dashboard
- **Admin Penetasan**: http://localhost/vigazafarm/public/admin/penetasan

---

## ⚡ Quick Commands

### Database
```powershell
# Check database status
php artisan migrate:status

# Fresh migration with seed
php artisan migrate:fresh --seed

# Show login info
php show_login_info.php

# Check users
php check_users.php
```

### Development
```powershell
# Start Laravel server
php artisan serve

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Build assets
npm run dev
```

---

## 📊 Database Overview

### Master Tables (5)
1. ✅ **pengguna** - User management (2 users)
2. ✅ **kandang** - Housing management (8 kandang)
3. ✅ **batch_produksi** - Production batch tracking
4. ✅ **stok_pakan** - Feed inventory (5 jenis pakan)
5. ✅ **parameter_standar** - DSS standards (18 parameters)

### Transaction Tables (8)
6. ✅ **penetasan** - Hatching records
7. ✅ **pembesaran** - Growing phase
8. ✅ **produksi** - Production phase
9. ✅ **telur** - Daily egg production
10. ✅ **pakan** - Feed consumption
11. ✅ **kematian** - Mortality records
12. ✅ **transaksi_pakan** - Feed transactions
13. ✅ **kesehatan** - Health & vaccination

### Financial Tables (3)
14. ✅ **keuangan** - Financial transactions
15. ✅ **penjualan_telur** - Egg sales
16. ✅ **penjualan_burung** - Quail sales

### DSS & Monitoring (4)
17. ✅ **monitoring_lingkungan** - Environment monitoring
18. ✅ **analisis_rekomendasi** - DSS recommendations
19. ✅ **laporan_harian** - Daily reports
20. ✅ **alert** - Alert notifications

**Total: 20 tables** ✨

---

## 🎯 Key Features

### ✅ Implemented
- User authentication (Owner & Operator)
- Database structure (20+ tables)
- Master data seeding
- Parameter standards
- Multi-housing management
- Feed inventory system

### 🔨 In Development
- Dashboard analytics
- Daily reports
- DSS recommendations
- Alert system
- Financial reports
- Export to Excel/PDF

---

## 📈 KPI Metrics

### FCR (Feed Conversion Ratio)
```
FCR = Total Pakan (kg) / (Total Telur × Berat Telur)
Target: 2.0 - 3.0
```

### HDP (Hen Day Production)
```
HDP = (Jumlah Telur / Jumlah Burung) × 100%
Target: 70-95%
```

### Mortalitas
```
Mortalitas = (Total Mati / Populasi Awal) × 100%
Target: < 2% per bulan
```

---

## 🐛 Troubleshooting

### Issue: Cannot connect to database
**Solution:**
1. Buka XAMPP Control Panel
2. Start MySQL
3. Refresh browser

### Issue: Password salah
**Solution:**
```powershell
# Reset password
php update_password.php
```

### Issue: Migration belum jalan
**Solution:**
```powershell
php artisan migrate:fresh --seed
```

---

## 📚 Documentation

- [DATABASE_DOCUMENTATION.md](DATABASE_DOCUMENTATION.md) - Struktur database lengkap
- [DSS_README.md](DSS_README.md) - Panduan implementasi lengkap
- [database/schema/dss_schema.sql](database/schema/dss_schema.sql) - SQL schema

---

## 📞 Support

- **Repository**: github.com/bolopakw01/vigazafarm
- **Developer**: Bolopa Kakungnge Walinono

---

**Last Updated**: October 1, 2025
**Database Version**: 2.0
**Status**: ✅ Ready for Development
