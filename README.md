# منصة دارنا العقارية (Darana API Platform)

منصة عقارية برمجية (API-First) تعتمد على إطار العمل Laravel 11. تم تصميم النظام وفق معماريّة سليمة (Clean Architecture)، وطباعة صارمة للأنواع (Strict Typing)، ودعم كامل للإصدارات المتعددة (V1 و V2) لخدمة تطبيقات الموبايل ولوحات التحكم بمرونة عالية.

---

## البنية الهندسية للمشروع (Architectural Overview)

يعتمد مشروع "دارنا" على فصل منطق العمليات (Business Logic) عن طبقة بروتوكولات النقل (HTTP Controllers) ونماذج البيانات (Models) من خلال طبقة خدمات متخصصة (Services Layer):

- **بنية API-First**: معالجة الطلبات عبر المتحكمات (Controllers) المربوطة بطلبات التحقق (FormRequests) والخدمات (Services)، مع إعادة البيانات عبر موارد واجهات البرمجة (API Resources).
- **نمط طبقة الخدمات (Service Layer Pattern)**: إدارة العمليات المعقدة والفلترة والمعاملات المالية داخل `PropertyService`، بينما يتولى `PropertyCacheService` إدارة التخزين المؤقت.
- **التحقق وتأكيد البيانات (Data Validation & Types)**: استخدام FormRequests مخصصة للتحقق من الصلاحيات والبيانات، مع استخدام التعدادات (Enums) لضبط القيم الثابتة بأسلوب آمن برمجياً.
- **العلاقات متعددة الأشكال (Polymorphic Relations)**: تجميع العلاقات المشتركة ضمن Traits مثل (`HasImage`, `HasComments`, `HasCategories`) وإدارتها مركزياً.

---

## هيكلية قاعدة البيانات والتحسينات (Database Schema & Optimization)

تم إعداد قواعد البيانات لمعالجة كفاءة استعلامات القراءة على الجداول الكبيرة:

- **الجداول الأساسية والفهارس (Indexes)**:
  - `governorates`: جدول مرجعي للمحافظات لربط وتسهيل الفلترة المكانية.
  - `offices`: جدول المكاتب العقارية المرتبطة بالحسابات البرمجية للمستخدمين.
  - `properties`: الجدول الرئيسي المخصص للعقارات، ويشمل البيانات الأساسية، التفاصيل المالية، والحقول الداخلية الحساسة. تم دعم الجدول بفهارس مركبة `(governorate_id, status, type)` وفهرس خاص لـ `is_featured` لتسريع عمليات الفلترة.
  - `images`: جدول متعدد الأشكال لإدارة الوسائط عبر النماذج المختلفة.

- **تعيين العلاقات متعددة الأشكال (Polymorphic MorphMap)**:
  تم تكوين الـ `Relation::morphMap` داخل ملف `AppServiceProvider` لضمان إدراج أسماء نصية صريحة ومختصرة (`property`, `office`) في قاعدة البيانات بدلاً من المسارات البرمجية الكاملة للـ Classes.

---

## التعدادات الصارمة (Strict Enums)

تم استخدام String Enums عبر النماذج وطلبات التحقق لضمان اتساق البيانات المطلوبة:

- `PropertyStatus`: `available`, `reserved`, `sold`
- `PropertyType`: `sale`, `rent`
- `Currency`: `SYP`, `USD`, `EUR`

---

## استراتيجية الترقيم وتصفح البيانات (Pagination Strategy)

تمت تطبيق استراتيجية دوال ترقيم مزدوجة لتلائم أنماط الاستخدام المختلفة:

1. **الترقيم المعتمد على الأوفست (`paginate`)**: مستخدم في لوحات التحكم والإدارة التي تتطلب معرفة إجمالي الأوراق والانتقال المباشر لأرقام الصفحات.
2. **الترقيم المعتمد على المؤشر (`cursorPaginate`)**: مستخدم في واجهات وتطبيقات الموبايل لدعم التصفح اللانهائي السريع وخفض الحمل على قاعدة البيانات في السجلات الضخمة.

---

## استراتيجية التخزين المؤقت (Redis Caching Strategy)

يتم التحكم بالتخزين المؤقت من خلال `PropertyCacheService` باستعمال وسم العلامات (Tagged Caching) في Redis:

- **البيانات المخزنة**: تُخزن تفاصيل العقار الفردية (`property_details_{id}`) لمدة 3600 ثانية (ساعة واحدة) لتقليل الاستعلامات المكررة.
- **استثناء الاستعلامات المفلترة**: لا يتم تخزين نتائج البحث الديناميكية متعددة الخيارات تجنباً لاستهلاك ذاكرة Cache وتشتتها.
- **إبطال الكاش (Cache Invalidation)**: عند تحديث أو حذف عقار، يتم مسح التاجات المرتبطة فوراً (`property_{id}`, `governorate_{id}`, `featured`, `properties`) لضمان تزامن البيانات بدقة.

---

## إصدارات واجهة البرمجة (API Versioning: V1 vs V2)

يدعم النظام إصدارات الـ API عبر التسميات الهيكلية للمسارات واستجابات الـ Resources:

- **الإصدار الأول (`/api/v1/`)**:
  - يعرض سعر العقار كقيمة رقمية مباشرة.
  - استجابات معيارية بسيطة مخصصة للأنظمة الأولية.

- **الإصدار الثاني (`/api/v2/`)**:
  - إعادة هيكلة السعر ليكون كائناً يحتوي على المبلغ ونوع العملة.
  - إظهار بعض البيانات المشروطة بالصلاحيات (مثل رقم المالك) بحسب سياق المستثمر أو المستخدم المسجل.
  - اعتماد الترقيم بالمؤشر (Cursor Pagination) بشكل افتراضي للقوائم.

---

## الهيكل المصدري للمشروع (Directory Structure)

```text
app/
├── Enums/
│   ├── Currency.php
│   ├── PropertyStatus.php
│   └── PropertyType.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── V1/
│   │       │   └── PropertyController.php
│   │       └── V2/
│   │           └── PropertyController.php
│   ├── Requests/
│   │   ├── StorePropertyRequest.php
│   │   └── UpdatePropertyRequest.php
│   └── Resources/
│       ├── V1/
│       │   └── PropertyResource.php
│       └── V2/
│           └── PropertyResource.php
├── Models/
│   ├── Concerns/
│   │   ├── HasCategories.php
│   │   ├── HasComments.php
│   │   └── HasImage.php
│   ├── Governorate.php
│   ├── Image.php
│   ├── Office.php
│   └── Property.php
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    ├── PropertyCacheService.php
    └── PropertyService.php
```

---

## إدارة النسخ والتتبع (Git Workflow)

تتم إضافة وإرسال التغييرات بأسلوب وحداتي وفق معايير Conventional Commits:

1. التهيئة الهيكلية الأساسية، ملفات الهجرة (Migrations)، الـ Enums، وضبط MorphMap.
2. إضافة متحكمات الإصدار الثاني (V2 Controllers) والـ Polymorphic Concerns.
3. توثيق المعمارية وقواعد البيانات ضمن ملف README.md.