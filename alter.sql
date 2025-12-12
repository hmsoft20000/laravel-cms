-- 1. جداول المحتوى الرئيسية (Services, Portfolios, Blogs, Items, Sectors)
-- هذه الجداول تحتوي عادة على Title, Content, Short Content

ALTER TABLE service_translations 
ADD FULLTEXT INDEX ft_service_title (title),
ADD FULLTEXT INDEX ft_service_content (content),
ADD FULLTEXT INDEX ft_service_short_content (short_content);

ALTER TABLE portfolio_translations 
ADD FULLTEXT INDEX ft_portfolio_title (title),
ADD FULLTEXT INDEX ft_portfolio_content (content),
ADD FULLTEXT INDEX ft_portfolio_short_content (short_content);

ALTER TABLE blog_translations 
ADD FULLTEXT INDEX ft_blog_title (title),
ADD FULLTEXT INDEX ft_blog_content (content),
ADD FULLTEXT INDEX ft_blog_short_content (short_content);

ALTER TABLE item_translations 
ADD FULLTEXT INDEX ft_item_title (title),
ADD FULLTEXT INDEX ft_item_content (content),
ADD FULLTEXT INDEX ft_item_short_content (short_content);

ALTER TABLE sectors_translations 
ADD FULLTEXT INDEX ft_sector_name (name),
ADD FULLTEXT INDEX ft_sector_short_content (short_content);

-- 2. جداول التصنيفات والخصائص (Categories, Attributes)

ALTER TABLE category_translations 
ADD FULLTEXT INDEX ft_category_title (title);

ALTER TABLE attribute_translations 
ADD FULLTEXT INDEX ft_attribute_title (title);


-- 3. جداول الجهات والمنظمات (Organizations, Teams)

ALTER TABLE organization_translations 
ADD FULLTEXT INDEX ft_org_name (name),
ADD FULLTEXT INDEX ft_org_content (content);


ALTER TABLE teams 
ADD FULLTEXT INDEX ft_team_name (name);

ALTER TABLE team_translations 
ADD FULLTEXT INDEX ft_team_job (job);

-- 4. جداول عامة (Sliders, FAQs, Contact Us)

ALTER TABLE sliders_translations 
ADD FULLTEXT INDEX ft_slider_title (title),
ADD FULLTEXT INDEX ft_slider_content (content);

ALTER TABLE faq_translations 
ADD FULLTEXT INDEX ft_faq_question (question),
ADD FULLTEXT INDEX ft_faq_answer (answer);

ALTER TABLE contact_us_messages 
ADD FULLTEXT INDEX ft_contact_name (name),
ADD FULLTEXT INDEX ft_contact_email (email),
ADD FULLTEXT INDEX ft_contact_subject (subject),
ADD FULLTEXT INDEX ft_contact_message (message);

-- 5. جداول الصلاحيات والأدوار (Roles, Permissions)

ALTER TABLE roles 
ADD FULLTEXT INDEX ft_role_name (name),
ADD FULLTEXT INDEX ft_role_description (description);

ALTER TABLE permissions 
ADD FULLTEXT INDEX ft_permission_name (name),
ADD FULLTEXT INDEX ft_permission_description (description);

-- 6. جداول القيم والإحصائيات (Our Values, Statistics)

ALTER TABLE our_value_translations 
ADD FULLTEXT INDEX ft_value_title (title),
ADD FULLTEXT INDEX ft_value_desc (description);

ALTER TABLE statistics_translations 
ADD FULLTEXT INDEX ft_stat_title (title);


-- 1. جداول خيارات وخصائص المنتجات (Shop Addons & Options)
ALTER TABLE item_addon_translations 
ADD FULLTEXT INDEX ft_item_addon_title (title);

ALTER TABLE item_addon_option_translations 
ADD FULLTEXT INDEX ft_item_addon_opt_title (title);

ALTER TABLE attribute_option_translations 
ADD FULLTEXT INDEX ft_attr_opt_title (title);

-- 2. جداول التحميلات والميزات (Downloads & Features)
ALTER TABLE download_item_translations 
ADD FULLTEXT INDEX ft_dl_item_title (title),
ADD FULLTEXT INDEX ft_dl_item_short_content (short_content),
ADD FULLTEXT INDEX ft_dl_item_content (content);

ALTER TABLE feature_translations 
ADD FULLTEXT INDEX ft_feature_title (title),
ADD FULLTEXT INDEX ft_feature_description (description);



-- 4. الصفحات القانونية والميتا (Legal & Page Meta)
-- ملاحظة: legal_translations لا تحتوي على title في المخطط، بل content و short_content
ALTER TABLE legal_translations 
ADD FULLTEXT INDEX ft_legal_content (content),
ADD FULLTEXT INDEX ft_legal_short_content (short_content),
ADD FULLTEXT INDEX ft_legal_meta_title (meta_title),
ADD FULLTEXT INDEX ft_legal_meta_desc (meta_description);

ALTER TABLE pages_meta_translations 
ADD FULLTEXT INDEX ft_pm_title (title),
ADD FULLTEXT INDEX ft_pm_desc (description),
ADD FULLTEXT INDEX ft_pm_keywords (keywords);








-- 1. تحسين العلاقات البوليمورفية (Polymorphic Optimization)
-- تأكدنا من وجود الفهرس (owner_type, owner_id) لسرعة الـ Join/WhereHas
-- بعض الجداول لديها فهرس لكن غير مركب بشكل صريح أو ناقص


-- جدول الأسئلة الشائعة (Faqs)
CREATE INDEX idx_faqs_owner ON faqs (owner_type, owner_id);

-- جدول الميزات (Features)
CREATE INDEX idx_features_owner ON features (owner_type, owner_id);

-- 2. تحسين الفلترة الشائعة (Status + Sorting)
-- هذه الفهارس تسرع الاستعلامات التي تطلب العناصر النشطة والمرتبة (وهي 90% من استعلامات الموقع)

CREATE INDEX idx_items_active_sort ON items (is_active, created_at);
CREATE INDEX idx_items_active_price ON items (is_active, price);

CREATE INDEX idx_blogs_active_sort ON blogs (is_active, sort_number);
CREATE INDEX idx_blogs_active_date ON blogs (is_active, created_at);

CREATE INDEX idx_services_active_sort ON services (is_active, sort_number);

CREATE INDEX idx_portfolios_active_sort ON portfolios (is_active, sort_number);

CREATE INDEX idx_sliders_status_type ON sliders (status, type, sort_number);

CREATE INDEX idx_categories_active_type ON categories (is_active, type, parent_id);

CREATE INDEX idx_users_active_type ON users (is_active, type);

-- 3. تحسين البحث في القيم (Attributes)
-- هذا الفهرس مهم جداً لسرعة الـ Filters الديناميكية
CREATE INDEX idx_attr_values_composite ON attribute_values (attribute_id, owner_type, owner_id);