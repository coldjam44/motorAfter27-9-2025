#!/bin/bash

# Script لإدارة خادم Motors Website باستخدام PM2
# Motors Website Server Management Script

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$SCRIPT_DIR"

# ألوان للمخرجات
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# دالة لفحص حالة PM2
check_pm2_status() {
    if ! command -v pm2 &> /dev/null; then
        print_error "PM2 غير مثبت على النظام"
        exit 1
    fi
    
    print_status "PM2 Version: $(pm2 --version)"
}

# بدء الخادم
start_server() {
    print_status "بدء تشغيل خادم Motors Website..."
    
    cd "$PROJECT_DIR"
    
    # مسح الكاش أولاً
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    # بدء التطبيق باستخدام PM2
    pm2 start ecosystem.config.json --env development
    
    print_success "تم بدء تشغيل الخادم بنجاح!"
    print_status "يمكنك الوصول للموقع على: http://localhost:8000"
    print_status "لمراقبة الخادم استخدم: pm2 monit"
}

# إيقاف الخادم
stop_server() {
    print_status "إيقاف خادم Motors Website..."
    pm2 stop ecosystem.config.json
    print_success "تم إيقاف الخادم بنجاح!"
}

# إعادة تشغيل الخادم
restart_server() {
    print_status "إعادة تشغيل خادم Motors Website..."
    cd "$PROJECT_DIR"
    
    # مسح الكاش
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    pm2 restart ecosystem.config.json
    print_success "تم إعادة تشغيل الخادم بنجاح!"
}

# حذف العمليات من PM2
delete_processes() {
    print_status "حذف عمليات Motors Website من PM2..."
    pm2 delete ecosystem.config.json
    print_success "تم حذف العمليات بنجاح!"
}

# عرض حالة الخادم
show_status() {
    print_status "حالة خادم Motors Website:"
    pm2 list
    echo ""
    print_status "معلومات مفصلة:"
    pm2 info motors-website 2>/dev/null || print_warning "الخادم غير يعمل"
}

# عرض السجلات
show_logs() {
    print_status "عرض سجلات الخادم (اضغط Ctrl+C للخروج):"
    pm2 logs motors-website
}

# مراقبة الخادم
monitor_server() {
    print_status "فتح مراقب PM2 (اضغط q للخروج):"
    pm2 monit
}

# تثبيت PM2 إذا لم يكن مثبت
install_pm2() {
    if command -v npm &> /dev/null; then
        print_status "تثبيت PM2..."
        npm install -g pm2
        print_success "تم تثبيت PM2 بنجاح!"
    else
        print_error "npm غير مثبت. يرجى تثبيت Node.js أولاً"
        exit 1
    fi
}

# إعداد PM2 للبدء التلقائي
setup_startup() {
    print_status "إعداد PM2 للبدء التلقائي عند إقلاع النظام..."
    pm2 startup
    pm2 save
    print_success "تم إعداد البدء التلقائي!"
}

# عرض المساعدة
show_help() {
    echo -e "${BLUE}Motors Website Server Management${NC}"
    echo "=================================="
    echo ""
    echo "الاستخدام: $0 [command]"
    echo ""
    echo "الأوامر المتاحة:"
    echo "  start      - بدء تشغيل الخادم"
    echo "  stop       - إيقاف الخادم"
    echo "  restart    - إعادة تشغيل الخادم"
    echo "  status     - عرض حالة الخادم"
    echo "  logs       - عرض سجلات الخادم"
    echo "  monitor    - مراقبة الخادم"
    echo "  delete     - حذف العمليات من PM2"
    echo "  install    - تثبيت PM2"
    echo "  startup    - إعداد البدء التلقائي"
    echo "  help       - عرض هذه المساعدة"
    echo ""
    echo "أمثلة:"
    echo "  $0 start     # بدء الخادم"
    echo "  $0 status    # فحص الحالة"
    echo "  $0 logs      # عرض السجلات"
}

# فحص PM2
check_pm2_status

# معالجة الأوامر
case "$1" in
    start)
        start_server
        ;;
    stop)
        stop_server
        ;;
    restart)
        restart_server
        ;;
    status)
        show_status
        ;;
    logs)
        show_logs
        ;;
    monitor)
        monitor_server
        ;;
    delete)
        delete_processes
        ;;
    install)
        install_pm2
        ;;
    startup)
        setup_startup
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        print_error "أمر غير صحيح: $1"
        echo ""
        show_help
        exit 1
        ;;
esac