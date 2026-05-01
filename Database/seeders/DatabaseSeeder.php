<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use App\Models\Region;
use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed all 27 Egyptian cities with Arabic names
        $cities = [
            ['name' => 'Cairo', 'name_ar' => 'القاهرة'],
            ['name' => 'Alexandria', 'name_ar' => 'الإسكندرية'],
            ['name' => 'Qalyubia', 'name_ar' => 'القليوبية'],
            ['name' => 'Dakahlia', 'name_ar' => 'الدقهلية'],
            ['name' => 'Gharbia', 'name_ar' => 'الغربية'],
            ['name' => 'Monufia', 'name_ar' => 'المنوفية'],
            ['name' => 'Beheira', 'name_ar' => 'البحيرة'],
            ['name' => 'Kafr El Sheikh', 'name_ar' => 'كفر الشيخ'],
            ['name' => 'Damietta', 'name_ar' => 'دمياط'],
            ['name' => 'Port Said', 'name_ar' => 'بورسعيد'],
            ['name' => 'Ismailia', 'name_ar' => 'الإسماعيلية'],
            ['name' => 'Suez', 'name_ar' => 'السويس'],
            ['name' => 'Giza', 'name_ar' => 'الجيزة'],
            ['name' => 'Fayoum', 'name_ar' => 'الفيوم'],
            ['name' => 'Beni Suef', 'name_ar' => 'بني سويف'],
            ['name' => 'Minya', 'name_ar' => 'المنيا'],
            ['name' => 'Assiut', 'name_ar' => 'أسيوط'],
            ['name' => 'Sohag', 'name_ar' => 'سوهاج'],
            ['name' => 'Qena', 'name_ar' => 'قنا'],
            ['name' => 'Luxor', 'name_ar' => 'الأقصر'],
            ['name' => 'Aswan', 'name_ar' => 'أسوان'],
            ['name' => 'Red Sea', 'name_ar' => 'البحر الأحمر'],
            ['name' => 'New Valley', 'name_ar' => 'الوادي الجديد'],
            ['name' => 'Matrouh', 'name_ar' => 'مطروح'],
            ['name' => 'North Sinai', 'name_ar' => 'شمال سيناء'],
            ['name' => 'South Sinai', 'name_ar' => 'جنوب سيناء'],
        ];

        foreach ($cities as $cityData) {
            City::create($cityData);
        }

        // Seed regions for all cities
        $allRegions = [
            // Cairo (1)
            ['city_id' => 1, 'name' => 'Nasr City', 'name_ar' => 'مدينة نصر'],
            ['city_id' => 1, 'name' => 'Heliopolis', 'name_ar' => 'مصر الجديدة'],
            ['city_id' => 1, 'name' => 'Maadi', 'name_ar' => 'المعادي'],
            ['city_id' => 1, 'name' => 'Helwan', 'name_ar' => 'حلوان'],
            ['city_id' => 1, 'name' => 'Shubra', 'name_ar' => 'شبرا'],
            ['city_id' => 1, 'name' => 'Zamalek', 'name_ar' => 'الزمالك'],
            ['city_id' => 1, 'name' => 'Ain Shams', 'name_ar' => 'عين شمس'],
            ['city_id' => 1, 'name' => 'El Mataria', 'name_ar' => 'المطرية'],
            ['city_id' => 1, 'name' => 'El Marg', 'name_ar' => 'المرج'],
            ['city_id' => 1, 'name' => 'Sayeda Zeinab', 'name_ar' => 'السيدة زينب'],
            
            // Alexandria (2)
            ['city_id' => 2, 'name' => 'Agami', 'name_ar' => 'العجمي'],
            ['city_id' => 2, 'name' => 'Sidi Gaber', 'name_ar' => 'سيدي جابر'],
            ['city_id' => 2, 'name' => 'Moharam Bek', 'name_ar' => 'محرم بك'],
            ['city_id' => 2, 'name' => 'Asafra', 'name_ar' => 'العصافرة'],
            ['city_id' => 2, 'name' => 'Smouha', 'name_ar' => 'سموحة'],
            ['city_id' => 2, 'name' => 'Montaza', 'name_ar' => 'المنتزه'],
            ['city_id' => 2, 'name' => 'Wardian', 'name_ar' => 'الورديان'],
            ['city_id' => 2, 'name' => 'Dekheila', 'name_ar' => 'الدخيلة'],
            ['city_id' => 2, 'name' => 'Camp Caesar', 'name_ar' => 'كامب شيزار'],
            ['city_id' => 2, 'name' => 'Bacchus', 'name_ar' => 'باكوس'],
            
            // Qalyubia (3)
            ['city_id' => 3, 'name' => 'Banha', 'name_ar' => 'بنها'],
            ['city_id' => 3, 'name' => 'Shubra El Kheima', 'name_ar' => 'شبرا الخيمة'],
            ['city_id' => 3, 'name' => 'Qanater El Khairiya', 'name_ar' => 'القناطر الخيرية'],
            ['city_id' => 3, 'name' => 'El Khanka', 'name_ar' => 'الخانكة'],
            ['city_id' => 3, 'name' => 'Tukh', 'name_ar' => 'طوخ'],
            ['city_id' => 3, 'name' => 'Qalyub', 'name_ar' => 'قليوب'],
            ['city_id' => 3, 'name' => 'Shibin El Qanater', 'name_ar' => 'شبين القناطر'],
            ['city_id' => 3, 'name' => 'Kafr Shukr', 'name_ar' => 'كفر شكر'],
            ['city_id' => 3, 'name' => 'Obour', 'name_ar' => 'العبور'],
            ['city_id' => 3, 'name' => 'El Khusus', 'name_ar' => 'الخصوص'],
            
            // Dakahlia (4)
            ['city_id' => 4, 'name' => 'Mansoura', 'name_ar' => 'المنصورة'],
            ['city_id' => 4, 'name' => 'Talkha', 'name_ar' => 'طلخا'],
            ['city_id' => 4, 'name' => 'Meet Ghamr', 'name_ar' => 'ميت غمر'],
            ['city_id' => 4, 'name' => 'Dekernes', 'name_ar' => 'دكرنس'],
            ['city_id' => 4, 'name' => 'Belqas', 'name_ar' => 'بلقاس'],
            ['city_id' => 4, 'name' => 'Senbellawein', 'name_ar' => 'السنبلاوين'],
            ['city_id' => 4, 'name' => 'Manzala', 'name_ar' => 'المنزلة'],
            ['city_id' => 4, 'name' => 'Sherbin', 'name_ar' => 'شربين'],
            ['city_id' => 4, 'name' => 'Aga', 'name_ar' => 'أجا'],
            ['city_id' => 4, 'name' => 'Beni Ebeid', 'name_ar' => 'بني عبيد'],
            
            // Gharbia (5)
            ['city_id' => 5, 'name' => 'Tanta', 'name_ar' => 'طنطا'],
            ['city_id' => 5, 'name' => 'El Mahalla El Kubra', 'name_ar' => 'المحلة الكبرى'],
            ['city_id' => 5, 'name' => 'Kafr El Zayat', 'name_ar' => 'كفر الزيات'],
            ['city_id' => 5, 'name' => 'Zefta', 'name_ar' => 'زفتى'],
            ['city_id' => 5, 'name' => 'Basyoun', 'name_ar' => 'بسيون'],
            ['city_id' => 5, 'name' => 'Samannoud', 'name_ar' => 'سمنود'],
            ['city_id' => 5, 'name' => 'El Santa', 'name_ar' => 'السنطة'],
            ['city_id' => 5, 'name' => 'Qutur', 'name_ar' => 'قطور'],
            ['city_id' => 5, 'name' => 'Mahallet Rouh', 'name_ar' => 'محلة روح'],
            ['city_id' => 5, 'name' => 'Kafr Selim', 'name_ar' => 'كفر سليم'],
            
            // Monufia (6)
            ['city_id' => 6, 'name' => 'Shebin El Kom', 'name_ar' => 'شبين الكوم'],
            ['city_id' => 6, 'name' => 'Menouf', 'name_ar' => 'منوف'],
            ['city_id' => 6, 'name' => 'Ashmoun', 'name_ar' => 'أشمون'],
            ['city_id' => 6, 'name' => 'Quesna', 'name_ar' => 'قويسنا'],
            ['city_id' => 6, 'name' => 'El Bagour', 'name_ar' => 'الباجور'],
            ['city_id' => 6, 'name' => 'Tala', 'name_ar' => 'تلا'],
            ['city_id' => 6, 'name' => 'Berket El Sab', 'name_ar' => 'بركة السبع'],
            ['city_id' => 6, 'name' => 'Sadat City', 'name_ar' => 'السادات'],
            ['city_id' => 6, 'name' => 'El Shohada', 'name_ar' => 'الشهداء'],
            ['city_id' => 6, 'name' => 'Sers El Layan', 'name_ar' => 'سرس الليان'],
            
            // Beheira (7)
            ['city_id' => 7, 'name' => 'Damanhour', 'name_ar' => 'دمنهور'],
            ['city_id' => 7, 'name' => 'Kafr El Dawar', 'name_ar' => 'كفر الدوار'],
            ['city_id' => 7, 'name' => 'Rosetta', 'name_ar' => 'رشيد'],
            ['city_id' => 7, 'name' => 'Edku', 'name_ar' => 'إدكو'],
            ['city_id' => 7, 'name' => 'Abu El Matamir', 'name_ar' => 'أبو المطامير'],
            ['city_id' => 7, 'name' => 'Abu Homs', 'name_ar' => 'أبو حمص'],
            ['city_id' => 7, 'name' => 'Delengat', 'name_ar' => 'الدلنجات'],
            ['city_id' => 7, 'name' => 'Rahmaniya', 'name_ar' => 'الرحمانية'],
            ['city_id' => 7, 'name' => 'Mahmoudiya', 'name_ar' => 'المحمودية'],
            ['city_id' => 7, 'name' => 'Hosh Essa', 'name_ar' => 'حوش عيسى'],
            
            // Kafr El Sheikh (8)
            ['city_id' => 8, 'name' => 'Kafr El Sheikh', 'name_ar' => 'كفر الشيخ'],
            ['city_id' => 8, 'name' => 'Desouk', 'name_ar' => 'دسوق'],
            ['city_id' => 8, 'name' => 'Fowa', 'name_ar' => 'فوه'],
            ['city_id' => 8, 'name' => 'Baltim', 'name_ar' => 'بلطيم'],
            ['city_id' => 8, 'name' => 'Biyala', 'name_ar' => 'بيلا'],
            ['city_id' => 8, 'name' => 'Sidi Salem', 'name_ar' => 'سيدي سالم'],
            ['city_id' => 8, 'name' => 'Hamoul', 'name_ar' => 'الحامول'],
            ['city_id' => 8, 'name' => 'Motobas', 'name_ar' => 'مطوبس'],
            ['city_id' => 8, 'name' => 'Riyadh', 'name_ar' => 'الرياض'],
            ['city_id' => 8, 'name' => 'Burullus', 'name_ar' => 'البرلس'],
            
            // Damietta (9)
            ['city_id' => 9, 'name' => 'Damietta', 'name_ar' => 'دمياط'],
            ['city_id' => 9, 'name' => 'Ras El Bar', 'name_ar' => 'رأس البر'],
            ['city_id' => 9, 'name' => 'Farskur', 'name_ar' => 'فارسكور'],
            ['city_id' => 9, 'name' => 'Kafr Saad', 'name_ar' => 'كفر سعد'],
            ['city_id' => 9, 'name' => 'Zarqa', 'name_ar' => 'الزرقا'],
            ['city_id' => 9, 'name' => 'El Rodah', 'name_ar' => 'الروضة'],
            ['city_id' => 9, 'name' => 'Ezbet El Borg', 'name_ar' => 'عزبة البرج'],
            ['city_id' => 9, 'name' => 'El Sarw', 'name_ar' => 'السرو'],
            ['city_id' => 9, 'name' => 'Meet Abu Ghalib', 'name_ar' => 'ميت أبو غالب'],
            ['city_id' => 9, 'name' => 'Kafr El Bateekh', 'name_ar' => 'كفر البطيخ'],
            
            // Port Said (10)
            ['city_id' => 10, 'name' => 'Port Fouad', 'name_ar' => 'بورفؤاد'],
            ['city_id' => 10, 'name' => 'El Sharq District', 'name_ar' => 'حي الشرق'],
            ['city_id' => 10, 'name' => 'El Arab District', 'name_ar' => 'حي العرب'],
            ['city_id' => 10, 'name' => 'El Manakh', 'name_ar' => 'حي المناخ'],
            ['city_id' => 10, 'name' => 'El Dawahy', 'name_ar' => 'حي الضواحي'],
            ['city_id' => 10, 'name' => 'El Zohour', 'name_ar' => 'حي الزهور'],
            ['city_id' => 10, 'name' => 'South District', 'name_ar' => 'حي الجنوب'],
            ['city_id' => 10, 'name' => 'El Qaboty', 'name_ar' => 'القابوطي'],
            ['city_id' => 10, 'name' => 'El Salam', 'name_ar' => 'السلام'],
            ['city_id' => 10, 'name' => 'El Gameel', 'name_ar' => 'الجميل'],
            
            // Ismailia (11)
            ['city_id' => 11, 'name' => 'Ismailia', 'name_ar' => 'الإسماعيلية'],
            ['city_id' => 11, 'name' => 'Fayed', 'name_ar' => 'فايد'],
            ['city_id' => 11, 'name' => 'Qantara East', 'name_ar' => 'القنطرة شرق'],
            ['city_id' => 11, 'name' => 'Qantara West', 'name_ar' => 'القنطرة غرب'],
            ['city_id' => 11, 'name' => 'Tal El Kebir', 'name_ar' => 'التل الكبير'],
            ['city_id' => 11, 'name' => 'Abu Suweir', 'name_ar' => 'أبو صوير'],
            ['city_id' => 11, 'name' => 'Qassasin', 'name_ar' => 'القصاصين'],
            ['city_id' => 11, 'name' => 'Sarabium', 'name_ar' => 'سرابيوم'],
            ['city_id' => 11, 'name' => 'Nefesha', 'name_ar' => 'نفيشة'],
            ['city_id' => 11, 'name' => 'Mostakbal', 'name_ar' => 'المستقبل'],
            
            // Suez (12)
            ['city_id' => 12, 'name' => 'Suez', 'name_ar' => 'السويس'],
            ['city_id' => 12, 'name' => 'Arbaeen', 'name_ar' => 'الأربعين'],
            ['city_id' => 12, 'name' => 'Faisal', 'name_ar' => 'فيصل'],
            ['city_id' => 12, 'name' => 'Ataqa', 'name_ar' => 'عتاقة'],
            ['city_id' => 12, 'name' => 'Ganayen', 'name_ar' => 'الجناين'],
            ['city_id' => 12, 'name' => 'Ain Sokhna', 'name_ar' => 'العين السخنة'],
            ['city_id' => 12, 'name' => 'Salam', 'name_ar' => 'السلام'],
            ['city_id' => 12, 'name' => 'Adabiya', 'name_ar' => 'الأدبية'],
            ['city_id' => 12, 'name' => 'Suez District', 'name_ar' => 'حي السويس'],
            ['city_id' => 12, 'name' => 'Faisal District', 'name_ar' => 'حي فيصل'],
            
            // Giza (13)
            ['city_id' => 13, 'name' => 'Giza', 'name_ar' => 'الجيزة'],
            ['city_id' => 13, 'name' => 'Haram', 'name_ar' => 'الهرم'],
            ['city_id' => 13, 'name' => 'Faisal', 'name_ar' => 'فيصل'],
            ['city_id' => 13, 'name' => 'Sheikh Zayed', 'name_ar' => 'الشيخ زايد'],
            ['city_id' => 13, 'name' => '6th of October', 'name_ar' => '6 أكتوبر'],
            ['city_id' => 13, 'name' => 'Ayyat', 'name_ar' => 'العياط'],
            ['city_id' => 13, 'name' => 'Badrashin', 'name_ar' => 'البدرشين'],
            ['city_id' => 13, 'name' => 'Saf', 'name_ar' => 'الصف'],
            ['city_id' => 13, 'name' => 'Atfih', 'name_ar' => 'أطفيح'],
            ['city_id' => 13, 'name' => 'Manshiyat Al Qanater', 'name_ar' => 'منشأة القناطر'],
            
            // Fayoum (14)
            ['city_id' => 14, 'name' => 'Fayoum', 'name_ar' => 'الفيوم'],
            ['city_id' => 14, 'name' => 'Senouris', 'name_ar' => 'سنورس'],
            ['city_id' => 14, 'name' => 'Itsa', 'name_ar' => 'إطسا'],
            ['city_id' => 14, 'name' => 'Tamiya', 'name_ar' => 'طامية'],
            ['city_id' => 14, 'name' => 'Youssef El Seddik', 'name_ar' => 'يوسف الصديق'],
            ['city_id' => 14, 'name' => 'Ibshaway', 'name_ar' => 'إبشواي'],
            ['city_id' => 14, 'name' => 'Kom Oshim', 'name_ar' => 'كوم أوشيم'],
            ['city_id' => 14, 'name' => 'Qasr El Basel', 'name_ar' => 'قصر الباسل'],
            ['city_id' => 14, 'name' => 'Dar El Salam', 'name_ar' => 'دار السلام'],
            ['city_id' => 14, 'name' => 'Manshaat Abdullah', 'name_ar' => 'منشأة عبد الله'],
            
            // Beni Suef (15)
            ['city_id' => 15, 'name' => 'Beni Suef', 'name_ar' => 'بني سويف'],
            ['city_id' => 15, 'name' => 'Wasta', 'name_ar' => 'الواسطى'],
            ['city_id' => 15, 'name' => 'Nasser', 'name_ar' => 'ناصر'],
            ['city_id' => 15, 'name' => 'Ihnasia', 'name_ar' => 'إهناسيا'],
            ['city_id' => 15, 'name' => 'Beba', 'name_ar' => 'ببا'],
            ['city_id' => 15, 'name' => 'Sumusta', 'name_ar' => 'سمسطا'],
            ['city_id' => 15, 'name' => 'Fashn', 'name_ar' => 'الفشن'],
            ['city_id' => 15, 'name' => 'Seds', 'name_ar' => 'سدس'],
            ['city_id' => 15, 'name' => 'New Beni Suef', 'name_ar' => 'بني سويف الجديدة'],
            ['city_id' => 15, 'name' => 'New Wasta', 'name_ar' => 'الواسطي الجديدة'],
            
            // Minya (16)
            ['city_id' => 16, 'name' => 'Minya', 'name_ar' => 'المنيا'],
            ['city_id' => 16, 'name' => 'Mallawi', 'name_ar' => 'ملوي'],
            ['city_id' => 16, 'name' => 'Samalut', 'name_ar' => 'سمالوط'],
            ['city_id' => 16, 'name' => 'Matai', 'name_ar' => 'مطاي'],
            ['city_id' => 16, 'name' => 'Beni Mazar', 'name_ar' => 'بني مزار'],
            ['city_id' => 16, 'name' => 'Maghagha', 'name_ar' => 'مغاغة'],
            ['city_id' => 16, 'name' => 'Deir Mawas', 'name_ar' => 'دير مواس'],
            ['city_id' => 16, 'name' => 'Adwa', 'name_ar' => 'العدوة'],
            ['city_id' => 16, 'name' => 'Abu Qurqas', 'name_ar' => 'أبو قرقاص'],
            ['city_id' => 16, 'name' => 'Sheikh Fadl', 'name_ar' => 'الشيخ فضل'],
            
            // Assiut (17)
            ['city_id' => 17, 'name' => 'Assiut', 'name_ar' => 'أسيوط'],
            ['city_id' => 17, 'name' => 'Dayrout', 'name_ar' => 'ديروط'],
            ['city_id' => 17, 'name' => 'Qusiya', 'name_ar' => 'القوصية'],
            ['city_id' => 17, 'name' => 'Manfalut', 'name_ar' => 'منفلوط'],
            ['city_id' => 17, 'name' => 'Abnoub', 'name_ar' => 'أبنوب'],
            ['city_id' => 17, 'name' => 'Abu Tig', 'name_ar' => 'أبو تيج'],
            ['city_id' => 17, 'name' => 'Sedfa', 'name_ar' => 'صدفا'],
            ['city_id' => 17, 'name' => 'Ghanaim', 'name_ar' => 'الغنايم'],
            ['city_id' => 17, 'name' => 'Sahel Selim', 'name_ar' => 'ساحل سليم'],
            ['city_id' => 17, 'name' => 'Badari', 'name_ar' => 'البداري'],
            
            // Sohag (18)
            ['city_id' => 18, 'name' => 'Sohag', 'name_ar' => 'سوهاج'],
            ['city_id' => 18, 'name' => 'Akhmim', 'name_ar' => 'أخميم'],
            ['city_id' => 18, 'name' => 'Girga', 'name_ar' => 'جرجا'],
            ['city_id' => 18, 'name' => 'Tahta', 'name_ar' => 'طهطا'],
            ['city_id' => 18, 'name' => 'Maragha', 'name_ar' => 'المراغة'],
            ['city_id' => 18, 'name' => 'Mansha', 'name_ar' => 'المنشأة'],
            ['city_id' => 18, 'name' => 'Dar El Salam', 'name_ar' => 'دار السلام'],
            ['city_id' => 18, 'name' => 'Balina', 'name_ar' => 'البلينا'],
            ['city_id' => 18, 'name' => 'Saqultah', 'name_ar' => 'ساقلتة'],
            ['city_id' => 18, 'name' => 'Osairat', 'name_ar' => 'العسيرات'],
            
            // Qena (19)
            ['city_id' => 19, 'name' => 'Qena', 'name_ar' => 'قنا'],
            ['city_id' => 19, 'name' => 'Nag Hammadi', 'name_ar' => 'نجع حمادي'],
            ['city_id' => 19, 'name' => 'Qus', 'name_ar' => 'قوص'],
            ['city_id' => 19, 'name' => 'Deshna', 'name_ar' => 'دشنا'],
            ['city_id' => 19, 'name' => 'Waqf', 'name_ar' => 'الوقف'],
            ['city_id' => 19, 'name' => 'Naqada', 'name_ar' => 'نقادة'],
            ['city_id' => 19, 'name' => 'Farshut', 'name_ar' => 'فرشوط'],
            ['city_id' => 19, 'name' => 'Abou Tesht', 'name_ar' => 'أبوتشت'],
            ['city_id' => 19, 'name' => 'Qift', 'name_ar' => 'قفط'],
            ['city_id' => 19, 'name' => 'Hegaza', 'name_ar' => 'حجازة'],
            
            // Luxor (20)
            ['city_id' => 20, 'name' => 'Luxor', 'name_ar' => 'الأقصر'],
            ['city_id' => 20, 'name' => 'Armant', 'name_ar' => 'أرمنت'],
            ['city_id' => 20, 'name' => 'Esna', 'name_ar' => 'إسنا'],
            ['city_id' => 20, 'name' => 'Tod', 'name_ar' => 'الطود'],
            ['city_id' => 20, 'name' => 'Bayadiya', 'name_ar' => 'البياضية'],
            ['city_id' => 20, 'name' => 'Zeniya', 'name_ar' => 'الزينية'],
            ['city_id' => 20, 'name' => 'Qurna', 'name_ar' => 'القرنة'],
            ['city_id' => 20, 'name' => 'Karnak', 'name_ar' => 'الكرنك'],
            ['city_id' => 20, 'name' => 'Habyl', 'name_ar' => 'الحبيل'],
            ['city_id' => 20, 'name' => 'Manshaat Al Amari', 'name_ar' => 'منشأة العماري'],
            
            // Aswan (21)
            ['city_id' => 21, 'name' => 'Aswan', 'name_ar' => 'أسوان'],
            ['city_id' => 21, 'name' => 'Kom Ombo', 'name_ar' => 'كوم أمبو'],
            ['city_id' => 21, 'name' => 'Edfu', 'name_ar' => 'إدفو'],
            ['city_id' => 21, 'name' => 'Daraw', 'name_ar' => 'دراو'],
            ['city_id' => 21, 'name' => 'Nasr Nubia', 'name_ar' => 'نصر النوبة'],
            ['city_id' => 21, 'name' => 'Abu Simbel', 'name_ar' => 'أبو سمبل'],
            ['city_id' => 21, 'name' => 'Kalabsha', 'name_ar' => 'كلابشة'],
            ['city_id' => 21, 'name' => 'Sebaeya', 'name_ar' => 'السباعية'],
            ['city_id' => 21, 'name' => 'Radesia', 'name_ar' => 'الرديسية'],
            ['city_id' => 21, 'name' => 'Baselia', 'name_ar' => 'البصيلية'],
            
            // Red Sea (22)
            ['city_id' => 22, 'name' => 'Hurghada', 'name_ar' => 'الغردقة'],
            ['city_id' => 22, 'name' => 'Safaga', 'name_ar' => 'سفاجا'],
            ['city_id' => 22, 'name' => 'Quseir', 'name_ar' => 'القصير'],
            ['city_id' => 22, 'name' => 'Marsa Alam', 'name_ar' => 'مرسى علم'],
            ['city_id' => 22, 'name' => 'Ras Ghareb', 'name_ar' => 'رأس غارب'],
            ['city_id' => 22, 'name' => 'Halaib', 'name_ar' => 'حلايب'],
            ['city_id' => 22, 'name' => 'Shalateen', 'name_ar' => 'شلاتين'],
            ['city_id' => 22, 'name' => 'Berenice', 'name_ar' => 'برنيس'],
            ['city_id' => 22, 'name' => 'El Gouna', 'name_ar' => 'الجونة'],
            ['city_id' => 22, 'name' => 'Sahl Hasheesh', 'name_ar' => 'سهل حشيش'],
            
            // New Valley (23)
            ['city_id' => 23, 'name' => 'Kharga', 'name_ar' => 'الخارجة'],
            ['city_id' => 23, 'name' => 'Dakhla', 'name_ar' => 'الداخلة'],
            ['city_id' => 23, 'name' => 'Farafra', 'name_ar' => 'الفرافرة'],
            ['city_id' => 23, 'name' => 'Paris', 'name_ar' => 'باريس'],
            ['city_id' => 23, 'name' => 'Balat', 'name_ar' => 'بلاط'],
            ['city_id' => 23, 'name' => 'Mut', 'name_ar' => 'موط'],
            ['city_id' => 23, 'name' => 'Qasr', 'name_ar' => 'القصر'],
            ['city_id' => 23, 'name' => 'Teneida', 'name_ar' => 'تنيدة'],
            ['city_id' => 23, 'name' => 'West Mawhoub', 'name_ar' => 'غرب الموهوب'],
            ['city_id' => 23, 'name' => 'East Bulaq', 'name_ar' => 'شرق بولاق'],
            
            // Matrouh (24)
            ['city_id' => 24, 'name' => 'Marsa Matrouh', 'name_ar' => 'مرسى مطروح'],
            ['city_id' => 24, 'name' => 'El Alamein', 'name_ar' => 'العلمين'],
            ['city_id' => 24, 'name' => 'Dabaa', 'name_ar' => 'الضبعة'],
            ['city_id' => 24, 'name' => 'Sidi Barrani', 'name_ar' => 'سيدي براني'],
            ['city_id' => 24, 'name' => 'Nagila', 'name_ar' => 'النجيلة'],
            ['city_id' => 24, 'name' => 'Sallum', 'name_ar' => 'السلوم'],
            ['city_id' => 24, 'name' => 'Hammam', 'name_ar' => 'الحمام'],
            ['city_id' => 24, 'name' => 'Fouka', 'name_ar' => 'فوكة'],
            ['city_id' => 24, 'name' => 'Ras El Hikma', 'name_ar' => 'رأس الحكمة'],
            ['city_id' => 24, 'name' => 'Galala', 'name_ar' => 'الجلالة'],
            
            // North Sinai (25)
            ['city_id' => 25, 'name' => 'Arish', 'name_ar' => 'العريش'],
            ['city_id' => 25, 'name' => 'Sheikh Zuweid', 'name_ar' => 'الشيخ زويد'],
            ['city_id' => 25, 'name' => 'Rafah', 'name_ar' => 'رفح'],
            ['city_id' => 25, 'name' => 'Bir El Abd', 'name_ar' => 'بئر العبد'],
            ['city_id' => 25, 'name' => 'Hasna', 'name_ar' => 'الحسنة'],
            ['city_id' => 25, 'name' => 'Nakhl', 'name_ar' => 'نخل'],
            ['city_id' => 25, 'name' => 'Quseima', 'name_ar' => 'القسيمة'],
            ['city_id' => 25, 'name' => 'Taloul', 'name_ar' => 'التلول'],
            ['city_id' => 25, 'name' => 'Gora', 'name_ar' => 'الجورة'],
            ['city_id' => 25, 'name' => 'Rawda', 'name_ar' => 'الروضة'],
            
            // South Sinai (26)
            ['city_id' => 26, 'name' => 'Sharm El Sheikh', 'name_ar' => 'شرم الشيخ'],
            ['city_id' => 26, 'name' => 'Dahab', 'name_ar' => 'دهب'],
            ['city_id' => 26, 'name' => 'Nuweiba', 'name_ar' => 'نويبع'],
            ['city_id' => 26, 'name' => 'Taba', 'name_ar' => 'طابا'],
            ['city_id' => 26, 'name' => 'Saint Catherine', 'name_ar' => 'سانت كاترين'],
            ['city_id' => 26, 'name' => 'Ras Sedr', 'name_ar' => 'رأس سدر'],
            ['city_id' => 26, 'name' => 'Abu Zenima', 'name_ar' => 'أبو زنيمة'],
            ['city_id' => 26, 'name' => 'Abu Redis', 'name_ar' => 'أبو رديس'],
            ['city_id' => 26, 'name' => 'El Tor', 'name_ar' => 'الطور'],
            ['city_id' => 26, 'name' => 'Wadi Feiran', 'name_ar' => 'وادي فيران'],
        ];

        foreach ($allRegions as $region) {
            Region::create($region);
        }

        // Create test users
        $student = User::create([
            'name' => 'Ahmed Student',
            'email' => 'student@test.com',
            'password' => 'password',
            'role_id' => 1, // student
            'language' => 'ar',
        ]);

        $owner = User::create([
            'name' => 'Mohamed Owner',
            'email' => 'owner@test.com',
            'password' => 'password',
            'role_id' => 2, // owner
            'language' => 'ar',
        ]);

        $broker = User::create([
            'name' => 'Ali Broker',
            'email' => 'broker@test.com',
            'password' => 'password',
            'role_id' => 3, // broker
            'language' => 'ar',
        ]);

        // Create listings for EVERY region in every city
        $types = ['room', 'apartment', 'studio', 'shared'];
        $prices = [1200, 1500, 1800, 2200, 2500, 2800, 3200, 3500, 3800, 4200, 4500, 4800, 5200, 5500, 6000, 6500, 7000, 7500, 8000];
        $owners = [$owner, $broker, $student];
        
        $listings = [];
        $cityAndRegionMap = [];
        
        // Get all regions grouped by city
        $regions = Region::with('city')->get();
        foreach ($regions as $region) {
            if (!isset($cityAndRegionMap[$region->city_id])) {
                $cityAndRegionMap[$region->city_id] = [];
            }
            $cityAndRegionMap[$region->city_id][] = $region;
        }
        
        // Create at least 1-2 listings per region
        foreach ($cityAndRegionMap as $cityId => $cityRegions) {
            foreach ($cityRegions as $idx => $region) {
                $type = $types[$idx % count($types)];
                $price = $prices[($cityId + $region->id) % count($prices)];
                $user = $owners[($region->id) % count($owners)];
                $bedrooms = $type === 'room' || $type === 'studio' ? 1 : 2;
                $bathrooms = $type === 'room' ? 1 : ($type === 'studio' ? 1 : 2);
                $areaSize = $type === 'room' ? rand(12, 20) : ($type === 'studio' ? rand(25, 40) : rand(50, 100));
                
                // Generate realistic addresses
                $streetNumbers = [123, 456, 789, 234, 567, 890, 345, 678, 901, 210];
                $streetNames = [
                    'Main Street', 'University Avenue', 'Central Road', 'Market Street', 'Garden Lane',
                    'Park Road', 'River Street', 'City Center', 'New Street', 'Tower Lane',
                    'شارع النيل', 'شارع التحرير', 'شارع الجمهورية', 'شارع الثورة', 'شارع النيس'
                ];
                $address = $streetNumbers[$idx % count($streetNumbers)] . ' ' . $streetNames[$idx % count($streetNames)];
                
                // Rules
                $rules = 'No smoking | Quiet hours 10PM-8AM | No pets allowed | Monthly payment required';
                $rules_ar = 'لا للتدخين | ساعات هدوء من 10 مساءً إلى 8 صباحًا | لا حيوانات أليفة | الدفع الشهري مطلوب';
                
                $listings[] = [
                    'user_id' => $user->id,
                    'title' => ucfirst($type) . ' in ' . $region->name,
                    'title_ar' => ucfirst($type) . ' في ' . $region->name_ar,
                    'description' => 'High quality ' . $type . ' accommodation in ' . $region->name . '. Fully furnished, modern amenities, close to university and shops.',
                    'description_ar' => 'سكن عالي الجودة من نوع ' . $type . ' في ' . $region->name_ar . '. مفروش بالكامل مع تجهيزات حديثة وقريب من الجامعة والمحلات.',
                    'price' => $price,
                    'city_id' => $cityId,
                    'region_id' => $region->id,
                    'type' => $type,
                    'bedrooms' => $bedrooms,
                    'bathrooms' => $bathrooms,
                    'is_available' => true,
                    'address' => $address . ', ' . $region->name,
                    'address_ar' => $address . '، ' . $region->name_ar,
                    'area_sqm' => $areaSize,
                    'rules' => $rules,
                    'rules_ar' => $rules_ar,
                ];
            }
        }
        
        // Create all listings with images (cycle through 22 available images for all 260 listings)
        $availableImages = [
            'listings/listing_1_01.jpg',
            'listings/listing_2_01.jpg',
            'listings/listing_3_01.jpg',
            'listings/listing_4_01.jpg',
            'listings/listing_5_01.jpg',
            'listings/listing_6_01.jpg',
            'listings/listing_7_01.jpg',
            'listings/listing_8_01.jpg',
            'listings/listing_9_01.jpg',
            'listings/listing_10_01.jpg',
            'listings/listing_11_01.jpg',
            'listings/listing_12_01.jpg',
            'listings/listing_13_01.jpg',
            'listings/listing_14_01.jpg',
            'listings/listing_15_01.jpg',
            'listings/listing_16_01.jpg',
            'listings/listing_17_01.jpg',
            'listings/listing_18_01.jpg',
            'listings/listing_19_01.jpg',
            'listings/listing_20_01.jpg',
            'listings/listing_21_01.jpg',
            'listings/listing_22_01.jpg',
        ];
        
        $imageCount = count($availableImages);
        $listingIndex = 0;
        
        foreach ($listings as $listing) {
            $created = Listing::create($listing);
            
            // Cycle through available images for all listings
            $imageIndex = $listingIndex % $imageCount;
            $imagePath = $availableImages[$imageIndex];
            
            $created->images()->create([
                'path' => $imagePath,
                'is_primary' => true,
                'order' => 1,
            ]);
            
            $listingIndex++;
        }
    }
}
