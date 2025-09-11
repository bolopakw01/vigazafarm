# 🐔 Vigaza Farm Management System

## Overview
Sistem Manajemen Peternakan Unggas yang terintegrasi dengan workflow automation dari Penetasan → Pembesaran → Produksi, dilengkapi dengan Role-Based Access Control (RBAC) dan monitoring real-time.

## 🚀 Features

### 🔐 Role-Based Access Control (RBAC)
- **Admin**: Full access ke semua fitur dan user management
- **Manager**: Access monitoring, reporting, dan operasional (tanpa user management)  
- **Operator**: Input data harian dan monitoring operasional

### 🔄 Integrated Workflow System
```
PENETASAN (Telur → DOC) → PEMBESARAN (Monitoring Harian) → PRODUKSI (Telur + Analytics)
```

### 📊 Real-time Monitoring & Analytics
- Dashboard statistics untuk setiap stage
- Performance metrics (HDP, FCR, Survival Rate)
- Trend analysis dan forecasting
- Monitoring harian otomatis

### 📋 Core Modules
1. **Penetasan Management**
   - Batch tracking dengan auto-generated codes
   - Monitoring suhu & kelembaban
   - Automatic transition ke pembesaran

2. **Pembesaran Management**  
   - Daily monitoring (berat, pakan, kematian)
   - Growth performance tracking
   - Automatic transition ke produksi

3. **Produksi Management**
   - Daily production records (telur, pakan, kematian)
   - Efficiency metrics calculation
   - Complete workflow history

4. **Kandang Management**
   - Multi-tipe kandang (Penetasan, Pembesaran, Produksi)
   - Capacity management
   - Status monitoring

5. **Karyawan Management**
   - Employee data dengan jabatan
   - Performance tracking
   - Role assignment

## 🛠 Technical Stack

### Backend
- **Framework**: CodeIgniter 3.x
- **Database**: MySQL 5.7+
- **PHP**: 7.4+

### Frontend
- **CSS Framework**: Bootstrap 4.x
- **Charts**: ApexCharts, Morris.js, C3.js
- **UI Components**: SweetAlert, DataTables, Select2

### Features
- **Authentication**: Session-based dengan password hashing
- **Security**: CSRF protection, XSS filtering
- **Architecture**: MVC pattern dengan enhanced models
- **Database**: Optimized dengan proper indexing

## 📦 Installation

### Requirements
- XAMPP/LAMP/WAMP with PHP 7.4+
- MySQL 5.7+
- Apache/Nginx

### Setup Steps

1. **Clone/Copy project** ke htdocs/www folder
```bash
cd /path/to/htdocs
# Copy project folder ke vigazafarm_clean
```

2. **Database Setup**
```sql
-- Import database file
mysql -u root -p < vigazafarm_clean_updated.sql
```

3. **Configuration**
```php
// application/config/database.php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'vigazafarm_clean',
    // ... other configs
);
```

4. **Access Application**
```
http://localhost/vigazafarm_clean
```

### Default Users
| Username | Password | Role | Access |
|----------|----------|------|--------|
| admin | admin123 | admin | Full Access |
| manager | admin123 | manager | Monitoring & Reports |
| operator | admin123 | operator | Data Input |

## 📁 Project Structure

```
vigazafarm_clean/
├── application/
│   ├── controllers/
│   │   ├── Base_Controller.php      # Enhanced RBAC controller
│   │   ├── Dashboard.php           # Main dashboard
│   │   ├── Penetasan.php          # Penetasan management
│   │   ├── Pembesaran.php         # Pembesaran management
│   │   ├── Produksi.php           # Produksi management
│   │   └── ...
│   ├── models/
│   │   ├── M_login.php            # Enhanced authentication
│   │   ├── M_penetasan.php        # Penetasan with workflow
│   │   ├── M_pembesaran.php       # Pembesaran with monitoring
│   │   ├── M_produksi.php         # Produksi with analytics
│   │   └── ...
│   ├── views/
│   │   ├── mimin/                 # Admin interface
│   │   ├── tbase/                 # Template components
│   │   └── ...
│   └── config/
│       ├── database.php           # Database configuration
│       └── ...
├── assets/                        # CSS, JS, Images
├── vigazafarm_clean_updated.sql   # Enhanced database schema
└── README.md
```

## 🔄 Workflow Process

### 1. Penetasan (Hatching)
```php
// Create new penetasan batch
$batch_code = $this->m_penetasan->generate_batch_code();
$penetasan_id = $this->m_penetasan->create_penetasan_workflow($data);

// Complete penetasan
$this->m_penetasan->complete_penetasan($id, $hasil_data);
// → Automatically creates pembesaran entry
```

### 2. Pembesaran (Growth)
```php
// Start pembesaran from penetasan
$this->m_pembesaran->start_pembesaran($id);

// Daily monitoring
$this->m_pembesaran->add_daily_monitoring($id, $monitoring_data);

// Complete pembesaran  
$this->m_pembesaran->complete_pembesaran($id, $result_data);
// → Automatically creates produksi entry
```

### 3. Produksi (Production)
```php
// Start produksi from pembesaran
$this->m_produksi->start_produksi($id);

// Daily production records
$this->m_produksi->add_daily_record($id, $daily_data);

// Analytics & reporting
$efficiency = $this->m_produksi->get_efficiency_metrics($id);
```

## 🎯 Key Features Detail

### Enhanced Models
- **Workflow Integration**: Seamless data flow antar stages
- **Auto-batch Generation**: Smart batch code creation
- **Complete Tracking**: Full audit trail dengan workflow logs
- **Performance Analytics**: Advanced metrics calculation

### RBAC System
- **Permission Matrix**: Granular access control
- **Session Security**: Secure session management
- **Backward Compatibility**: Support legacy authentication

### Dashboard Analytics
- **Real-time Statistics**: Live performance monitoring
- **Trend Analysis**: Historical data visualization
- **Performance Metrics**: KPI tracking per kandang/batch
- **Workflow Timeline**: Complete batch journey tracking

## 🔧 API Endpoints

### Authentication
- `POST /mimin/masuk` - User login
- `GET /mimin/keluar` - User logout

### Penetasan
- `GET /penetasan` - List penetasan
- `POST /penetasan/add` - Create new penetasan
- `PUT /penetasan/complete/{id}` - Complete penetasan

### Pembesaran  
- `GET /pembesaran` - List pembesaran
- `POST /pembesaran/monitoring` - Add daily monitoring
- `PUT /pembesaran/complete/{id}` - Complete pembesaran

### Produksi
- `GET /produksi` - List produksi  
- `POST /produksi/daily` - Add daily production
- `GET /produksi/analytics/{id}` - Get efficiency metrics

## 📊 Database Schema

### Core Tables
- `users` - User management dengan RBAC
- `kos_penetasan` - Penetasan data dengan workflow tracking
- `kos_pembesaran` - Pembesaran dengan monitoring integration
- `kos_produksi` - Produksi dengan analytics
- `workflow_logs` - Complete workflow tracking

### Monitoring Tables  
- `pembesaran_monitoring` - Daily pembesaran monitoring
- `produksi_harian` - Daily production records

### Supporting Tables
- `kos_kandang` - Kandang management
- `kos_karyawan` - Employee management
- `kos_mesin` - Equipment management

## 🎨 UI/UX Features

### Responsive Design
- Mobile-friendly interface
- Adaptive layouts untuk semua screen size
- Touch-optimized controls

### Interactive Elements
- Real-time charts dan graphs
- Interactive dashboards
- Modal forms untuk quick actions
- Toast notifications

### User Experience
- Role-based navigation
- Contextual help
- Breadcrumb navigation
- Quick access shortcuts

## 📈 Performance Optimizations

### Database
- Proper indexing untuk query performance
- Optimized joins dengan views
- Batch processing untuk large datasets

### Frontend
- Minified CSS/JS assets
- Image optimization
- Lazy loading untuk charts
- Caching strategy

## 🔒 Security Features

### Authentication & Authorization
- Password hashing dengan PHP password_hash()
- Session security dengan proper timeout
- CSRF protection
- XSS filtering

### Data Protection
- SQL injection prevention
- Input validation & sanitization
- Secure file upload handling
- Access logging

## 🚀 Future Enhancements

### Planned Features
- [ ] REST API untuk mobile app integration
- [ ] Real-time notifications dengan WebSocket
- [ ] Advanced reporting dengan PDF export
- [ ] Inventory management integration
- [ ] Financial module expansion
- [ ] IoT sensor integration
- [ ] Machine learning untuk predictive analytics

### Technical Improvements
- [ ] Migration ke CodeIgniter 4.x
- [ ] Docker containerization
- [ ] CI/CD pipeline setup
- [ ] Unit testing implementation
- [ ] API documentation dengan Swagger

## 📞 Support & Contact

### Project Information
- **Version**: 2.0 Enhanced
- **Last Updated**: August 2025
- **Compatibility**: PHP 7.4+, MySQL 5.7+

### Development Team
- **Lead Developer**: GitHub Copilot AI
- **Architecture**: MVC with Enhanced Workflow Integration
- **Framework**: CodeIgniter 3.x Enhanced

## 📝 License

This project is proprietary software for Vigaza Farm Management System.

---

## 🎉 Quick Start Guide

1. **Setup database** dengan file `vigazafarm_clean_updated.sql`
2. **Configure** database connection di `application/config/database.php`  
3. **Access** http://localhost/vigazafarm_clean
4. **Login** dengan admin/admin123
5. **Explore** integrated workflow: Penetasan → Pembesaran → Produksi

**Happy Farming! 🐔🥚🌾**
